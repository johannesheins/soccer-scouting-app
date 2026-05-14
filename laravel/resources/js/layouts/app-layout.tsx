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
            <div className="max-w-6xl">
                {children}
            </div>
            <Modal />
        </AppLayoutTemplate>
    );
}
