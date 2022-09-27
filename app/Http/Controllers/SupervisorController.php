<?php

namespace App\Http\Controllers;

use App\Models\Supervisor;
use Illuminate\Http\Request;
use App\Services\AccessRightService;
use Exception;

class SupervisorController extends Controller
{
    protected $accessRightService;

    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Shifting Supervisor");

        if (!$rolesPermissions['view']) {
            abort(401);
        }

        return view('supervisor.index');
    }
    public function getSupervisor()
    {
        $supervisor = Supervisor::orderBy('name', 'asc')->get();
        return $supervisor;
    }
    public function create()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Shifting Supervisor");

        if (!$rolesPermissions['create']) {
            abort(401);
        }

        return view('supervisor.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',

        ]);

        $created_at = \Carbon\Carbon::now();
        try {
            Supervisor::create([
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
        $rolesPermissions = $this->accessRightService->hasPermissions("Shifting Supervisor");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $supervisor = Supervisor::where('id', $id)->first();
        return view('supervisor.edit', compact('supervisor'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        $updated_at = \Carbon\Carbon::now();
        try {
            $supervisor = Supervisor::find($request->id);

            $data = [
                'name' => strtoupper($request->name),
                'description' => $request->description,
                'active'    => $request->active,
                'updated_at' => $updated_at
            ];

            $supervisor->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }
    public function getSupervisorActive()
    {
        $supervisor = Supervisor::where('active',1)->orderBy('name', 'asc')->get();
        return $supervisor;
    }
}
