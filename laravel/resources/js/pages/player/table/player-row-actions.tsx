import {router} from "@inertiajs/react";
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
                        <span className="sr-only">Menü öffnen</span>
                        <MoreHorizontal className="h-4 w-4"/>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    {canView && (
                        <DropdownMenuItem onClick={() => router.visit(playerRoute.show.url(player.id))}>
                            Spieler ansehen
                        </DropdownMenuItem>
                    )}

                    {showEvaluationSeparator && <DropdownMenuSeparator/>}

                    {canCreateGameEvaluation && (
                        <DropdownMenuItem onClick={() => router.visit(gameEvaluation.create.url({query: {[Name.playerId]: player.id}}))}>
                            Spielbewertung erstellen
                        </DropdownMenuItem>
                    )}
                    {canCreatePlayerEvaluation && (
                        <DropdownMenuItem onClick={() => router.visit(playerEvaluation.create.url({query: {[Name.playerId]: player.id}}))}>
                            Interne Spielerbewertung erstellen
                        </DropdownMenuItem>
                    )}
                    {canViewEvaluation && (
                        <DropdownMenuItem onClick={() => evaluationSearchRequest({player_ids: [player.id], open_tab: 'player'})}>
                            Bewertung anzeigen
                        </DropdownMenuItem>
                    )}

                    {showEditDeleteSeparator && <DropdownMenuSeparator/>}

                    {canEdit && (
                        <DropdownMenuItem onClick={() => router.visit(playerRoute.edit.url(player.id))}>
                            Spieler bearbeiten
                        </DropdownMenuItem>
                    )}
                    {canDelete && (
                        <DropdownMenuItem onSelect={() => setDeleteOpen(true)} className="text-destructive!">
                            Spieler löschen
                        </DropdownMenuItem>
                    )}
                </DropdownMenuContent>
            </DropdownMenu>

            <AlertDialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Spieler wirklich löschen?</AlertDialogTitle>
                        <AlertDialogDescription>
                            Diese Aktion kann nicht rückgängig gemacht werden.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Abbrechen</AlertDialogCancel>
                        <AlertDialogAction variant="destructive" onClick={() => router.delete(playerRoute.destroy.url(player.id))}>
                            Löschen
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}
