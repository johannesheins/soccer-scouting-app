import {History} from "lucide-react";
import {Badge} from "@/components/ui/badge";
import {Button} from "@/components/ui/button";
import {date} from "@/locale/date-locale";
import { t } from '@/locale/translate';
import type {AuditVersion} from "@/types/audit-version";

export const eventLabel = (event: AuditVersion['event']) => ({
    created: t('Created'),
    updated: t('Updated'),
    deleted: t('Deleted'),
})[event];

type Props<T extends AuditVersion> = {
    versions: T[],
    selectedId?: number,
    fields: Record<string, string>,
    showUser?: boolean,
    canRestore?: boolean,
    onSelect: (id: number) => void,
    onRestore: (version: T) => void,
}
export function VersionList<T extends AuditVersion>({versions, selectedId, fields, showUser = true, canRestore = true, onSelect, onRestore}: Props<T>) {
    return (
        <nav className="grid gap-2 content-start">
            {versions.map((version, index) => (
                <div key={version.id} className="grid">
                    <Button variant={version.id === selectedId ? 'secondary' : 'ghost'} onClick={() => onSelect(version.id)}
                        className="col-start-1 row-start-1 h-auto flex-col items-start gap-1 whitespace-normal text-start pe-12"
                    >
                        <span className="font-medium">{date(version.created_at, 'Pp')}</span>
                        <span className="text-muted-foreground text-xs">{eventLabel(version.event)} {showUser && version.user && `- ${version.user.firstname} ${version.user.lastname}`}</span>
                        {version.changes.length > 0 && (
                            <span className="flex flex-wrap gap-1">
                                {version.changes.filter(field => fields[field]).map(field => (
                                    <Badge key={field} variant="outline">{fields[field]}</Badge>
                                ))}
                            </span>
                        )}
                    </Button>
                    <Button
                        className="col-start-1 row-start-1 justify-self-end mt-1 me-1"
                        title={t('Restore version')}
                        variant="ghost"
                        onClick={() => onRestore(version)}
                        hidden={index === 0 || !canRestore}
                    >
                        <History />
                    </Button>
                </div>
            ))}
        </nav>
    );
}
