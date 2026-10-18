import { setLayoutProps } from '@inertiajs/react';
import EvaluationForm from "@/components/from/evaluation-form";
import { useLastVisitedUrl } from '@/hooks/use-previous-url';
import { t } from '@/locale/translate';
import evaluation from "@/routes/evaluation";
import playerEvaluation from "@/routes/evaluation/player";

export default function PlayerEvaluationEdit() {
    const searchUrl = useLastVisitedUrl(evaluation.search.url());

    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Evaluations'),
                href: searchUrl,
            },
            {
                title: t('Edit internal player evaluation'),
            },
        ],
    });

    return <EvaluationForm type="player" route={playerEvaluation} edit backHref={searchUrl}/>;
}
