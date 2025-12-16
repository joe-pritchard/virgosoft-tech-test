import { useCookies } from '@vueuse/integrations/useCookies'

const ensureStartsWithHost = (url: string): string => {
    if (url.startsWith('https://')) {
        return url
    }

    const newUrl = new URL(url, window.location.origin)
    return newUrl.toString()
}

export async function fetchJson<T extends object>(
    url: string,
    method: 'POST' | 'GET' | 'PUT',
    body?: object,
    options?: RequestInit,
): Promise<{ data: T }> {
    const finalOptions = {
        method,
        headers: {
            'Access-Control-Allow-Credentials': 'True',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
        ...options,
    }

    if (body !== undefined) {
        finalOptions.body = JSON.stringify(body)
    }

    const xsrfCookie = useCookies().get('XSRF-TOKEN')
    if (xsrfCookie) {
        finalOptions.headers = {
            ...finalOptions.headers,
            'X-XSRF-TOKEN': xsrfCookie,
        }
    }

    const response = await fetch(ensureStartsWithHost(url), finalOptions)

    if (!response.ok) {
        const json = await response.json()
        throw (
            json ??
            new Error(`Fetch error: ${response.status} ${response.statusText}`)
        )
    }

    return response.json().catch(() => response.body)
}
