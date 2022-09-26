<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Services\AuditService;
use DateTime;
use Exception;
use PDO;

class LoginController extends Controller
{
    // use AuthenticatesUsers;
    protected $auditService;
    public function __construct(
        AuditService $auditService
    ) {
        $this->auditService = $auditService;
    }
    public function index()
    {
        return auth()->check() ? redirect()->route('deptuser.index') : view('auth.login');
    }

    public function forgot_password()
    {
        return view('auth.forgot_password');
    }
    public function login(Request $request)
    {
        $checker = auth()->attempt([
            'username' => $request->username,
            'password' => $request->password,
            'isActive' => 1
        ]);
        if ($checker) {
            $this->auditService->create($request, "Login User : " . auth()->user()->username, "Login");
            return response()->json(['result' => 'Success']);
        } else {
            return response()->json(['result' => 'Failed']);
        }
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            $this->auditService->create($request, "Logout User : " .  auth()->user()->username, "Logout");
        }
        Auth::logout();
        Session::flush();
        return auth()->logout() ?? redirect()->route('login');
    }

    public function change_password()
    {

        $id = \Auth::user()->id;
        return view('auth.change_password');
    }

    public function updatePassword(Request $request)
    {

        $userid = \Auth::user()->id;
        $userpassword = \Auth::user()->password;
        $user = User::find($userid);

        $hasher = app('hash');

        if ($hasher->check($request->current_password, $userpassword)) {
            $data = [
                'password' => \Hash::make($request->new_confirm_password),
                'dtpassexpiration' => Carbon::now()->addDays(90),
                'first_login' => 0,
                'pass' => $request->new_confirm_password
            ];
            $user->update($data);
            User::find(Auth::user()->id)->update(['pass' => $request->new_confirm_password]);

            Session::remove('firstLogin');
            $today = new DateTime();
            $dateexp = new DateTime(Auth::user()->dtpassexpiration);
            $today->setTime(0, 0, 0);
            $interval = $today->diff($dateexp);

            $expDay = (int)$interval->format("%r%a");
            Session::put('expDay', $expDay);
            $this->updateP1users(\Hash::make($request->new_confirm_password));
            return response()->json(['result' => 'Success']);
        } else {
            return response()->json(['result' => 'Failed']);
        }
    }
}
