<?php

namespace App\Http\Controllers;

use App\Models\DeptuserTrans;
use App\Models\TransmittalItem;
use App\Models\Worksheet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Services\AccessRightService;
use Illuminate\Support\Facades\Redirect;

class AnalystController extends Controller
{
    protected $accessRightService;
    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst Worksheets");

        if (!$rolesPermissions['view']) {
            abort(401);
        }

        return view('analyst.index');
    }
    public function view($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst Worksheets");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $worksheet = Worksheet::where('id', $id)->first();
        return view('analyst.view', compact('worksheet'));
    }

    public function edit($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst Worksheets");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $worksheet = Worksheet::where('id', $id)->first();
        return view('analyst.edit', compact('worksheet'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'id'    => 'required',
            'dateweighed'    => 'required',
            'shiftweighed'    => 'required',
            'micnocheckweights'    => 'required',
            'measuredby'    => 'required',
            'analyzedby'    => 'required',
        ]);
        try {
            $worksheet = Worksheet::find($request->id);
            $data = [
                'dateweighed' => $request->dateweighed,
                'shiftweighed' => $request->shiftweighed,
                'micnocheckweights' => $request->micnocheckweights,
                'measuredby' => $request->measuredby,
                'analyzedby' => $request->analyzedby,
                'isAnalyzed' => 1
            ];

            $worksheet->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function transmittal()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst Transmittals");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('analyst.transmittal');
    }
    public function getTransmittal(Request $request)
    {
        $currentMonth = Carbon::now()->month;

        $dateFrom = $request->dateFrom ?? Carbon::createFromDate(null, $currentMonth, 1)->toDateString();
        $dateTo = $request->dateTo ?? Carbon::createFromDate(null, $currentMonth, Carbon::createFromDate(null, $currentMonth, 1)->daysInMonth)->toDateString();

        $transmittal = DeptuserTrans::where([['isdeleted', 0], ['status', 'Approved'], ['transType', 'Solutions']])
            ->whereBetween('datesubmitted', [$dateFrom, $dateTo])
            ->orderBy('transmittalno', 'asc')->get();

        return $transmittal;
    }

    public function viewTransmittal($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst Transmittals");

        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('analyst.view_transmittal', compact('transmittal'));
    }
    public function receive($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst Transmittals");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('analyst.receive', compact('transmittal'));
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
    public function editTransmittal($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Analyst Transmittals");

        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('analyst.edit_transmittal', compact('transmittal'));
    }
    public function getItems(Request $request)
    {
        $items = TransmittalItem::where([['isdeleted', 0], ['transmittalno', $request->transmittalno], ['isAssayed', 0]])->get();
        return  $items;
    }
    public function reassay(Request $request)
    {
        $request->validate(['id' => 'required']);
        try {
            $item = TransmittalItem::find($request->id);
            $data = [
                //'labbatch' => NULL,
                'reassayed' => 1,
                'reaasyedby' => auth()->user()->username,
                //'isAssayed' => 0,
                //'assayedby' => NULL,

            ];
            $item->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage(), 500]);
        }
    }
    public function AnalyticalResult($data)
    {
        $input = explode('*', $data);
        $transType = $input[2];
        //dd($data);
        if ($input[2] == "Mine Drill") {
            $transType = "Minedrill";
        };
        $pdf = new \setasign\Fpdi\Fpdi('P');
        $streamContext = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);
        $filecontent = file_get_contents(config('app.api_path') . $transType . '.pdf', false, $streamContext);
        $pagecount = $pdf->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($filecontent));
        $tpl = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->useTemplate($tpl, 5, 0, 200);
        $pdf->SetFont('Helvetica');
        $pdf->SetFontSize('6');
        
        //ROCK
        if ($transType == "Rock") {
            $pdf->SetXY(94, 247);
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L');

            $pdf->SetXY(162, 247);
            $pdf->Cell(50, 10, $input[1], 0, 0, 'L');

            $items = TransmittalItem::where('labbatch', $input[3])->orderBy('sampleno')->get();
            $yy = 67; $i = 1;

            foreach($items as $item){
                $pdf->SetXY(25, $yy); $pdf->Cell(50, 10, $i, 0, 0, 'L');
                $pdf->SetXY(50, $yy); $pdf->Cell(50, 10, $item->sampleno, 0, 0, 'L');
                $yy+=4.85; $i++;
            }

        // BULK
        } else if ($transType == "Bulk") {
            $pdf->SetXY(89, 249);
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L');

            $pdf->SetXY(156, 249);
            $pdf->Cell(50, 10, $input[1], 0, 0, 'L');

            $pdf->SetXY(89, 35);
            $pdf->Cell(50, 10, $input[3], 0, 0, 'L');

            $items = TransmittalItem::where('labbatch', $input[3])->orderBy('sampleno')->get();
            $yy = 70; $i = 1;

            foreach($items as $item){
                $pdf->SetXY(10, $yy); $pdf->Cell(50, 10, $i, 0, 0, 'L');
                $pdf->SetXY(30, $yy); $pdf->Cell(50, 10, 'LEVEL 5', 0, 0, 'L');
                $pdf->SetXY(50, $yy); $pdf->Cell(50, 10, $item->sampleno, 0, 0, 'L');
                $pdf->SetXY(76, $yy); $pdf->Cell(50, 10, $item->augradegpt, 0, 0, 'L');
                $yy+=5; $i++;
            }

        } else if ($transType == "Carbon") {
            $pdf->SetXY(44, 226);
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L');

            $pdf->SetXY(139, 226);
            $pdf->Cell(50, 10, $input[1], 0, 0, 'L');

            $items = TransmittalItem::where('labbatch', $input[3])->get();
            $yy = 94; $i = 1;

            foreach($items as $item){
                $pdf->SetXY(20, $yy); $pdf->Cell(50, 10, $i, 0, 0, 'L');
                $pdf->SetXY(27, $yy); $pdf->Cell(50, 10, $item->sampleno, 0, 0, 'L');
                $pdf->SetXY(138, $yy); $pdf->Cell(50, 10, $item->initialaggpt, 0, 0, 'L');
                $pdf->SetXY(162, $yy); $pdf->Cell(50, 10, $item->agdoremg, 0, 0, 'L');
                $yy+=4.3; $i++;
            }

        } else if ($transType == "Minedrill") {
            $pdf->SetXY(94, 247); 
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L'); 

            $pdf->SetXY(162, 247); 
            $pdf->Cell(50, 10, $input[1], 0, 0, 'L');

            $items = TransmittalItem::where('labbatch', $input[3])->orderBy('sampleno')->get();
            $yy = 67; $i = 1;

            foreach($items as $item){
                $pdf->SetXY(25, $yy); $pdf->Cell(50, 10, $i, 0, 0, 'L');
                $pdf->SetXY(50, $yy); $pdf->Cell(50, 10, $item->sampleno, 0, 0, 'L');
                $yy+=4.85; $i++;
            }

        } else if ($transType == "Solids") {
            $pdf->SetXY(49, 202); 
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L'); 

            $pdf->SetXY(124, 202); 
            $pdf->Cell(50, 10, $input[1], 0, 0, 'C'); // add the text, align to Center of cell

        } else if ($transType == "Solutions") {
            $pdf->SetXY(51, 206); // set the position of the box
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L'); // add the text, align to Center of cell

            $pdf->SetXY(114, 206); // set the position of the box
            $pdf->Cell(50, 10, $input[1], 0, 0, 'C'); // add the text, align to Center of cell

        } else {
            $pdf->SetXY(89, 249); // set the position of the box
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L'); // add the text, align to Center of cell

            $pdf->SetXY(137, 249); // set the position of the box
            $pdf->Cell(50, 10, $input[1], 0, 0, 'C'); // add the text, align to Center of cell
        }

        
        $pdf->Output('' , $input[3]."_".$transType .'.pdf',false);

    }
}
