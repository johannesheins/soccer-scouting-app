import {router} from "@inertiajs/react";
import {HistoryDialog} from "@/components/history-dialog";
import {ClubPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import { t } from '@/locale/translate';
import {ClubView} from "@/pages/club/club-view";
import clubRoute from "@/routes/club";
import type {Club, ClubVersion} from "@/types/club";

type Props = {
    club: Club,
    versions: ClubVersion[],
}
export default function ClubViewHistory({club, versions}: Props) {
    const canEdit = useHasRight(ClubPermissions.Edit);

    const fields: Record<string, string> = {
        clubname: t('Club name'),
        zip_code: t('Postcode'),
        city: t('City'),
    };

    const restoreVersion = (version: ClubVersion) => {
        router.put(clubRoute.update.url(club.id), version.new_values);
    };

    return (
        <HistoryDialog variant="large" versions={versions} fields={fields} canRestore={canEdit} onRestore={restoreVersion}>
            {version => <ClubView club={version.club}/>}
        </HistoryDialog>
    );
}
