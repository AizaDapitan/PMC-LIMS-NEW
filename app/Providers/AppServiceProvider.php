<?php

namespace App\Providers;

use DateTime;
use App\Models\Worksheet;
use App\Models\Application;
use App\Models\DeptuserTrans;
use App\Models\TransmittalItem;
use App\Models\RolesPermissions;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        //
        view()->composer(
            'layouts.app',
            function ($view) {
                $forOffApproval = DeptuserTrans::where([['status', 'Pending'], ['isdeleted', false], ['isSaved', 1],['transcode',1], ['section', auth()->user()->section]])->count();
                $forReceive = DeptuserTrans::where([['status', 'Approved'], ['isReceived', false], ['transcode', 1]])->WhereNotIn('transType', ['Solids', 'Solutions'])->count();
                $unsaved = DeptuserTrans::where([['isSaved', 0], ['created_by', auth()->user()->username], ['isdeleted', 0], ['transcode', 1]])->count();
                // $usertrans = DeptuserTrans::where([['isReceived', true], ['isdeleted', 0]])->get();
                $trans_nos = DeptuserTrans::where([['isReceived', true], ['isdeleted', 0],['transType', '<>', 'Solutions']])->get('transmittalno')->toArray();
                $forAssayer = 0;
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
                            $forAssayer += 1;
                        }
                    }
                }
                $forDigesterTrans = DeptuserTrans::where([['status', 'Approved'], ['isReceived', false], ['transType', 'Solids']])->count();
                $forDigesterWorksheet = Worksheet::where([['isdeleted', 0], ['isApproved', 0]])->orderBy('created_at', 'desc')->count();
                $forDigester = $forDigesterTrans + $forDigesterWorksheet;
                $forAnalystWorksheet = Worksheet::where([['isdeleted', 0], ['isApproved', 1]])->orderBy('created_at', 'desc')->count();
                $forAnalystTrans = DeptuserTrans::where([['status', 'Approved'], ['isReceived', false], ['transType', 'Solutions']])->count();
                $forAnalyst = $forAnalystWorksheet + $forAnalystTrans;
                $forOfficer = Worksheet::where([['isdeleted', 0], ['isAnalyzed', 1],['isPosted', 0]])->count();
                $unsavedOfficer = DeptuserTrans::where([['isSaved', 0], ['created_by', auth()->user()->username], ['isdeleted', 0], ['transcode', 2]])->count();
                $officerSolutions = DeptuserTrans::where([['isReceived', true], ['isdeleted', 0],['transType', 'Solutions'],['isPosted',false]])->count();
                $OfficerNotif = $forOfficer +  $unsavedOfficer + $officerSolutions;
                // $forOfficerPosted = Worksheet::where([['isdeleted', 0], ['isPosted', 1]])->count();

                //modules
                $showDeptRequestorModule = RolesPermissions::where([['role_id', auth()->user()->role_id], ['module_id', 1]])->count();
                $showDeptOfficerModule = RolesPermissions::where([['role_id', auth()->user()->role_id], ['module_id', 2]])->count();
                $showReceivingModule = RolesPermissions::where([['role_id', auth()->user()->role_id], ['module_id', 3]])->count();
                $showAssayerModule = RolesPermissions::where([['role_id', auth()->user()->role_id], ['module_id', 4]])->count();
                $showDigesterModule = RolesPermissions::where([['role_id', auth()->user()->role_id], ['module_id', 5]])->count();
                $showAnalystModule = RolesPermissions::where([['role_id', auth()->user()->role_id], ['module_id', 6]])->count();
                $showOfficerModule = RolesPermissions::where([['role_id', auth()->user()->role_id], ['module_id', 7]])->count();
                $showMaintenanceModule = RolesPermissions::where([['role_id', auth()->user()->role_id], ['module_id', 8]])->count();

                $reason = "";
                $scheduledate = "";
                $scheduletime = "";

                $from = now()->setTime(0, 0, 0)->toDateTimeString();
                $to = now()->subDays(-5)->setTime(0, 0, 0)->toDateTimeString();
                $schedule =  Application::where('deleted_at', null)->whereBetween('scheduled_date', [$from, $to])->orderBy('scheduled_date', 'asc')->first();
                $datetime = new DateTime();
                $currentDateTime = new DateTime();
                if ($schedule) {
                    $datetime = $schedule['scheduled_date'] . ' ' . $schedule['scheduled_time'];
                    $datetime = new DateTime($datetime);
                    $currentDateTime = new DateTime();
                    if ($currentDateTime < $datetime) {

                        $reason = $schedule['reason'];
                        $scheduledate = $schedule['scheduled_date'];
                        $scheduletime = str_replace(':00.0000000', '', $schedule['scheduled_time']);
                    }
                }

                $view->with(
                    compact(
                        'forOffApproval',
                        'forReceive',
                        'unsaved',
                        'forAssayer',
                        'forDigester',
                        'forDigesterWorksheet',
                        'forDigesterTrans',
                        'forAnalystWorksheet',
                        'forAnalystTrans',
                        'forAnalyst',
                        'forOfficer',
                        'unsavedOfficer',
                        'OfficerNotif',
                        'officerSolutions','reason',
                        'scheduledate',
                        'scheduletime',
                        'datetime',
                        'currentDateTime',
                        'showDeptRequestorModule',
                        'showDeptOfficerModule',
                        'showReceivingModule',
                        'showAssayerModule',
                        'showDigesterModule',
                        'showAnalystModule',
                        'showOfficerModule',
                        'showMaintenanceModule'

                        // 'forOfficerPosted'
                    )
                );
            }
        );
    }
}
