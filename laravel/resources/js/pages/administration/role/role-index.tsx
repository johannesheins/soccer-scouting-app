import {Head, Link, usePage} from '@inertiajs/react';
import {administration} from '@/routes';
import {DataTable} from "@/components/table/data-table";
import {roleColumns} from "@/pages/administration/role/table/role-columns";
import {Role} from "@/types/types";
import {Plus, UserRoundPlus, UserSearch} from "lucide-react";
import role from "@/routes/administration/role";
import player from "@/routes/player";
import PlayerSearchForm from "@/pages/player/player-search-form";

type Props = {roles: Role[]}
export default function RoleIndex() {
    const { roles } = usePage<Props>().props;

    return (
        <>
            <Head title="Rolen" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative aspect-video md:aspect-32/9 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Link href={role.create()} title="Rolle erstellen" className="flex flex-col gap-2 justify-center items-center h-full">
                        <Plus className={"size-10 icon-color"}/>
                        <p className="text-icon-color font-bold">Rolle erstellen</p>
                    </Link>
                </div>
                <div className="content-center relative flex-1 overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={roleColumns} data={roles} textOnEmpty="Keine Role gefunden." className={"h-full"}/>
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
