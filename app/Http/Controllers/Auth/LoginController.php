<?php

namespace App\Http\Controllers\Auth;

use App\DismissTrial;
use App\HelperFunction;
use App\Http\Controllers\Controller;
use App\OfficeUser;
use App\UsageLogs;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Anhskohbo\NoCaptcha;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;
        /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|string',
                'password' => 'required|string',
                //'g-recaptcha-response' => 'required|captcha'
            ]
        );
    }

    public function authenticated(Request $request, $user)
    {
        //dd($this->getOfficeSubscription($user));
        if (!$user->verified) {
            auth()->logout();
            return back()->with('flash_warning', 'You need to confirm your account. We have sent you an activation code, please check your email.');
        }

        if($user->trial == 1){

            $offices = OfficeUser::where('user_id',$user->id)->get();

            $trial = false;

            foreach ($offices as $office) {

                if($this->getOfficeSubscription()["date_difference"] >= 0){
                    $trial = true;
                }
            }

            if($trial) {
                $message = 'Please select an option below:<br /><a href="http://helpdesk.blackboardbs.com/product/2" class="btn btn-sm">Purchase Subscription</a><a href="'.route('subscription.cancel',$user->id).'" class="btn btn-sm">Cancel subscription</a>';
                auth()->logout();
                return back()->with(['flash_info' => 'Your trial subscription has expired.','message'=>$message]);
            }
        }

            $request->session()->put('platform','desktop');

            $usage = new UsageLogs();
            $usage->user_ip = $request->getClientIp();
            $usage->user_id = Auth::id();
            $usage->user_name = User::select(DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('id',Auth::id())->first()->full_name;
            $usage->login_status = 1;
            $usage->save();

            //insert datetime and ip address into users table
            $user->update([
                'last_login_at' => Carbon::now()->toDateTimeString(),
                'last_login_ip' => $request->getClientIp()
            ]);

            return redirect()->intended($this->redirectPath());
    }

    public function showLoginForm(){
        return view('auth.login');
}

    public function getOfficeSubscription(){
        $office_id = OfficeUser::select('office_id')->where('user_id',Auth::id())->first()->office_id;

        $helper = new HelperFunction();

        return $helper->officeSubscription($office_id);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $request->session()->put('login_error', trans('auth.failed'));
        throw ValidationException::withMessages(
            [
                'error' => [trans('auth.failed')],
            ]
        );
    }
}
