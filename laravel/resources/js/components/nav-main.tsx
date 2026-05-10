import {Link, usePage} from '@inertiajs/react';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';
import type {Auth, NavItem} from '@/types';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const { auth } = usePage<{ auth: Auth }>().props;
    const activeAndAllowedItems = items.filter(i => i.isActive !== false && (i.isAdministrationOnly === true && auth.user.isAdministrator || i.isAdministrationOnly !== true));

    const { isCurrentUrl } = useCurrentUrl();

    if(activeAndAllowedItems.length > 0) {
        return (
            <SidebarGroup className="px-2 py-0">
                <SidebarGroupLabel>Platform</SidebarGroupLabel>
                <SidebarMenu>
                    {items.map((item) => (
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
