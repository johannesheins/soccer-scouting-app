import {Head, Link, usePage} from '@inertiajs/react';
import {administration} from '@/routes';
import {DataTable} from "@/components/table/data-table";
import {userGroupColumns} from "@/pages/administration/user-group/table/user-group-columns";
import {UserGroup} from "@/types/types";
import {Plus} from "lucide-react";
import userGroup from "@/routes/administration/user-group";

type Props = {userGroups: UserGroup[]}
export default function UserGroupIndex() {
    const { userGroups } = usePage<Props>().props;

    return (
        <>
            <Head title="Benutzergruppen" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative aspect-video md:aspect-32/9 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Link href={userGroup.create()} title="Benutzergruppe erstellen" className="flex flex-col gap-2 justify-center items-center h-full">
                        <Plus className={"size-10 icon-color"}/>
                        <p className="text-icon-color font-bold">Benutzergruppe erstellen</p>
                    </Link>
                </div>
                <div className="content-center relative flex-1 overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={userGroupColumns} data={userGroups} textOnEmpty="Keine Benutzergruppe gefunden."/>
                </div>
            </div>
        </>
    );
}

UserGroupIndex.layout = {
    breadcrumbs: [
        {
            title: 'Verwaltung',
            href: administration(),
        },
        {
            title: 'Benutzergruppen'
        }
    ],
};
