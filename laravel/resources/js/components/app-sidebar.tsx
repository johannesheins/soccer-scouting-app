import {Link} from '@inertiajs/react';
import {FileUserIcon, LayoutGrid, Settings, Shield, User} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import {RightEnum} from "@/enums";
import {administration, dashboard} from "@/routes";
import club from "@/routes/club";
import evaluation from "@/routes/evaluation";
import player from "@/routes/player";
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Spieler',
        href: player.index(),
        icon: User,
        right: RightEnum.PlayerIndex,
    },
    {
        title: 'Bewertung',
        href: evaluation.index(),
        icon: FileUserIcon,
        right: RightEnum.EvaluationIndex,
    },
    {
        title: 'Verein',
        href: club.index(),
        icon: Shield,
        right: RightEnum.ClubIndex,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Verwaltung',
        href: administration(),
        icon: Settings,
        isAdministrationOnly: true
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
