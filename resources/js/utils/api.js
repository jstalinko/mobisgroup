import { Storage } from "./helpers";
export function getDeviceId()
{
    const match = document.cookie.match(/device_id=([^;]+)/);
    // get from localstorage if not found in cookie
    if (match) {
        return decodeURIComponent(match[1]);
    } else {
        return localStorage.getItem('device_id') || null;
    }
}
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
        ...{"X-XSRF-TOKEN": getXsrfToken()},
        ...{"X-DEVICE-ID": getDeviceId()},
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
function getXsrfToken() {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : "";
}

export function getService() {
    // --- ambil dari cookie ---
    const getCookie = (name) => {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    };

    // Coba ambil dari cookie dulu
    let service = getCookie("service_active_app_x");

    // Jika cookie kosong → ambil dari localStorage
    if (!service) {
        service = localStorage.getItem("service_active_app_x");
    }

    // Jika dua-duanya kosong → default
    if (!service) {
        service = "dramabox";
    }

    // Bersihkan karakter tidak aman
    service = service
        .trim()
        .replace(/['"]/g, '')
        .replace(/%22|%20/g, '');

    return service;
}



export function getLang() {
    let lang = localStorage.getItem('site_lang') || 'in';

    // Bersihkan karakter tidak penting
    lang = lang
        .trim()                // hapus spasi depan/belakang
        .replace(/['"]/g, '')  // hapus " dan '
        .replace(/%22|%20/g, ''); // hapus encode quotes & spaces
    
    return lang;
}
export async function getTheater() {
    const service = getService();
    return await http(`/api/v1/${service}/theaters?lang=${getLang()}`);
}
export async function getRecommend(page = 1) {
    const service = getService();
    return await http(`/api/v1/${service}/recommend?page=${page}&lang=${getLang()}`);
}
export async function getCategory() {
    const service = getService();
    return await http(`/api/v1/${service}/categories`);
}
export async function getTheaterDetail(bookId)
{
    const service = getService();
    return await http(`/api/v1/${service}/detail/${bookId}?lang=${getLang()}`);

    
}
export async function getChapterDetail(bookId)
{
    const service = getService();
    return await http(`/api/v1/${service}/detail/chapter/${bookId}?lang=${getLang()}`);

    
}

export async function getPlayerVideo(bookId,episode)
{
    const service = getService();
    return await http(`/api/v1/${service}/stream/${bookId}?episode=${episode}`);
}
export async function getSearch(query)
{
    const service = getService();
    return await http(`/api/v1/${service}/search` , {
        method:'POST',
        body: {
            query: query
        }
    });
    
}