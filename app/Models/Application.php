<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable  as AuditableContract;
use OwenIt\Auditing\Auditable;


class Application extends Model implements AuditableContract
{
    use Auditable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'scheduled_date', 
        'scheduled_time',
        'reason',
        'posted_date',
        'posted_time',
        'deleted_at'
        
    ];    

    protected $auditInclude = [
        'scheduled_date', 
        'scheduled_time',
        'reason',
        'posted_date',
        'posted_time',
        'deleted_at'
        
    ];   
    
    // public function getStatusAttribute()
    // {
    //     $status = 'Active';
    //     if ($this->active != 1) {
    //         $status  = 'Inactive';
    //     }
    //     return $status;
    // }

}
