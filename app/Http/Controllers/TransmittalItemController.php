<?php

namespace App\Http\Controllers;

use App\Models\TransmittalItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransmittalItemController extends Controller
{
    public function uploadItems(Request $request)
    {
        $request->validate(['itemFile' => "required|mimes:csv,txt"]);

        try {
            $item = TransmittalItem::where('transmittalno', $request->transmittalno);

            $data = [
                'isdeleted' => 1,
                'deleted_at' => Carbon::now()
            ];
            $item->update($data);

            $filenamewithextension = $request->file('itemFile')->getClientOriginalName();

            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

            //get file extension
            $extension = $request->file('itemFile')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename . '_' . $request->transmittalno . '_' . auth()->user()->id . '.' . $extension;

            $request->file('itemFile')->storeAs(('public/items files/'), $filenametostore);

            $items = [];

            $count = 0;

            $requiredHeaders = array('Sample No', 'description', 'elements', 'method code', 'comments'); //headers we expect

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
                            'sampleno' => $data[0],
                            'description' => $data[1],
                            'elements' => $data[2],
                            'methodcode' => $data[3],
                        ],
                        [
                            'sampleno' => 'required',
                            'description' => 'required',
                            'elements' => 'required',
                            'methodcode' => 'required',
                        ],
                        [
                            'sampleno.required' => 'Uploading Item: sampleno is required! Check csv file row # ' . $row,
                            'description.required' => 'Uploading Item: description is required! Check csv file row # ' . $row,
                            'elements.required' => 'Uploading Item: elements is required! Check csv file row # ' . $row,
                            'methodcode.required' => 'Uploading Item: methodcode is required! Check csv file row # ' . $row,
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
                TransmittalItem::create([
                    'sampleno' => $item[0],
                    'description' => $item[1],
                    'elements' => $item[2],
                    'methodcode' =>  $item[3],
                    'comments'    => $item[4],
                    'transmittalno' => $request->transmittalno,
                    'username' => auth()->user()->username,
                ]);
            }
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['errors' =>  $e->getMessage()], 500);
        }
    }
    public function getItems(Request $request)
    {
        $items = [];
        if($request->reqfrom === "view_assayer"){
            $items = TransmittalItem::where([['isdeleted', 0], ['isAssayed', 0], ['transmittalno', $request->transmittalno]])->get();
        }else{
            $items = TransmittalItem::where([['isdeleted', 0], ['transmittalno', $request->transmittalno]])->get();
        }
        
        /*$items->transform(function ($item) {
            $item->samplewtvolume = intval($item->samplewtvolume);
            return $item;
        });*/
        
        return  $items;
    }
    public function store(Request $request)
    {
        if ($request->isAssayer) {
            $request->validate([
                'sampleno' => 'required',
                'samplewtgrams' => 'required',
                'fluxg' => 'required',
                'flourg' => 'required',
                'niterg' => 'required',
                'leadg' => 'required',
                'silicang' => 'required',
                'crusibleused' => 'required'
            ]);
        } else {
            $request->validate([
                'sampleno' => 'required',
                'description' => 'required',
                'elements' => 'required',
                'methodcode' => 'required',
                'transmittalno' => 'required'
            ]);
        }
        try {
            if ($request->isAssayer) {
                TransmittalItem::create([
                    'sampleno' => $request->sampleno,
                    'samplewtgrams' => $request->samplewtgrams,
                    'fluxg' => $request->fluxg,
                    'flourg' =>  $request->flourg,
                    'niterg' => $request->niterg,
                    'leadg' => $request->leadg,
                    'silicang' => $request->silicang,
                    'crusibleused' => $request->crusibleused,
                    'labbatch' => $request->labbatch,
                    'assayedby' => auth()->user()->username,
                    'assayed_at' => Carbon::now(),
                ]);
            } else {
                TransmittalItem::create([
                    'sampleno' => $request->sampleno,
                    'description' => $request->description,
                    'elements' => $request->elements,
                    'methodcode' =>  $request->methodcode,
                    'transmittalno' => $request->transmittalno,
                    'comments' => $request->comments,
                    'username' => auth()->user()->username,
                    'source'    => $request->source,
                ]);
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
            $item = TransmittalItem::find($request->id);

            $data = [
                'isdeleted' => 1,
                'deleted_at' => Carbon::now(),
                'deletedby' => auth()->user()->username
            ];
            $item->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 500]);
        }
    }
    public function update(Request $request)
    {

        if ($request->isAssayer) {
            $request->validate([
                'id' => 'required',
                'sampleno' => 'required',
                'samplewtgrams' => 'required',
                'fluxg' => 'required',
                'flourg' => 'required',
                'niterg' => 'required',
                'leadg' => 'required',
                'silicang' => 'required',
                'crusibleused' => 'required'
            ]);
        } else if ($request->isAnalyst) {
            $request->validate([
                'id' => 'required',
                'auprillmg' => 'required',
                'augradegpt' => 'required',
                'assreadingppm' => 'required',
                'agdoremg' => 'required',
                'initialaggpt' => 'required',
                'crusibleclearance' => 'required',
                'inquartmg' => 'required',
                'methodremarks' => 'required'
            ]);
        } else if ($request->receiving){
            $request->validate([
                'id' => 'required',
                'sampleno' => 'required',
                'description' => 'required',
                'elements' => 'required',
                'methodcode' => 'required',
                'transmittalno' => 'required',
                'samplewtvolume' => 'required',
            ]);
        }else{
            $request->validate([
                'id' => 'required',
                'sampleno' => 'required',
                'description' => 'required',
                'elements' => 'required',
                'methodcode' => 'required',
                'transmittalno' => 'required',
            ]);
        }
        try {
            $item = TransmittalItem::find($request->id);
            $receiver = "";
            if ($request->receiving) {
                $receiver = auth()->user()->username;
            }
            if ($request->isAssayer) {
                $data = [
                    'sampleno' => $request->sampleno,
                    'samplewtgrams' => $request->samplewtgrams,
                    'fluxg' => $request->fluxg,
                    'flourg' => $request->flourg,
                    'niterg' =>  $request->niterg,
                    'leadg' => $request->leadg,
                    'silicang' => $request->silicang,
                    'crusibleused'    => $request->crusibleused,
                    'updatedby' => auth()->user()->username,
                    'assayedby'  =>  auth()->user()->username,
                    'assayed_at' => Carbon::now(),
                ];
            } else  if ($request->isAnalyst) {
                $data = [
                    'auprillmg' => $request->auprillmg,
                    'augradegpt' => $request->augradegpt,
                    'assreadingppm' => $request->assreadingppm,
                    'agdoremg' => $request->agdoremg,
                    'initialaggpt' =>  $request->initialaggpt,
                    'crusibleclearance' => $request->crusibleclearance,
                    'inquartmg' => $request->inquartmg,
                    'methodremarks'    => $request->methodremarks,
                    'updatedby' => auth()->user()->username
                ];
            } else if ($request->receiving) {
                $data = [
                    'sampleno' => $request->sampleno,
                    'description' => $request->description,
                    'elements' => $request->elements,
                    'comments' => $request->comments,
                    'methodcode' =>  $request->methodcode,
                    'transmittalno' => $request->transmittalno,
                    'samplewtvolume' => $request->samplewtvolume,
                    'source'    => $request->source,
                    'updatedby' => auth()->user()->username,
                    'receiveby'  => $receiver
                ];
            } else{
                $data = [
                    'sampleno' => $request->sampleno,
                    'description' => $request->description,
                    'elements' => $request->elements,
                    'comments' => $request->comments,
                    'methodcode' =>  $request->methodcode,
                    'transmittalno' => $request->transmittalno,
                ];
            }
            $item->update($data);
            return response()->json('success');
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }
    public function getWorksheetItems(Request $request)
    {
        $items = TransmittalItem::where('labbatch', $request->labbatch)
            ->orderBy('order');

        if ($request->reqfrom === "edit_analyst" || $request->reqfrom === "view_analyst") {
            // No additional conditions needed
        } elseif ($request->reqfrom === "edit_worksheet" && $request->is_reassay === true) {
            $items->where('reassayed', 1);
        }
        
        $items = $items->get();

        $items->transform(function ($item) {
            /*$item->samplewtgrams = intval($item->samplewtgrams);
            $item->fluxg = intval($item->fluxg);
            $item->flourg = intval($item->flourg);
            $item->niterg = intval($item->niterg);
            $item->leadg = intval($item->leadg);
            $item->silicang = intval($item->silicang);*/

            /*$item->auprillmg = intval($item->auprillmg) != 0 ? intval($item->auprillmg) : '';
            $item->augradegpt = intval($item->augradegpt) != 0 ? intval($item->augradegpt) : '';
            $item->assreadingppm = intval($item->assreadingppm) != 0 ? intval($item->assreadingppm) : '';
            $item->agdoremg = intval($item->agdoremg) != 0 ? intval($item->agdoremg) : '';
            $item->initialaggpt = intval($item->initialaggpt) != 0 ? intval($item->initialaggpt) : '';
            $item->inquartmg = intval($item->inquartmg) != 0 ? intval($item->inquartmg) : '';*/

            $item->auprillmg = intval($item->auprillmg) != 0 ? $item->auprillmg : '';
            $item->augradegpt = intval($item->augradegpt) != 0 ? $item->augradegpt : '';
            $item->assreadingppm = intval($item->assreadingppm) != 0 ? $item->assreadingppm : '';
            $item->agdoremg = intval($item->agdoremg) != 0 ? $item->agdoremg : '';
            $item->initialaggpt = intval($item->initialaggpt) != 0 ? $item->initialaggpt : '';
            $item->inquartmg = intval($item->inquartmg) != 0 ? $item->inquartmg : '';
            return $item;
        });
        return $items;
    }

}
