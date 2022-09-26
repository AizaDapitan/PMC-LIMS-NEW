<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable  as AuditableContract;
use OwenIt\Auditing\Auditable;

class TransmittalItem extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'sampleno', 'description', 'elements', 'methodcode', 'comments', 'isdeleted', 'transmittalno',
        'username', 'deleted_at','samplewtvolume','labbatch','reassayed','reaasyedby','source','updatedby','deletedby','receiveby',
        'samplewtgrams','fluxg','flourg','niterg','leadg','silicang','crusibleused','assayedby','assayed_at','isAssayed','auprillmg',
        'augradegpt','assreadingppm','agdoremg','initialaggpt','crusibleclearance','inquartmg','methodremarks','isDuplicate'
    ];
    protected $auditInclude = [
        'sampleno', 'description', 'elements', 'methodcode', 'comments', 'isdeleted', 'transmittalno',
        'username', 'deleted_at','samplewtvolume','labbatch','reassayed','reaasyedby','source','updatedby','deletedby','receiveby',
        'samplewtgrams','fluxg','flourg','niterg','leadg','silicang','crusibleused','assayedby','assayed_at','isAssayed','auprillmg',
        'augradegpt','assreadingppm','agdoremg','initialaggpt','crusibleclearance','inquartmg','methodremarks','isDuplicate'
    ];
}
