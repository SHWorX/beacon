/*
 * Project:     beacon
 * File:        apiClient.js
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

class ApiClient {
    constructor(baseURL)
    {
        this.baseURL = baseURL;
    }

    /**
     * Send request to API
     *
     * @param endpoint
     * @param options
     * @returns {Promise<*>}
     */
    async request(endpoint, options = {})
    {
        /**
         * If endpoint starts with "/api/" we cut off "/api", because the baseUrl already ends with "/api".
         * This is the case if the method "route()" is used to get a route.
         */
        if (endpoint.startsWith('/api/')) {
            endpoint = endpoint.substring(4);
        }

        const url = `${this.baseURL}${endpoint}`;

        const config = {
            method: options.method || 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...(options.headers || {})
            },
            credentials: 'same-origin',
        };

        if (options.body) {
            config.body = JSON.stringify(options.body);
        }

        let response;

        try {
            response = await fetch(url, config);
        } catch (err) {
            throw new ApiError('Network error', 0, null);
        }

        let data = null;

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            data = await response.json();
        } else {
            data = await response.text();
        }

        if (!response.ok) {
            throw new ApiError(
                data?.message || 'API Error',
                response.status,
                data
            );
        }

        return data;
    }

    /**
     * GET request
     *
     * @param endpoint
     * @param headers
     * @returns {Promise<*>}
     */
    get(endpoint, headers = {})
    {
        return this.request(endpoint, {
            method: 'GET',
            headers
        });
    }

    /**
     * POST request
     *
     * @param endpoint
     * @param body
     * @param headers
     * @returns {Promise<*>}
     */
    post(endpoint, body = {}, headers = {})
    {
        return this.request(endpoint, {
            method: 'POST',
            body,
            headers
        });
    }

    /**
     * PUT request
     *
     * @param endpoint
     * @param body
     * @param headers
     * @returns {Promise<*>}
     */
    put(endpoint, body = {}, headers = {})
    {
        return this.request(endpoint, {
            method: 'PUT',
            body,
            headers
        });
    }

    /**
     * DELETE request
     *
     * @param endpoint
     * @param headers
     * @returns {Promise<*>}
     */
    delete(endpoint, headers = {})
    {
        return this.request(endpoint, {
            method: 'DELETE',
            headers
        });
    }
}

class ApiError extends Error
{
    constructor(message, status, payload) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.payload = payload;
    }
}

const meta = document.querySelector('meta[name="app-config"]');
const config = JSON.parse(meta.content);

window.api = new ApiClient(config.apiBaseUrl);
