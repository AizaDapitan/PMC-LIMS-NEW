<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeptUserTransRequest;
use App\Models\DeptuserTrans;
use App\Models\TransmittalItem;
use App\Models\Worksheet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Services\AccessRightService;

class OfficerController extends Controller
{
    protected $accessRightService;
    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Worksheets");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('officer.index');
    }
    public function getWorksheet()
    {
        $worksheet = Worksheet::where([['isdeleted', 0],['isAnalyzed',1],['isPosted',0]])->orderBy('created_at', 'desc')->get();
        return $worksheet;
    }
    public function edit($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Worksheets");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $worksheet = Worksheet::where('id', $id)->first();
        return view('officer.edit', compact('worksheet'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'id'    => 'required',
        ]);
        try {
            $worksheet = Worksheet::find($request->id);
            $data = [
                'posted_at' => Carbon::now(),
                'posted_by' => auth()->user()->username,
                'isPosted' => 1
            ];

            $worksheet->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function view($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Worksheets");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $worksheet = Worksheet::where('id', $id)->first();
        return view('officer.view', compact('worksheet'));
    }
    public function getPostedWorksheet()
    {
        $worksheet = Worksheet::where([['isdeleted', 0],['isPosted',1]])->orderBy('created_at', 'desc')->get();
        return $worksheet;
    }
    public function posted()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Worksheets");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('officer.posted');
    }
    public function unpost($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Worksheets");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $worksheet = Worksheet::where('id', $id)->first();
        return view('officer.editposted', compact('worksheet'));
    }
    public function updateposted(Request $request)
    {
        $request->validate([
            'id'    => 'required',
        ]);
        try {
            $worksheet = Worksheet::find($request->id);
            $data = [
                'isPosted' => 0
            ];

            $worksheet->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function viewposted($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Worksheets");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $worksheet = Worksheet::where('id', $id)->first();
        return view('officer.viewposted', compact('worksheet'));
    }
    public function transmittal()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Transmittals");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
       return view('officer.transmittal');
    }
    public function getTransmittal()
    {
        $deptusers = DeptuserTrans::where([['isdeleted', 0], ['isSaved', 1],['transcode',2]])->orderBy('datesubmitted', 'desc')->get();

        return $deptusers;
    }
    public function createTransmittal()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Transmittals");

        if (!$rolesPermissions['create']) {
            abort(401);
        }
        return view('officer.createtransmittal');
    }
    public function checkTransNo(Request $request)
    {
        $transmittal = DeptuserTrans::where([['transmittalno', $request->transmittalno],['isSaved',1]])->get();
        return $transmittal;
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
                    'created_by' => auth()->user()->username,
                    'isSaved'   => 1,
                    'transType' => $request->transType,
                    'transcode' => 2
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
                    'status' =>  $request->status,
                    'created_by' => auth()->user()->username,
                    'isSaved'   => 1,
                    'transType' => $request->transType,
                    'transcode' => 2
                ]);
            }
            TransmittalItem::where('transmittalno', $request->transmittalno)->update(['source' => $request->source]);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
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
                    'status' =>  $request->status,
                    'transType' => $request->transType,
                    'created_by' => auth()->user()->username,
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
                    'status' =>  $request->status,
                    'transType' => $request->transType,
                    'created_by' => auth()->user()->username,
                    'transcode' => 2
                ];
                $transmittal = DeptuserTrans::create($data);
                return response()->json(['id' => $transmittal->id]);
            }
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function editTransmittal($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Transmittals");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('officer.edittransmittal', compact('transmittal'));
    }
    public function updateTransmittal(Request $request)
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
                'status' =>  $request->status,
                'created_by' => auth()->user()->username,
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
    public function viewTransmittal($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Transmittals");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('officer.viewtransmittal', compact('transmittal'));
    }
    public function deleteTransmittal(Request $request)
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
    public function unsavedTrans()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Officer Unsaved Transmittals");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('officer.unsaved');
    }
    public function getUnsaved()
    {
        
        $unsavedTrans = DeptuserTrans::where([['isSaved', 0], ['created_by', auth()->user()->username], ['isdeleted', 0],['transcode',2 ]])->orderBy('transmittalno', 'asc')->get();

        return $unsavedTrans;
    }
    public function transmittal_Solutions()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Solutions");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('officer.solutions');
    }
    public function getTransmittal_Solutions()
    {
        $transmittal = DeptuserTrans::where([['isdeleted', 0], ['isReceived', true], ['transType', 'Solutions']])
            ->orderBy('transmittalno', 'asc')->get();

        return $transmittal;
    }
    public function view_solution($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Solutions");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('officer.view_solutions', compact('transmittal'));
    }
    public function post_solution($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Solutions");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('officer.post_solutions', compact('transmittal'));
    }
    public function post(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        try {

            $deptuserTrans = DeptuserTrans::find($request->id);

            $data = [
                'posted_at' => Carbon::now(),
                'postedBy' => auth()->user()->username,
                'isPosted' =>  true,
            ];
            $deptuserTrans->update($data);

            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }
}
