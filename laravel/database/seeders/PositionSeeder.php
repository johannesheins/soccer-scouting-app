<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\PositionGroup;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positionGroups = [
            ['id' => 1, 'name' => 'Abwehr'],
            ['id' => 2, 'name' => 'Mittelfeld'],
            ['id' => 3, 'name' => 'Angriff'],
        ];
        foreach($positionGroups as $group){
            PositionGroup::updateOrCreate(['id' => $group['id']], ['name' => $group['name']]);
        }

        $positions = [
            ['id' => 1,  'position_group_id' => 1, 'position_code' => 'TW'],

            ['id' => 2,  'position_group_id' => 1, 'position_code' => 'IV'],
            ['id' => 3,  'position_group_id' => 1, 'position_code' => 'AV'],
            ['id' => 4,  'position_group_id' => 1, 'position_code' => 'LAV'],
            ['id' => 5,  'position_group_id' => 1, 'position_code' => 'RAV'],

            ['id' => 6,  'position_group_id' => 2, 'position_code' => 'ZM'],
            ['id' => 7,  'position_group_id' => 2, 'position_code' => 'RM'],
            ['id' => 8,  'position_group_id' => 2, 'position_code' => 'LM'],
            ['id' => 9,  'position_group_id' => 2, 'position_code' => 'ZDM'],
            ['id' => 10, 'position_group_id' => 2, 'position_code' => 'ZOM'],

            ['id' => 11, 'position_group_id' => 3, 'position_code' => 'ST'],
            ['id' => 12, 'position_group_id' => 3, 'position_code' => 'MS'],
            ['id' => 13, 'position_group_id' => 3, 'position_code' => 'LA/LS'],
            ['id' => 14, 'position_group_id' => 3, 'position_code' => 'RA/RS'],
            ['id' => 15, 'position_group_id' => 3, 'position_code' => 'LF'],
            ['id' => 16, 'position_group_id' => 3, 'position_code' => 'RF'],
        ];
        foreach($positions as $position){
            Position::updateOrCreate(
                [
                    'id' => $position['id']
                ],
                [
                    'position_group_id' => $position['position_group_id'],
                    'position_code' => $position['position_code']
                ],
            );
        }
    }
}
