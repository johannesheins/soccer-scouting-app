import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { Modal } from '@/lib/inertia-modal';
import type { BreadcrumbItem } from '@/types';

export default function AppLayout({
    breadcrumbs = [],
    children,
}: {
    breadcrumbs?: BreadcrumbItem[];
    children: React.ReactNode;
}) {
    return (
        <AppLayoutTemplate breadcrumbs={breadcrumbs}>
            <div className="w-full h-full grid justify-items-center">
                <div className="max-w-400 w-full">
                    {children}
                </div>
            </div>
            <Modal />
        </AppLayoutTemplate>
    );
}
