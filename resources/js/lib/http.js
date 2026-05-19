/**
 * JSON API helper with CSRF and standardized error handling.
 */
export async function api(url, options = {}) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const method = (options.method || 'GET').toUpperCase();

    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(options.headers || {}),
    };

    if (csrf && method !== 'GET') {
        headers['X-CSRF-TOKEN'] = csrf;
    }

    if (options.body && !(options.body instanceof FormData) && !headers['Content-Type']) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        method,
        headers,
    });

    let payload = {};
    const contentType = response.headers.get('content-type') || '';

    if (contentType.includes('application/json')) {
        payload = await response.json();
    }

    if (!response.ok) {
        const error = new Error(payload.message || 'Request failed');
        error.status = response.status;
        error.payload = payload;
        throw error;
    }

    return payload;
}

export function postJson(url, body = {}) {
    return api(url, {
        method: 'POST',
        body: JSON.stringify(body),
    });
}
