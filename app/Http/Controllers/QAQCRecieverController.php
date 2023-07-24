<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
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
}
