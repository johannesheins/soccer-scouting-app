import {router} from "@inertiajs/react";
import player from "@/routes/player";
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
import {MoreHorizontal} from "lucide-react"
import {Button} from "@/components/ui/button"
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import type {Player} from "@/types/types";
import {Dialog, DialogContent} from "@/components/ui/dialog";
import {PlayerView} from "../player-view";
import {useHasRight} from "@/hooks/use-has-right";
import {RightEnum} from "@/enums";

const playerRoute = player;
export function PlayerRowActions({player}: { player: Player }) {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [viewOpen, setViewOpen] = useState(false);
    const canView = useHasRight(RightEnum.PlayerView);
    const canEdit = useHasRight(RightEnum.PlayerEdit);
    const canDelete = useHasRight(RightEnum.PlayerDestroy);

    const canCreateEvaluation = useHasRight(RightEnum.EvaluationCreate);
    const canViewEvaluation = useHasRight(RightEnum.EvaluationView);

    if (!canView && !canEdit && !canDelete && !canCreateEvaluation && !canViewEvaluation) return null;

    const showEvaluationSeparator = canView && (canCreateEvaluation || canViewEvaluation);
    const showEditDeleteSeparator = (canView || canCreateEvaluation || canViewEvaluation) && (canEdit || canDelete);

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

                    {canCreateEvaluation && ( //TODO Implement evaluation creation from player
                        <DropdownMenuItem onClick={() => alert('Feature needs to be implemented.')}>
                            Spielerbewertung erstellen
                        </DropdownMenuItem>
                    )}
                    {canViewEvaluation && ( //TODO Implement evaluation search with pre-loaded player id
                        <DropdownMenuItem onClick={() => alert('Feature needs to be implemented.')}>
                            Spielerbewertungen anzeigen
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

            <Dialog open={viewOpen} onOpenChange={setViewOpen}>
                <DialogContent>
                    <PlayerView player={player} />
                </DialogContent>
            </Dialog>

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
