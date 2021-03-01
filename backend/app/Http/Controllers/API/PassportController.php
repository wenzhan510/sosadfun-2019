<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Hash;
use Carbon;
use Cache;
use ConstantObjects;
use DB;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\PasswordReset;
use App\Models\HistoricalPasswordReset;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Sosadfun\Traits\SwitchableMailerTraits;
use \App\Models\InvitationToken;

class PassportController extends Controller
{
    use SwitchableMailerTraits;

    public function __construct()
    {
        $this->middleware('auth:api')->only('logout');
    }

    /**
    * Get a validator for an incoming registration request.
    *
    * @param  array  $data
    * @return \Illuminate\Contracts\Validation\Validator
    */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|alpha_dash|unique:users|display_length:2,8',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:10|max:32|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-_]).{6,}$/',
        ]);
        //password_confirmation must be included in this string
    }

    /**
    * Create a new user instance after a valid registration.
    *
    * @param  array  $data
    * @return \App\Models\User
    */

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $info = UserInfo::create([
            'user_id' => $user->id,
        ]);
        return $user;
    }


    protected function create_by_invitation_email(array $data, $application)
    {
        return DB::transaction( function() use($data, $application){
            $user = User::firstOrCreate([
                'email' => $data['email']
            ],[
                'name' => $data['name'],
                'password' => bcrypt($data['password']),
                'activated' => true,
                'level' => 0,
            ]);
            $info = UserInfo::firstOrCreate([
                'user_id' => $user->id
            ],[
                'email_verified_at' => Carbon::now(),
                'creation_ip' => request()->ip(),
            ]);

            $application->update(['user_id'=>$user->id]);
            return $user;
        });
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->error($validator->errors(), 422);
        }
        $user = $this->create($request->all());
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
        $success['id'] = $user->id;
        return response()->success($success);
    }

    // ============ register by invitation token =======================
    protected function create_by_invitation_token(array $data, $invitation_token, $application)
    {
        $new_user_base = array_key_exists($invitation_token->token_level, config('constants.new_user_base')) ? config('constants.new_user_base')[$invitation_token->token_level]:'';

        return DB::transaction( function() use($data, $invitation_token, $new_user_base, $application){
            $user = User::create([
                'email' => $data['email'],
                'name' => $data['name'],
                'password' => bcrypt($data['password']),
                'activated' => false,
                'level' => $new_user_base? $new_user_base['level']:0,
            ]);
            $info = UserInfo::create([
                'user_id' => $user->id,
                'invitation_token' => $invitation_token->token,
                'activation_token' => str_random(45),
                'invitor_id' => $invitation_token->is_public?0:$invitation_token->user_id,
                'salt' => $new_user_base? $new_user_base['salt']:0,
                'fish' => $new_user_base? $new_user_base['fish']:0,
                'ham' => $new_user_base? $new_user_base['ham']:0,
                'creation_ip' => request()->ip(),
            ]);

            if($application){
                $application->update(['user_id'=>$user->id]);
            }

            $invitation_token->inactive_once();
            return $user;
        });
    }

    public function register_by_invitation_token_submit_token(Request $request)
    {
        // TODO: recaptcha
        $this->validate($request, [
            'invitation_token' => 'required|string|min:6|max:191'
        ]);

        $token = $request->invitation_token;
        $invitation_token = $this->findInvitationToken($token);

        if(!$invitation_token) { abort(404, '邀请码不存在'); }

        if(($invitation_token->invitation_times < 1)||($invitation_token->invite_until <  Carbon::now())){
            abort(404, '邀请码已失效');
        }
        return response()->success('token verified');
    }

    private function findInvitationToken($token){
        return Cache::remember('findInvitationToken.'.$token, 5, function() use($token) {
            return InvitationToken::where('token',$token)->first();
        });
    }

    // ==========================================================================

    public function register_by_invitation(Request $request)
    {
        $user = [];

        if(ConstantObjects::black_list_emails()->where('email',request('email'))->first()){
            abort(499, '黑名单');
        }

        if($request->invitation_type==='token'){

            $invitation_token = InvitationToken::where('token', request('invitation_token'))->first();

            $application = \App\Models\RegistrationApplication::where('email', request('email'))->first();

            if(!$invitation_token){abort(404,'邀请码不存在');}

            if(($invitation_token->invitation_times < 1)||($invitation_token->invite_until <  Carbon::now())){
                Cache::forget('findInvitationToken.'.$token);
                abort(444);
            }

            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                return response()->error($validator->errors(), 422);
            }

            $user = $this->create_by_invitation_token($request->all(), $invitation_token, $application);

        }
        if($request->invitation_type==='email'){

            if(!request('email')||!request('token')) { abort(422,'缺少必要的信息，不能定位申请记录'); }
            $application = \App\Models\RegistrationApplication::where('email',request('email'))->where('token',request('token'))->first();

            if(!$application){abort(404,'不存在对应的申请记录');}

            if($application->user_id>0){abort(409,'本申请已经注册，不能重复注册');}

            if(!$application->is_passed){abort(444,'申请未通过');}

            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                return response()->error($validator->errors(), 422);
            }

            $user = $this->create_by_invitation_email($request->all(), $application);
        }

        if(!$user||!$request->invitation_type){abort(422,'缺少注册类型信息，未能注册成功');}

        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
        $success['id'] = $user->id;
        return response()->success($success);
    }


    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            if(!$user){abort(404);}
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['name'] =  $user->name;
            $success['id'] = $user->id;
            return response()->success($success);
        }
        else{
            return response()->error(config('error.401'), 401);
        }
    }

    public function logout(){
        $user = auth('api')->user();
        $user->token()->revoke();
        return response()->success([
            'user_id' => $user->id,
            'message' => "you've logged out!",
        ]);
    }

    public function reset_password_via_email(Request $request)
    {
        $data = $request->all();
        $rules = [
            'password' => 'required|string|min:10|max:32|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-_]).{6,}$/',
            'token' => 'required|string',
            'email' => 'required|email'
        ];
        $validator = Validator::make($data, $rules);
        if($validator->fails()) {
            return response()->error($validator->errors(), 422);
        }

        if(Cache::has('reset-password-email-limit:'.$request->email)){
            return response()->error('请等待一定间隙再尝试', 410);
        }

        $token_check = PasswordReset::where('email', $request->email)->latest()->first();

        Cache::put('reset-password-email-limit:'.$request->email, true, 5);

        if(!$token_check){abort(404, '重置请求不存在');}

        if(!Hash::check($request->token, $token_check->token)){abort(444, 'token已过期或已失效');}
        if($token_check->created_at<Carbon::now()->subMinutes(30)){abort(444, '重置请求过期');}


        $user_check = User::where('email', $token_check->email)->first();
        if(!$user_check){abort(404, '无法找到需要修改的账户信息');}
        $info = $user_check->info;
        if(!$info){abort(404, '用户内容不存在');}

        DB::transaction(function()use($request,$user_check,$info,$token_check){
            HistoricalPasswordReset::create([
                'user_id' => $user_check->id,
                'ip_address' => request()->ip(),
                'old_password' => $user_check->password,
            ]);
            $user_check->forceFill([
                'password'=>bcrypt($request->password),
                'remember_token'=>str_random(60)
            ])->save();
            $info->forceFill([
                'activation_token'=>null,
                'email_verified_at' => Carbon::now()
            ])->save();
            $token_check->delete();
        });

        $tokens = $user_check->tokens;

        foreach($tokens as $token){
            $token->revoke();
        }

        $success['token'] =  $user_check->createToken('MyApp')->accessToken;
        $success['name'] =  $user_check->name;
        $success['id'] = $user_check->id;
        return response()->success($success);
    }

    public function reset_password_via_password(Request $request)
    {

        // TODO 在登陆情况下，用户可以凭借旧密码验证自己的身份，将旧密码更换成新密码
        // TODO 更新密码之后，在HistoricalPasswordReset留下记录
        // TODO 更新密码后，原先的token全失活
        // TODO 更新密码后，向邮箱发送密码已修改的提醒邮件
        // TODO 如果新旧密码重复，提醒用户conflict contents
        // 更改后，发送一封关于密码已修改的邮件
    }
    public function reset_email_via_password(Request $request)
    {
        // TODO 在登陆状态下，用户可以凭借旧密码，申请将邮箱更换为新邮箱
        // 提交申请之后，系统会记录这次尝试，并向新邮箱发送确认邮件
        // 以下是过渡系统内容，供参考
        // $user = Auth::user();
        // $info = $user->info;
        // if(Cache::has('email-modification-limit-' . request()->ip())){
        //     return redirect('/')->with('danger', '你的IP今天已经修改过邮箱，请不要重复修改邮箱');
        // }
        // if(!Hash::check(request('old-password'), $user->password)) {
        //     return back()->with("danger", "你的旧密码输入错误");
        // }
        //
        // if(ConstantObjects::black_list_emails()->where('email',request('email'))->first()){
        //     return back()->with('danger', '邮箱'.request('email').'存在违规记录，禁止在本站使用。');
        // }
        //
        // if(preg_match('/qq\.com/', request('email'))){
        //     return back()->with('danger', 'qq邮箱拒收本站邮件，请勿使用qq邮箱。');
        // }
        //
        // if(preg_match('/\.con$/', request('email'))){
        //     return back()->with('danger', '请确认邮箱拼写正确。');
        // }
        //
        // $this->validate($request, [
        //     'email' => 'required|string|email|max:255|unique:users|confirmed',
        //     'g-recaptcha-response' => 'required|nocaptcha'
        // ]);
        //
        // $old_email = $user->email;
        //
        // if($old_email==$request->email){
        //     return redirect()->back()->with('warning','已经修改为这个邮箱，无需重复修改。');
        // }
        //
        // $previous_history_counts = HistoricalEmailModification::where('user_id','=',Auth::id())->where('created_at','>',Carbon::now()->subMonth(1)->toDateTimeString())->count();
        // if ($previous_history_counts>=config('constants.monthly_email_resets')){
        //     return redirect()->back()->with('warning','一月内只能修改'.config('constants.monthly_email_resets').'次邮箱。');
        // }
        //
        // $record = HistoricalEmailModification::create([
        //     'old_email' => $old_email,
        //     'new_email' => request('email'),
        //     'user_id' => Auth::id(),
        //     'ip_address' => request()->ip(),
        //     'old_email_verified_at' => $info->email_verified_at,
        //     'token' => str_random(30),
        //     'email_changed_at' => null,
        // ]);
        //
        // Cache::put('email-modification-limit-' . request()->ip(), true, 1440);
        //
        // $this->sendChangeEmailConfirmationTo($user, $record, true);
        //
        // return redirect()->route('user.edit', Auth::id())->with("success", "重置邮箱请求已登记，请查收邮箱，根据指示完成重置操作的后续步骤");
    }
    public function reset_email_via_token(Request $request)
    {
        // TODO 收到确认邮件之后，用户可凭借邮件中的链接，确认并更改邮箱
        // 更改后，向旧邮箱发送一封关于修改邮箱记录的邮件
        // 确定更改后，重置这个账户下所有相关token

        // $record = HistoricalEmailModification::onWriteConnection()->where('token',$token)->first();
        // if(!$record){
        //     return redirect('/')->with('warning','输入的token已失效或不存在');
        // }
        // $user = User::find($record->user_id);
        // $info = UserInfo::find($record->user_id);
        // if(!$user||!$info){
        //     abort(404);
        // }
        // if($record->new_email==$user->email){
        //     return redirect('/')->with('warning','已经转化成本邮箱，无需继续重置');
        // }
        // if($record->old_email!=$user->email){
        //     return redirect('/')->with('warning','原邮箱已更改，信息失效无法再行修改');
        // }
        //
        // $this->sendChangeEmailNotificationTo($user, $record, true);
        //
        // $user->forceFill([
        //     'email' => $record->new_email,
        //     'remember_token' => str_random(60),
        //     'activated' => 1,
        // ])->save();
        //
        // $info->forceFill([
        //     'activation_token' => str_random(30),
        //     'email_verified_at' => Carbon::now(),
        // ])->save();
        //
        // $record->update([
        //     'token' => str_random(30),
        //     'email_changed_at' => Carbon::now(),
        // ]);
        //
        // session()->flash('success', '邮箱已重置');
        //
        // return redirect('/');
    }

    protected function sendChangeEmailConfirmationTo($user, $record)
    {
        $view = 'auth.confirm_email_change';
        $data = compact('user', 'record');
        $to = $record->new_email;
        $subject = $user->name."的废文网账户信息更改确认！";

        $this->send_email_from_ses_server($view, $data, $to, $subject);
    }

    protected function sendChangeEmailNotificationTo($user, $record)
    {
        $view = 'auth.change_email_notification';
        $data = compact('user', 'record');
        $to = $user->email;
        $subject = $user->name."的废文网账户信息更改提醒！";

        $this->send_email_from_ses_server($view, $data, $to, $subject);
    }
}
