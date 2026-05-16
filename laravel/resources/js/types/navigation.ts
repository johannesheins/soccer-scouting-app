import type { InertiaLinkProps } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import type { RightEnum } from '@/enums';

export type BreadcrumbItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
};

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    target?: '_self' | '_blank' | '_parent' | '_top',
    isAdministrationOnly?: boolean;
    isActive?: boolean;
    right?: RightEnum;
};
