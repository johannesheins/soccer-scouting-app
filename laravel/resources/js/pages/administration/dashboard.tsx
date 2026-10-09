import {Head, Link} from '@inertiajs/react';
import {Layers, Star, User, Users} from "lucide-react";
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import user from "@/routes/administration/user";
import userGroup from "@/routes/administration/user-group";
import evaluationCriteria from "@/routes/evaluation-criteria";
import evaluationCriteriaGroup from "@/routes/evaluation-criteria-group";

export default function Dashboard() {
    return (
        <>
            <Head title={t('Home')} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href={userGroup.index()} title={t('User groups')} className="flex flex-col gap-2 justify-center items-center h-full">
                            <Users className={"size-10 icon-color"}/>
                            <p className="text-icon-color font-bold">{t('User groups')}</p>
                        </Link>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href={user.index()} title={t('Users')} className="flex flex-col gap-2 justify-center items-center h-full">
                            <User className={"size-10 icon-color"}/>
                            <p className="text-icon-color font-bold">{t('Users')}</p>
                        </Link>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href={evaluationCriteria.index()} title={t('Evaluation criteria')} className="flex flex-col gap-2 justify-center items-center h-full">
                            <Star className={"size-10 icon-color"}/>
                            <p className="text-icon-color font-bold">{t('Evaluation criteria')}</p>
                        </Link>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href={evaluationCriteriaGroup.index()} title={t('Criteria groups')} className="flex flex-col gap-2 justify-center items-center h-full">
                            <Layers className={"size-10 icon-color"}/>
                            <p className="text-icon-color font-bold">{t('Criteria groups')}</p>
                        </Link>
                    </div>
                </div>
                <div className="relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </>
    );
}

Dashboard.layout = () => ({
    breadcrumbs: [
        {
            title: t('Administration'),
            href: administration(),
        },
    ],
});
