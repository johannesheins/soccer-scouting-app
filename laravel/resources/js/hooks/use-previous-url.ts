import {router} from "@inertiajs/react";
import {useEffect, useState} from "react";

let _previousUrl: string | null = null;
let _currentUrl: string | null = null;
const _visitedUrls: string[] = [];

router.on('navigate', (event) => {
    _previousUrl = _currentUrl;
    _currentUrl = event.detail.page.url;
    _visitedUrls.push(_currentUrl);

    if (_visitedUrls.length > 50) {
        _visitedUrls.shift();
    }
});

export function usePreviousUrl(){
    const [url, setUrl] = useState(_previousUrl);

    useEffect(() => {
        return router.on('navigate', () => setUrl(_previousUrl))
    }, []);

    return url;
}

export function useLastVisitedUrl(url: string){
    usePreviousUrl();

    return _visitedUrls.findLast((visited) => visited.startsWith(url)) ?? url;
}
