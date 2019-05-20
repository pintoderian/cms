<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yadahan\AuthenticationLog\AuthenticationLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username(){
        return 'username';
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));
        if ($token) {
            $this->guard()->setToken($token);
            return true;
        }
        
        return false;
    }
    /**
     * Extrae el ultimo van del usuario
     */
    public function ban_value($id){
        $user = DB::table('bans')->where('bannable_id', $id)->orderBy("id","desc")->first();
        return $user;
    }
    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $token = (string) $this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');
        if ($this->guard()->user()->isNotBanned()) {
            $this->log_access_user($request);
            return [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $expiration - time(),
            ];
        }else{
            $user_id = $this->guard()->id();
            $ban = $this->ban_value($user_id);
            $this->guard()->logout();
            $comment = isset($ban->comment) ? " por ".$ban->comment : NULL;
            return response()->json([
                "message" => "The given data was invalid.",
                "errors" => ["username" => "Su cuenta ha sido baneada".$comment] 
            ], 422);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->log_logout_user($request);
        $this->guard()->logout();
    }
    /**
     * Logs Access User
     */
    public function log_access_user($request){
        $user = Auth::User();
        if($user->can("access_panel")){
            $ip = $request->ip();
            $userAgent = $request->userAgent();
            $known = $user->authentications()->whereIpAddress($ip)->whereUserAgent($userAgent)->first();
            $authenticationLog = new AuthenticationLog([
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'login_at' => Carbon::now()
            ]);
            $user->authentications()->save($authenticationLog);
        }
    }
    /**
     * Logs logout user
     */
    public function log_logout_user($request){
        $user = Auth::User();
        if($user->can("access_panel")){
            $ip = $request->ip();
            $userAgent = $request->userAgent();
            $authenticationLog = $user->authentications()->whereIpAddress($ip)->whereUserAgent($userAgent)->first();
            if (! $authenticationLog) {
                $authenticationLog = new AuthenticationLog([
                    'ip_address' => $ip,
                    'user_agent' => $userAgent
                ]);
            }
            $authenticationLog->logout_at = Carbon::now();
            $user->authentications()->save($authenticationLog);
        }
    }
}
