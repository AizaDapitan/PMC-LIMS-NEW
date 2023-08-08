<?php

use App\Http\Controllers\AccessRightsController;
use App\Http\Controllers\AnalystController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AssayerController;
use App\Http\Controllers\AssistantQaOfficerController;
use App\Http\Controllers\ChiefChemistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DeptUserController;
use App\Http\Controllers\DeptOfficerController;
use App\Http\Controllers\DigesterController;
use App\Http\Controllers\FireAssayerController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\QAAnalystController;
use App\Http\Controllers\QAQCRecieverController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\TransmittalItemController;
use App\Http\Controllers\UserController;
use Symfony\Component\HttpKernel\DataCollector\RouterDataCollector;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->name("login");
Route::post('auth/login', [LoginController::class, 'login'])->name("auth.login");
Route::get('auth/adminlogin', [LoginController::class, 'adminIndex'])->name("auth.adminlogin");
Route::post('auth/adminSubmit', [LoginController::class, 'adminSubmit'])->name("auth.adminSubmit");
Route::get('auth/forgot_password', [LoginController::class, 'forgot_password'])->name("auth.forgot_password");
Route::post('auth/sendRequest', [LoginController::class, 'sendRequest'])->name("auth.sendRequest");

Route::middleware(['auth'])->group(function () {

    Route::post('auth/updatePassword', [LoginController::class, 'updatePassword'])->name("auth.updatePassword");
    Route::get('auth/change_password', [LoginController::class, 'change_password'])->name("auth.change_password");
    Route::get('logout', [LoginController::class, 'logout'])->name("logout");

    // Dept User Controller
    Route::group(
        ['prefix' => 'deptuser'],
        function () {
            Route::get('/dashboard', [DeptUserController::class, 'index'])->name("deptuser.index");
            Route::get('/unsaved-transmittal', [DeptUserController::class, 'unsavedTrans'])->name("deptuser.unsavedTrans");
            Route::post('/getUnsavedTrans', [DeptUserController::class, 'getUnsaved'])->name("deptuser.getUnsaved");
            Route::post('/getDeptUsers', [DeptUserController::class, 'getDeptUsers'])->name("deptuser.getDeptUsers");
            Route::get('/create-transmittal', [DeptUserController::class, 'create'])->name("deptuser.create");
            Route::post('/autosave', [DeptUserController::class, 'autosave'])->name("deptuser.autosave");
            Route::get('/edit-transmittal/{id}', [DeptUserController::class, 'edit'])->name("deptuser.edit");
            Route::post('/store', [DeptUserController::class, 'store'])->name("deptuser.store");
            Route::post('/update', [DeptUserController::class, 'update'])->name("deptuser.update");
            Route::post('/delete', [DeptUserController::class, 'delete'])->name("deptuser.delete");
            Route::get('/view-transmittal/{id}', [DeptUserController::class, 'view'])->name("deptuser.view");
            Route::post('/checkTransNo', [DeptUserController::class, 'checkTransNo'])->name("deptuser.checkTransNo");
            Route::get('/getDeptOfficerEmails', [DeptUserController::class, 'getDeptOfficerEmails'])->name("deptuser.getDeptOfficerEmails");
        }
    );
    Route::group(
        ['prefix' => 'transItem'],
        function () {
            Route::post('/store', [TransmittalItemController::class, 'store'])->name("transItem.store");
            Route::post('/getItems', [TransmittalItemController::class, 'getItems'])->name("transItem.getItems");
            Route::post('/uploaditems', [TransmittalItemController::class, 'uploadItems'])->name("transItem.uploadItems");
            Route::post('/delete', [TransmittalItemController::class, 'delete'])->name("transItem.delete");
            Route::post('/getWorksheetItems', [TransmittalItemController::class, 'getWorksheetItems'])->name("transItem.getWorksheetItems");
            Route::post('/update', [TransmittalItemController::class, 'update'])->name("transItem.update");
        }
    );

    // Dept Officer Controller
    Route::group(
        ['prefix' => 'deptofficer'],
        function () {
            Route::get('/dashboard', [DeptOfficerController::class, 'index'])->name("deptofficer.index");
            Route::post('/getDeptOfficers', [DeptOfficerController::class, 'getDeptOfficers'])->name("deptofficer.getDeptOfficers");
            Route::get('/edit-transmittal/{id}', [DeptOfficerController::class, 'edit'])->name("deptofficer.edit");
            Route::post('/update', [DeptOfficerController::class, 'update'])->name("deptofficer.update");
            Route::get('/view-transmittal/{id}', [DeptOfficerController::class, 'view'])->name("deptofficer.view");
        }
    );
    // QA/QC Receiver Controller
    Route::group(
        ['prefix' => 'qaqcreceiver'],
        function () {
            Route::get('/dashboard', [QAQCRecieverController::class, 'index'])->name("qaqcreceiver.index");
            Route::post('/getTransmittal', [QAQCRecieverController::class, 'getTransmittal'])->name("qaqcreceiver.getTransmittal");
            Route::get('/view-transmittal/{id}', [QAQCRecieverController::class, 'view'])->name("qaqcreceiver.view");
            Route::get('/receive-transmittal/{id}', [QAQCRecieverController::class, 'receive'])->name("qaqcreceiver.receive");
            Route::post('/receiveTransmittal', [QAQCRecieverController::class, 'receiveTransmittal'])->name("qaqcreceiver.receiveTransmittal");
            Route::get('/edit-transmittal/{id}', [QAQCRecieverController::class, 'edit'])->name("qaqcreceiver.edit");
            Route::post('/getItems', [QAQCRecieverController::class, 'getItems'])->name("qaqcreceiver.getItems");
            Route::post('/download-csv', [QAQCRecieverController::class, 'downloadCSV'])->name("qaqcreceiver.downloadCsv");
            Route::post('/uploaditems', [QAQCRecieverController::class, 'uploadItems'])->name("qaqcreceiver.uploadItems");
            // Route::post('/update', [DeptOfficerController::class, 'update'])->name("deptofficer.update");
        }
    );
    // Assayer
    Route::group(
        ['prefix' => 'assayer'],
        function () {
            Route::get('/dashboard', [AssayerController::class, 'index'])->name("assayer.index");
            Route::post('/getTransmittal', [AssayerController::class, 'getTransmittal'])->name("assayer.getTransmittal");
            Route::get('/view-transmittal/{id}', [AssayerController::class, 'view'])->name("assayer.view");
            Route::get('/create-worksheet/{id}', [AssayerController::class, 'create'])->name("assayer.create");
            Route::post('/getItems', [AssayerController::class, 'getItems'])->name("assayer.getItems");
            Route::post('/getItemList', [AssayerController::class, 'getItemList'])->name("assayer.getItemList");
            Route::post('/store', [AssayerController::class, 'store'])->name("assayer.store");
            Route::get('/worksheet', [AssayerController::class, 'worksheet'])->name("assayer.worksheet");
            Route::post('/getWorksheet', [AssayerController::class, 'getWorksheet'])->name("assayer.getWorksheet");
            Route::get('/edit-worksheet/{id}', [AssayerController::class, 'edit'])->name("assayer.edit");
            Route::post('/update', [AssayerController::class, 'update'])->name("assayer.update");
            Route::post('/getWorksheetItems', [AssayerController::class, 'getWorksheetItems'])->name("assayer.getWorksheetItems");
            Route::get('/view-worksheet/{id}', [AssayerController::class, 'viewWorksheet'])->name("assayer.viewWorksheet");
            Route::post('/delete', [AssayerController::class, 'delete'])->name("assayer.delete");
            Route::post('/checkBatchNo', [AssayerController::class, 'checkBatchNo'])->name("deptuser.checkBatchNo");
            Route::post('/addSample', [AssayerController::class, 'addSample'])->name("assayer.addSample");
            Route::post('/excludeSample', [AssayerController::class, 'excludeSample'])->name("assayer.excludeSample");
            Route::post('/duplicateSample', [AssayerController::class, 'duplicateSample'])->name("assayer.duplicateSample");
            Route::post('/download-csv', [AssayerController::class, 'downloadCSV'])->name("assayer.downloadCsv");
            Route::post('/uploaditems', [AssayerController::class, 'uploadItems'])->name("assayer.uploadItems");
        }
    );
    // Digester
    Route::group(
        ['prefix' => 'digester'],
        function () {
            Route::get('/dashboard', [DigesterController::class, 'index'])->name("digester.index");
            Route::get('/transmittal', [DigesterController::class, 'transmittal'])->name("digester.transmittal");
            Route::get('/view-worksheet/{id}', [DigesterController::class, 'viewWorksheet'])->name("digester.viewWorksheet");
            Route::post('/getWorksheet', [DigesterController::class, 'getWorksheet'])->name("digester.getWorksheet");
            Route::post('/approve', [DigesterController::class, 'approve'])->name("digester.approve");
            Route::post('/getTransmittal', [DigesterController::class, 'getTransmittal'])->name("digester.getTransmittal");
            Route::get('/edit-transmittal/{id}', [DigesterController::class, 'edit'])->name("digester.edit");
            Route::post('/getItems', [DigesterController::class, 'getItems'])->name("digester.getItems");
            Route::get('/view-transmittal/{id}', [DigesterController::class, 'view'])->name("digester.view");
            Route::get('/receive-transmittal/{id}', [DigesterController::class, 'receive'])->name("digester.receive");
            Route::post('/receiveTransmittal', [DigesterController::class, 'receiveTransmittal'])->name("digester.receiveTransmittal");
        }
    );
    // Analyst
    Route::group(
        ['prefix' => 'analyst'],
        function () {
            Route::get('/dashboard', [AnalystController::class, 'index'])->name("analyst.index");
            Route::get('/view/{id}', [AnalystController::class, 'view'])->name("analyst.view");
            Route::get('/edit/{id}', [AnalystController::class, 'edit'])->name("analyst.edit");
            Route::post('/update', [AnalystController::class, 'update'])->name("analyst.update");
            Route::get('/transmittal', [AnalystController::class, 'transmittal'])->name("analyst.transmittal");
            Route::post('/getTransmittal', [AnalystController::class, 'getTransmittal'])->name("analyst.getTransmittal");
            Route::get('/view-transmittal/{id}', [AnalystController::class, 'viewTransmittal'])->name("analyst.viewTransmittal");
            Route::get('/receive-transmittal/{id}', [AnalystController::class, 'receive'])->name("analyst.receive");
            Route::post('/receiveTransmittal', [AnalystController::class, 'receiveTransmittal'])->name("analyst.receiveTransmittal");
            Route::get('/edit-transmittal/{id}', [AnalystController::class, 'editTransmittal'])->name("analyst.editTransmittal");
            Route::post('/getItems', [AnalystController::class, 'getItems'])->name("analyst.getItems");
            Route::post('/reassay', [AnalystController::class, 'reassay'])->name("analyst.reassay");
            Route::get('/AnalyticalResult/{data}', [AnalystController::class, 'analyticalResult'])->name("analyst.analyticalresult");
            Route::post('/download-csv', [AnalystController::class, 'downloadCSV'])->name("analyst.downloadCsv");
            Route::post('/uploaditems', [AnalystController::class, 'uploadItems'])->name("analyst.uploadItems");
        }
    );
    // Officer
    Route::group(
        ['prefix' => 'officer'],
        function () {
            Route::get('/dashboard', [OfficerController::class, 'index'])->name("officer.index");
            Route::get('/posted', [OfficerController::class, 'posted'])->name("officer.posted");
            Route::post('/getWorksheet', [OfficerController::class, 'getWorksheet'])->name("officer.getWorksheet");
            Route::post('/getPostedWorksheet', [OfficerController::class, 'getPostedWorksheet'])->name("officer.getPostedWorksheet");
            Route::get('/view/{id}', [OfficerController::class, 'view'])->name("officer.view");
            Route::get('/edit/{id}', [OfficerController::class, 'edit'])->name("officer.edit");
            Route::post('/update', [OfficerController::class, 'update'])->name("officer.update");
            Route::post('/getItems', [OfficerController::class, 'getItems'])->name("officer.getItems");
            Route::get('/unpost/{id}', [OfficerController::class, 'unpost'])->name("officer.unpost");
            Route::post('/updateposted', [OfficerController::class, 'updateposted'])->name("officer.updateposted");
            Route::get('/view-posted/{id}', [OfficerController::class, 'viewposted'])->name("officer.viewposted");
            Route::get('/transmittal', [OfficerController::class, 'transmittal'])->name("officer.transmittal");
            Route::post('/getTransmittal', [OfficerController::class, 'getTransmittal'])->name("officer.getTransmittal");
            Route::get('/create-transmittal', [OfficerController::class, 'createTransmittal'])->name("officer.createTransmittal");
            Route::post('/checkTransNo', [OfficerController::class, 'checkTransNo'])->name("officer.checkTransNo");
            Route::post('/store', [OfficerController::class, 'store'])->name("officer.store");
            Route::post('/autosave', [OfficerController::class, 'autosave'])->name("officer.autosave");
            Route::get('/edit-transmittal/{id}', [OfficerController::class, 'editTransmittal'])->name("officer.editTransmittal");
            Route::post('/update-transmittal', [OfficerController::class, 'updateTransmittal'])->name("officer.updateTransmittal");
            Route::get('/view-transmittal/{id}', [OfficerController::class, 'viewTransmittal'])->name("officer.viewTransmittal");
            Route::post('/delete', [OfficerController::class, 'deleteTransmittal'])->name("officer.deleteTransmittal");
            Route::get('/unsaved-transmittal', [OfficerController::class, 'unsavedTrans'])->name("officer.unsavedTrans");
            Route::post('/getUnsavedTrans', [OfficerController::class, 'getUnsaved'])->name("deptuser.getUnsaved");
            Route::get('/Solutions-dashboard', [OfficerController::class, 'transmittal_solutions'])->name("officer.solutions");
            Route::post('/getTransmittal-solutions', [OfficerController::class, 'getTransmittal_solutions'])->name("officer.getTransmittal_solutions");
            Route::get('/view-solution/{id}', [OfficerController::class, 'view_solution'])->name("officer.view_solution");
            Route::get('/post-solution/{id}', [OfficerController::class, 'post_solution'])->name("officer.edit_solution");
            Route::post('/post', [OfficerController::class, 'post'])->name('officer.post');
        }
    );
    // Role Controller
    Route::group(
        ['prefix' => 'users'],
        function () {
            Route::get('/dashboard', [UserController::class, 'index'])->name("users.index");
            Route::get('/getAllUsers', [UserController::class, 'getAllUsers'])->name("users.getAllUsers");
            Route::get('/create', [UserController::class, 'create'])->name("users.create");
            Route::get('/employee-lookup', [UserController::class, 'employee_lookup'])->name("users.employee_lookup");
            Route::post('/store', [UserController::class, 'store'])->name("users.store");
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name("users.edit");
            Route::post('/update', [UserController::class, 'update'])->name("users.update");
            Route::post('/deactivate', [UserController::class, 'deactivate'])->name("users.deactivate");
            Route::post('/activate', [UserController::class, 'activate'])->name("users.activate");
            Route::get('/getSections', [UserController::class, 'getSections'])->name("roles.getSections");
        }
    );
    // Access Rights
    Route::group(
        ['prefix' => 'accessrights'],
        function () {
            Route::get('/user', [AccessRightsController::class, 'user'])->name('accessrights.user');
            Route::get('/role', [AccessRightsController::class, 'role'])->name('accessrights.role');
            Route::get('/getUsers', [AccessRightsController::class, 'getUsers'])->name('accessrights.getUsers');
            Route::get('/getModules', [AccessRightsController::class, 'getModules'])->name('accessrights.getModules');
            Route::post('/storeUser', [AccessRightsController::class, 'storeUser'])->name('accessrights.storeUser');
            Route::post('/getUserAccessRights', [AccessRightsController::class, 'getUserAccessRights'])->name('accessrights.getUserAccessRights');
            Route::post('/storeRole', [AccessRightsController::class, 'storeRole'])->name('accessrights.storeRole');
            Route::post('/getRoleAccessRights', [AccessRightsController::class, 'getRoleAccessRights'])->name('accessrights.getRoleAccessRights');
        }
    );
    // Role Controller
    Route::group(
        ['prefix' => 'roles'],
        function () {
            Route::get('/create', [RoleController::class, 'create'])->name("roles.create");
            Route::get('/getRoles', [RoleController::class, 'getRoles'])->name("roles.getRoles");
            Route::post('/store', [RoleController::class, 'store'])->name("roles.store");
            Route::get('/dashboard', [RoleController::class, 'index'])->name("roles.index");
            Route::get('/edit/{id}', [RoleController::class, 'edit'])->name("roles.edit");
            Route::post('/update', [RoleController::class, 'update'])->name("roles.update");
            Route::get('/rolesList', [RoleController::class, 'rolesList'])->name("roles.list");
            Route::get('/rolesList_selected', [RoleController::class, 'rolesList_selected'])->name("roles.list_selected");
        }
    );
    // Permission Controller
    Route::group(
        ['prefix' => 'permissions'],
        function () {
            Route::get('/create', [PermissionController::class, 'create'])->name("permissions.create");
            Route::get('/getPermissions', [PermissionController::class, 'getPermissions'])->name("permissions.getPermissions");
            Route::post('/store', [PermissionController::class, 'store'])->name("permissions.store");
            Route::get('/dashboard', [PermissionController::class, 'index'])->name("permissions.index");
            Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name("permissions.edit");
            Route::post('/update', [PermissionController::class, 'update'])->name("permissions.update");
            Route::get('/modulesList', [PermissionController::class, 'modulesList'])->name("permissions.list");
            Route::get('/getPermission_selected', [PermissionController::class, 'getPermission_selected'])->name("permissions.getPermission_selected");
        }
    );
    // Report Controller
    Route::group(
        ['prefix' => 'reports'],
        function () {
            Route::get('/audit-logs', [ReportController::class, 'auditLogs'])->name("reports.auditLogs");
            Route::post('/getAuditLogs', [ReportController::class, 'getAuditLogs'])->name("reports.getAuditLogs");
            Route::get('/error-logs', [ReportController::class, 'errorLogs'])->name("reports.errorLogs");
            Route::post('/getErrorLogs', [ReportController::class, 'getErrorLogs'])->name("reports.getErrorLogs");
        }
    );
    // Application Controller
    Route::group(
        ['prefix' => 'applications'],
        function () {
            Route::get('/dashboard', [ApplicationController::class, 'index'])->name("applications.index");
            Route::get('/getScheduledShutdown', [ApplicationController::class, 'getScheduledShutdown'])->name("applications.getScheduledShutdown");
            Route::get('/create', [ApplicationController::class, 'create'])->name("applications.create");
            Route::post('/store', [ApplicationController::class, 'store'])->name("applications.store");
            Route::get('/edit/{id}', [ApplicationController::class, 'edit'])->name("applications.edit");
            Route::post('/update', [ApplicationController::class, 'update'])->name("applications.update");
            Route::post('/delete', [ApplicationController::class, 'delete'])->name("applications.delete");

            Route::post('systemDown', [ApplicationController::class, 'systemDown'])->name('applications.systemDown');
            Route::post('systemUp', [ApplicationController::class, 'systemUp'])->name('applications.systemUp');
            Route::post('reindex', [ApplicationController::class, 'reindex'])->name('applications.reindex');
            Route::get('systemDown_auto', [ApplicationController::class, 'systemDown_auto'])->name('applications.systemDown_auto');
        }
    );
     // Supervisor Controller
     Route::group(
        ['prefix' => 'supervisors'],
        function () {
            Route::get('/dashboard', [SupervisorController::class, 'index'])->name("supervisors.index");
            Route::get('/getSupervisor', [SupervisorController::class, 'getSupervisor'])->name("supervisors.getSupervisor");
            Route::get('/create', [SupervisorController::class, 'create'])->name("supervisors.create");
            Route::post('/store', [SupervisorController::class, 'store'])->name("supervisors.store");
            Route::get('/edit/{id}', [SupervisorController::class, 'edit'])->name("supervisors.edit");
            Route::post('/update', [SupervisorController::class, 'update'])->name("supervisors.update");
            Route::get('/getSupervisorActive', [SupervisorController::class, 'getSupervisorActive'])->name("supervisors.getSupervisorActive");
        }
    );
     // Fire Assayer Controller
     Route::group(
        ['prefix' => 'fireassayers'],
        function () {
            Route::get('/dashboard', [FireAssayerController::class, 'index'])->name("fireassayers.index");
            Route::get('/getFireAssayer', [FireAssayerController::class, 'getFireAssayer'])->name("fireassayers.getFireAssayer");
            Route::get('/create', [FireAssayerController::class, 'create'])->name("fireassayers.create");
            Route::post('/store', [FireAssayerController::class, 'store'])->name("fireassayers.store");
            Route::get('/edit/{id}', [FireAssayerController::class, 'edit'])->name("fireassayers.edit");
            Route::post('/update', [FireAssayerController::class, 'update'])->name("fireassayers.update");
            Route::get('/getFireAssayerActive', [FireAssayerController::class, 'getFireAssayerActive'])->name("fireassayers.getFireAssayerActive");
        }
    );
     // Assistant QA Officer Controller
     Route::group(
        ['prefix' => 'assistantofficers'],
        function () {
            Route::get('/dashboard', [AssistantQaOfficerController::class, 'index'])->name("assistantofficers.index");
            Route::get('/getAssistantOfficer', [AssistantQaOfficerController::class, 'getAssistantOfficer'])->name("assistantofficers.getAssistantOfficer");
            Route::get('/create', [AssistantQaOfficerController::class, 'create'])->name("assistantofficers.create");
            Route::post('/store', [AssistantQaOfficerController::class, 'store'])->name("assistantofficers.store");
            Route::get('/edit/{id}', [AssistantQaOfficerController::class, 'edit'])->name("assistantofficers.edit");
            Route::post('/update', [AssistantQaOfficerController::class, 'update'])->name("assistantofficers.update");
        }
    );
    // Chief Chemist Controller
    Route::group(
        ['prefix' => 'chiefchemists'],
        function () {
            Route::get('/dashboard', [ChiefChemistController::class, 'index'])->name("chiefchemists.index");
            Route::get('/getChiefChemist', [ChiefChemistController::class, 'getChiefChemist'])->name("chiefchemists.getChiefChemist");
            Route::get('/create', [ChiefChemistController::class, 'create'])->name("chiefchemists.create");
            Route::post('/store', [ChiefChemistController::class, 'store'])->name("chiefchemists.store");
            Route::get('/edit/{id}', [ChiefChemistController::class, 'edit'])->name("chiefchemists.edit");
            Route::post('/update', [ChiefChemistController::class, 'update'])->name("chiefchemists.update");
            Route::get('/getChiefChemistActive', [ChiefChemistController::class, 'getChiefChemistActive'])->name("chiefchemists.getChiefChemistActive");
        }
    );
    // QA Analyst Controller
    Route::group(
        ['prefix' => 'qaanalysts'],
        function () {
            Route::get('/dashboard', [QAAnalystController::class, 'index'])->name("qaanalysts.index");
            Route::get('/getAnalyst', [QAAnalystController::class, 'getAnalyst'])->name("qaanalysts.getAnalyst");
            Route::get('/create', [QAAnalystController::class, 'create'])->name("qaanalysts.create");
            Route::post('/store', [QAAnalystController::class, 'store'])->name("qaanalysts.store");
            Route::get('/edit/{id}', [QAAnalystController::class, 'edit'])->name("qaanalysts.edit");
            Route::post('/update', [QAAnalystController::class, 'update'])->name("qaanalysts.update");
            Route::get('/getAnalystActive', [QAAnalystController::class, 'getAnalystActive'])->name("qaanalysts.getAnalystActive");
        }
    );
});
