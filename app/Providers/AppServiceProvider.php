<?php

namespace App\Providers;

use App\Models\DeptuserTrans;
use App\Models\TransmittalItem;
use App\Models\Worksheet;
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
                $forOffApproval = DeptuserTrans::where([['status', 'Pending'], ['isdeleted', false], ['isSaved', 1],['transcode',1]])->count();
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
                        'officerSolutions'
                        // 'forOfficerPosted'
                    )
                );
            }
        );
    }
}
