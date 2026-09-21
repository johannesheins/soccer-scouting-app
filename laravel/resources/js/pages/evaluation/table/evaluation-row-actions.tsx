import {router} from "@inertiajs/react";
import {MoreHorizontal} from "lucide-react";
import {useState} from "react";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle
} from "@/components/ui/alert-dialog";
import {Button} from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";
import {EvaluationTypes, GameEvaluationPermissions, PlayerEvaluationPermissions, PlayerPermissions} from "@/enums";
import {useUser} from "@/hooks/use-auth";
import {useHasRight} from "@/hooks/use-has-right";
import gameEvaluationRoute from "@/routes/evaluation/game";
import playerEvaluationRoute from "@/routes/evaluation/player";
import player from "@/routes/player";
import type {Evaluation} from "@/types/evaluation/evaluation";
import {PlayerEvaluation} from "@/types/evaluation/player-evaluation";
import {GameEvaluation} from "@/types/evaluation/game-evaluation";

export default function EvaluationRowActions({evaluation}: { evaluation: Evaluation }) {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const isGameEvaluation = evaluation.evaluation_type === 'game';
    const isPlayerEvaluation = evaluation.evaluation_type === 'player';


    const gameEvaluation = isGameEvaluation ? evaluation as GameEvaluation : undefined;
    const playerEvaluation = isPlayerEvaluation ? evaluation as PlayerEvaluation : undefined;


    let evaluationPermissions = null;
    let evaluationRoute = null;
    switch (evaluation.evaluation_type) {
        case EvaluationTypes.GAME:
            evaluationPermissions = GameEvaluationPermissions;
            evaluationRoute = gameEvaluationRoute;
            break;
        case EvaluationTypes.PLAYER:
            evaluationPermissions = PlayerEvaluationPermissions;
            evaluationRoute = playerEvaluationRoute;
            break;
        default:
            throw new Error(`Unknown evaluation type: ${evaluation.evaluation_type}`);
    }

    const currentUserIsCreator = evaluation.creator.id ?? null === useUser().id;

    const canView = useHasRight(evaluationPermissions.ViewAll) || (useHasRight(evaluationPermissions.View) && currentUserIsCreator)
    const canViewPlayer = useHasRight(PlayerPermissions.View) && (isGameEvaluation || isPlayerEvaluation);

    const canEdit = useHasRight(evaluationPermissions.EditAll) || (useHasRight(evaluationPermissions.Edit) && currentUserIsCreator);
    const canDelete = useHasRight(evaluationPermissions.DestroyAll) || (useHasRight(evaluationPermissions.Destroy) && currentUserIsCreator);

    const showPlayerSeparator = canView && canViewPlayer;
    const showEditDeleteSeparator = (canView || canViewPlayer) && (canDelete || canEdit);

    return (
        <>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" className="h-8 w-8 p-0">
                        <span className="sr-only">Menü öffnen</span>
                        <MoreHorizontal className="h-4 w-4"/>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    {canView && (
                        <DropdownMenuItem onClick={() => router.visit(evaluationRoute.show.url(evaluation.id))}>
                            Bewertung ansehen
                        </DropdownMenuItem>
                    )}

                    {showPlayerSeparator && <DropdownMenuSeparator/>}

                    {canViewPlayer && (
                        <DropdownMenuItem onClick={() => router.visit(player.show.url(gameEvaluation?.player.id ?? playerEvaluation?.player.id ?? ''))}>
                            Spieler ansehen
                        </DropdownMenuItem>
                    )}

                    {showEditDeleteSeparator && <DropdownMenuSeparator/>}

                    {canEdit && (
                        <DropdownMenuItem onClick={() => router.visit(evaluationRoute.edit.url(evaluation.id))}>
                            Bewertung bearbeiten
                        </DropdownMenuItem>
                    )}

                    {canDelete && (
                        <DropdownMenuItem onSelect={() => setDeleteOpen(true)} className="text-destructive!">
                            Bewertung löschen
                        </DropdownMenuItem>
                    )}
                </DropdownMenuContent>
            </DropdownMenu>

            <AlertDialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Bewertung wirklich löschen?</AlertDialogTitle>
                        <AlertDialogDescription>
                            Diese Aktion kann nicht rückgängig gemacht werden.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Abbrechen</AlertDialogCancel>
                        <AlertDialogAction variant="destructive" onClick={() => router.delete(evaluationRoute.destroy.url(evaluation.id))}>
                            Löschen
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    )
}
