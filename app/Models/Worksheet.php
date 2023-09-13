<?php

namespace App\Models;

use DateTime;
use OwenIt\Auditing\Auditable;
use App\Models\TransmittalItem;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable  as AuditableContract;

class Worksheet extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'labbatch', 'dateshift', 'timeshift', 'fusionfurno', 'fusiontimefrom', 'fusiontimeto',
        'fusion', 'cupellationfurno', 'cupellationtimefrom', 'cupellationtimeto', 'cupellation', 'temperature', 'moldused', 'fireassayer', 'createdby',
        'isdeleted', 'deleted_at', 'updatedby', 'deleteby','isApproved','approvedby','approved_at','transType','dateweighed','shiftweighed','micnocheckweights',
        'measuredby','analyzedby','isAnalyzed','isPosted','posted_at','posted_by'
    ];
    protected $auditInclude = [
        'labbatch', 'dateshift', 'timeshift', 'fusionfurno', 'fusiontimefrom', 'fusiontimeto',
        'fusion', 'cupellationfurno', 'cupellationtimefrom', 'cupellationtimeto', 'cupellation', 'temperature', 'moldused', 'fireassayer', 'createdby',
        'isdeleted', 'deleted_at', 'updatedby', 'deleteby','isApproved','approvedby','approved_at','transType','dateweighed','shiftweighed','micnocheckweights',
        'measuredby','analyzedby','isAnalyzed','isPosted','posted_at','posted_by'
    ];
    protected $appends = ['fusion_furnace','cupellation_furnace','statuses','is_reassay'];

    public function getFusionFurnaceAttribute()
    {
        $from = new DateTime($this->fusiontimefrom);
        $from = $from->format('h:i A');
        $to = new DateTime($this->fusiontimeto);
        $to = $to->format('h:i A');
        $fusionFurnace = $this->fusionfurno . '-' . $from  . '-' . $to;
        return $fusionFurnace;
    }
    public function getCupellationFurnaceAttribute()
    {
        $from = new DateTime($this->cupellationtimefrom);
        $from = $from->format('h:i A');
        $to = new DateTime($this->cupellationtimeto);
        $to = $to->format('h:i A');
        $cupellationFurnace = $this->cupellationfurno . '-' . $from  . '-' . $to;
        return $cupellationFurnace;
    }
    public function getStatusesAttribute()
    {
        $statuses = "Pending";
        if ($this->isApproved) {
            $statuses = 'Approved';
        } 
        return $statuses;
    }

    public function getIsReassayAttribute(){
        $items = TransmittalItem::where('labbatch', $this->labbatch)->get();
        foreach ($items as $item) {
            if ($item->reassayed) {
                return true;
            }
        }
        return false;
    }

}
