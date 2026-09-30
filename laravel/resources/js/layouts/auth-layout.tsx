import LanguageTabs from '@/components/language-tabs';
import LocaleConflictDialog from '@/components/locale-conflict-dialog';
import AuthLayoutTemplate from '@/layouts/auth/auth-simple-layout';

export default function AuthLayout({
    title = '',
    description = '',
    children,
}: {
    title?: string;
    description?: string;
    children: React.ReactNode;
}) {
    return (
        <div className="relative">
            <LanguageTabs className="absolute top-4 right-4 md:top-6 md:right-6" />
            <AuthLayoutTemplate title={title} description={description}>
                {children}
            </AuthLayoutTemplate>
            <LocaleConflictDialog />
        </div>
    );
}
