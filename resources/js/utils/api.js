import { Storage } from "./helpers";

export async function http(url, options = {}) {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    const mobisToken = Storage.get('mobis_token');
    const mobisLicense = Storage.get('mobis_license_key');

    const defaultHeaders = {
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        ...(csrfToken && { "X-CSRF-TOKEN": csrfToken }),
        ...(options.body && { "Content-Type": "application/json" }),

        // Tambahan otomatis
        ...(mobisToken && { "Authorization": `Bearer ${mobisToken}` }),
        ...(mobisLicense && { "X-LICENSE-KEY": mobisLicense }),
    };

    const config = {
        method: options.method || "GET",
        headers: {
            ...defaultHeaders,
            ...(options.headers || {})
        },
        body: options.body ? JSON.stringify(options.body) : undefined
    };

    const response = await fetch(url, config);

    // Auto JSON parse
    try {
        return await response.json();
    } catch (err) {
        return response; // fallback
    }
}


function getService() {
    return localStorage.getItem("service_active_app_x") || "dramabox";
}
export async function getTheater() {
    const service = getService();
    return await http(`/api/v1/${service}/theaters`);
}
export async function getRecommend(page = 1) {
    const service = getService();
    return await http(`/api/v1/${service}/recommend?page=${page}`);
}
export async function getCategory() {
    const service = getService();
    return await http(`/api/v1/${service}/categories`);
}
export async function getTheaterDetail(bookId)
{
    const service = getService();
    return await http(`/api/v1/${service}/detail/${bookId}`);

    
}
export async function getChapterDetail(bookId)
{
    const service = getService();
    return await http(`/api/v1/${service}/detail/recommend/${bookId}`);

    
}

export async function getPlayerVideo(bookId,episode)
{
    const service = getService();
    return await http(`/api/v1/${service}/play/${bookId}/${episode}`);
}