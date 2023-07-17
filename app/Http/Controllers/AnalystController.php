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
        $input = explode(',', $data);
        $transType = $input[2];
        if ($input[2] == "Mine Drill") {
            $transType = "Minedrill";
        };
        $pdf = new \setasign\Fpdi\Fpdi('P');
        // Reference the PDF you want to use (use relative path)
        // DD(env('APP_URL') . '/template/certificate.pdf');
        $streamContext = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);
        // $filecontent = file_get_contents('https://localhost/camm/api/' . $transType . '.pdf', false, $streamContext);
        $filecontent = file_get_contents(config('app.api_path') . $transType . '.pdf', false, $streamContext);
        $pagecount = $pdf->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($filecontent));
        // $pagecount = $pdf->setSourceFile("https://localhost/camm/api/certificate.pdf");
        // Import the first page from the PDF and add to dynamic PDF
        $tpl = $pdf->importPage(1);
        $pdf->AddPage();
        // Use the imported page as the template
        $pdf->useTemplate($tpl, 5, 0, 200);
        // Set the default font to use
        $pdf->SetFont('Helvetica');
        $pdf->SetFontSize('6'); // set font size
        if ($transType == "Rock") {
            $pdf->SetXY(95, 247); // set the position of the box
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L'); // add the text, align to Center of cell

            $pdf->SetXY(142, 247); // set the position of the box
            $pdf->Cell(50, 10, $input[1], 0, 0, 'C'); // add the text, align to Center of cell

        } else if ($transType == "Bulk") {
            $pdf->SetXY(89, 249); // set the position of the box
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L'); // add the text, align to Center of cell

            $pdf->SetXY(137, 249); // set the position of the box
            $pdf->Cell(50, 10, $input[1], 0, 0, 'C'); // add the text, align to Center of cell
        } else if ($transType == "Carbon") {
            $pdf->SetXY(44, 226); // set the position of the box
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L'); // add the text, align to Center of cell

            $pdf->SetXY(119, 226); // set the position of the box
            $pdf->Cell(50, 10, $input[1], 0, 0, 'C'); // add the text, align to Center of cell
        } else if ($transType == "Minedrill") {
            $pdf->SetXY(94, 247); // set the position of the box
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L'); // add the text, align to Center of cell

            $pdf->SetXY(142, 247); // set the position of the box
            $pdf->Cell(50, 10, $input[1], 0, 0, 'C'); // add the text, align to Center of cell

        } else if ($transType == "Solids") {
            $pdf->SetXY(49, 202); // set the position of the box
            $pdf->Cell(50, 10, $input[0], 0, 0, 'L'); // add the text, align to Center of cell

            $pdf->SetXY(124, 202); // set the position of the box
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
        $pdf->Output('' , $transType .'.pdf',false);
    }
}
