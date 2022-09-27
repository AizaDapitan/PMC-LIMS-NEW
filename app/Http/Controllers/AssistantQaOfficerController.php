<?php

namespace App\Http\Controllers;

use App\Models\AssistantQaOfficer;
use Illuminate\Http\Request;
use App\Services\AccessRightService;
use Exception;

class AssistantQaOfficerController extends Controller
{
    
    protected $accessRightService;

    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Assistant QA Officer");

        if (!$rolesPermissions['view']) {
            abort(401);
        }

        return view('assistantofficer.index');
    }
    public function getAssistantOfficer()
    {
        $assistant = AssistantQaOfficer::orderBy('name', 'asc')->get();
        return $assistant;
    }
    public function create()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Assistant QA Officer");

        if (!$rolesPermissions['create']) {
            abort(401);
        }

        return view('assistantofficer.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',

        ]);

        $created_at = \Carbon\Carbon::now();
        try {
            AssistantQaOfficer::create([
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
        $rolesPermissions = $this->accessRightService->hasPermissions("Assistant QA Officer");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $assistant = AssistantQaOfficer::where('id', $id)->first();
        return view('assistantofficer.edit', compact('assistant'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        $updated_at = \Carbon\Carbon::now();
        try {
            $assistant = AssistantQaOfficer::find($request->id);

            $data = [
                'name' => strtoupper($request->name),
                'description' => $request->description,
                'active'    => $request->active,
                'updated_at' => $updated_at
            ];

            $assistant->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }
}
