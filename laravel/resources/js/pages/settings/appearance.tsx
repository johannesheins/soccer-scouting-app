import { Head } from '@inertiajs/react';
import AppearanceTabs from '@/components/appearance-tabs';
import Heading from '@/components/heading';
import { edit as editAppearance } from '@/routes/appearance';

export default function Appearance() {
    return (
        <>
            <Head title="Erscheinungsbild" />

            <h1 className="sr-only">Erscheinungsbild</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="Erscheinungsbild"
                    description="Passe das Erscheinungsbild deines Kontos an"
                />
                <AppearanceTabs />
            </div>
        </>
    );
}

Appearance.layout = {
    breadcrumbs: [
        {
            title: 'Erscheinungsbild',
            href: editAppearance(),
        },
    ],
};
