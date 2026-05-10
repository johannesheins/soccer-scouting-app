import {router} from "@inertiajs/react";
import {useEffect, useState} from "react";

let _previousUrl: string | null = null;
let _currentUrl: string | null = null;

router.on('navigate', (event) => {
    _previousUrl = _currentUrl;
    _currentUrl = event.detail.page.url;
});

export function usePreviousUrl(){
    const [url, setUrl] = useState(_previousUrl);

    useEffect(() => {
        return router.on('navigate', () => setUrl(_previousUrl))
    }, []);

    return url;
}
