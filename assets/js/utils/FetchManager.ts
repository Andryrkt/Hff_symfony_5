export class FetchManager {
  apiBase: string;

  constructor() {
    const meta = typeof document !== "undefined"
      ? document.querySelector('meta[name="api-base"]')
      : null;
    const configuredBase = meta?.getAttribute("content") || "";
    this.apiBase = configuredBase.replace(/\/$/, "");
  }

  withBase(endpoint: string): string {
    if (!endpoint) return endpoint;
    // Absolute URL -> return as-is
    if (/^https?:\/\//i.test(endpoint)) return endpoint;
    // If a base was configured, prefix it
    return this.apiBase ? `${this.apiBase}${endpoint}` : endpoint;
  }

  async handleResponse(response: Response, responseType: string, urlForError: string): Promise<any> {
    if (response.ok) {
      if (responseType === "text") return await response.text();
      // default to JSON if possible
      const contentType = response.headers.get("content-type") || "";
      if (responseType === "json" || /application\/(ld\+)?json/i.test(contentType)) {
        return await response.json();
      }
      return await response.text();
    }

    // Build rich error
    let bodySnippet = "";
    try {
      const text = await response.text();
      bodySnippet = text?.slice(0, 500) || "";
    } catch (_) {
      // ignore
    }
    const status = `${response.status} ${response.statusText}`.trim();
    throw new Error(`HTTP ${status} for ${urlForError}${bodySnippet ? ` — ${bodySnippet}` : ""}`);
  }

  async get(endpoint: string, responseType = "json"): Promise<any> {
    const url = this.withBase(endpoint);
    const response = await fetch(url, {
      method: "GET",
      headers: { Accept: "application/json, application/ld+json;q=0.9, */*;q=0.1" },
      credentials: "include",
    });
    return this.handleResponse(response, responseType, `GET ${endpoint}`);
  }

  async post(endpoint: string, data: any): Promise<any> {
    const url = this.withBase(endpoint);
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json, application/ld+json;q=0.9, */*;q=0.1",
      },
      body: JSON.stringify(data),
      credentials: "include",
    });
    return this.handleResponse(response, "json", `POST ${endpoint}`);
  }

  async put(endpoint: string, data: any): Promise<any> {
    const url = this.withBase(endpoint);
    const response = await fetch(url, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json, application/ld+json;q=0.9, */*;q=0.1",
      },
      body: JSON.stringify(data),
      credentials: "include",
    });
    return this.handleResponse(response, "json", `PUT ${endpoint}`);
  }

  async delete(endpoint: string): Promise<any> {
    const url = this.withBase(endpoint);
    const response = await fetch(url, {
      method: "DELETE",
      headers: { Accept: "application/json, application/ld+json;q=0.9, */*;q=0.1" },
      credentials: "include",
    });
    return this.handleResponse(response, "json", `DELETE ${endpoint}`);
  }
}
