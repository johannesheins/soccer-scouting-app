import {router} from "@inertiajs/react";
import {HistoryDialog} from "@/components/history-dialog";
import {PlayerPermissions, PlayerRequestNameEnum as Name} from "@/enums";
import { t } from '@/locale/translate';
import {PlayerView} from "@/pages/player/player-view";
import playerRoute from "@/routes/player";
import type {Player, PlayerVersion} from "@/types/player";
import {useHasRight} from "@/hooks/use-has-right";

type Props = {
    player: Player,
    versions: PlayerVersion[],
}
export default function PlayerViewHistory({player, versions}: Props) {
    const canEdit = useHasRight(PlayerPermissions.Edit);

    const fields: Record<string, string> = {
        firstname: t('First name'),
        lastname: t('Last name'),
        year_of_birth: t('Year of birth'),
        height: t('Height (cm)'),
        strong_foot: t('Strong foot'),
        club_id: t('Club'),
        positions: t('Positions'),
    };

    const restoreVersion = (version: PlayerVersion) => {
        router.put(playerRoute.update.url(player.id), {...version.new_values, [Name.positionIds]: version.new_values.positions});
    };

    return (
        <HistoryDialog variant="large" versions={versions} fields={fields} canRestore={canEdit} onRestore={restoreVersion}>
            {version => <PlayerView player={version.player}/>}
        </HistoryDialog>
    );
}
