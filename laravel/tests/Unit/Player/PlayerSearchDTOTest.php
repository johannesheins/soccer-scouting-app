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

    // yearsOfBirth
    public function test_years_of_birth_are_mapped(): void
    {
        $dto = new PlayerSearchDTO(['years_of_birth' => [1995, 1996]]);
        $this->assertSame([1995, 1996], $dto->yearsOfBirth);
    }

    public function test_years_of_birth_defaults_to_empty_array_when_missing(): void
    {
        $dto = new PlayerSearchDTO([]);
        $this->assertSame([], $dto->yearsOfBirth);
    }

    // clubs (key mapping: club_ids → clubs)
    public function test_club_ids_are_mapped_to_club_ids(): void
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
            'years_of_birth' => [1995],
            'club_ids' => [1, 2],
            'position_ids' => [3],
        ]);

        $this->assertSame('John', $dto->firstname);
        $this->assertSame('Doe', $dto->lastname);
        $this->assertSame([1995], $dto->yearsOfBirth);
        $this->assertSame([1, 2], $dto->clubIds);
        $this->assertSame([3], $dto->positionIds);
    }
}
