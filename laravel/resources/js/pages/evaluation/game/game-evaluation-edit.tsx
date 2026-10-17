import { setLayoutProps } from '@inertiajs/react';
import EvaluationForm from "@/components/from/evaluation-form";
import { useLastVisitedUrl } from '@/hooks/use-previous-url';
import { t } from '@/locale/translate';
import evaluation from "@/routes/evaluation";
import gameEvaluation from "@/routes/evaluation/game";

export default function GameEvaluationEdit() {
    const searchUrl = useLastVisitedUrl(evaluation.search.url());

    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Evaluations'),
                href: searchUrl,
            },
            {
                title: t('Edit match evaluation'),
            },
        ],
    });

    return <EvaluationForm type="game" route={gameEvaluation} edit={true}/>;
}
