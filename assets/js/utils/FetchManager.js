export class FetchManager {
  async get(endpoint, responseType = "json") {
    const response = await fetch(`${endpoint}`);
    if (!response.ok) {
      throw new Error(`Failed to fetch data from ${endpoint}`);
    }
    return responseType === "json"
      ? await response.json()
      : await response.text();
  }

  async post(endpoint, data) {
    const response = await fetch(`${endpoint}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });
    if (!response.ok) {
      throw new Error(`Failed to post data to ${endpoint}`);
    }
    return await response.json();
  }

  async put(endpoint, data) {
    const response = await fetch(`${endpoint}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });
    if (!response.ok) {
      throw new Error(`Failed to put data to ${endpoint}`);
    }
    return await response.json();
  }

  async delete(endpoint) {
    const response = await fetch(`${endpoint}`, {
      method: "DELETE",
    });
    if (!response.ok) {
      throw new Error(`Failed to delete data from ${endpoint}`);
    }
    return await response.json();
  }
}
