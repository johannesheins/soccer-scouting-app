import { Link } from '@inertiajs/react';
import { Fragment } from 'react';
import {
    Breadcrumb,
    BreadcrumbEllipsis,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { t } from '@/locale/translate';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

export function Breadcrumbs({
    breadcrumbs,
}: {
    breadcrumbs: BreadcrumbItemType[];
}) {
    const collapsed = breadcrumbs.slice(0, -2);

    return (
        <>
            {breadcrumbs.length > 0 && (
                <Breadcrumb className="min-w-0">
                    <BreadcrumbList className="flex-nowrap">
                        {collapsed.length > 0 && (
                            <>
                                <BreadcrumbItem className="md:hidden">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger className="hover:text-foreground" aria-label={t('More')}>
                                            <BreadcrumbEllipsis className="size-6" />
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="start">
                                            {collapsed.map((item, index) => (
                                                <DropdownMenuItem key={index} asChild>
                                                    <Link href={item.href}>{item.title}</Link>
                                                </DropdownMenuItem>
                                            ))}
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </BreadcrumbItem>
                                <BreadcrumbSeparator className="md:hidden" />
                            </>
                        )}
                        {breadcrumbs.map((item, index) => {
                            const isLast = index === breadcrumbs.length - 1;
                            const hiddenOnMobile = index < collapsed.length && 'hidden md:inline-flex';

                            return (
                                <Fragment key={index}>
                                    <BreadcrumbItem className={hiddenOnMobile || 'min-w-0'}>
                                        <Tooltip>
                                            <TooltipTrigger asChild>
                                                {isLast ? (
                                                    <BreadcrumbPage className="truncate">
                                                        {item.title}
                                                    </BreadcrumbPage>
                                                ) : (
                                                    <BreadcrumbLink asChild className="truncate">
                                                        <Link href={item.href}>
                                                            {item.title}
                                                        </Link>
                                                    </BreadcrumbLink>
                                                )}
                                            </TooltipTrigger>
                                            <TooltipContent side="bottom" className="max-w-none whitespace-nowrap">{item.title}</TooltipContent>
                                        </Tooltip>
                                    </BreadcrumbItem>
                                    {!isLast && <BreadcrumbSeparator className={hiddenOnMobile || undefined} />}
                                </Fragment>
                            );
                        })}
                    </BreadcrumbList>
                </Breadcrumb>
            )}
        </>
    );
}
