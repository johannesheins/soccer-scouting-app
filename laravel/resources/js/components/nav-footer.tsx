import type { ComponentPropsWithoutRef } from 'react';
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { toUrl } from '@/lib/utils';
import type { NavItem } from '@/types';
import { Link } from "@inertiajs/react";
import { useUser } from '@/hooks/use-auth';

export function NavFooter({
    items,
    className,
    ...props
}: ComponentPropsWithoutRef<typeof SidebarGroup> & {
    items: NavItem[];
}) {
    const user = useUser();
    const activeAndAllowedItems = items.filter(i => i.isActive !== false && (i.isAdministrationOnly === true && user.is_administrator || i.isAdministrationOnly !== true));

    if(activeAndAllowedItems.length > 0){
        return (
            <SidebarGroup
                {...props}
                className={`group-data-[collapsible=icon]:p-0 ${className || ''}`}
            >
                <SidebarGroupContent>
                    <SidebarMenu>
                        {activeAndAllowedItems.map((item) => (
                            <SidebarMenuItem key={item.title}>
                                <SidebarMenuButton
                                    asChild
                                    className="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                                >
                                    <Link
                                        href={toUrl(item.href)}
                                        target={item.target}
                                        rel="noopener noreferrer"
                                    >
                                        {item.icon && (
                                            <item.icon className="h-5 w-5"/>
                                        )}
                                        <span>{item.title}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        ))}
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        );
    }
}
