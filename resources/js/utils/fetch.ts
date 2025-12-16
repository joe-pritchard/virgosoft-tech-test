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
): Promise<T> {
    const finalOptions = {
        method,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': (
                document.querySelector(
                    'meta[name="csrf-token"]',
                ) as HTMLMetaElement
            ).content,
        },
        ...options,
    }

    if (body !== undefined) {
        options.body = JSON.stringify(body)
    }

    const response = await fetch(ensureStartsWithHost(url), finalOptions)

    if (!response.ok) {
        throw new Error(
            `Fetch error: ${response.status} ${response.statusText}`,
        )
    }

    return response.json()
}
