import {router} from "@inertiajs/react";
import evaluation from "@/routes/evaluation";

export function evaluationSearchRequest(data: any) {
    const jsonData = JSON.stringify(data);
    const base64Encoded = btoa(jsonData);
    return router.get(evaluation.search.url()+'/'+base64Encoded);
}
