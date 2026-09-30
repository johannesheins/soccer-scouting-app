import { Link } from '@inertiajs/react';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useUser } from '@/hooks/use-auth';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { t } from '@/locale/translate';
import type { NavItem } from '@/types';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const user = useUser();
    const activeAndAllowedItems = items.filter(i =>
        i.isActive !== false && (i.right === undefined || user.rights.includes(i.right) || user.is_administrator)
    );

    const { isCurrentUrl } = useCurrentUrl();

    if(activeAndAllowedItems.length > 0) {
        return (
            <SidebarGroup className="px-2 py-0">
                <SidebarGroupLabel>{t('Navigation')}</SidebarGroupLabel>
                <SidebarMenu>
                    {activeAndAllowedItems.map((item) => (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                isActive={isCurrentUrl(item.href)}
                                tooltip={{children: item.title}}
                            >
                                <Link href={item.href} prefetch>
                                    {item.icon && <item.icon/>}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ))}
                </SidebarMenu>
            </SidebarGroup>
        );
    }
}
