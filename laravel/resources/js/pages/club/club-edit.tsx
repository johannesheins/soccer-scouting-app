import { setLayoutProps } from '@inertiajs/react';
import ClubForm from "@/components/from/club-form";
import { usePreviousUrl } from '@/hooks/use-previous-url';
import club from '@/routes/club';

export default function ClubEdit() {
    const previousUrl = usePreviousUrl();
    const usePrevious: boolean = previousUrl?.startsWith(club.search.url()) ?? false

    setLayoutProps({
        breadcrumbs: [
            {
                title: 'Verein',
                href: club.index(),
            },
            {
                title: 'Verein suchen',
                href: usePrevious ? previousUrl : club.search()
            },
            {
                title: 'Verein bearbeiten'
            },
        ],
    });

    return <ClubForm edit backHref={usePrevious ? previousUrl : club.search.url()}/>;
}
