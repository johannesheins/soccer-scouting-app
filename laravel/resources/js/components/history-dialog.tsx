import {ListClock} from "lucide-react";
import type {ComponentProps, ReactNode} from "react";
import {useState} from "react";
import {Button} from "@/components/ui/button";
import {Dialog, DialogContent, DialogTitle} from "@/components/ui/dialog";
import {Separator} from "@/components/ui/separator";
import {eventLabel, VersionList} from "@/components/version-list";
import {useModal} from "@/lib/inertia-modal";
import {date} from "@/locale/date-locale";
import { t } from '@/locale/translate';
import type {AuditVersion} from "@/types/audit-version";

type Props<T extends AuditVersion> = {
    versions: T[],
    fields: Record<string, string>,
    showUser?: boolean,
    canRestore?: boolean,
    onRestore: (version: T) => void,
    children: (version: T) => ReactNode,
    variant?: ComponentProps<typeof DialogContent>['variant'],
}
export function HistoryDialog<T extends AuditVersion>({versions, fields, showUser, canRestore, onRestore, children, variant = 'extra-large'}: Props<T>) {
    const { close } = useModal();
    const newestFirst = [...versions].reverse();
    const [selectedId, setSelectedId] = useState(newestFirst[0]?.id);
    const selected = newestFirst.find(version => version.id === selectedId);
    const [historyOpen, setHistoryOpen] = useState(false);

    const selectVersion = (id: number) => {
        setSelectedId(id);
        setHistoryOpen(false);
    };

    const versionList = (
        <VersionList
            versions={newestFirst}
            selectedId={selectedId}
            fields={fields}
            showUser={showUser}
            onSelect={selectVersion}
            canRestore={canRestore}
            onRestore={onRestore}
        />
    );

    return (
        <Dialog open onOpenChange={(open) => !open && close()}>
            <DialogContent variant={variant} className="overflow-hidden" aria-describedby={undefined}>
                <DialogTitle className="sr-only">{t('Change history')}</DialogTitle>
                {versions.length <= 0 && <p className="p-5">{t('No changes recorded')}</p>}
                {versions.length > 0 && (
                    <div className="grid grid-rows-[auto_1fr] md:grid-rows-1 md:grid-cols-[1fr_auto_3fr] gap-4 h-[calc(90vh-4rem)] sm:h-[calc(80vh-4rem)]">
                        <Button variant="outline" className="justify-start md:hidden" onClick={() => setHistoryOpen(true)}>
                            <ListClock />
                            {selected && <span>{eventLabel(selected.event)} · {date(selected.created_at, 'Pp')}</span>}
                        </Button>
                        {historyOpen && (
                            <div className="absolute inset-0 z-10 rounded-lg overflow-hidden md:hidden">
                                <div className="absolute inset-0 bg-black/50 animate-in fade-in-0" onClick={() => setHistoryOpen(false)}/>
                                <div className="absolute inset-y-0 left-0 w-3/4 max-w-sm grid grid-rows-[auto_1fr] gap-4 p-4 bg-background border-r animate-in slide-in-from-left">
                                    <p className="font-semibold">{t('Change history')}</p>
                                    <div className="min-h-0 overflow-y-auto">{versionList}</div>
                                </div>
                            </div>
                        )}

                        <div className="hidden md:block md:min-h-0 md:overflow-y-auto">{versionList}</div>

                        <Separator orientation="vertical" className="hidden md:block"/>

                        <div className="min-h-0 overflow-y-auto">
                            {selected && children(selected)}
                        </div>
                    </div>
                )}
            </DialogContent>
        </Dialog>
    );
}
