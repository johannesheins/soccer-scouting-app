import {Head, usePage} from "@inertiajs/react";
import React from "react";

import {DataTable} from "@/components/table/data-table";
import { t } from '@/locale/translate';
import ClubSearchForm from "@/pages/club/club-search-form";
import {clubColumns} from "@/pages/club/table/club-columns";
import club from "@/routes/club";
import type {Club} from "@/types/club";

type Props = { clubs: Club[] };

export default function ClubSearch(){
    const { clubs } = usePage<Props>().props;

    return (
        <>
            <Head title={t('Search clubs')} />
            <div className="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <ClubSearchForm />
                </div>

                <div className="relative overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={clubColumns} data={clubs} textOnEmpty={t('No club found')}/>
                </div>
            </div>
        </>
    )
}

ClubSearch.layout = () => ({
    breadcrumbs: [
        {
            title: t('Clubs'),
            href: club.index(),
        },
        {
            title: t('Search clubs'),
        },
    ],
});
