import {Head, useForm, usePage} from '@inertiajs/react';
import React, {useState} from "react";
import DashboardController from "@/actions/App/Http/Controllers/Settings/DashboardController";
import Heading from '@/components/heading';
import InputError from "@/components/input-error";
import {Button} from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import MultipleSelector from "@/components/ui/multi-select";
import type {Option} from "@/components/ui/multi-select";
import {toClubOptions} from "@/hooks/form-options";
import dashboard from "@/routes/settings/dashboard";
import {Club} from "@/types/types";
import {useHasRight} from "@/hooks/use-has-right";
import {RightEnum} from "@/enums";
import {Field} from "@/components/ui/field";

type Props = { clubs: Club[]; playerQuickSearchUserClubs: Club[] };

export default function Dashboard() {
    const { clubs, playerQuickSearchUserClubs } = usePage<Props>().props;
    const clubOptions = toClubOptions(clubs);
    const playerQuickSearchClubs = playerQuickSearchUserClubs.map(c => c.id);
    const [selectedClubs, setSelectedClubs] = useState<Option[]>(
        clubOptions.filter(o => playerQuickSearchClubs.includes(Number(o.value)))
    );

    const canSearchPlayers = useHasRight(RightEnum.PlayerSearch);

    const { setData, post, processing, errors } = useForm({
        club_ids: playerQuickSearchClubs,
    })

    function submit(){
        post(DashboardController.updatePinnedClubs.url());
    }

    function onChange(options: Option[]){
        setSelectedClubs(options);
        setData('club_ids', options.map(o => Number(o.value)));
    }

    return (
        <>
            <Head title="Dashboardeinstellungen" />

            <h1 className="sr-only">Dashboardeinstellungen</h1>

            {!canSearchPlayers && (
                <div className="space-y-6">
                    <Heading
                        variant="small"
                        title="Keine Einstellungen für das Dashboard verfügbar"
                        description="Dir fehlen die nötigen Berechtigungen, um Dashboard-Einstellungen vorzunehmen"
                    />
                </div>
            )}

            {canSearchPlayers && (
                <div className="space-y-6">
                    <Heading
                        variant="small"
                        title="Spieler-Schnellsuche"
                        description="Lege drei Verine fest, welche auf dem Dashboard angezeigt werden sollen"
                    />

                    <Field className="grid gap-2">
                        <Label htmlFor="clubs">Vereine</Label>

                        <MultipleSelector
                            value={selectedClubs}
                            onChange={onChange}
                            defaultOptions={clubOptions}
                            maxSelected={3}
                            groupBy="group"
                            placeholder="Vereine wählen"
                            hidePlaceholderWhenSelected
                            emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                        />

                        <InputError
                            className="mt-2"
                            message={errors.club_ids}
                        />
                    </Field>

                    <Field className="grid gap-2">
                        <Label htmlFor="clubs">Vereine</Label>

                        <MultipleSelector
                            value={selectedClubs}
                            onChange={onChange}
                            defaultOptions={clubOptions}
                            maxSelected={3}
                            groupBy="group"
                            placeholder="Vereine wählen"
                            hidePlaceholderWhenSelected
                            emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                        />

                        <InputError
                            className="mt-2"
                            message={errors.club_ids}
                        />
                    </Field>

                    <div className="flex items-center gap-4">
                        <Button disabled={processing} onClick={submit}>
                            Speichern
                        </Button>
                    </div>
                </div>
            )}
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboardeinstellungen',
            href: dashboard.index.url()
        },
    ],
};
