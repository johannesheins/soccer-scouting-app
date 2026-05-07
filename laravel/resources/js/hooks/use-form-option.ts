import { useState } from 'react';
import type { Option } from '@/components/ui/multi-select';
import type { Club, Player, Position } from '@/types/scouting';

export function useFormOption(positions: Position[], clubs: Club[], player?: Player) {
    const positionOptions: Option[] = positions.map(p => ({
        value: String(p.id),
        label: p.position_code,
        group: p.position_group?.name ?? '',
    }));

    const playerPositions: string[] = player?.positions.map(pos => String(pos.id)) ?? [];

    const [selectedPositions, setSelectedPositions] = useState<Option[]>(
        positionOptions.filter(o => playerPositions.includes(o.value))
    );

    const clubsByLetter = clubs.reduce<Record<string, Club[]>>((acc, c) => {
        const letter = c.clubname.charAt(0).toUpperCase();
        (acc[letter] ??= []).push(c);
        return acc;
    }, {});

    return { positionOptions, playerPositions, selectedPositions, setSelectedPositions, clubsByLetter };
}
