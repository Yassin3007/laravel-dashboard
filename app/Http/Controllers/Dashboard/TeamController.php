<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Models\Company;
use App\Services\TeamService;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $teams = $this->teamService->getAllPaginated(15 , ['company']);

        return view('dashboard.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $companies = Company::query()->active()->get();
        return view('dashboard.teams.create',compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TeamRequest $request
     * @return RedirectResponse
     */
    public function store(TeamRequest $request): RedirectResponse
    {
        try {
            $this->teamService->create($request->validated());

            return redirect()->route('teams.index')
                ->with('success', 'Team created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating Team: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Team $team
     * @return View
     */
    public function show(Team $team): View
    {
        return view('dashboard.teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Team $team
     * @return View
     */
    public function edit(Team $team): View
    {
        $companies = Company::query()->active()->get();

        return view('dashboard.teams.edit', compact('team','companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TeamRequest $request
     * @param Team $team
     * @return RedirectResponse
     */
    public function update(TeamRequest $request, Team $team): RedirectResponse
    {
        try {
            $this->teamService->update($team, $request->validated());

            return redirect()->route('teams.index')
                ->with('success', 'Team updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating Team: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Team $team
     * @return RedirectResponse
     */
    public function destroy(Team $team): RedirectResponse
    {
        try {
            $this->teamService->delete($team);

            return redirect()->route('teams.index')
                ->with('success', 'Team deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting Team: ' . $e->getMessage());
        }
    }
}
