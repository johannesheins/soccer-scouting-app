<?php

namespace Tests\Unit\Player;

use App\DTOs\PlayerSearchDTO;
use PHPUnit\Framework\TestCase;

class PlayerSearchDTOTest extends TestCase
{
    // firstname
    public function test_firstname_gets_trimmed(): void
    {
        $dto = new PlayerSearchDTO(['firstname' => ' John ']);
        $this->assertSame('John', $dto->firstname);
    }

    public function test_firstname_gets_set(): void
    {
        $dto = new PlayerSearchDTO(['firstname' => 'John']);
        $this->assertSame('John', $dto->firstname);
    }

    public function test_firstname_is_null_when_missing(): void
    {
        $dto = new PlayerSearchDTO([]);
        $this->assertNull($dto->firstname);
    }

    public function test_firstname_is_null_when_explicitly_null(): void
    {
        $dto = new PlayerSearchDTO(['firstname' => null]);
        $this->assertNull($dto->firstname);
    }

    // lastname
    public function test_lastname_gets_trimmed(): void
    {
        $dto = new PlayerSearchDTO(['lastname' => '  Doe  ']);
        $this->assertSame('Doe', $dto->lastname);
    }

    public function test_lastname_gets_set(): void
    {
        $dto = new PlayerSearchDTO(['lastname' => 'Doe']);
        $this->assertSame('Doe', $dto->lastname);
    }

    public function test_lastname_is_null_when_missing(): void
    {
        $dto = new PlayerSearchDTO([]);
        $this->assertNull($dto->lastname);
    }

    // year_of_birth
    public function test_year_of_birth_gets_set(): void
    {
        $dto = new PlayerSearchDTO(['year_of_birth' => 1995]);
        $this->assertSame(1995, $dto->year_of_birth);
    }

    public function test_year_of_birth_is_null_when_missing(): void
    {
        $dto = new PlayerSearchDTO([]);
        $this->assertNull($dto->year_of_birth);
    }

    // clubs (key mapping: club_ids → clubs)
    public function test_club_ids_are_mapped_to_clubs(): void
    {
        $dto = new PlayerSearchDTO(['club_ids' => [1, 2, 3]]);
        $this->assertSame([1, 2, 3], $dto->clubIds);
    }

    public function test_clubs_defaults_to_empty_array_when_missing(): void
    {
        $dto = new PlayerSearchDTO([]);
        $this->assertSame([], $dto->clubIds);
    }

    // positions (key mapping: position_ids → positions)
    public function test_position_ids_are_mapped_to_positions(): void
    {
        $dto = new PlayerSearchDTO(['position_ids' => [4, 5]]);
        $this->assertSame([4, 5], $dto->positionIds);
    }

    public function test_positions_defaults_to_empty_array_when_missing(): void
    {
        $dto = new PlayerSearchDTO([]);
        $this->assertSame([], $dto->positionIds);
    }

    // vollständiger Input
    public function test_all_fields_are_mapped_correctly(): void
    {
        $dto = new PlayerSearchDTO([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'year_of_birth' => 1995,
            'club_ids' => [1, 2],
            'position_ids' => [3],
        ]);

        $this->assertSame('John', $dto->firstname);
        $this->assertSame('Doe', $dto->lastname);
        $this->assertSame(1995, $dto->year_of_birth);
        $this->assertSame([1, 2], $dto->clubIds);
        $this->assertSame([3], $dto->positionIds);
    }
}
