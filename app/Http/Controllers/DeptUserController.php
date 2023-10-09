<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Transmittal;
use Illuminate\Http\Request;
use App\Models\DeptuserTrans;
use PhpParser\Node\Stmt\Else_;
use App\Models\TransmittalItem;
use App\Http\Requests\UserRequest;
use App\Services\UserRightService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Services\AccessRightService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\TransmittalRequest;
use App\Http\Requests\DeptUserTransRequest;

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

    public function getDeptUsers(Request $request)
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

        $deptusers = DeptuserTrans::where([['isdeleted', 0], ['isSaved', 1], ['transcode', 1], ['created_by', auth()->user()->username]])
            ->whereBetween('datesubmitted', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')->get();
        $deptusers->transform(function ($item) {
            $item->timesubmitted = date('g:i A', strtotime($item->timesubmitted));
        
            return $item;
        });
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
            if ($request->hasFile('cocFile')) {
                $filenamewithextension = $request->file('cocFile')->getClientOriginalName();
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                $extension = $request->file('cocFile')->getClientOriginalExtension();

                $filenametostore = $filename . '_' . $request->transmittalno .  '.' . $extension;
                $request->file('cocFile')->storeAs('public/coc files/', $filenametostore);
            }else{
                $filenametostore = "";
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
            
            $items = TransmittalItem::where([['isdeleted', 0], ['transmittalno', $request->transmittalno]])->get();
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
                'transType' => $request->transType,
                'name' => auth()->user()->name,
                'items' => $items,
            ];
            try{
                Mail::send('emails.transmittalStored', $data,
                function($message) use ($request){
                    $message->to($request->email_address, auth()->user()->dept)
                    ->subject('PMC-LIMS : Transmittal No. '.$request->transmittalno.' successfully created.');
                });
            }catch(Exception $e){

            }
            
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
            $items = TransmittalItem::where([['isdeleted', 0], ['transmittalno', $request->transmittalno]])->get();
            $data["items"] = $items;

            try{
                Mail::send('emails.transmittalSaved', $data,
                function($message) use ($request){
                    $message->to($request->email_address, auth()->user()->dept)
                    ->subject('PMC-LIMS : Transmittal No. '.$request->transmittalno.' details successfully updated.');
                });
            }catch(Exception $e){

            }

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
        $unsavedTrans->transform(function ($item) {
            // Transform the timesubmitted field to 12-hour format (2:56 PM)
            $timestamp = strtotime($item->timesubmitted);
            $item->timesubmitted = date('g:i A', $timestamp);
        
            return $item;
        });
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

    public function getDeptOfficerEmails(){
        $officers = User::where([['isActive', 1], ['assigned_module', 'Department Officer'], ['dept', auth()->user()->dept]])->get();
        return $officers;
    }

    public function printTransmittal($data){
        $input = explode('*', $data);
        $transType = $input[1];
        if ($input[1] == "Mine Drill" || $input[1] == "Rock") {
            $transType = "Minedrill";
        }else if ($input[1] == "Bulk" || $input[1] == "Cut"){
            $transType = "Bulk_Cut";
        }

        $pdf = new \setasign\Fpdi\Fpdi('P');
        $streamContext = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);
        $filecontent = file_get_contents(config('app.api_path') . 'Transmittals/Transmittal-'.$transType . '.pdf', false, $streamContext);
        $pagecount = $pdf->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($filecontent));
        $tpl = $pdf->importPage(1);
        
        $transmittal = DeptuserTrans::where("transmittalno", $input[0])->first();
        $items = TransmittalItem::where([['isdeleted', 0], ['transmittalno', $input[0]]])->get();

        if ($input[1] == "Bulk" || $input[1] == "Cut") {  
            $numberOfGroups = ceil(count($items) / 19);
            for ($groupNumber = 1; $groupNumber <= $numberOfGroups; $groupNumber++) {
                $pdf->AddPage();
                $pdf->useTemplate($tpl, 5, 0, 200);
                $pdf->SetFont('Helvetica');
                $pdf->SetFontSize('9');

                $pdf->SetXY(163, 5); $pdf->Cell(50, 10, $input[0], 0, 0, 'L');
                $pdf->SetXY(163, 12.5); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->datesubmitted))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(163, 20.5); $pdf->Cell(50, 10, date('g:i A', strtotime($transmittal->timesubmitted)), 0, 0, 'L');
                $pdf->SetXY(150, 28.5); $pdf->Cell(50, 10, $transmittal->email_address, 0, 0, 'L');

                $pdf->SetFontSize('24');
                $pdf->SetXY(70, 45); $pdf->Cell(80, 10, strtoupper($input[1]) . " SAMPLES", 0, 0, 'C');

                $pdf->SetFontSize('9');
                $pdf->SetXY(35, 52.5); $pdf->Cell(50, 10, $transmittal->purpose, 0, 0, 'L');
                $pdf->SetXY(40, 59); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->date_needed))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(35, 65); $pdf->Cell(50, 10, $transmittal->priority, 0, 0, 'L');
                $pdf->SetXY(35, 71); $pdf->Cell(50, 10, $transmittal->source, 0, 0, 'L');

                $i = 1; $yy = 90.4;
                $startIndex = ($groupNumber - 1) * 19; // Calculate the starting index for this group

                for ($j = $startIndex; $j < min($startIndex + 19, count($items)); $j++) {
                    $item = $items[$j];

                    $pdf->SetXY(50.6, $yy); $pdf->Cell(16.2, 7.1, $i, 0, 0, 'C');
                    $pdf->SetXY(67.1, $yy); $pdf->Cell(32, 7.1, $item->sampleno, 0, 0, 'C');
                    $pdf->SetXY(99, $yy); $pdf->Cell(36.2, 7.1, $item->elements, 0, 0, 'C');
                    $pdf->SetXY(135, $yy); $pdf->Cell(32, 7.1, $item->comments, 0, 0, 'C');
                    $yy += 7.1;
                    $i++;
                }

                $pdf->SetXY(135, 225); $pdf->Cell(32, 7, $i - 1, 0, 0, 'C');
                $pdf->SetXY(40, 246); $pdf->Cell(50, 7, (User::where("username", $transmittal->created_by)->first())->name, 0, 0, 'L');

                if ($input[2] == "2") {
                    $pdf->SetXY(15.5, 252); $pdf->Cell(50, 7, "Approved by:      " . (User::where("username", $transmittal->approver)->first())->name, 0, 0, 'L');
                }
            }
        }

        if($input[1] == "Mine Drill" || $input[1] == "Rock"){
            $numberOfGroups = ceil(count($items) / 19);
            for ($groupNumber = 1; $groupNumber <= $numberOfGroups; $groupNumber++) {
                $pdf->AddPage();
                $pdf->useTemplate($tpl, 5, 0, 200);
                $pdf->SetFont('Helvetica');
                $pdf->SetFontSize('9');

                $pdf->SetXY(163, 5); $pdf->Cell(50, 10, $input[0], 0, 0, 'L');
                $pdf->SetXY(163, 12.5); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->datesubmitted))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(163, 20.5); $pdf->Cell(50, 10, date('g:i A', strtotime($transmittal->timesubmitted)), 0, 0, 'L');
                $pdf->SetXY(150, 28.5); $pdf->Cell(50, 10, $transmittal->email_address, 0, 0, 'L');
    
                $pdf->SetFontSize('24');
                $pdf->SetXY(70, 45); $pdf->Cell(80, 10, ($input[1] == "Rock" ? "ROCK DRILL" : strtoupper($input[1]))." SAMPLES", 0, 0, 'C');
    
                $pdf->SetFontSize('9');
                $pdf->SetXY(35, 52.5); $pdf->Cell(50, 10, $transmittal->purpose, 0, 0, 'L');
                $pdf->SetXY(40, 59); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->date_needed))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(35, 65); $pdf->Cell(50, 10, $transmittal->priority, 0, 0, 'L');
                $pdf->SetXY(35, 71); $pdf->Cell(50, 10, $transmittal->source, 0, 0, 'L');

                $yy = 90.3; $i = 1;
                $startIndex = ($groupNumber - 1) * 19; // Calculate the starting index for this group

                for ($j = $startIndex; $j < min($startIndex + 19, count($items)); $j++) {
                    $item = $items[$j];

                    $pdf->SetXY(15.6, $yy); $pdf->Cell(16.2, 7.1, $i, 0, 0, 'C');
                    $pdf->SetXY(31.8, $yy); $pdf->Cell(32, 7.1, $item->sampleno, 0, 0, 'C');
                    $pdf->SetXY(63.8, $yy); $pdf->Cell(30, 7.1, $item->description, 0, 0, 'C');
                    $pdf->SetXY(93.8, $yy); $pdf->Cell(36, 7.1, $item->elements, 0, 0, 'C');
                    $pdf->SetXY(129.8, $yy); $pdf->Cell(33.5, 7.1, $item->methodcode, 0, 0, 'C');
                    $pdf->SetXY(163, $yy); $pdf->Cell(32, 7.1, $item->comments, 0, 0, 'C');
                    $yy+=7.1; $i++;
                }

                $pdf->SetXY(163, 225); $pdf->Cell(32, 7, $i - 1, 0, 0, 'C');
                $pdf->SetXY(40, 246); $pdf->Cell(50, 7, (User::where("username", $transmittal->created_by)->first())->name, 0, 0, 'L');

                if($input[2] == "2"){
                    $pdf->SetXY(15.5, 252); $pdf->Cell(50, 7, "Approved by:      ".(User::where("username", $transmittal->approver)->first())->name, 0, 0, 'L'); 
                }
            }
        }

        if ($input[1] == "Carbon") {
            $numberOfGroups = ceil(count($items) / 22);
            for ($groupNumber = 1; $groupNumber <= $numberOfGroups; $groupNumber++) {
                $pdf->AddPage();
                $pdf->useTemplate($tpl, 5, 0, 200);
                $pdf->SetFont('Helvetica');
                $pdf->SetFontSize('9');

                $pdf->SetXY(163, 6); $pdf->Cell(50, 10, $input[0], 0, 0, 'L');
                $pdf->SetXY(173, 13); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->datesubmitted))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(173, 20.5); $pdf->Cell(50, 10, date('g:i A', strtotime($transmittal->timesubmitted)), 0, 0, 'L');
                $pdf->SetXY(160, 28); $pdf->Cell(50, 10, $transmittal->email_address, 0, 0, 'L');

                $pdf->SetXY(34, 56.5); $pdf->Cell(50, 10, $transmittal->purpose, 0, 0, 'L');
                $pdf->SetXY(42, 62.5); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->date_needed))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(34, 68.5); $pdf->Cell(50, 10, $transmittal->priority, 0, 0, 'L');
                $pdf->SetXY(42, 74.5); $pdf->Cell(50, 10, $input[0], 0, 0, 'L');


                $yy = 94.4; $i = 1;
                $startIndex = ($groupNumber - 1) * 22; // Calculate the starting index for this group

                for ($j = $startIndex; $j < min($startIndex + 22, count($items)); $j++) {
                    $item = $items[$j];
                    
                    $pdf->SetXY(32, $yy); $pdf->Cell(16.2, 7.1, $i, 0, 0, 'C');
                    $pdf->SetXY(48.2, $yy); $pdf->Cell(32, 7.1, $item->sampleno, 0, 0, 'C');
                    $pdf->SetXY(80.2, $yy); $pdf->Cell(36.2, 7.1, $item->elements, 0, 0, 'C');
                    $pdf->SetXY(116.6, $yy); $pdf->Cell(27, 7.1, $item->methodcode, 0, 0, 'C');
                    $pdf->SetXY(143.6, $yy); $pdf->Cell(38, 7.1, $item->comments, 0, 0, 'C');
                    $yy+=7.1; $i++;
                }

                $pdf->SetXY(143.6, 250); $pdf->Cell(38, 7, $i - 1, 0, 0, 'C');
                $pdf->SetXY(15, 257); $pdf->Cell(50, 7, "Prepared by:      ".(User::where("username", $transmittal->created_by)->first())->name, 0, 0, 'L');

                if($input[2] == "2"){
                    $pdf->SetXY(15, 260); $pdf->Cell(50, 7, "Approved by:      ".(User::where("username", $transmittal->approver)->first())->name, 0, 0, 'L'); 
                }
            }
        }
        
        $pdf->Output('' , $input[0]."_".$input[1] .'.pdf',false);
    }
}
