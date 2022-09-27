<?php

namespace App\Http\Controllers;

use App\Models\Analyst;
use Illuminate\Http\Request;
use App\Services\AccessRightService;
use Exception;

class QAAnalystController extends Controller
{
    protected $accessRightService;

    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst");

        if (!$rolesPermissions['view']) {
            abort(401);
        }

        return view('qaanalyst.index');
    }
    public function getAnalyst()
    {
        $analyst = Analyst::orderBy('name', 'asc')->get();
        return $analyst;
    }
    public function create()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst");

        if (!$rolesPermissions['create']) {
            abort(401);
        }

        return view('qaanalyst.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',

        ]);

        $created_at = \Carbon\Carbon::now();
        try {
            Analyst::create([
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
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $analyst = Analyst::where('id', $id)->first();
        return view('qaanalyst.edit', compact('analyst'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        $updated_at = \Carbon\Carbon::now();
        try {
            $analyst = Analyst::find($request->id);

            $data = [
                'name' => strtoupper($request->name),
                'description' => $request->description,
                'active'    => $request->active,
                'updated_at' => $updated_at
            ];

            $analyst->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }
    public function getAnalystActive()
    {
        $analyst = Analyst::where('active',1)->orderBy('name', 'asc')->get();
        return $analyst;
    }
}
