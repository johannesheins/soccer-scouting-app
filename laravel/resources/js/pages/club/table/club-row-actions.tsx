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
import {RightEnum} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import club from "@/routes/club";
import type {Club} from "@/types/types";

const clubRoute = club;
export function ClubRowActions({club}: { club: Club }) {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const canView = useHasRight(RightEnum.ClubView);
    const canEdit = useHasRight(RightEnum.ClubEdit);
    const canDelete = useHasRight(RightEnum.ClubDestroy);

    if (!canView && !canEdit && !canDelete) {
return null;
}

    const showEditDeleteSeparator = canView && (canEdit || canDelete);

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
                        <DropdownMenuItem onClick={() => router.visit(clubRoute.show.url(club.id))}>
                            Verein ansehen
                        </DropdownMenuItem>
                    )}

                    {showEditDeleteSeparator && <DropdownMenuSeparator/>}

                    {canEdit && (
                        <DropdownMenuItem onClick={() => router.visit(clubRoute.edit.url(club.id))}>
                            Verein bearbeiten
                        </DropdownMenuItem>
                    )}
                    {canDelete && (
                        <DropdownMenuItem onSelect={() => setDeleteOpen(true)} className="text-destructive!">
                            Verein löschen
                        </DropdownMenuItem>
                    )}
                </DropdownMenuContent>
            </DropdownMenu>

            <AlertDialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Verein wirklich löschen?</AlertDialogTitle>
                        <AlertDialogDescription>
                            Diese Aktion kann nicht rückgängig gemacht werden.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Abbrechen</AlertDialogCancel>
                        <AlertDialogAction variant="destructive" onClick={() => router.delete(clubRoute.destroy.url(club.id))}>
                            Löschen
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}
