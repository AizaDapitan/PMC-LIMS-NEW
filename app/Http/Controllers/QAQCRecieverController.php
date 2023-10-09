<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DeptuserTrans;
use App\Models\TransmittalItem;
use App\Services\AccessRightService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QAQCRecieverController extends Controller
{
    protected $accessRightService;
    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function Index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Receiving Transmittals");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
       return view('qaqcreceiver.index');
    }
    public function getTransmittal(Request $request)
    {
        // dd( DeptuserTrans::where([['isdeleted', 0],['status','Approved']])->WhereNotIn('transType',['Solid','Solutions'])
        // ->orderBy('transmittalno', 'asc')->toSql());
        $currentMonth = Carbon::now()->month;

        $dateFrom = $request->dateFrom ?? Carbon::createFromDate(null, $currentMonth, 1)->toDateString();
        $dateTo = $request->dateTo ?? Carbon::createFromDate(null, $currentMonth, Carbon::createFromDate(null, $currentMonth, 1)->daysInMonth)->toDateString();

        $transmittal = DeptuserTrans::where([['isdeleted', 0],['status','Approved']])
            ->whereBetween('datesubmitted', [$dateFrom, $dateTo])
            ->WhereNotIn('transType',['Solids','Solutions'])
            ->orderBy('datesubmitted', 'desc')->get();

        $transmittal->transform(function ($item) {
            // Transform the timesubmitted field to 12-hour format (2:56 PM)
            $timestamp = strtotime($item->timesubmitted);
            $item->timesubmitted = date('g:i A', $timestamp);
        
            return $item;
        });

        return $transmittal;
    }
    public function view($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Receiving Transmittals");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('qaqcreceiver.view', compact('transmittal'));
    }
    public function receive($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Receiving Transmittals");
        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('qaqcreceiver.receive', compact('transmittal'));
    }
    public function receiveTransmittal(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        try {
          
            $deptuserTrans = DeptuserTrans::find($request->id);

            $data = [
                'received_date' => Carbon::now(),
                'receiver' => auth()->user()->username,
                'isReceived' =>  true,
            ];
            $deptuserTrans->update($data);

            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Receiving Transmittals");
        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('qaqcreceiver.edit', compact('transmittal'));
    }

    public function update(Request $request){
        $request->validate([
            'shifting_supervisor' => 'required'
        ]);
        try {
          
            $deptuserTrans = DeptuserTrans::find($request->id);

            $data = [
                'supervisor' => $request->shifting_supervisor,
            ];
            $deptuserTrans->update($data);

            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }

    public function getItems(Request $request)
    {
        $items = TransmittalItem::where([['isdeleted', 0], ['transmittalno', $request->transmittalno],['isAssayed',0]])->get();
        return  $items;
    }

    public function downloadCSV(Request $request){

        $items = TransmittalItem::select('id', 'sampleno', 'description', 'elements', 'methodcode', 'samplewtvolume')
            ->where([['isdeleted', 0],['isAssayed', 0],['transmittalno', $request->transmittalno]])->get();

        $result = [['Item Id', 'Sample No', 'Description', 'Elements', 'Method Code', 'Sample Wt./Volume']];
        foreach ($items as $item) {
            $result[] = [
                $item->id,
                $item->sampleno,
                $item->description,
                $item->elements,
                $item->methodcode,
                $item->samplewtvolume
            ];
        }

        $filename = 'csv_' . Str::random(8) . '.csv';
        $filePath = 'template/' . $filename;
        $csvContent = '';

        foreach ($result as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=' . $filename);
    }

    public function uploadItems(Request $request)
    {
        $request->validate(['itemFile' => "required|mimes:csv,txt"]);

        try {

            $filenamewithextension = $request->file('itemFile')->getClientOriginalName();
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

            //get file extension
            $extension = $request->file('itemFile')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename . auth()->user()->id . '.' . $extension;

            $request->file('itemFile')->storeAs(('public/items files/'), $filenametostore);

            $items = [];

            $count = 0;

            $requiredHeaders = array('Item Id', 'Sample No', 'Description', 'Elements', 'Method Code', 'Sample Wt./Volume'); //headers we expect

            if (($open = fopen(storage_path() . "/app/public/items files/" . $filenametostore, "r")) !== FALSE) {

                $firstLine = fgets($open); //get first line of csv file

                $foundHeaders = str_getcsv(trim($firstLine), ',', '"'); //parse to array

                if ($foundHeaders !== $requiredHeaders) {
                    fclose($open);
                    $error =   ['Uploading Item' => ['Headers do not match: '  . implode(', ', $foundHeaders)]];
                    return response()->json(['errors' => $error], 500);
                }
                
                while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                    $count++;
                    if ($count == 0) {
                        continue;
                    }
                    $row = $count;

                    $validator = Validator::make(
                        [
                            'id' => $data[0],
                            'samplewtvolume' => $data[5],
                        ],
                        [
                            'id' => 'required',
                            'samplewtvolume' => 'required',
                        ],
                        [
                            'id.required' => 'Uploading Item: ID is required! Check csv file row # ' . $row,
                            'samplewtvolume.required' => 'Uploading Item: Samplewtvolume is required! Check csv file row # ' . $row,
                            
                        ]
                    );
                    // dd($validator->errors());
                    if ($validator->fails()) {
                        fclose($open);
                        return response()->json(['errors' => $validator->errors()], 500);
                    }
                    $items[] = $data;
                    
                }

                fclose($open);
            }
            foreach ($items as $item) {
                $id = $item[0];
                $samplewtvolume = $item[5];
                
                TransmittalItem::where('id', $id)->update(['samplewtvolume' => $samplewtvolume]);
            }
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
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
        $filecontent = file_get_contents(config('app.api_path') . 'Transmittals/QAQCReceiving-'.$transType . '.pdf', false, $streamContext);
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
                $pdf->SetXY(163, 7.5); $pdf->Cell(50, 10, $input[0], 0, 0, 'L');
                $pdf->SetXY(163, 15); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->datesubmitted))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(163, 22); $pdf->Cell(50, 10, date('g:i A', strtotime($transmittal->timesubmitted)), 0, 0, 'L');
                $pdf->SetXY(150, 29.5); $pdf->Cell(50, 10, $transmittal->email_address, 0, 0, 'L');
                $pdf->SetFontSize('24');
                $pdf->SetXY(66, 46); $pdf->Cell(80, 10, strtoupper($input[1])." SAMPLES", 0, 0, 'C');
                $pdf->SetFontSize('9');
                $pdf->SetXY(34, 53.6); $pdf->Cell(50, 10, $transmittal->purpose, 0, 0, 'L');
                $pdf->SetXY(39, 59.5); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->date_needed))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(34, 65.5); $pdf->Cell(50, 10, $transmittal->priority, 0, 0, 'L');
                $pdf->SetXY(34, 72); $pdf->Cell(50, 10, $transmittal->source, 0, 0, 'L');

                $yy = 91.7; $i = 1;

                $startIndex = ($groupNumber - 1) * 19; // Calculate the starting index for this group
                for ($j = $startIndex; $j < min($startIndex + 19, count($items)); $j++) {
                    $item = $items[$j];

                    $pdf->SetXY(45, $yy); $pdf->Cell(16.2, 7.1, $i, 0, 0, 'C');
                    $pdf->SetXY(55, $yy); $pdf->Cell(32, 7.1, $item->sampleno, 0, 0, 'C');
                    $pdf->SetXY(83, $yy); $pdf->Cell(26, 7.1, intval($item->samplewtvolume), 0, 0, 'C');
                    $pdf->SetXY(107, $yy); $pdf->Cell(36.2, 7.1, $item->elements, 0, 0, 'C');
                    $pdf->SetXY(137, $yy); $pdf->Cell(32, 7, $item->comments, 0, 0, 'C');
                    $yy+=7.1; $i++;
                }

                $pdf->SetXY(137, 226); $pdf->Cell(32, 7, $i - 1, 0, 0, 'C');
                $pdf->SetXY(38, 242); $pdf->Cell(50, 7, (User::where("username", $transmittal->created_by)->first())->name, 0, 0, 'L');
                $pdf->SetXY(38, 248); $pdf->Cell(50, 7, (User::where("username", $transmittal->approver)->first())->name, 0, 0, 'L'); 
                $pdf->SetXY(146, 238.6); $pdf->Cell(50, 7, (User::where("username", $transmittal->receiver)->first())->name, 0, 0, 'L');
                $pdf->SetXY(157, 244.6); $pdf->Cell(50, 7, $transmittal->supervisor, 0, 0, 'L');
                $pdf->SetXY(165, 250.9); $pdf->Cell(50, 7, (Carbon::parse($transmittal->received_date))->format('F j, Y g:i A'), 0, 0, 'L');
            }
        }
        if($input[1] == "Mine Drill" || $input[1] == "Rock"){
            $numberOfGroups = ceil(count($items) / 19);
            for ($groupNumber = 1; $groupNumber <= $numberOfGroups; $groupNumber++) {
                $pdf->AddPage();
                $pdf->useTemplate($tpl, 5, 0, 200);
                $pdf->SetFont('Helvetica');
                $pdf->SetFontSize('9');
                $pdf->SetXY(163, 7.8); $pdf->Cell(50, 10, $input[0], 0, 0, 'L');
                $pdf->SetXY(163, 14.8); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->datesubmitted))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(163, 22.5); $pdf->Cell(50, 10, date('g:i A', strtotime($transmittal->timesubmitted)), 0, 0, 'L');
                $pdf->SetXY(150, 29.6); $pdf->Cell(50, 10, $transmittal->email_address, 0, 0, 'L');

                $pdf->SetFontSize('24');
                $pdf->SetXY(70, 46); $pdf->Cell(80, 10, ($input[1] == "Rock" ? "ROCK DRILL" : strtoupper($input[1]))." SAMPLES", 0, 0, 'C');

                $pdf->SetFontSize('9');
                $pdf->SetXY(35, 53); $pdf->Cell(50, 10, $transmittal->purpose, 0, 0, 'L');
                $pdf->SetXY(40, 59.5); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->date_needed))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(35, 65.5); $pdf->Cell(50, 10, $transmittal->priority, 0, 0, 'L');
                $pdf->SetXY(35, 71.5); $pdf->Cell(50, 10, $transmittal->source, 0, 0, 'L');
            
                $yy = 91; $i = 1;

                $startIndex = ($groupNumber - 1) * 19; // Calculate the starting index for this group
                for ($j = $startIndex; $j < min($startIndex + 19, count($items)); $j++) {
                    $item = $items[$j];
                    $pdf->SetXY(15.6, $yy); $pdf->Cell(16.2, 7.1, $i, 0, 0, 'C');
                    $pdf->SetXY(31.8, $yy); $pdf->Cell(22, 7.1, $item->sampleno, 0, 0, 'C');
                    $pdf->SetXY(53.8, $yy); $pdf->Cell(27, 7.1, intval($item->samplewtvolume), 0, 0, 'C');
                    $pdf->SetXY(80.8, $yy); $pdf->Cell(30, 7.1, $item->description, 0, 0, 'C');
                    $pdf->SetXY(110.8, $yy); $pdf->Cell(32, 7.1, $item->elements, 0, 0, 'C');
                    $pdf->SetXY(142.8, $yy); $pdf->Cell(26.5, 7.1, $item->methodcode, 0, 0, 'C');
                    $pdf->SetXY(169, $yy); $pdf->Cell(25, 7.1, $item->comments, 0, 0, 'C');
                    $yy+=7.1; $i++;
                }

                $pdf->SetXY(169, 225.5); $pdf->Cell(25, 7.1, $i - 1, 0, 0, 'C');
                $pdf->SetXY(40, 241.5); $pdf->Cell(50, 7, (User::where("username", $transmittal->created_by)->first())->name, 0, 0, 'L');
                $pdf->SetXY(40, 248); $pdf->Cell(50, 7, (User::where("username", $transmittal->approver)->first())->name, 0, 0, 'L');
                $pdf->SetXY(146, 238.6); $pdf->Cell(50, 7, (User::where("username", $transmittal->receiver)->first())->name, 0, 0, 'L');
                $pdf->SetXY(157, 244.6); $pdf->Cell(50, 7, $transmittal->supervisor, 0, 0, 'L');
                $pdf->SetXY(165, 250.9); $pdf->Cell(50, 7, (Carbon::parse($transmittal->received_date))->format('F j, Y g:i A'), 0, 0, 'L');
            }
        }

        if($input[1] == "Carbon"){
            $numberOfGroups = ceil(count($items) / 22);
            for ($groupNumber = 1; $groupNumber <= $numberOfGroups; $groupNumber++) {
                $pdf->AddPage();
                $pdf->useTemplate($tpl, 5, 0, 200);
                $pdf->SetFont('Helvetica');
                $pdf->SetFontSize('9');
                $pdf->SetXY(163, 8); $pdf->Cell(50, 10, $input[0], 0, 0, 'L');
                $pdf->SetXY(173, 15); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->datesubmitted))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(173, 22); $pdf->Cell(50, 10, date('g:i A', strtotime($transmittal->timesubmitted)), 0, 0, 'L');
                $pdf->SetXY(160, 30); $pdf->Cell(50, 10, $transmittal->email_address, 0, 0, 'L');

                $pdf->SetXY(30, 58.5); $pdf->Cell(50, 10, $transmittal->purpose, 0, 0, 'L');
                $pdf->SetXY(40, 64.5); $pdf->Cell(50, 10, (Carbon::createFromFormat('Y-m-d', $transmittal->date_needed))->format('F j, Y'), 0, 0, 'L');
                $pdf->SetXY(30, 70.5); $pdf->Cell(50, 10, $transmittal->priority, 0, 0, 'L');
                $pdf->SetXY(40, 76.5); $pdf->Cell(50, 10, $input[0], 0, 0, 'L');

                $yy = 96.2; $i = 1;

                $startIndex = ($groupNumber - 1) * 22; // Calculate the starting index for this group
                for ($j = $startIndex; $j < min($startIndex + 22, count($items)); $j++) {
                    $item = $items[$j];
                    $pdf->SetXY(25, $yy); $pdf->Cell(14, 7.1, $i, 0, 0, 'C');
                    $pdf->SetXY(39, $yy); $pdf->Cell(28.2, 7.1, $item->sampleno, 0, 0, 'C');
                    $pdf->SetXY(67.2, $yy); $pdf->Cell(26.7, 7.1, intval($item->samplewtvolume), 0, 0, 'C');
                    $pdf->SetXY(93.9, $yy); $pdf->Cell(32, 7.1, $item->elements, 0, 0, 'C');
                    $pdf->SetXY(126, $yy); $pdf->Cell(23.2, 7.1, $item->methodcode, 0, 0, 'C');
                    $pdf->SetXY(149.2, $yy); $pdf->Cell(34, 7.1, $item->comments, 0, 0, 'C');
                    $yy+=7.1; $i++;
                }

                $pdf->SetXY(149.2, 252); $pdf->Cell(34, 7, $i - 1, 0, 0, 'C');
                $pdf->SetXY(37, 258); $pdf->Cell(50, 7, (User::where("username", $transmittal->created_by)->first())->name, 0, 0, 'L');
                $pdf->SetXY(37, 264); $pdf->Cell(50, 7, (User::where("username", $transmittal->approver)->first())->name, 0, 0, 'L');
            } 
        }

        $pdf->Output('' ,"QAQC-".$input[0]."_".$input[1] .'.pdf',false);
    }
}
