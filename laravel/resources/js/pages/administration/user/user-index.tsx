import {Head, Link, usePage} from '@inertiajs/react';
import {administration} from '@/routes';
import {DataTable} from "@/components/table/data-table";
import {Plus} from "lucide-react";
import {User} from "@/types";
import {userColumns} from "@/pages/administration/user/table/user-columns";
import user from "@/routes/administration/user";

type Props = {users: User[]}
export default function UserIndex() {
    const { users } = usePage<Props>().props;

    return (
        <>
            <Head title="Benutzer" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative aspect-video md:aspect-32/9 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Link href={user.create()} title="Benutzer erstellen" className="flex flex-col gap-2 justify-center items-center h-full">
                        <Plus className={"size-10 icon-color"}/>
                        <p className="text-icon-color font-bold">Benutzer erstellen</p>
                    </Link>
                </div>
                <div className="content-center relative flex-1 overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={userColumns} data={users} textOnEmpty="Keine Benutzergruppe gefunden."/>
                </div>
            </div>
        </>
    );
}

UserIndex.layout = {
    breadcrumbs: [
        {
            title: 'Verwaltung',
            href: administration(),
        },
        {
            title: 'Benutzer'
        }
    ],
};
