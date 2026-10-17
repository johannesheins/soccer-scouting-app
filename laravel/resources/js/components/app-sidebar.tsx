import {Link} from '@inertiajs/react';
import { ClipboardPlus, FilePlus, FileSearch, FileUserIcon, LayoutGrid, Search, Settings, Shield, ShieldPlus, User, UserRoundPlus, UserSearch } from 'lucide-react';
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
import { ClubPermissions, EvaluationPermissions, GameEvaluationPermissions, PlayerEvaluationPermissions, PlayerPermissions } from '@/enums';
import { t } from '@/locale/translate';
import {administration, dashboard} from "@/routes";
import club from "@/routes/club";
import evaluation from "@/routes/evaluation";
import player from "@/routes/player";
import type { NavItem, NavLink } from '@/types';

const mainNavItems = (): NavItem[] => [
    {
        title: t('Home'),
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: t('Players'),
        icon: User,
        subItems: [
            {
                title: t('Create player'),
                href: player.create(),
                icon: UserRoundPlus,
                right: PlayerPermissions.Create,
            },
            {
                title: t('Player search'),
                href: player.search(),
                icon: UserSearch,
                right: PlayerPermissions.Search,
            },
        ]
    },
    {
        title: t('Evaluations'),
        icon: FileUserIcon,
        subItems: [
            {
                title: t('Create match evaluation'),
                href: evaluation.game.create(),
                icon: FilePlus,
                right: GameEvaluationPermissions.Create,
            },
            {
                title: t('Create internal player evaluation'),
                href: evaluation.player.create(),
                icon: ClipboardPlus,
                right: PlayerEvaluationPermissions.Create,
            },
            {
                title: t('Evaluation search'),
                href: evaluation.search(),
                icon: FileSearch,
                right: EvaluationPermissions.Search,
            },
        ]
    },
    {
        title: t('Clubs'),
        icon: Shield,
        subItems: [
            {
                title: t('Create club'),
                href: club.create(),
                icon: ShieldPlus,
                right: ClubPermissions.Create,
            },
            {
                title: t('Club search'),
                href: club.search(),
                icon: Search,
                right: ClubPermissions.Search,
            },
        ]
    },
];

const footerNavItems = (): NavLink[] => [
    {
        title: t('Administration'),
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
                <NavMain items={mainNavItems()} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems()} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
