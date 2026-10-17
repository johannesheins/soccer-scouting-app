import ClubForm from "@/components/from/club-form";
import { t } from '@/locale/translate';
import club from "@/routes/club";

export default function ClubCreate() {
    return <ClubForm />;
}

ClubCreate.layout = () => ({
    breadcrumbs: [
        {
            title: t('Clubs'),
            href: club.search(),
        },
        {
            title: t('Create club'),
        },
    ],
});
