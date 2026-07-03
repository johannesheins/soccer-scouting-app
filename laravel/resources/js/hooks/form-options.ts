import type { Option } from '@/components/ui/multi-select';
import type {Club, EvaluationCriteriaGroup, PlayerSmall, Position, UserGroup} from '@/types/types';

export function toPositionOptions(positions: Position[]): Option[] {
    return positions.map(p => ({
        value: String(p.id),
        label: p.position_code,
        group: p.position_group?.name ?? '',
    }));
}

export function toPlayerPositionIds(player?: PlayerSmall): string[] {
    return player?.positions.map(pos => String(pos.id)) ?? [];
}

export function groupClubsByLetter(clubs: Club[]): Record<string, Club[]> {
    return clubs.reduce<Record<string, Club[]>>((acc, c) => {
        const letter = c.clubname.charAt(0).toUpperCase();
        (acc[letter] ??= []).push(c);
        return acc;
    }, {});
}

export function toClubOptions(clubs: Club[]): Option[]{
    return clubs.map(c => ({
        value: String(c.id),
        label: c.clubname,
        group: c.clubname.substring(0,1).toUpperCase()
    }))
}
export function getYears(firstYear: number = 0, lastYear: null|number = null, limit: number = 0): string[]{
    const MAX = 9998;
    const currentYear = new Date().getFullYear()
    const years = [];

    const start = Math.max(firstYear, 2000);
    const end = lastYear !== null ? Math.min(lastYear, MAX) : MAX;
    const last = limit > 0 ? firstYear + limit : currentYear;

    for(let y = start; y < last && currentYear < end; y++){ //Stop at year 9998, cause the db field only allows 9 chars
        years.push(`${y}/${y+1}`);
    }

    return years;
}

export function getYearOptions(): Option[]{
    return getYears().sort().reverse().map(y => ({
        value: y,
        label: y,
    }))
}

export function toUserGroupOptions(userGroups: UserGroup[]): Option[]{
    return userGroups.map(userGroup => ({
        value: String(userGroup.id),
        label: userGroup.name
    }))
}

export function toEvaluationCriteriaGroupOptions(groups: EvaluationCriteriaGroup[]): Option[] {
    return groups.map(g => ({
        value: String(g.id),
        label: g.name,
    }))
}
