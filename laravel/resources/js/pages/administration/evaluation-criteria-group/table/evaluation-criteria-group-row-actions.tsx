import {router} from "@inertiajs/react";
import {Link} from "@inertiajs/react";
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
import { t } from '@/locale/translate';
import evaluationCriteriaGroup from "@/routes/evaluation-criteria-group";
import type {EvaluationCriteriaGroup} from "@/types/evaluation-criteria";

export function EvaluationCriteriaGroupRowActions({group}: { group: EvaluationCriteriaGroup }) {
    const [deleteOpen, setDeleteOpen] = useState(false);

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
                    <DropdownMenuItem asChild>
                        <Link href={evaluationCriteriaGroup.edit.url(group.id)}>{t('Edit')}</Link>
                    </DropdownMenuItem>
                    <DropdownMenuSeparator/>
                    <DropdownMenuItem onSelect={() => setDeleteOpen(true)} className="text-destructive!">
                        {t('Delete criteria group')}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <AlertDialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>{t('Delete this criteria group?')}</AlertDialogTitle>
                        <AlertDialogDescription>
                            {t('This action cannot be undone. Criteria in this group will no longer be assigned to a group.')}
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>{t('Cancel')}</AlertDialogCancel>
                        <AlertDialogAction variant="destructive"
                                          onClick={() => router.delete(evaluationCriteriaGroup.destroy.url(group.id))}>
                            {t('Delete')}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}