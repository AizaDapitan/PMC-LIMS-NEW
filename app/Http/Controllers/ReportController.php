<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \OwenIt\Auditing\Models\Audit;
use App\Services\AccessRightService;
use App\Services\ReportService;

class ReportController extends Controller
{
    protected $accessRightService;
    protected $reportService;
    public function __construct(
        ReportService $reportService,
        AccessRightService $accessRightService
    ) {
        $this->accessRightService = $accessRightService;
        $this->reportService = $reportService;
    }
    public function auditLogs()
    {

        return view('report.audit_logs');
    }
    public function getAuditLogs(Request $request)
    {
        $rolesPermissions = $this->accessRightService->hasPermissions("User Action Monitoring");
        if (!$rolesPermissions['view']) {
            abort(401);
        }
        $dateFrom = now()->toDateString();
        $dateTo = now()->toDateString();
        if (isset($request->dateFrom)) {
            $dateFrom = $request->dateFrom;
        }
        if (isset($request->dateTo)) {
            $dateTo = $request->dateTo;
        }

        $audits = Audit::select('audits.*', 'u.username')
            ->leftjoin('users as u', 'audits.user_id', '=', 'u.id')
            ->when(isset($dateTo), function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('audits.created_at',  [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            })
            ->when(!isset($dateTo), function ($q) use ($dateFrom) {
                $q->whereDate('created_at', $dateFrom);
            })
            ->orderBy('created_at', 'desc')->get();
        $this->reportService->create("Audit Logs", $request);

        return $audits;
    }
}
