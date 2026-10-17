import type { InertiaLinkProps } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import type { HTMLAttributeAnchorTarget } from 'react';
import type { ClubPermissions, EvaluationPermissions, GameEvaluationPermissions, PlayerEvaluationPermissions, PlayerPermissions } from '@/enums';

export type Href = NonNullable<InertiaLinkProps['href']>;

export type BreadcrumbItem = {
    title: string;
    href: Href;
};

export type PermissionEnum = PlayerPermissions | EvaluationPermissions | GameEvaluationPermissions | PlayerEvaluationPermissions | ClubPermissions;

type NavItemBase = {
    title: string;
    icon: LucideIcon | null;
    isAdministrationOnly?: boolean;
    isActive?: boolean;
    right?: PermissionEnum;
};

export type NavLink = NavItemBase & {
    href: Href;
    target?: HTMLAttributeAnchorTarget;
    subItems?: never;
};

export type NavGroup = NavItemBase & {
    href?: Href;
    target?: never;
    subItems: NavLink[];
};

export type NavItem = NavLink | NavGroup;
