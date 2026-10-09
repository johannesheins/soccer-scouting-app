import {Head, Link, usePage} from '@inertiajs/react';
import {Plus} from "lucide-react";
import {DataTable} from "@/components/table/data-table";
import { t } from '@/locale/translate';
import {userGroupColumns} from "@/pages/administration/user-group/table/user-group-columns";
import {administration} from '@/routes';
import userGroup from "@/routes/administration/user-group";
import type {UserGroup} from "@/types/user-group";

type Props = {userGroups: UserGroup[]}
export default function UserGroupIndex() {
    const { userGroups } = usePage<Props>().props;

    return (
        <>
            <Head title={t('User groups')} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative aspect-video md:aspect-32/9 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Link href={userGroup.create()} title={t('Create user group')} className="flex flex-col gap-2 justify-center items-center h-full">
                        <Plus className={"size-10 icon-color"}/>
                        <p className="text-icon-color font-bold">{t('Create user group')}</p>
                    </Link>
                </div>
                <div className="content-center relative flex-1 overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={userGroupColumns} data={userGroups} textOnEmpty={t('No user groups found.')}/>
                </div>
            </div>
        </>
    );
}

UserGroupIndex.layout = () => ({
    breadcrumbs: [
        {
            title: t('Administration'),
            href: administration(),
        },
        {
            title: t('User groups')
        }
    ],
});
