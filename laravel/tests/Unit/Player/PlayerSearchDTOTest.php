<?php

namespace Tests\Unit\Player;

use App\DTOs\PlayerSearchDTO;
use App\Enums\FootEnum;
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

    public function test_firstname_empty_string_stays_empty_string(): void
    {
        $dto = new PlayerSearchDTO(['firstname' => '']);
        $this->assertSame('', $dto->firstname);
    }

    public function test_firstname_whitespace_only_becomes_empty_string(): void
    {
        $dto = new PlayerSearchDTO(['firstname' => '   ']);
        $this->assertSame('', $dto->firstname);
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

    public function test_lastname_is_null_when_explicitly_null(): void
    {
        $dto = new PlayerSearchDTO(['lastname' => null]);
        $this->assertNull($dto->lastname);
    }

    public function test_lastname_empty_string_stays_empty_string(): void
    {
        $dto = new PlayerSearchDTO(['lastname' => '']);
        $this->assertSame('', $dto->lastname);
    }

    public function test_lastname_whitespace_only_becomes_empty_string(): void
    {
        $dto = new PlayerSearchDTO(['lastname' => '   ']);
        $this->assertSame('', $dto->lastname);
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

    public function test_years_of_birth_defaults_to_empty_array_when_explicitly_null(): void
    {
        $dto = new PlayerSearchDTO(['years_of_birth' => null]);
        $this->assertSame([], $dto->yearsOfBirth);
    }

    // heightFrom / heightTo
    public function test_height_from_and_height_to_are_mapped(): void
    {
        $dto = new PlayerSearchDTO(['height_from' => 170, 'height_to' => 190]);
        $this->assertSame(170, $dto->heightFrom);
        $this->assertSame(190, $dto->heightTo);
    }

    public function test_height_from_is_null_when_missing(): void
    {
        $dto = new PlayerSearchDTO([]);
        $this->assertNull($dto->heightFrom);
    }

    public function test_height_from_is_null_when_explicitly_null(): void
    {
        $dto = new PlayerSearchDTO(['height_from' => null]);
        $this->assertNull($dto->heightFrom);
    }

    public function test_height_to_is_null_when_missing(): void
    {
        $dto = new PlayerSearchDTO([]);
        $this->assertNull($dto->heightTo);
    }

    public function test_height_to_is_null_when_explicitly_null(): void
    {
        $dto = new PlayerSearchDTO(['height_to' => null]);
        $this->assertNull($dto->heightTo);
    }

    // strongFoots
    public function test_strong_foots_are_mapped(): void
    {
        $dto = new PlayerSearchDTO(['strong_foots' => [FootEnum::LEFT->value, FootEnum::RIGHT->value]]);
        $this->assertSame([FootEnum::LEFT->value, FootEnum::RIGHT->value], $dto->strongFoots);
    }

    public function test_strong_foots_defaults_to_empty_array_when_missing(): void
    {
        $dto = new PlayerSearchDTO([]);
        $this->assertSame([], $dto->strongFoots);
    }

    public function test_strong_foots_defaults_to_empty_array_when_explicitly_null(): void
    {
        $dto = new PlayerSearchDTO(['strong_foots' => null]);
        $this->assertSame([], $dto->strongFoots);
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

    public function test_clubs_defaults_to_empty_array_when_explicitly_null(): void
    {
        $dto = new PlayerSearchDTO(['club_ids' => null]);
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

    public function test_positions_defaults_to_empty_array_when_explicitly_null(): void
    {
        $dto = new PlayerSearchDTO(['position_ids' => null]);
        $this->assertSame([], $dto->positionIds);
    }

    // vollständiger Input
    public function test_all_fields_are_mapped_correctly(): void
    {
        $dto = new PlayerSearchDTO([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'years_of_birth' => [1995],
            'height_from' => 170,
            'height_to' => 190,
            'strong_foots' => [FootEnum::LEFT->value],
            'club_ids' => [1, 2],
            'position_ids' => [3],
        ]);

        $this->assertSame('John', $dto->firstname);
        $this->assertSame('Doe', $dto->lastname);
        $this->assertSame([1995], $dto->yearsOfBirth);
        $this->assertSame(170, $dto->heightFrom);
        $this->assertSame(190, $dto->heightTo);
        $this->assertSame([FootEnum::LEFT->value], $dto->strongFoots);
        $this->assertSame([1, 2], $dto->clubIds);
        $this->assertSame([3], $dto->positionIds);
    }
}
