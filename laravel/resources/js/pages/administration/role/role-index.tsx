import {Head, usePage} from '@inertiajs/react';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import {administration, dashboard} from '@/routes';
import {DataTable} from "@/components/table/data-table";
import {roleColumns} from "@/pages/administration/role/table/role-columns";
import {Role} from "@/types/types";

type Props = {roles: Role[]}
export default function RoleIndex() {
    const { roles } = usePage<Props>().props;

    return (
        <>
            <Head title="Rolen" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
                <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <DataTable columns={roleColumns} data={roles} textOnEmpty="Keine Role gefunden."/>
                </div>
            </div>
        </>
    );
}

RoleIndex.layout = {
    breadcrumbs: [
        {
            title: 'Verwaltung',
            href: administration(),
        },
        {
            title: 'Rollen'
        }
    ],
};
