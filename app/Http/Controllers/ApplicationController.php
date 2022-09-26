<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccessRightService;
use App\Models\Application;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    protected $accessRightService;
    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index(Request $request)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Application Maintenance");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('application.index');
    }
    public function getScheduledShutdown()
    {
        $schedule = Application::where('deleted_at', null)->get();
        return $schedule;
    }
    public function create()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Application Maintenance");
        if (!$rolesPermissions['create']) {
            abort(401);
        }
        return view('application.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'scheduled_date' => 'required',
            'scheduled_time' => 'required',
            'reason' => 'required',

        ]);
        try {
            Application::create([
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'reason' => $request->reason,
            ]);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Application Maintenance");
        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $schedule = Application::where('id', $id)->first();
        return view('application.edit', compact('schedule'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'scheduled_date' => 'required',
            'scheduled_time' => 'required',
            'reason' => 'required',

        ]);
        try {
            $application = Application::find($request->id);

            $data = [
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'reason' => $request->reason,
            ];


            $application->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        try {
            $shutdownapplication = Application::find($request->id);

            $data = [
                'deleted_at' => Carbon::now(),
            ];
            $shutdownapplication->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function reindex(Request $request)
    {
        try {
            DB::update('EXEC runScheduledIndexing');
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }

    public function systemUp()
    {
        try {
            Artisan::call('up');

            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }

    public function systemDown()
    {
        try {
            $sessions = glob(storage_path("framework/sessions/*"));
            foreach ($sessions as $file) {
                if (is_file($file))
                    unlink($file);
            }
            Artisan::call('down');
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }public function systemDown_auto()
    {
        try {
            $sessions = glob(storage_path("framework/sessions/*"));
            foreach ($sessions as $file) {
                if (is_file($file))
                    unlink($file);
            }
            Artisan::call('down');
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
}
