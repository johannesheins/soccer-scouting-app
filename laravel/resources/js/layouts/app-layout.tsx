import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { Modal } from '@/lib/inertia-modal';
import type { BreadcrumbItem } from '@/types';
import background_logo from '@/images/background_image.png'

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
                <div className="col-start-1 row-start-1 max-w-400 w-full z-10">
                    {children}
                </div>
                <div className="col-start-1 row-start-1 opacity-4 md:grid items-center p-15 hidden">
                    <img src={background_logo} alt="logo"/>
                </div>
            </div>
            <Modal />
        </AppLayoutTemplate>
    );
}
