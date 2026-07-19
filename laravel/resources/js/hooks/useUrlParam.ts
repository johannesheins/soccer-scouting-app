export function useUrlParam(key: string):string{
    const params = new URLSearchParams(window.location.search);
    return params.get(key) ?? '';
}

export function useUrlParamBracket(key: string):string[]{
    const params = new URLSearchParams(window.location.search);

    const bracketed: string[] = [];
    params.keys().forEach((k: string) => {
        if(!k.startsWith(`${key}[`)){
            return;
        }
        const id = Number(k.match(/\[([0-9]+)]/)?.[1]);
        bracketed[id] = params.get(k)!;
    })
    return bracketed.length > 0 ? bracketed : params.getAll(key);
}
