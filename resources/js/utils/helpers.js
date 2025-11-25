import { getService } from "./api";

export function formatCurrency(amount) {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "IDR",
        currencyDisplay: "narrowSymbol",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

export function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
export function stripTags(html) {
    // Menghapus semua tag HTML dari teks yang diberikan
    return html.replace(/<[^>]*>?/gm, "");
}
export function imageUrl(string) {
    const urlPattern = /^(http|https):\/\//i;
    
    // Check if the string matches the regular expression
    if (urlPattern.test(string)) {
        return string;
    } else {
        return '/storage/' + string;
    }
}
export function replaceTimestamp(url) {
    const timestamp = Math.floor(Date.now() / 1000) * 1000; // timestamp * 1000

    // Jika sudah ada ?t= di URL
    if (url.includes("?t=")) {
        return url.replace(/(\?t=)(\d+)/, `$1${timestamp}`);
    }

    // Jika URL sudah punya query lain
    if (url.includes("?")) {
        return url + `&t=${timestamp}`;
    }

    // Jika belum ada query sama sekali
    return url + `?t=${timestamp}`;
}

export function slugify(text, maxLength = 60) {
  let slug = text
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '') 
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '') 
    .trim()
    .replace(/\s+/g, '-') 
    .replace(/-+/g, '-'); 

  // batasi panjang
  if (slug.length > maxLength) {
    slug = slug.substring(0, maxLength);
  }

  // jika terpotong di tengah dash, bersihkan dash di akhir
  return slug.replace(/-+$/g, '');
}

export const Storage = {
    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            sessionStorage.setItem(key, JSON.stringify(value));
        }
    },

    get(key) {
        let value = localStorage.getItem(key);

        if (!value) {
            value = sessionStorage.getItem(key);
        }

        try {
            return JSON.parse(value);
        } catch {
            return value;
        }
    },

    delete(key) {
        localStorage.removeItem(key);
        sessionStorage.removeItem(key);
    }
};
export const siteSetting = () => {
    const el = document.querySelector('meta[name="site_setting"]');

    if (!el) return null; // tidak ada meta

    try {
        let json = el.getAttribute("content");
           
        return JSON.parse(json);
    } catch (e) {
        console.error("Invalid JSON in meta site_setting:", e);
        return null;
    }
};

export const generateUUID = () => {
    // Public Domain/MIT
    var d = new Date().getTime();//Timestamp
    var d2 = (performance && performance.now && (performance.now()*1000)) || 0;//Time in microseconds since page-load or 0 if unsupported
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random() * 16;//random number between 0 and 16
        if(d > 0){//Use timestamp until depleted
            r = (d + r)%16 | 0;
            d = Math.floor(d/16);
        } else {//Use microseconds since page-load if supported
            r = (d2 + r)%16 | 0;
            d2 = Math.floor(d2/16);
        }
        return (c==='x' ? r : (r&0x3|0x8)).toString(16);
    });
}
export const nginxCacheVideo = (url, bookId) => {
    
    if (!url) {
        return ''; 
    }
    
    const setting = siteSetting();
    const service = getService();
    
    if (setting.nginx_cache) {
        const encodeUri = encodeURIComponent(url);
        return 'https://' + setting.site_url + '/v?src=' + encodeUri + '&s=' + service + (bookId ? '&b=' + bookId : '');
    } else {
        return url;
    }
};