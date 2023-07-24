<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeptUserTransRequest;
use App\Http\Requests\TransmittalRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\DeptuserTrans;
use App\Models\Transmittal;
use App\Models\TransmittalItem;
use App\Services\UserRightService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\AccessRightService;

class DeptOfficerController extends Controller
{
    protected $accessRightService;
    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Department Officer Transmittals");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('deptofficer.index');
    }

    public function getDeptOfficers(Request $request)
    {
        $currentMonth = Carbon::now()->month;

        $firstDay = Carbon::createFromDate(null, $currentMonth, 1);
        $lastDay = Carbon::createFromDate(null, $currentMonth, $firstDay->daysInMonth);

        $dateFrom = $firstDay->toDateString();
        $dateTo = $lastDay->toDateString();
        if (isset($request->dateFrom)) {
            $dateFrom = $request->dateFrom;
        }
        if (isset($request->dateTo)) {
            $dateTo = $request->dateTo;
        }

        $deptofficers = DeptuserTrans::where([['isdeleted', 0], ['isSaved', 1], ['transcode', 1], ['section', auth()->user()->section]])
            ->whereBetween('datesubmitted', [$dateFrom, $dateTo])
            ->orderBy('datesubmitted', 'desc')->get();
        
        $deptofficers->transform(function ($item) {
            // Transform the timesubmitted field to 12-hour format (2:56 PM)
            $timestamp = strtotime($item->timesubmitted);
            $item->timesubmitted = date('g:i A', $timestamp);
        
            return $item;
        });
        return $deptofficers;
    }

    public function deptofficersList(DeptuserTrans $deptofficer)
    {
        $deptofficers = $deptofficer->where('active', 1)->get();
        return $deptofficers;
    }

    public function edit($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Department Officer Transmittals");
        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        // dd($transmittal);
        return view('deptofficer.edit', compact('transmittal'));
    }

    public function view($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Department Officer Transmittals");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        // dd($transmittal);
        return view('deptofficer.view', compact('transmittal'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        try {

            $deptuserTrans = DeptuserTrans::find($request->id);

            $data = [
                'approvedDate' => Carbon::now(),
                'approver' => auth()->user()->username,
                'status' =>  $request->status,
            ];
            $deptuserTrans->update($data);

            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }
    public function delete(Request $request)
    {
        $request->validate(['id' => 'required']);
        try {
            $item = DeptuserTrans::find($request->id);

            $data = [
                'isdeleted' => 1,
                'deleted_at' => Carbon::now()
            ];
            $item->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 500]);
        }
    }
}
