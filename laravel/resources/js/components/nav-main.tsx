import { Link } from '@inertiajs/react';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';
import type { NavItem } from '@/types';
import { useUser } from '@/hooks/use-auth';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const user = useUser();
    const activeAndAllowedItems = items.filter(i =>
        i.isActive !== false
        && (i.isAdministrationOnly !== true || user.isAdministrator)
        && (i.right === undefined || user.rights.includes(i.right))
    );

    const { isCurrentUrl } = useCurrentUrl();

    if(activeAndAllowedItems.length > 0) {
        return (
            <SidebarGroup className="px-2 py-0">
                <SidebarGroupLabel>Platform</SidebarGroupLabel>
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
