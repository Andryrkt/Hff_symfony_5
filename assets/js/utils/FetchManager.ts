/**
 * Gestionnaire de requêtes HTTP avec support de timeout, retry et cache
 */

/**
 * Configuration pour les requêtes HTTP
 */
export interface FetchConfig {
  timeout?: number;
  retries?: number;
  retryDelay?: number;
  cache?: boolean;
  headers?: Record<string, string>;
  signal?: AbortSignal;
}

/**
 * Erreur HTTP personnalisée
 */
export class FetchError extends Error {
  constructor(
    public status: number,
    public statusText: string,
    public url: string,
    public body?: string
  ) {
    super(`HTTP ${status} ${statusText} for ${url}${body ? ` — ${body}` : ''}`);
    this.name = 'FetchError';
    Object.setPrototypeOf(this, FetchError.prototype);
  }
}

/**
 * Gestionnaire de requêtes HTTP
 */
export class FetchManager {
  private apiBase: string;
  private defaultConfig: FetchConfig = {
    timeout: 30000, // 30 secondes
    retries: 3,
    retryDelay: 1000,
    cache: false
  };
  private cache: Map<string, { data: any; timestamp: number }> = new Map();
  private readonly CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

  constructor(config?: Partial<FetchConfig>) {
    const meta = typeof document !== 'undefined'
      ? document.querySelector('meta[name="api-base"]')
      : null;
    const configuredBase = meta?.getAttribute('content') || '';
    this.apiBase = configuredBase.replace(/\/$/, '');

    if (config) {
      this.defaultConfig = { ...this.defaultConfig, ...config };
    }
  }

  /**
   * Construit l'URL complète avec la base API
   */
  private withBase(endpoint: string): string {
    if (!endpoint) return endpoint;
    // Absolute URL -> return as-is
    if (/^https?:\/\//i.test(endpoint)) return endpoint;
    // If a base was configured, prefix it
    return this.apiBase ? `${this.apiBase}${endpoint}` : endpoint;
  }

  /**
   * Gère la réponse HTTP
   */
  private async handleResponse(
    response: Response,
    responseType: string,
    urlForError: string
  ): Promise<any> {
    if (response.ok) {
      if (responseType === 'text') return await response.text();

      const contentType = response.headers.get('content-type') || '';
      if (responseType === 'json' || /application\/(ld\+)?json/i.test(contentType)) {
        return await response.json();
      }
      return await response.text();
    }

    // Build rich error
    let bodySnippet = '';
    try {
      const text = await response.text();
      bodySnippet = text?.slice(0, 500) || '';
    } catch (_) {
      // ignore
    }

    throw new FetchError(
      response.status,
      response.statusText,
      urlForError,
      bodySnippet
    );
  }

  /**
   * Effectue une requête avec timeout
   */
  private async fetchWithTimeout(
    url: string,
    options: RequestInit,
    timeout: number
  ): Promise<Response> {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), timeout);

