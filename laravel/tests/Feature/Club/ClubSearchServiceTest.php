<?php

namespace Tests\Feature\Club;

use App\DTOs\ClubSearchDTO;
use App\Models\Club;
use App\Services\ClubSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private ClubSearchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClubSearchService();
    }

    #region clubname
    public function test_filters_by_clubname(): void
    {
        Club::factory()->create(['clubname' => 'FC Testhausen']);
        Club::factory()->create(['clubname' => 'SV Musterstadt']);

        $result = $this->search(['clubname' => 'FC Testhausen']);
        $this->assertCount(1, $result);

        $club = $result->first();
        $this->assertSame('FC Testhausen', $club->clubname);
    }

    public function test_clubname_filter_matches_partial(): void
    {
        Club::factory()->create(['clubname' => 'FC Testhausen']);
        Club::factory()->create(['clubname' => 'SV Musterstadt']);

        $result = $this->search(['clubname' => 'Test']);
        $this->assertCount(1, $result);

        $club = $result->first();
        $this->assertSame('FC Testhausen', $club->clubname);
    }

    public function test_no_clubname_filter_returns_all(): void
    {
        Club::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_string_clubname_returns_all(): void
    {
        Club::factory()->count(3)->create();

        $result = $this->search(['clubname' => '']);
        $this->assertCount(3, $result);
    }
    #endregion

    #region zipCode
    public function test_filters_by_zip_code(): void
    {
        Club::factory()->create(['zip_code' => '12345']);
        Club::factory()->create(['zip_code' => '54321']);

        $result = $this->search(['zip_code' => '12345']);
        $this->assertCount(1, $result);

        $club = $result->first();
        $this->assertSame('12345', $club->zip_code);
    }

    public function test_no_zip_code_filter_returns_all(): void
    {
        Club::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }
    #endregion

    #region city
    public function test_filters_by_city(): void
    {
        Club::factory()->create(['city' => 'Testhausen']);
        Club::factory()->create(['city' => 'Musterstadt']);

        $result = $this->search(['city' => 'Testhausen']);
        $this->assertCount(1, $result);

        $club = $result->first();
        $this->assertSame('Testhausen', $club->city);
    }

    public function test_no_city_filter_returns_all(): void
    {
        Club::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }
    #endregion

    #region leere Ergebnisse
    public function test_returns_empty_collection_when_no_match(): void
    {
        Club::factory()->create(['clubname' => 'FC Testhausen']);

        $result = $this->search(['clubname' => 'Nonexistent']);

        $this->assertCount(0, $result);
    }
    #endregion

    #region kombinierte Filter
    public function test_combines_multiple_filters(): void
    {
        $match = Club::factory()->create([
            'clubname' => 'FC Testhausen',
            'zip_code' => '12345',
            'city' => 'Testhausen',
        ]);

        Club::factory()->create(['clubname' => 'FC Testhausen Zwei', 'zip_code' => '54321', 'city' => 'Musterstadt']);
        Club::factory()->create(['clubname' => 'SV Musterstadt', 'zip_code' => '12345', 'city' => 'Testhausen']);

        $result = $this->search([
            'clubname' => 'FC Testhausen',
            'zip_code' => '12345',
            'city' => 'Testhausen',
        ]);

        $this->assertCount(1, $result);
        $this->assertSame($match->id, $result->first()->id);
    }
    #endregion

    private function search(array $params): \Illuminate\Database\Eloquent\Collection
    {
        return $this->service->searchClubs(new ClubSearchDTO($params));
    }
}
