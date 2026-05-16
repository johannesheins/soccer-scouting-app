import {Head, Link} from '@inertiajs/react';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import {administration} from '@/routes';
import {Star, User, Users} from "lucide-react";
import userGroup from "@/routes/administration/user-group";
import user from "@/routes/administration/user";
import evaluationCriteria from "@/routes/evaluation-criteria";

export default function Dashboard() {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href={userGroup.index()} title="Benutzergruppen" className="flex flex-col gap-2 justify-center items-center h-full">
                            <Users className={"size-10 icon-color"}/>
                            <p className="text-icon-color font-bold">Benutzergruppen</p>
                        </Link>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href={user.index()} title="Benutzer" className="flex flex-col gap-2 justify-center items-center h-full">
                            <User className={"size-10 icon-color"}/>
                            <p className="text-icon-color font-bold">Benutzer</p>
                        </Link>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href={evaluationCriteria.index()} title="Bewertungskriterien" className="flex flex-col gap-2 justify-center items-center h-full">
                            <Star className={"size-10 icon-color"}/>
                            <p className="text-icon-color font-bold">Bewertungskriterien</p>
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

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Verwaltung',
            href: administration(),
        },
    ],
};
