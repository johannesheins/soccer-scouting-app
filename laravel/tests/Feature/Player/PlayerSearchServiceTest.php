<?php

namespace Tests\Feature\Player;

use App\DTOs\PlayerSearchDTO;
use App\Enums\FootEnum;
use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use App\Services\PlayerSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlayerSearchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PlayerSearchService();
    }

    #region firstname
    public function test_filters_by_firstname(): void
    {
        Player::factory()->create(['firstname' => 'John']);
        Player::factory()->create(['firstname' => 'Jane']);

        $result = $this->search(['firstname' => 'John']);
        $this->assertCount(1, $result);

        $player = $result->first();
        $this->assertSame('John', $player->firstname);
    }

    public function test_firstname_filter_matches_partial(): void
    {
        Player::factory()->create(['firstname' => 'Johannes']);
        Player::factory()->create(['firstname' => 'Jane']);

        $result = $this->search(['firstname' => 'Jo']);
        $this->assertCount(1, $result);

        $player = $result->first();
        $this->assertSame('Johannes', $player->firstname);
    }

    public function test_no_firstname_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_string_firstname_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search(['firstname' => '']);
        $this->assertCount(3, $result);
    }

    #endregion

    #region lastname
    public function test_filters_by_lastname(): void
    {
        Player::factory()->create(['lastname' => 'Müller']);
        Player::factory()->create(['lastname' => 'Schmidt']);

        $result = $this->search(['lastname' => 'Müller']);

        $this->assertCount(1, $result);

        $player = $result->first();
        $this->assertSame('Müller', $player->lastname);
    }

    public function test_lastname_filter_matches_partial(): void
    {
        Player::factory()->create(['lastname' => 'Müller']);
        Player::factory()->create(['lastname' => 'Meier']);

        $result = $this->search(['lastname' => 'ller']);

        $this->assertCount(1, $result);

        $player = $result->first();
        $this->assertSame('Müller', $player->lastname);
    }

    public function test_no_lastname_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_string_lastname_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search(['lastname' => '']);
        $this->assertCount(3, $result);
    }

    #endregion

    #region yearsOfBirth
    public function test_filters_by_year_of_birth(): void
    {
        Player::factory()->create(['year_of_birth' => '1995/1996']);
        Player::factory()->create(['year_of_birth' => '2000/2001']);

        $result = $this->search(['years_of_birth' => ['1995/1996']]);

        $this->assertCount(1, $result);

        $player = $result->first();
        $this->assertSame('1995/1996', $player->year_of_birth);
    }

    public function test_filters_by_multiple_years_of_birth(): void
    {
        Player::factory()->create(['year_of_birth' => 1995]);
        Player::factory()->create(['year_of_birth' => 1998]);
        Player::factory()->create(['year_of_birth' => 2000]);

        $result = $this->search(['years_of_birth' => [1995, 1998]]);

        $this->assertCount(2, $result);
    }

    public function test_no_years_of_birth_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_years_of_birth_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search(['years_of_birth' => []]);
        $this->assertCount(3, $result);
    }

    #endregion

    #region height
    public function test_filters_by_height_from(): void
    {
        Player::factory()->create(['height' => 170]);
        Player::factory()->create(['height' => 190]);

        $result = $this->search(['height_from' => 180]);

        $this->assertCount(1, $result);
        $this->assertSame(190, $result->first()->height);
    }

    public function test_filters_by_height_to(): void
    {
        Player::factory()->create(['height' => 170]);
        Player::factory()->create(['height' => 190]);

        $result = $this->search(['height_to' => 180]);

        $this->assertCount(1, $result);
        $this->assertSame(170, $result->first()->height);
    }

    public function test_filters_by_height_range(): void
    {
        Player::factory()->create(['height' => 170]);
        Player::factory()->create(['height' => 180]);
        Player::factory()->create(['height' => 190]);

        $result = $this->search(['height_from' => 175, 'height_to' => 185]);

        $this->assertCount(1, $result);
        $this->assertSame(180, $result->first()->height);
    }

    public function test_no_height_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    #endregion

    #region strongFoots
    public function test_filters_by_strong_foot(): void
    {
        Player::factory()->create(['strong_foot' => FootEnum::LEFT->value]);
        Player::factory()->create(['strong_foot' => FootEnum::RIGHT->value]);

        $result = $this->search(['strong_foots' => [FootEnum::LEFT->value]]);

        $this->assertCount(1, $result);
        $this->assertSame(FootEnum::LEFT->value, $result->first()->strong_foot);
    }

    public function test_filters_by_multiple_strong_foots(): void
    {
        Player::factory()->create(['strong_foot' => FootEnum::LEFT->value]);
        Player::factory()->create(['strong_foot' => FootEnum::RIGHT->value]);
        Player::factory()->create(['strong_foot' => FootEnum::BOTH->value]);

        $result = $this->search(['strong_foots' => [FootEnum::LEFT->value, FootEnum::RIGHT->value]]);

        $this->assertCount(2, $result);
    }

    public function test_no_strong_foots_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_strong_foots_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search(['strong_foots' => []]);
        $this->assertCount(3, $result);
    }

    #endregion

    #region clubIds
    public function test_filters_by_club(): void
    {
        $club = Club::factory()->create();
        $otherClub = Club::factory()->create();

        Player::factory()->create(['club_id' => $club->id]);
        Player::factory()->create(['club_id' => $otherClub->id]);

        $result = $this->search(['club_ids' => [$club->id]]);

        $this->assertCount(1, $result);
        $this->assertSame($club->id, $result->first()->club_id);
    }

    public function test_filters_by_multiple_clubs(): void
    {
        $club1 = Club::factory()->create();
        $club2 = Club::factory()->create();
        $otherClub = Club::factory()->create();

        Player::factory()->create(['club_id' => $club1->id]);
        Player::factory()->create(['club_id' => $club2->id]);
        Player::factory()->create(['club_id' => $otherClub->id]);

        $result = $this->search(['club_ids' => [$club1->id, $club2->id]]);

        $this->assertCount(2, $result);
    }

    public function test_no_club_ids_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_club_ids_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search(['club_ids' => []]);
        $this->assertCount(3, $result);
    }

    #endregion

    #region positionIds
    public function test_filters_by_position(): void
    {
        $position = Position::factory()->create();
        $otherPosition = Position::factory()->create();

        $match = Player::factory()->create();
        $match->positions()->attach($position->id);

        $noMatch = Player::factory()->create();
        $noMatch->positions()->attach($otherPosition->id);

        $result = $this->search(['position_ids' => [$position->id]]);

        $this->assertCount(1, $result);
        $this->assertSame($match->id, $result->first()->id);
    }

    public function test_filters_by_multiple_positions(): void
    {
        $pos1 = Position::factory()->create();
        $pos2 = Position::factory()->create();
        $otherPos = Position::factory()->create();

        $match1 = Player::factory()->create();
        $match1->positions()->attach($pos1->id);

        $match2 = Player::factory()->create();
        $match2->positions()->attach($pos2->id);

        $noMatch = Player::factory()->create();
        $noMatch->positions()->attach($otherPos->id);

        $result = $this->search(['position_ids' => [$pos1->id, $pos2->id]]);

        $this->assertCount(2, $result);
    }

    public function test_no_position_ids_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_position_ids_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search(['position_ids' => []]);
        $this->assertCount(3, $result);
    }

    #endregion

    #region leere Ergebnisse
    public function test_returns_empty_collection_when_no_match(): void
    {
        Player::factory()->create(['firstname' => 'John']);

        $result = $this->search(['firstname' => 'Nonexistent']);

        $this->assertCount(0, $result);
    }
    #endregion

    #region kombinierte Filter
    public function test_combines_multiple_filters(): void
    {
        $club = Club::factory()->create();

        $match = Player::factory()->create([
            'firstname' => 'John',
            'year_of_birth' => 1995,
            'club_id' => $club->id,
        ]);

        Player::factory()->create(['firstname' => 'John', 'year_of_birth' => 2000]);
        Player::factory()->create(['firstname' => 'Jane', 'year_of_birth' => 1995]);

        $result = $this->search([
            'firstname'      => 'John',
            'years_of_birth'  => [1995],
            'club_ids'       => [$club->id],
        ]);

        $this->assertCount(1, $result);
        $this->assertSame($match->id, $result->first()->id);
    }

    #endregion

    #region with (Eager Loading)
    public function test_eager_loads_given_relations(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();
        $player = Player::factory()->create(['club_id' => $club->id]);
        $player->positions()->attach($position->id);

        $result = $this->service->searchPlayers(new PlayerSearchDTO([]), ['club', 'positions']);

        $this->assertTrue($result->first()->relationLoaded('club'));
        $this->assertTrue($result->first()->relationLoaded('positions'));
    }
    #endregion

    private function search(array $params): \Illuminate\Database\Eloquent\Collection
    {
        return $this->service->searchPlayers(new PlayerSearchDTO($params), []);
    }
}
