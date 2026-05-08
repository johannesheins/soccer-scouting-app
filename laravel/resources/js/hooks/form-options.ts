import type { Option } from '@/components/ui/multi-select';
import type { Club, PlayerSmall, Position } from '@/types/types';

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