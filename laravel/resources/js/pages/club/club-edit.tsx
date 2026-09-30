import { setLayoutProps } from '@inertiajs/react';
import ClubForm from "@/components/from/club-form";
import { usePreviousUrl } from '@/hooks/use-previous-url';
import { t } from '@/locale/translate';
import club from '@/routes/club';

export default function ClubEdit() {
    const previousUrl = usePreviousUrl();
    const usePrevious: boolean = previousUrl?.startsWith(club.search.url()) ?? false

    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Clubs'),
                href: club.index(),
            },
            {
                title: t('Search clubs'),
                href: usePrevious ? previousUrl : club.search()
            },
            {
                title: t('Edit club')
            },
        ],
    });

    return <ClubForm edit backHref={usePrevious ? previousUrl : club.search.url()}/>;
}
