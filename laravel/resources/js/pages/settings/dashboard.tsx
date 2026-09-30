import {Head, useForm, usePage} from '@inertiajs/react';
import { t } from '@/locale/translate';
import React, {useState} from "react";
import DashboardSettingsController from "@/actions/App/Http/Controllers/Settings/DashboardSettingsController";
import Heading from '@/components/heading';
import YearOfBirthInput from "@/components/input/year-of-birth-input";
import InputError from "@/components/input-error";
import {Button} from "@/components/ui/button";
import {Field} from "@/components/ui/field";
import { Label } from '@/components/ui/label';
import MultipleSelector from "@/components/ui/multi-select";
import type {Option} from "@/components/ui/multi-select";
import {PlayerPermissions} from "@/enums/permission/player-permissions";
import {toClubOptions} from "@/hooks/form-options";
import {useHasRight} from "@/hooks/use-has-right";
import dashboard from "@/routes/settings/dashboard";
import type {Club} from "@/types/club";
import type {playerQuickSearchUserClubs, PlayerQuickSearchUserYears} from "@/types/player";

type Props = { clubs: Club[]; playerQuickSearchUserClubs: playerQuickSearchUserClubs, playerQuickSearchUserYears: PlayerQuickSearchUserYears };

export default function Dashboard() {
    const { clubs, playerQuickSearchUserClubs, playerQuickSearchUserYears } = usePage<Props>().props;

    const clubOptions = toClubOptions(clubs);
    const playerQuickSearchClubs = playerQuickSearchUserClubs.map(c => c.id);
    const playerQuickSearchYears = playerQuickSearchUserYears.map(y => y.year_of_birth);
    const [selectedClubs, setSelectedClubs] = useState<Option[]>(
        clubOptions.filter(o => playerQuickSearchClubs.includes(Number(o.value)))
    );

    const canSearchPlayers = useHasRight(PlayerPermissions.Search);

    const { data, setData, post, processing, errors } = useForm({
        club_ids: playerQuickSearchClubs,
        years_of_birth: playerQuickSearchYears,
    })

    function submit(){
        post(DashboardSettingsController.updatePlayerQuickSearchSettings.url());
    }

    function onChange(options: Option[]){
        setSelectedClubs(options);
        setData('club_ids', options.map(o => Number(o.value)));
    }

    return (
        <>
            <Head title={t('Home settings')} />

            <h1 className="sr-only">{t('Home settings')}</h1>

            {!canSearchPlayers && (
                <div className="space-y-6">
                    <Heading
                        variant="small"
                        title={t('No home page settings available')}
                        description={t('You don\'t have permission to change the home page settings')}
                    />
                </div>
            )}

            {canSearchPlayers && (
                <div className="space-y-6">
                    <Heading
                        variant="small"
                        title={t('Player quick search')}
                        description={t('Choose which clubs and years of birth are shown on the home page')}
                    />

                    <Field className="grid gap-2">
                        <Label htmlFor="clubs">{t('Clubs')}</Label>

                        <MultipleSelector
                            value={selectedClubs}
                            onChange={onChange}
                            defaultOptions={clubOptions}
                            maxSelected={3}
                            groupBy="group"
                            placeholder={t('Select clubs')}
                            hidePlaceholderWhenSelected
                            emptyIndicator={<p className="text-center text-sm">{t('No club found')}</p>}
                        />

                        <InputError
                            className="mt-2"
                            message={errors.club_ids}
                        />
                    </Field>

                    <Field className="grid gap-2">
                        <YearOfBirthInput variant="multiple" name="years_of_birth" setData={setData} selectedValues={data.years_of_birth} error={errors.years_of_birth} maxSelected={6}/>
                    </Field>

                    <div className="flex items-center gap-4">
                        <Button disabled={processing} onClick={submit}>
                            {t('Save')}
                        </Button>
                    </div>
                </div>
            )}
        </>
    );
}

Dashboard.layout = () => ({
    breadcrumbs: [
        {
            title: t('Home settings'),
            href: dashboard.index.url()
        },
    ],
});
