<?php

namespace Tests\Feature\Player;

use App\DTOs\PlayerSearchDTO;
use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use App\Services\PlayerSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Laravel\Prompts\error;

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
        /** @var Player $player */
        $player = $result->first();
        $this->assertSame('John', $player->firstname);
    }

    public function test_firstname_filter_matches_partial(): void
    {
        Player::factory()->create(['firstname' => 'Johannes']);
        Player::factory()->create(['firstname' => 'Jane']);

        $result = $this->search(['firstname' => 'Jo']);

        $this->assertCount(1, $result);
        /** @var Player $player */
        $player = $result->first();
        $this->assertSame('Johannes', $player->firstname);
    }

    public function test_no_firstname_filter_returns_all(): void
    {
        Player::factory()->count(3)->create();

        $result = $this->search([]);

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
        /** @var Player $player */
        $player = $result->first();
        $this->assertSame('Müller', $player->lastname);
    }

    public function test_lastname_filter_matches_partial(): void
    {
        Player::factory()->create(['lastname' => 'Müller']);
        Player::factory()->create(['lastname' => 'Meier']);

        $result = $this->search(['lastname' => 'ller']);

        $this->assertCount(1, $result);
        /** @var Player $player */
        $player = $result->first();
        $this->assertSame('Müller', $player->lastname);
    }

    #endregion

    #region yearsOfBirth
    public function test_filters_by_year_of_birth(): void
    {
        Player::factory()->create(['year_of_birth' => 1995]);
        Player::factory()->create(['year_of_birth' => 2000]);

        $result = $this->search(['years_of_birth' => [1995]]);

        $this->assertCount(1, $result);
        /** @var Player $player */
        $player = $result->first();
        $this->assertSame(1995, $player->year_of_birth);
    }

    public function test_filters_by_multiple_years_of_birth(): void
    {
        Player::factory()->create(['year_of_birth' => 1995]);
        Player::factory()->create(['year_of_birth' => 1998]);
        Player::factory()->create(['year_of_birth' => 2000]);

        $result = $this->search(['years_of_birth' => [1995, 1998]]);

        $this->assertCount(2, $result);
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

    private function search(array $params): \Illuminate\Database\Eloquent\Collection
    {
        return $this->service->searchPlayers(new PlayerSearchDTO($params), []);
    }
}
