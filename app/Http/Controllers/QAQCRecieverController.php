<?php

namespace App\Http\Controllers;

use App\Models\DeptuserTrans;
use App\Models\TransmittalItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Services\AccessRightService;

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
    public function getTransmittal()
    {
        // dd( DeptuserTrans::where([['isdeleted', 0],['status','Approved']])->WhereNotIn('transType',['Solid','Solutions'])
        // ->orderBy('transmittalno', 'asc')->toSql());
        $transmittal = DeptuserTrans::where([['isdeleted', 0],['status','Approved']])->WhereNotIn('transType',['Solids','Solutions'])
        ->orderBy('transmittalno', 'asc')->get();

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
}
