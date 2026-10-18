import { setLayoutProps } from '@inertiajs/react';
import ClubForm from "@/components/from/club-form";
import { useLastVisitedUrl } from '@/hooks/use-previous-url';
import { t } from '@/locale/translate';
import club from "@/routes/club";

export default function ClubCreate() {
    const searchUrl = useLastVisitedUrl(club.search.url());

    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Club search'),
                href: searchUrl,
            },
            {
                title: t('Create club'),
            },
        ],
    });

    return <ClubForm />;
}
