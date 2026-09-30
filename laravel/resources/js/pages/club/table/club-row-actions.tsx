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
import {ClubPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import { t } from '@/locale/translate';
import club from "@/routes/club";
import type {Club} from "@/types/club";

const clubRoute = club;
export function ClubRowActions({club}: { club: Club }) {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const canView = useHasRight(ClubPermissions.View);
    const canEdit = useHasRight(ClubPermissions.Edit);
    const canDelete = useHasRight(ClubPermissions.Destroy);

    if (!canView && !canEdit && !canDelete) {
return null;
}

    const showEditDeleteSeparator = canView && (canEdit || canDelete);

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
                        <DropdownMenuItem onClick={() => router.visit(clubRoute.show.url(club.id))}>
                            {t('View club')}
                        </DropdownMenuItem>
                    )}

                    {showEditDeleteSeparator && <DropdownMenuSeparator/>}

                    {canEdit && (
                        <DropdownMenuItem onClick={() => router.visit(clubRoute.edit.url(club.id))}>
                            {t('Edit club')}
                        </DropdownMenuItem>
                    )}
                    {canDelete && (
                        <DropdownMenuItem onSelect={() => setDeleteOpen(true)} className="text-destructive!">
                            {t('Delete club')}
                        </DropdownMenuItem>
                    )}
                </DropdownMenuContent>
            </DropdownMenu>

            <AlertDialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>{t('Delete this club?')}</AlertDialogTitle>
                        <AlertDialogDescription>
                            {t('This action cannot be undone.')}
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>{t('Cancel')}</AlertDialogCancel>
                        <AlertDialogAction variant="destructive" onClick={() => router.delete(clubRoute.destroy.url(club.id))}>
                            {t('Delete')}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}
