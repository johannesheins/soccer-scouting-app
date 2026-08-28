<?php

namespace Tests\Unit\Club;

use App\DTOs\ClubSearchDTO;
use PHPUnit\Framework\TestCase;

class ClubSearchDTOTest extends TestCase
{
    // clubname
    public function test_clubname_gets_trimmed(): void
    {
        $dto = new ClubSearchDTO(['clubname' => ' FC Testhausen ']);
        $this->assertSame('FC Testhausen', $dto->clubname);
    }

    public function test_clubname_gets_set(): void
    {
        $dto = new ClubSearchDTO(['clubname' => 'FC Testhausen']);
        $this->assertSame('FC Testhausen', $dto->clubname);
    }

    public function test_clubname_is_null_when_missing(): void
    {
        $dto = new ClubSearchDTO([]);
        $this->assertNull($dto->clubname);
    }

    public function test_clubname_is_null_when_explicitly_null(): void
    {
        $dto = new ClubSearchDTO(['clubname' => null]);
        $this->assertNull($dto->clubname);
    }

    public function test_clubname_empty_string_stays_empty_string(): void
    {
        $dto = new ClubSearchDTO(['clubname' => '']);
        $this->assertSame('', $dto->clubname);
    }

    public function test_clubname_whitespace_only_becomes_empty_string(): void
    {
        $dto = new ClubSearchDTO(['clubname' => '   ']);
        $this->assertSame('', $dto->clubname);
    }

    // zipCode
    public function test_zip_code_gets_trimmed(): void
    {
        $dto = new ClubSearchDTO(['zip_code' => ' 12345 ']);
        $this->assertSame('12345', $dto->zipCode);
    }

    public function test_zip_code_is_null_when_missing(): void
    {
        $dto = new ClubSearchDTO([]);
        $this->assertNull($dto->zipCode);
    }

    public function test_zip_code_is_null_when_explicitly_null(): void
    {
        $dto = new ClubSearchDTO(['zip_code' => null]);
        $this->assertNull($dto->zipCode);
    }

    // city
    public function test_city_gets_trimmed(): void
    {
        $dto = new ClubSearchDTO(['city' => ' Testhausen ']);
        $this->assertSame('Testhausen', $dto->city);
    }

    public function test_city_is_null_when_missing(): void
    {
        $dto = new ClubSearchDTO([]);
        $this->assertNull($dto->city);
    }

    public function test_city_is_null_when_explicitly_null(): void
    {
        $dto = new ClubSearchDTO(['city' => null]);
        $this->assertNull($dto->city);
    }

    // vollständiger Input
    public function test_all_fields_are_mapped_correctly(): void
    {
        $dto = new ClubSearchDTO([
            'clubname' => 'FC Testhausen',
            'zip_code' => '12345',
            'city' => 'Testhausen',
        ]);

        $this->assertSame('FC Testhausen', $dto->clubname);
        $this->assertSame('12345', $dto->zipCode);
        $this->assertSame('Testhausen', $dto->city);
    }
}
