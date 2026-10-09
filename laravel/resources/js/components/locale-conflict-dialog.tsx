import { usePage } from '@inertiajs/react';
import { useState } from 'react';
import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { switchLocale } from '@/locale/switch-locale';
import { t } from '@/locale/translate';

// Asks which locale to keep when the user's saved locale differs from this browser's cookie.
// Choosing one saves it on the user and in the cookie, which resolves the conflict.
export default function LocaleConflictDialog() {
    const { locales, localeConflict } = usePage().props;
    const [processing, setProcessing] = useState(false);

    if (!localeConflict) {
        return null;
    }

    const { account, browser } = localeConflict;

    const choose = (locale: string) =>
        switchLocale(locale, {
            onStart: () => setProcessing(true),
            onFinish: () => setProcessing(false),
        });

    return (
        // No onOpenChange: the dialog stays open until a locale is chosen
        <AlertDialog open>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>
                        {t('Which language should be used?')}
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        {t(
                            'Your account is set to :account, but this browser is set to :browser.',
                            {
                                account: locales[account] ?? account,
                                browser: locales[browser] ?? browser,
                            },
                        )}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <Button
                        variant="outline"
                        lang={browser.replace('_', '-')}
                        disabled={processing}
                        onClick={() => choose(browser)}
                    >
                        {locales[browser] ?? browser}
                    </Button>
                    <Button
                        lang={account.replace('_', '-')}
                        disabled={processing}
                        onClick={() => choose(account)}
                    >
                        {locales[account] ?? account}
                    </Button>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
