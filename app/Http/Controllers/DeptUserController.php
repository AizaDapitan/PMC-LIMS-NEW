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
use Illuminate\Support\Facades\Storage;
use App\Services\AccessRightService;
use PhpParser\Node\Stmt\Else_;

class DeptUserController extends Controller
{
    protected $accessRightService;
    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $assigned_module = auth()->user()->assigned_module;
        if (auth()->user()->role != 'ADMIN') {
            if ($assigned_module == 'Department User') {
                $rolesPermissions = $this->accessRightService->hasPermissions("Department User Transmittals");
                if (!$rolesPermissions['view']) {
                    abort(401);
                }
                return view('deptuser.index');
            } else if ($assigned_module == 'Department Officer') {
                return redirect()->route('deptofficer.index');
            } else if ($assigned_module == 'Receiving') {
                return redirect()->route('qaqcreceiver.index');
            } else if ($assigned_module == 'Assayer') {
                return redirect()->route('assayer.index');
            } else if ($assigned_module == 'Tech/Digester') {
                return redirect()->route('digester.index');
            } else if ($assigned_module == 'Analyst') {
                return redirect()->route('analyst.index');
            } else if ($assigned_module == 'Officer') {
                return redirect()->route('officer.index');
            }
        } else {
            return view('deptuser.index');
        }
    }

    public function getDeptUsers()
    {
        $deptusers = DeptuserTrans::where([['isdeleted', 0], ['isSaved', 1], ['transcode', 1], ['created_by', auth()->user()->username]])->orderBy('datesubmitted', 'desc')->get();

        return $deptusers;
    }

    public function deptusersList(DeptuserTrans $deptuser)
    {
        $deptusers = $deptuser->where([['active', 1], ['isSaved', 1], ['transcode', 1], ['created_by', auth()->user()->username]])->get();
        return $deptusers;
    }
    public function create()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Department User Transmittals");
        if (!$rolesPermissions['create']) {
            abort(401);
        }
        return view('deptuser.create');
    }
    public function store(DeptUserTransRequest $request)
    {
        try {
            $filenamewithextension = $request->file('cocFile')->getClientOriginalName();
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $extension = $request->file('cocFile')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename . '_' . $request->transmittalno .  '.' . $extension;

            $request->file('cocFile')->storeAs(('public/coc files/'), $filenametostore);
            $deptuserTrans = DeptuserTrans::find($request->id);

            if ($deptuserTrans) {
                $data = [
                    'transmittalno' => $request->transmittalno,
                    'purpose' => $request->purpose,
                    'datesubmitted' =>  $request->datesubmitted,
                    'timesubmitted' =>   $request->timesubmitted,
                    'date_needed'    =>  $request->date_needed,
                    'priority' => $request->priority,
                    'status' =>  $request->status,
                    'email_address' => $request->email_address,
                    'source' =>  $request->source,
                    'cocFile' => $filenametostore,
                    'isSaved'   => 1,
                    'transType' => $request->transType,
                    'transcode' => 1,
                ];
                $deptuserTrans->update($data);
            } else {
                DeptuserTrans::create([
                    'transmittalno' => $request->transmittalno,
                    'purpose' => $request->purpose,
                    'datesubmitted' =>  $request->datesubmitted,
                    'timesubmitted' =>   $request->timesubmitted,
                    'date_needed'    =>  $request->date_needed,
                    'priority' => $request->priority,
                    'status' =>  $request->status,
                    'email_address' => $request->email_address,
                    'source' =>  $request->source,
                    'cocFile' => $filenametostore,
                    'created_by' => auth()->user()->username,
                    'isSaved'   => 1,
                    'transType' => $request->transType,
                    'transcode' => 1,
                    'section'   => auth()->user()->section
                ]);
            }
            TransmittalItem::where('transmittalno', $request->transmittalno)->update(['source' => $request->source]);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Department User Transmittals");
        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        // dd($transmittal);
        return view('deptuser.edit', compact('transmittal'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'transmittalno' => 'required',
            'purpose' => 'required',
            'datesubmitted' => 'required',
            'timesubmitted' => 'required',
            'date_needed' => 'required',
            'priority' => 'required',
            'status' => 'required',
            'email_address' => 'required|email',
            'source' => 'required',
            'transType' => 'required',
        ]);
        try {
            $filenametostore = $request->cocFile;
            if ($request->hasFile('cocFile')) {
                $filenamewithextension = $request->file('cocFile')->getClientOriginalName();
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                $extension = $request->file('cocFile')->getClientOriginalExtension();

                //filename to store
                $filenametostore = $filename . '_' . $request->transmittalno .  '.' . $extension;

                $request->file('cocFile')->storeAs(('public/coc files/'), $filenametostore);
            }
            $deptuserTrans = DeptuserTrans::find($request->id);

            $data = [
                'transmittalno' => $request->transmittalno,
                'purpose' => $request->purpose,
                'datesubmitted' =>  $request->datesubmitted,
                'timesubmitted' =>   $request->timesubmitted,
                'date_needed'    =>  $request->date_needed,
                'priority' => $request->priority,
                'status' =>  $request->status,
                'email_address' => $request->email_address,
                'source' =>  $request->source,
                'cocFile' => $request->hasFile('cocFile') ? $filenametostore : $deptuserTrans->cocFile,
                'isSaved'   => 1,
                'transType' => $request->transType,
            ];
            $deptuserTrans->update($data);
            TransmittalItem::where('transmittalno', $request->transmittalno)->update(['source' => $request->source]);

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
            return response()->json(['errors' => $e->getMessage(), 500]);
        }
    }
    public function view($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Department User Transmittals");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('deptuser.view', compact('transmittal'));
    }
    public function autosave(Request $request)
    {
        try {
            $filenametostore = "";
            if ($request->hasFile('cocFile')) {
                $filenamewithextension = $request->file('cocFile')->getClientOriginalName();
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                $extension = $request->file('cocFile')->getClientOriginalExtension();

                //filename to store
                $filenametostore = $filename . '_' . $request->transmittalno .  '.' . $extension;

                $request->file('cocFile')->storeAs(('public/coc files/'), $filenametostore);
            }
            $deptuserTrans = DeptuserTrans::find($request->id);

            if ($deptuserTrans) {
                $data = [
                    'transmittalno' => $request->transmittalno,
                    'purpose' => $request->purpose,
                    'datesubmitted' =>  $request->datesubmitted,
                    'timesubmitted' =>   $request->timesubmitted,
                    'date_needed'    =>  $request->date_needed,
                    'priority' => $request->priority,
                    'status' =>  $request->status,
                    'email_address' => $request->email_address,
                    'source' =>  $request->source,
                    'cocFile' => $request->hasFile('cocFile') ? $filenametostore : $deptuserTrans->cocFile,
                    'transType' => $request->transType
                ];
                $deptuserTrans->update($data);
                return response()->json(['id' => $deptuserTrans->id]);
            } else {
                $data = [
                    'transmittalno' => $request->transmittalno,
                    'purpose' => $request->purpose,
                    'datesubmitted' =>  $request->datesubmitted,
                    'timesubmitted' =>   $request->timesubmitted,
                    'date_needed'    =>  $request->date_needed,
                    'priority' => $request->priority,
                    'status' =>  $request->status,
                    'email_address' => $request->email_address,
                    'source' =>  $request->source,
                    'cocFile' => $filenametostore,
                    'transType' => $request->transType,
                    'created_by' => auth()->user()->username,
                    'transcode' => 1,
                    'section'   => auth()->user()->section
                ];
                $transmittal = DeptuserTrans::create($data);
                return response()->json(['id' => $transmittal->id]);
            }
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function unsavedTrans()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Department User Unsaved Transmittals");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('deptuser.unsaved');
    }
    public function getUnsaved()
    {
        $unsavedTrans = DeptuserTrans::where([['isSaved', 0], ['created_by', auth()->user()->username], ['isdeleted', 0], ['transcode', 1]])->orderBy('transmittalno', 'asc')->get();

        return $unsavedTrans;
    }
    public function checkTransNo(Request $request)
    {
        $transmittal = DeptuserTrans::where([['transmittalno', $request->transmittalno],['isSaved',1]])->get();
        // $exists = false;
        // if (count($transmittal) > 0) {
        //     $exists = true;
        // }
        return $transmittal;
    }
}
