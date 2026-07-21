import api from "@/routes/api";
import type {Player} from "@/types/types";

export default async function fetchPlayerSearchData(data: Record<string, string | string[] | number | number[]>): Promise<Player[]> {
    const params = new URLSearchParams();
    Object.entries(data).forEach(([key, value]) => {
        if (Array.isArray(value)) {
            value.forEach(v => params.append(key, String(v)));
        } else {
            params.append(key, String(value));
        }
    });

    const url = api.player.search.url() + '?' + params.toString();
    const res = await fetch(url);

    return res.json();
}

export async function fetchPlayerData(data: Record<string, string | string[]>, url: string): Promise<Player> {
    const xsrfToken = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/)?.[1];

    const res = await fetch(url, {
        method: 'post',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(xsrfToken ? {'X-XSRF-TOKEN': decodeURIComponent(xsrfToken)} : {}),
        },
        body: JSON.stringify(data),
    });

    return res.json();
}
