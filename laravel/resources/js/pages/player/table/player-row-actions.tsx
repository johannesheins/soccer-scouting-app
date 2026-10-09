import {router} from "@inertiajs/react";
import { t } from '@/locale/translate';
import {MoreHorizontal} from "lucide-react"
import {useState} from "react";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog"
import {Button} from "@/components/ui/button"
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import {
    GameEvaluationPermissions,
    PlayerEvaluationPermissions,
    PlayerPermissions,
    PlayerRequestNameEnum as Name
} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import {evaluationSearchRequest} from "@/request/evaluation-search-request";
import gameEvaluation from "@/routes/evaluation/game";
import playerEvaluation from "@/routes/evaluation/player";
import player from "@/routes/player";
import type {Player} from "@/types/player";

const playerRoute = player;
export function PlayerRowActions({player}: { player: Player }) {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const canView = useHasRight(PlayerPermissions.View);
    const canEdit = useHasRight(PlayerPermissions.Edit);
    const canDelete = useHasRight(PlayerPermissions.Destroy);

    const canCreateGameEvaluation = useHasRight(GameEvaluationPermissions.Create);
    const canCreatePlayerEvaluation = useHasRight(GameEvaluationPermissions.Create);
    const canViewEvaluation = useHasRight(GameEvaluationPermissions.View) || useHasRight(PlayerEvaluationPermissions.View);

    if (!canView && !canEdit && !canDelete && !canCreateGameEvaluation && !canCreatePlayerEvaluation && !canViewEvaluation) {
        return null;
    }

    const showEvaluationSeparator = canView && (canCreateGameEvaluation || canCreatePlayerEvaluation || canViewEvaluation);
    const showEditDeleteSeparator = (canView || canCreateGameEvaluation || canCreatePlayerEvaluation || canViewEvaluation) && (canEdit || canDelete);

    return (
        <>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" className="h-8 w-8 p-0">
                        <span className="sr-only">{t('Open menu')}</span>
                        <MoreHorizontal className="h-4 w-4"/>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    {canView && (
                        <DropdownMenuItem onClick={() => router.visit(playerRoute.show.url(player.id))}>
                            {t('View player')}
                        </DropdownMenuItem>
                    )}

                    {showEvaluationSeparator && <DropdownMenuSeparator/>}

                    {canCreateGameEvaluation && (
                        <DropdownMenuItem onClick={() => router.visit(gameEvaluation.create.url({query: {[Name.playerId]: player.id}}))}>
                            {t('Create match evaluation')}
                        </DropdownMenuItem>
                    )}
                    {canCreatePlayerEvaluation && (
                        <DropdownMenuItem onClick={() => router.visit(playerEvaluation.create.url({query: {[Name.playerId]: player.id}}))}>
                            {t('Create internal player evaluation')}
                        </DropdownMenuItem>
                    )}
                    {canViewEvaluation && (
                        <DropdownMenuItem onClick={() => evaluationSearchRequest({player_ids: [player.id], open_tab: 'player'})}>
                            {t('View evaluations')}
                        </DropdownMenuItem>
                    )}

                    {showEditDeleteSeparator && <DropdownMenuSeparator/>}

                    {canEdit && (
                        <DropdownMenuItem onClick={() => router.visit(playerRoute.edit.url(player.id))}>
                            {t('Edit player')}
                        </DropdownMenuItem>
                    )}
                    {canDelete && (
                        <DropdownMenuItem onSelect={() => setDeleteOpen(true)} className="text-destructive!">
                            {t('Delete player')}
                        </DropdownMenuItem>
                    )}
                </DropdownMenuContent>
            </DropdownMenu>

            <AlertDialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>{t('Delete this player?')}</AlertDialogTitle>
                        <AlertDialogDescription>
                            {t('This action cannot be undone.')}
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>{t('Cancel')}</AlertDialogCancel>
                        <AlertDialogAction variant="destructive" onClick={() => router.delete(playerRoute.destroy.url(player.id))}>
                            {t('Delete')}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}
