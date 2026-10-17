import { Link } from '@inertiajs/react';
import { ChevronRight, ChevronsDownUp, ChevronsUpDown } from 'lucide-react';
import { useState } from 'react';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarGroup,
    SidebarGroupAction,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuAction,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { useUser } from '@/hooks/use-auth';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { t } from '@/locale/translate';
import type { NavItem, NavLink } from '@/types';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const user = useUser();
    const { currentUrl, isCurrentUrl } = useCurrentUrl();
    const { state, isMobile } = useSidebar();
    const isIconMode = state === 'collapsed' && !isMobile;
    const [openItems, setOpenItems] = useState<Record<string, boolean>>({});
    const [prevUrl, setPrevUrl] = useState(currentUrl);

    function isVisible(item: NavItem) {
        return item.isActive !== false && (item.isAdministrationOnly !== true || user.is_administrator);
    }

    function isAllowed(item: NavItem) {
        return item.right === undefined || user.is_administrator || user.rights.includes(item.right);
    }

    function visibleSubItems(item: NavItem): NavLink[] {
        return isAllowed(item) ? item.subItems?.filter(isVisible) ?? [] : [];
    }

    function containsCurrentUrl(item: NavItem) {
        return visibleSubItems(item).some((subItem) => isCurrentUrl(subItem.href));
    }

    const visibleItems = items.filter(isVisible);
    const collapsibleItems = visibleItems.filter((item) => visibleSubItems(item).length > 0);

    if (prevUrl !== currentUrl) {
        setPrevUrl(currentUrl);
        setOpenItems((prev) => ({
            ...prev,
            ...Object.fromEntries(collapsibleItems.filter(containsCurrentUrl).map((item) => [item.title, true])),
        }));
    }

    function isOpen(item: NavItem) {
        return openItems[item.title] ?? containsCurrentUrl(item);
    }

    const isAnyOpen = collapsibleItems.some(isOpen);

    function subContent(subItem: NavLink) {
        return <>{subItem.icon && <subItem.icon />}<span>{subItem.title}</span></>;
    }

    function toggleAll() {
        setOpenItems(Object.fromEntries(collapsibleItems.map((item) => [item.title, !isAnyOpen])));
    }

    if (visibleItems.length === 0) {
        return null;
    }

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>{t('Navigation')}</SidebarGroupLabel>
            {collapsibleItems.length > 0 && (
                <SidebarGroupAction className="top-1.5" title={isAnyOpen ? t('Collapse All') : t('Expand All')} onClick={toggleAll}>
                    {isAnyOpen ? <ChevronsDownUp /> : <ChevronsUpDown />}
                    <span className="sr-only">{isAnyOpen ? t('Collapse All') : t('Expand All')}</span>
                </SidebarGroupAction>
            )}
            <SidebarMenu>
                {visibleItems.map((item) => {
                    const subItems = visibleSubItems(item);
                    const content = <>{item.icon && <item.icon />}<span>{item.title}</span></>;

                    if (subItems.length === 0) {
                        return (
                            <SidebarMenuItem key={item.title}>
                                <SidebarMenuButton asChild isActive={item.href !== undefined && isCurrentUrl(item.href)} tooltip={{ children: item.title }}>
                                    {isAllowed(item) && item.href !== undefined
                                        ? <Link href={item.href} prefetch>{content}</Link>
                                        : <span aria-disabled="true">{content}</span>}
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        );
                    }

                    if (isIconMode) {
                        return (
                            <SidebarMenuItem key={item.title}>
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <SidebarMenuButton isActive={containsCurrentUrl(item)} tooltip={{ children: item.title }}>
                                            {content}
                                        </SidebarMenuButton>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent side="right" align="start" className="min-w-48">
                                        <DropdownMenuLabel>{item.title}</DropdownMenuLabel>
                                        {subItems.map((subItem) => (
                                            <DropdownMenuItem key={subItem.title} asChild disabled={!isAllowed(subItem)}>
                                                {isAllowed(subItem)
                                                    ? <Link href={subItem.href} prefetch>{subContent(subItem)}</Link>
                                                    : <span>{subContent(subItem)}</span>}
                                            </DropdownMenuItem>
                                        ))}
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </SidebarMenuItem>
                        );
                    }

                    return (
                        <Collapsible
                            key={item.title}
                            asChild
                            open={isOpen(item)}
                            onOpenChange={(open) => setOpenItems((prev) => ({ ...prev, [item.title]: open }))}
                        >
                            <SidebarMenuItem>
                                <SidebarMenuButton asChild isActive={item.href !== undefined && isCurrentUrl(item.href)} tooltip={{ children: item.title }}>
                                    {item.href !== undefined
                                        ? <Link href={item.href} prefetch>{content}</Link>
                                        : <CollapsibleTrigger>{content}</CollapsibleTrigger>}
                                </SidebarMenuButton>
                                <CollapsibleTrigger asChild>
                                    <SidebarMenuAction className="data-[state=open]:rotate-90">
                                        <ChevronRight />
                                        <span className="sr-only">{t('Toggle submenu')}</span>
                                    </SidebarMenuAction>
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenuSub>
                                        {subItems.map((subItem) => (
                                            <SidebarMenuSubItem key={subItem.title}>
                                                <SidebarMenuSubButton asChild isActive={isCurrentUrl(subItem.href)}>
                                                    {isAllowed(subItem)
                                                        ? <Link href={subItem.href} prefetch>{subContent(subItem)}</Link>
                                                        : <span aria-disabled="true">{subContent(subItem)}</span>}
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        ))}
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </Collapsible>
                    );
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}
