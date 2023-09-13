<?php

namespace App\Models;

use App\Models\DeptuserTrans;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable  as AuditableContract;

class TransmittalItem extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'sampleno', 'description', 'elements', 'methodcode', 'comments', 'isdeleted', 'transmittalno',
        'username', 'deleted_at','samplewtvolume','labbatch','reassayed','reaasyedby','source','updatedby','deletedby','receiveby',
        'samplewtgrams','fluxg','flourg','niterg','leadg','silicang','crusibleused','assayedby','assayed_at','isAssayed','auprillmg',
        'augradegpt','assreadingppm','agdoremg','initialaggpt','crusibleclearance','inquartmg','methodremarks','isDuplicate','order'
    ];
    protected $auditInclude = [
        'sampleno', 'description', 'elements', 'methodcode', 'comments', 'isdeleted', 'transmittalno',
        'username', 'deleted_at','samplewtvolume','labbatch','reassayed','reaasyedby','source','updatedby','deletedby','receiveby',
        'samplewtgrams','fluxg','flourg','niterg','leadg','silicang','crusibleused','assayedby','assayed_at','isAssayed','auprillmg',
        'augradegpt','assreadingppm','agdoremg','initialaggpt','crusibleclearance','inquartmg','methodremarks','isDuplicate','order'
    ];

    protected $appends = ["trans_type"];

    public function getTransTypeAttribute(){
        $transmittals = DeptuserTrans::where('transmittalno', $this->transmittalno)->pluck('transType')->implode(', ');
        return $transmittals;
    }
}
