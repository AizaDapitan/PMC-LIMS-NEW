<?php

namespace App\Http\Controllers;

use App\Models\FireAssayer;
use Illuminate\Http\Request;
use App\Services\AccessRightService;
use Exception;

class FireAssayerController extends Controller
{
    
    protected $accessRightService;

    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Fire Assayer");

        if (!$rolesPermissions['view']) {
            abort(401);
        }

        return view('fireassayer.index');
    }
    public function getFireAssayer()
    {
        $fireAssayer = FireAssayer::orderBy('name', 'asc')->get();
        return $fireAssayer;
    }
    public function create()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Fire Assayer");

        if (!$rolesPermissions['create']) {
            abort(401);
        }

        return view('fireassayer.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',

        ]);

        $created_at = \Carbon\Carbon::now();
        try {
            FireAssayer::create([
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
        $rolesPermissions = $this->accessRightService->hasPermissions("Fire Assayer");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $fireassayer = FireAssayer::where('id', $id)->first();
        return view('fireassayer.edit', compact('fireassayer'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        $updated_at = \Carbon\Carbon::now();
        try {
            $fireassayer = FireAssayer::find($request->id);

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
    public function getFireAssayerActive()
    {
        $fireAssayer = FireAssayer::where('active',1)->orderBy('name', 'asc')->get();
        return $fireAssayer;
    }
}
