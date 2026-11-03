let apiBase = getInitialApiBase();

export class ApiError extends Error {
  constructor(message, { status = 0, code = "API_ERROR", details = null } = {}) {
    super(message);
    this.name = "ApiError";
    this.status = status;
    this.code = code;
    this.details = details;
  }
}

function getInitialApiBase() {
  const requestedBase = new URLSearchParams(window.location.search).get("api");
  const savedBase = window.localStorage.getItem("dashbite.apiBase");
  return (requestedBase || savedBase || "/api").trim().replace(/\/+$/, "") || "/api";
}

async function request(path, { method = "GET", body } = {}) {
  let response;
  try {
    response = await window.fetch(`${apiBase}${path}`, {
      method,
      headers: body === undefined ? { Accept: "application/json" } : {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      ...(body === undefined ? {} : { body: JSON.stringify(body) }),
    });
  } catch (error) {
    const reason = error instanceof Error ? error.message : "Network request failed.";
    throw new ApiError(`Could not reach ${apiBase}${path}. Check the API URL and network connection. ${reason}`, {
      code: "NETWORK_ERROR",
    });
  }

  const responseText = await response.text();
  let data = null;
  if (responseText) {
    try {
      data = JSON.parse(responseText);
    } catch {
      if (response.ok) {
        throw new ApiError("The server returned a response that is not valid JSON.", {
          status: response.status,
          code: "INVALID_RESPONSE",
        });
      }
      data = { message: responseText };
    }
  }

  if (!response.ok) {
    throw new ApiError(data?.message || `Request failed with HTTP ${response.status}.`, {
      status: response.status,
      code: data?.error || "HTTP_ERROR",
      details: data,
    });
  }

  return data;
}

export const api = Object.freeze({
  createOrder(payload) {
    return request("/orders", { method: "POST", body: payload });
  },
  acceptOffer(payload) {
    return request("/dispatch/accept-offer", { method: "POST", body: payload });
  },
});

export function getApiBaseUrl() {
  return apiBase;
}

export function setApiBaseUrl(value) {
  const base = value.trim().replace(/\/+$/, "");
  const isRelativePath = base.startsWith("/") && !base.startsWith("//");
  if (!base || (!isRelativePath && !/^https?:\/\//i.test(base))) {
    throw new Error("Enter an /api path or an absolute HTTP(S) API URL.");
  }
  if (!isRelativePath) {
    const parsed = new URL(base);
    if (parsed.username || parsed.password) {
      throw new Error("API URLs must not contain embedded credentials.");
    }
  }
  window.localStorage.setItem("dashbite.apiBase", base);
  apiBase = base;
  return apiBase;
}

export function formatError(error) {
  if (!(error instanceof Error)) return "The request failed unexpectedly.";
  if (typeof error.code === "string" && error.code !== "API_ERROR" && error.code !== "NETWORK_ERROR") {
    return `${error.code}: ${error.message}`;
  }
  return error.message;
}
