<?php

namespace App\Http\Controllers;

use App\Models\ChiefChemist;
use Illuminate\Http\Request;
use App\Services\AccessRightService;
use Exception;

class ChiefChemistController extends Controller
{
    
    protected $accessRightService;

    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Chief Chemist");

        if (!$rolesPermissions['view']) {
            abort(401);
        }

        return view('chiefchemist.index');
    }
    public function getChiefChemist()
    {
        $assistant = ChiefChemist::orderBy('name', 'asc')->get();
        return $assistant;
    }
    public function create()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Chief Chemist");

        if (!$rolesPermissions['create']) {
            abort(401);
        }

        return view('chiefchemist.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',

        ]);

        $created_at = \Carbon\Carbon::now();
        try {
            ChiefChemist::create([
                'name' => strtoupper($request->name),
                'description' => $request->description,
                'active' => 1,
                // 'created_at' => $created_at,
            ]);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Chief Chemist");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $chemist = ChiefChemist::where('id', $id)->first();
        return view('chiefchemist.edit', compact('chemist'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        $updated_at = \Carbon\Carbon::now();
        try {
            $fireassayer = ChiefChemist::find($request->id);

            $data = [
                'name' => strtoupper($request->name),
                'description' => $request->description,
                'active'    => $request->active,
                'updated_at' => $updated_at
            ];

            $fireassayer->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }
    public function getChiefChemistActive()
    {
        $assistant = ChiefChemist::where('active',1)->orderBy('name', 'asc')->get();
        return $assistant;
    }
}
