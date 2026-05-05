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
        </>
    );
}

PlayerCreate.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: player(),
        },
        {
            title: 'Spieler erstellen',
        },
    ],
};
