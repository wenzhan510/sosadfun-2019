<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Sosadfun\Traits\SwitchableMailerTraits;
use DB;
use Carbon\Carbon;
use Cache;
use Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    use SwitchableMailerTraits;

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
       // Cache::flush();
        //captcha不存在 验证码功能
        // $request->validate([
        //     'captcha' => 'required|captcha'
        // ]);
       $validator = Validator::make($request->all(), [
        'email' => 'required|email'
        ]);
        if ($validator->fails()) {
        return response()->error("邮箱格式不正确", 422);
        }

        /*
        FIXME: comment this out for frontend testing
        Please uncomment before move to production
        if(Cache::has('reset-password-request-limit-' . request()->ip())){
            return response()->error('当前ip('.request()->ip().')已于10分钟内提交过重置密码请求。', 498);
        }
        Cache::put('reset-password-request-limit-' . request()->ip(), true, 10);
        if(Cache::has('reset-password-limit-' . request()->ip())){
            return response()->error('当前ip('.request()->ip().')已于1小时内成功重置密码。', 498);
        }
        */

        $user_check = User::where('email', $request->email)->first();

        if (!$user_check) {
            return response()->error("该邮箱账户不存在", 404);
        }

        if ($user_check->created_at>Carbon::now()->subDay()){
            return response()->error("当日注册的用户不能重置密码", 412);
        }
        $info=$user_check->info;
         if($info&&$info->no_logging_until&&$info->no_logging_until>Carbon::now()){
            return response()->error('封禁管理中的账户不能重置密码',412);
        }

        $email_check = PasswordReset::where('email', $request->email)->first();
    //该邮箱12小时内已发送过重置邮件。请不要重复发送邮件，避免被识别为垃圾邮件。
        if ($email_check&&$email_check->created_at>Carbon::now()->subHours(12)){
            return response()->error("该邮箱12小时内已发送过重置邮件。请不要重复发送邮件，避免被识别为垃圾邮件。", 410);
        }

        $token = str_random(40);

        $reset_record = PasswordReset::updateOrCreate([
            'email' => $request->email,
        ],[
            'token'=>bcrypt($token),
        ]);
        $this->sendEmailConfirmationTo($user_check, $token);

        Cache::put($token, $request->email, 60);
        Cache::put('reset-password-limit-' . request()->ip(), true, 60);

        return response()->success(['email'=>$request->email]);
    }
    protected function sendEmailConfirmationTo($user, $token)
    {
        $view = 'auth.passwords.reset_password_email';
        $data = compact('user','token');
        $to = $user->email;
        $subject = $user->name."的废文网密码重置申请";
        $this->send_email_from_ses_server($view, $data, $to, $subject);
    }
}
