import {Evaluation} from "@/types/types";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";
import {Button} from "@/components/ui/button";
import {MoreHorizontal} from "lucide-react";
import {useState} from "react";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle
} from "@/components/ui/alert-dialog";
import {router} from "@inertiajs/react";
import evaluation from "@/routes/evaluation";
import {useHasRight} from "@/hooks/use-has-right";
import {RightEnum} from "@/enums";
import {useUser} from "@/hooks/use-auth";
import player from "@/routes/player";
const evaluationRoute = evaluation;
export default function EvaluationRowActions({evaluation}: { evaluation: Evaluation }) {
    const [deleteOpen, setDeleteOpen] = useState(false);

    const currentUserIsCreator = evaluation.creator.id ?? null === useUser().id;

    const canView = useHasRight(RightEnum.EvaluationViewAll) || (useHasRight(RightEnum.EvaluationView) && currentUserIsCreator)
    const canViewPlayer = useHasRight(RightEnum.PlayerView);

    const canEdit = useHasRight(RightEnum.EvaluationEditAll) || (useHasRight(RightEnum.EvaluationEdit) && currentUserIsCreator);
    const canDelete = useHasRight(RightEnum.EvaluationDestroyAll) || (useHasRight(RightEnum.EvaluationDestroy) && currentUserIsCreator);

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
                        <DropdownMenuItem onClick={() => router.visit(player.show.url(evaluation.player.id))}>
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
