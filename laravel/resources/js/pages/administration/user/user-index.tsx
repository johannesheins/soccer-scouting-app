import {Head, Link, usePage} from '@inertiajs/react';
import {Plus} from "lucide-react";
import {DataTable} from "@/components/table/data-table";
import { t } from '@/locale/translate';
import {userColumns} from "@/pages/administration/user/table/user-columns";
import {administration} from '@/routes';
import user from "@/routes/administration/user";
import type {User} from "@/types";

type Props = {users: User[]}
export default function UserIndex() {
    const { users } = usePage<Props>().props;

    return (
        <>
            <Head title={t('Users')} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative aspect-video md:aspect-32/9 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Link href={user.create()} title={t('Create user')} className="flex flex-col gap-2 justify-center items-center h-full">
                        <Plus className={"size-10 icon-color"}/>
                        <p className="text-icon-color font-bold">{t('Create user')}</p>
                    </Link>
                </div>
                <div className="content-center relative flex-1 overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={userColumns} data={users} textOnEmpty={t('No users found.')}/>
                </div>
            </div>
        </>
    );
}

UserIndex.layout = () => ({
    breadcrumbs: [
        {
            title: t('Administration'),
            href: administration(),
        },
        {
            title: t('Users')
        }
    ],
});
