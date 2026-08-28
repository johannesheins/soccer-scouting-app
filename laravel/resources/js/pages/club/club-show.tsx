import {Dialog, DialogContent} from '@/components/ui/dialog';
import { useModal } from '@/lib/inertia-modal';
import { ClubView } from '@/pages/club/club-view';
import type { Club } from '@/types/types';

export default function ClubShow({ club }: { club: Club }) {
    const { close } = useModal();

    return (
        <Dialog open onOpenChange={(open) => !open && close()}>
            <DialogContent>
                <ClubView club={club} />
            </DialogContent>
        </Dialog>
    );
}