    try {
      const response = await fetch(url, {
        ...options,
        signal: controller.signal
      });
      return response;
    } catch (error) {
      if (error instanceof Error && error.name === 'AbortError') {
        throw new Error(`Request timeout after ${timeout}ms for ${url}`);
      }
      throw error;
    } finally {
      clearTimeout(timeoutId);
    }
  }

  /**
   * Effectue une requête avec retry logic
   */
  private async fetchWithRetry(
    url: string,
    options: RequestInit,
    config: FetchConfig
  ): Promise<Response> {
    const { retries = 0, retryDelay = 1000, timeout = 30000 } = config;
    let lastError: Error | null = null;

    for (let attempt = 0; attempt <= retries; attempt++) {
      try {
        return await this.fetchWithTimeout(url, options, timeout);
      } catch (error) {
        lastError = error as Error;

        // Ne pas retry sur les erreurs 4xx (client errors)
        if (error instanceof FetchError && error.status >= 400 && error.status < 500) {
          throw error;
        }

        // Dernier essai
        if (attempt === retries) {
          throw lastError;
        }

        // Attendre avant le prochain essai (exponential backoff)
        const delay = retryDelay * Math.pow(2, attempt);
        await new Promise(resolve => setTimeout(resolve, delay));
      }
    }

    throw lastError || new Error('Request failed');
  }

  /**
   * Récupère depuis le cache si disponible
   */
  private getFromCache(key: string): any | null {
    const cached = this.cache.get(key);
    if (!cached) return null;

    const isExpired = Date.now() - cached.timestamp > this.CACHE_DURATION;
    if (isExpired) {
      this.cache.delete(key);
      return null;
    }

    return cached.data;
  }

  /**
   * Stocke dans le cache
   */
  private setCache(key: string, data: any): void {
    this.cache.set(key, {
      data,
      timestamp: Date.now()
    });
  }

  /**
   * Effectue une requête GET
   */
  async get(
    endpoint: string,
    responseType: 'json' | 'text' = 'json',
    config?: FetchConfig
  ): Promise<any> {
    const url = this.withBase(endpoint);
    const mergedConfig = { ...this.defaultConfig, ...config };

    // Vérifier le cache
    if (mergedConfig.cache) {
      const cached = this.getFromCache(url);
      if (cached !== null) {
        return cached;
      }
    }

    const response = await this.fetchWithRetry(
      url,
      {
        method: 'GET',
        headers: {
          Accept: 'application/json, application/ld+json;q=0.9, */*;q=0.1',
          ...mergedConfig.headers
        },
        credentials: 'include'
      },
      mergedConfig
    );

    const data = await this.handleResponse(response, responseType, `GET ${endpoint}`);

    // Stocker dans le cache
    if (mergedConfig.cache) {
      this.setCache(url, data);
    }

    return data;
  }

  /**
   * Effectue une requête POST
   */
  async post(endpoint: string, data: any, config?: FetchConfig): Promise<any> {
    const url = this.withBase(endpoint);
    const mergedConfig = { ...this.defaultConfig, ...config };

    const response = await this.fetchWithRetry(
      url,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json, application/ld+json;q=0.9, */*;q=0.1',
          ...mergedConfig.headers
        },
        body: JSON.stringify(data),
        credentials: 'include'
      },
      mergedConfig
    );

    return this.handleResponse(response, 'json', `POST ${endpoint}`);
  }

  /**
   * Effectue une requête PUT
   */
  async put(endpoint: string, data: any, config?: FetchConfig): Promise<any> {
    const url = this.withBase(endpoint);
    const mergedConfig = { ...this.defaultConfig, ...config };

    const response = await this.fetchWithRetry(
      url,
      {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json, application/ld+json;q=0.9, */*;q=0.1',
          ...mergedConfig.headers
        },
        body: JSON.stringify(data),
        credentials: 'include'
      },
      mergedConfig
    );

    return this.handleResponse(response, 'json', `PUT ${endpoint}`);
  }

  /**
   * Effectue une requête PATCH
   */
  async patch(endpoint: string, data: any, config?: FetchConfig): Promise<any> {
    const url = this.withBase(endpoint);
    const mergedConfig = { ...this.defaultConfig, ...config };

    const response = await this.fetchWithRetry(
      url,
      {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json, application/ld+json;q=0.9, */*;q=0.1',
          ...mergedConfig.headers
        },
        body: JSON.stringify(data),
        credentials: 'include'
      },
      mergedConfig
    );

    return this.handleResponse(response, 'json', `PATCH ${endpoint}`);
  }

  /**
   * Effectue une requête DELETE
   */
  async delete(endpoint: string, config?: FetchConfig): Promise<any> {
    const url = this.withBase(endpoint);
    const mergedConfig = { ...this.defaultConfig, ...config };

    const response = await this.fetchWithRetry(
      url,
      {
        method: 'DELETE',
        headers: {
          Accept: 'application/json, application/ld+json;q=0.9, */*;q=0.1',
          ...mergedConfig.headers
        },
        credentials: 'include'
      },
      mergedConfig
    );

    return this.handleResponse(response, 'json', `DELETE ${endpoint}`);
  }

  /**
   * Effectue une requête générique
   */
  async request(
    method: string,
    endpoint: string,
    options?: {
      data?: any;
      responseType?: 'json' | 'text';
      config?: FetchConfig;
    }
  ): Promise<any> {
    const { data, responseType = 'json', config } = options || {};

    switch (method.toUpperCase()) {
      case 'GET':
        return this.get(endpoint, responseType, config);
      case 'POST':
        return this.post(endpoint, data, config);
      case 'PUT':
        return this.put(endpoint, data, config);
      case 'PATCH':
        return this.patch(endpoint, data, config);
      case 'DELETE':
        return this.delete(endpoint, config);
      default:
        throw new Error(`Unsupported HTTP method: ${method}`);
    }
  }

  /**
   * Vide le cache
   */
  clearCache(): void {
    this.cache.clear();
  }

  /**
   * Supprime une entrée du cache
   */
  removeCacheEntry(endpoint: string): void {
    const url = this.withBase(endpoint);
    this.cache.delete(url);
  }
}
