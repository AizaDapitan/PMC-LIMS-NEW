<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Worksheet;
use App\Models\FireAssayer;
use App\Models\Transmittal;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DeptuserTrans;
use App\Models\TransmittalItem;
use App\Services\AccessRightService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AssayerController extends Controller
{
    protected $accessRightService;
    public function __construct(
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
    }
    public function Index()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Assayer Transmittals");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('assayer.index');
    }
    public function getTransmittal(Request $request)
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

        $transmittals = DeptuserTrans::where([['isdeleted', 0], ['isReceived', 1],['transType','<>','Solutions']])
            ->whereBetween('datesubmitted', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')->get();
        $transnos = [];
        foreach ($transmittals as $transmittal) {
            $count = 0;
            $count = TransmittalItem::where([['isdeleted', 0], ['transmittalno', $transmittal->transmittalno], ['isAssayed', 0]])->count();

            if ($count > 0) {
                $transno = $transmittal->transmittalno;
                array_push($transnos, $transmittal->transmittalno);
            }
        }
        $transmittals = $transmittals->whereIn('transmittalno', $transnos)->values();

        return $transmittals;
    }
    public function view($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Assayer Transmittals");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $transmittal = DeptuserTrans::where('id', $id)->first();
        return view('assayer.view', compact('transmittal'));
    }
    public function create($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Assayer Worksheets");
        if (!$rolesPermissions['create']) {
            abort(401);
        }
        $transids = $id;
        $ids = explode(',', $transids);
        $transmittal = DeptuserTrans::whereIn('transmittalno', $ids)->first();
        return view('assayer.create', compact('transids', 'transmittal'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'labbatch' => 'required',
            'dateshift' => 'required',
            'timeshift' => 'required',
            'fusionfurno' => 'required',
            'fusiontimefrom' => 'required',
            'fusiontimeto' => 'required',
            'fusion' => 'required',
            'cupellationfurno' => 'required',
            'cupellationtimefrom' => 'required',
            'cupellationtimeto' => 'required',
            'cupellation' => 'required',
            'temperature' => 'required',
            'moldused' => 'required',
            'fireassayer' => 'required',
            'transType' => 'required'
        ]);
        try {
            $data = [
                'labbatch' => $request->labbatch,
                'dateshift' => $request->dateshift,
                'timeshift' => $request->timeshift,
                'fusionfurno' => $request->fusionfurno,
                'fusiontimefrom' => $request->fusiontimefrom,
                'fusiontimeto' => $request->fusiontimeto,
                'fusion' => $request->fusion,
                'cupellationfurno' => $request->cupellationfurno,
                'cupellationtimefrom' => $request->cupellationtimefrom,
                'cupellationtimeto' => $request->cupellationtimeto,
                'cupellation' => $request->cupellation,
                'temperature' => $request->temperature,
                'moldused' => $request->moldused,
                'fireassayer' => $request->fireassayer,
                'transType' => $request->transType,
                'createdby' => auth()->user()->username
            ];

            Worksheet::create($data);
            $transids = explode(',', $request->ids);
            // $itemsId = TransmittalItem::whereIn('transmittalno', $transids)->where('reassayed', 0)->get('id')->toArray();
            $itemdata = [
                'labbatch' => $request->labbatch,
                'isAssayed' => 1,
                'reassayed' => 0,
                'assayedby' => auth()->user()->username,
                'assayed_at' => Carbon::now(),
            ];
            $itemIds = explode(",", $request->itemIds);
            TransmittalItem::whereIn('id', $itemIds)->update($itemdata);
            // DeptuserTrans::whereIn('transmittalno', $transids)->update(['isAssayed' => 1, 'assayedby' =>  auth()->user()->username]);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function getItems(Request $request)
    {
        $labbatch = $request->labbatch;
        if ($labbatch == "") {
            $labbatch = "0";
        }

        $transids = explode(',', $request->ids);
        // $items = TransmittalItem::whereIn('transmittalno', $transids)->where(function($q){
        //     $q->where( 'isAssayed', 0)->orWhere('reAssayed',1);
        // })->Orwhere('labbatch', $labbatch)->get();
        $items = TransmittalItem::whereIn('transmittalno', $transids)->where('isdeleted', '<>', '1')->where( 'isAssayed', 0)->Orwhere('labbatch', $labbatch)->OrderBy('sampleno')->get();
        $items->transform(function ($item) {
            $item->samplewtgrams = intval($item->samplewtgrams);
            $item->fluxg = intval($item->fluxg);
            $item->flourg = intval($item->flourg);
            $item->niterg = intval($item->niterg);
            $item->leadg = intval($item->leadg);
            $item->silicang = intval($item->silicang);
            return $item;
        });

        return  $items;
    }
    public function worksheet()
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Assayer Worksheets");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        return view('assayer.worksheet');
    }
    public function getWorksheet(Request $request)
    {
        $currentMonth = Carbon::now()->month;

        $dateFrom = $request->dateFrom ?? Carbon::createFromDate(null, $currentMonth, 1)->toDateString();
        $dateTo = $request->dateTo ?? Carbon::createFromDate(null, $currentMonth, Carbon::createFromDate(null, $currentMonth, 1)->daysInMonth)->toDateString();

        $worksheet = Worksheet::where('isdeleted', 0)
            ->when(!$request->reqfrom, function ($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('dateshift', [$dateFrom, $dateTo]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $worksheet;
    }

    public function Edit($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Assayer Worksheets");
        if (!$rolesPermissions['edit']) {
            abort(401);
        }
        $worksheet = Worksheet::where('id', $id)->first();
        return view('assayer.edit', compact('worksheet'));
    }
    public function getWorksheetItems(Request $request)
    {
        // $transids = explode(',', $request->ids);
        $items = TransmittalItem::where('labbatch', $request->labbatch)->orderBy('sampleno')->get();
        $items->transform(function ($item) {
            $item->samplewtvolume = intval($item->samplewtvolume);
            $item->samplewtgrams = intval($item->samplewtgrams);
            $item->fluxg = intval($item->fluxg);
            $item->flourg = intval($item->flourg);
            $item->niterg = intval($item->niterg);
            $item->leadg = intval($item->leadg);
            $item->silicang = intval($item->silicang);
            return $item;
        });
        return  $items;
    }
    public function update(Request $request)
    {
        $request->validate([
            'id'    => 'required',
            'labbatch' => 'required',
            'dateshift' => 'required',
            'timeshift' => 'required',
            'fusionfurno' => 'required',
            'fusiontimefrom' => 'required',
            'fusiontimeto' => 'required',
            'fusion' => 'required',
            'cupellationfurno' => 'required',
            'cupellationtimefrom' => 'required',
            'cupellationtimeto' => 'required',
            'cupellation' => 'required',
            'temperature' => 'required',
            'moldused' => 'required',
            'fireassayer' => 'required',
            'transType' => 'required',
        ]);
        try {
            $worksheet = Worksheet::find($request->id);
            $data = [
                'labbatch' => $request->labbatch,
                'dateshift' => $request->dateshift,
                'timeshift' => $request->timeshift,
                'fusionfurno' => $request->fusionfurno,
                'fusiontimefrom' => $request->fusiontimefrom,
                'fusiontimeto' => $request->fusiontimeto,
                'fusion' => $request->fusion,
                'cupellationfurno' => $request->cupellationfurno,
                'cupellationtimefrom' => $request->cupellationtimefrom,
                'cupellationtimeto' => $request->cupellationtimeto,
                'cupellation' => $request->cupellation,
                'temperature' => $request->temperature,
                'moldused' => $request->moldused,
                'fireassayer' => $request->fireassayer,
                'transType' => $request->transType,
                'createdby' => auth()->user()->username
            ];

            $worksheet->update($data);
            TransmittalItem::where('labbatch', $request->labbatch)->update(['reassayed' => 0]);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function viewWorksheet($id)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("Assayer Worksheets");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $worksheet = Worksheet::where('id', $id)->first();
        return view('assayer.viewworksheet', compact('worksheet'));
    }
    public function delete(Request $request)
    {
        $request->validate(['id' => 'required']);
        try {
            $worksheet = Worksheet::find($request->id);

            $data = [
                'isdeleted' => 1,
                'deleted_at' => Carbon::now(),
                'deleteby' => auth()->user()->username,
            ];
            $worksheet->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage(), 500]);
        }
    }
    public function checkBatchNo(Request $request)
    {
        $worksheet = Worksheet::where('labbatch', $request->labbatch)->get();
        return $worksheet;
    }
    public function getItemList(Request $request)
    {
        $trans_nos = DeptuserTrans::where([['isReceived', true], ['isdeleted', 0],['transType',$request->transType],['transType','<>','Solutions']])->get('transmittalno')->toArray();
        $forAssayer = 0;
        $transids = [];
        foreach ($trans_nos as $trans_no) {
            $items = TransmittalItem::where([['transmittalno', $trans_no['transmittalno']], ['isAssayed', 0]])->get();
            if (count($items) > 0) {
                $count = 0;
                foreach ($items as $item) {
                    if ($item->samplewtvolume == null || $item->samplewtvolume == '') {
                        $count += 1;
                    }
                }
                if ($count == 0) {
                    // dd($trans_no["transmittalno"]);
                    array_push($transids, $trans_no["transmittalno"]);
                }
            }
        }
        $labbatch = $request->labbatch;
        $itemsList = TransmittalItem::whereIn('transmittalno', $transids)->where('isAssayed', 0)->OrderBy('sampleno')->get();
        $itemsList = $itemsList->WhereNull('labbatch')->values();
        return  $itemsList;
    }
    public function addSample(Request $request)
    {
        $itemdata = [
            'labbatch' => $request->labbatch,
            'isAssayed' => 1,
            'assayedby' => auth()->user()->username,
            'assayed_at' => Carbon::now(),
        ];
        $itemIds = explode(",", $request->itemIds);
        TransmittalItem::whereIn('id', $itemIds)->update($itemdata);
        return response()->json('success');
    }
    public function excludeSample(Request $request)
    {
        $request->validate(['id' => 'required']);
        try {
            $item = TransmittalItem::find($request->id);
            $data = [
                'labbatch' => NULL,
                'isAssayed' => 0,
                'assayedby' => NULL,
            ];
            $item->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage(), 500]);
        }
    }
    public function duplicateSample(Request $request)
    {
        $request->validate(['id' => 'required']);
        try {
            $item = TransmittalItem::find($request->id);

            TransmittalItem::create([
                'sampleno' => $item->sampleno,
                'samplewtvolume' => $item->samplewtvolume,
                'samplewtgrams' => $item->samplewtgrams,
                'fluxg' => $item->fluxg,
                'flourg' =>  $item->flourg,
                'niterg' => $item->niterg,
                'leadg' => $item->leadg,
                'silicang' => $item->silicang,
                'crusibleused' => $item->crusibleused,
                'labbatch' => $item->labbatch,
                'assayedby' => auth()->user()->username,
                'assayed_at' => Carbon::now(),
                'description' => $item->description,
                'elements' => $item->elements,
                'methodcode' =>  $item->methodcode,
                'transmittalno' => $item->transmittalno,
                'comments' => $item->comments,
                'username' => auth()->user()->username,
                'source'    => $item->source,
                'isDuplicate' => 1
            ]);

            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage(), 500]);
        }
    }

    public function downloadCSV(Request $request){

        $labbatch = $request->labbatch;
        if ($labbatch == "") {
            $labbatch = "0";
        }
    
        $transids = explode(',', $request->ids);
        
        //$items = TransmittalItem::whereIn('transmittalno', $transids)->where('isdeleted', '<>', '1')->where( 'isAssayed', 0)->Orwhere('labbatch', $labbatch)->OrderBy('sampleno')->get();
    
        $items = TransmittalItem::select('id', 'sampleno', 'description', 'source', 'transmittalno', 'samplewtgrams', 'fluxg', 'flourg', 'niterg', 'leadg', 'silicang', 'crusibleused')
            ->whereIn('transmittalno', $transids)
            ->where([['isdeleted', 0],['isAssayed', 0]])
            ->Orwhere('labbatch', $labbatch)
            ->OrderBy('sampleno')->get();
    
        $result = [['Item Id', 'Sample No', 'Description', 'Source', 'Transmittal No', 'Sample Wt. (Grams)', 'Flux (Grams)', 'Flour (Grams)', 'Niter (Grams)', 'Lead (Grams)', 'Silican (Grams)', 'Crusible Used']];
        foreach ($items as $item) {
            $result[] = [
                $item->id,
                $item->sampleno,
                $item->description,
                $item->source,
                $item->transmittalno,
                $item->samplewtgrams,
                $item->fluxg,
                $item->flourg,
                $item->niterg,
                $item->leadg,
                $item->silicang,
                $item->crusibleused
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

            $requiredHeaders = array('Item Id', 'Sample No', 'Description', 'Source', 'Transmittal No', 'Sample Wt. (Grams)', 'Flux (Grams)', 'Flour (Grams)', 'Niter (Grams)', 'Lead (Grams)', 'Silican (Grams)', 'Crusible Used'); //headers we expect

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
                            'samplewtgrams' => $data[5],
                            'fluxg' => $data[6],
                            'flourg' => $data[7],
                            'niterg' => $data[8],
                            'leadg' => $data[9],
                            'silicang' => $data[10],
                            'crusibleused' => $data[11],
                        ],
                        [
                            'id' => 'required',
                            'samplewtgrams' => 'required',
                            'fluxg' => 'required',
                            'flourg' => 'required',
                            'niterg' => 'required',
                            'leadg' => 'required',
                            'silicang' => 'required',
                            'crusibleused' => 'required',
                        ],
                        [
                            'id.required' => 'Uploading Item: ID is required! Check csv file row # ' . $row,
                            'samplewtgrams.required' => 'Uploading Item: Samplewtgrams is required! Check csv file row # ' . $row,
                            'fluxg.required' => 'Uploading Item: Flux (Grams) is required! Check csv file row # ' . $row,
                            'flourg.required' => 'Uploading Item: Flour (Grams) is required! Check csv file row # ' . $row,
                            'niterg.required' => 'Uploading Item: Niter (Grams) is required! Check csv file row # ' . $row,
                            'leadg.required' => 'Uploading Item: Lead (Grams) is required! Check csv file row # ' . $row,
                            'silicang.required' => 'Uploading Item: Silican (Grams) is required! Check csv file row # ' . $row,
                            'crusibleused.required' => 'Uploading Item: Crusible Used is required! Check csv file row # ' . $row,
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
                $samplewtgrams = $item[5];
                $fluxg = $item[6];
                $flourg = $item[7];
                $niterg = $item[8];
                $leadg = $item[9];
                $silicang = $item[10];
                $crusibleused = $item[11];
                
                TransmittalItem::where('id', $id)->update([
                    'samplewtgrams' => $samplewtgrams,
                    'fluxg' => $fluxg,
                    'flourg' => $flourg,
                    'niterg' => $niterg,
                    'leadg' => $leadg,
                    'silicang' => $silicang,
                    'crusibleused' => $crusibleused
                ]);
            }
            //Storage::delete('public/items files/' . $filenametostore);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
}