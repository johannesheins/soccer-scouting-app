<?php

namespace App\Http\Controllers;

use App\DTOs\ClubSearchDTO;
use App\Http\Requests\Club\ClubSearchRequest;
use App\Services\ClubSearchService;

class ClubSearchController extends Controller
{
    public function index(ClubSearchRequest $request)
    {
        $clubs = self::search($request);

        return inertia('club/club-search', [
            'clubs' => $clubs->getData(),
        ]);
    }

    public function search(ClubSearchRequest $request)
    {
        $clubSearchDTO = new ClubSearchDTO($request->validated());
        $clubSearchService = new ClubSearchService();
        $clubs = $clubSearchService->searchClubs($clubSearchDTO)->toArray();

        return response()->json($clubs);
    }
}
