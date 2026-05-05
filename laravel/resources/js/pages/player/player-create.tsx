import { Head } from '@inertiajs/react';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { UserRoundPlus } from 'lucide-react';
import {player} from '@/routes';
import {
    Field,
    FieldContent,
    FieldDescription,
    FieldError,
    FieldGroup,
    FieldLabel,
    FieldLegend,
    FieldSeparator,
    FieldSet,
    FieldTitle,
} from "@/components/ui/field"
import {Input} from "@/components/ui/input";

export default function PlayerCreate() {
    return (
        <>
            <Head title="Spieler erstellen" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative min-h-screen bg-sidebar flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <FieldSet>
                        <FieldGroup>
                            <Field>
                                <FieldLabel htmlFor="fistname">Vorname</FieldLabel>
                                <Input id="fistname"/>
                            </Field>
                            <Field>
                                <FieldLabel htmlFor="lastname">Nachname</FieldLabel>
                                <Input id="lastname"/>
                            </Field>
                        </FieldGroup>
                    </FieldSet>
                </div>
            </div>
        </>
    );
}

PlayerCreate.layout = {
    breadcrumbs: [
        {
            title: 'Spieler erstellen',
            href: player(),
        },
    ],
};
