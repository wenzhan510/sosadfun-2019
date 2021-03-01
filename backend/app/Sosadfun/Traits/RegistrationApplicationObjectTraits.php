<?php
namespace App\Sosadfun\Traits;

use Cache;
use DB;
use ConstantObjects;
use App\Models\RegistrationApplication;
use Auth;

trait RegistrationApplicationObjectTraits{

    public function findApplicationViaEmail($email, $nullable=false)
    {
        return Cache::remember('findApplicationViaEmail.'.$email, 30, function() use($email, $nullable) {
            $message = $this->checkApplicationViaEmail($email);
            if ($message["code"] != 200) {
                abort($message["code"],$message["msg"]);
            }
            $application = RegistrationApplication::where('email',$email)->first();
            if (!$nullable) {
                if (!$application) {
                    abort(404,'申请记录不存在。');
                }
                if ($application->is_forbidden) {
                    abort(499,'此邮箱已被拉黑。');
                }
            }
            return $application;
        });
    }

    public function refreshFindApplicationViaEmail($email)
    {
        Cache::forget('findApplicationViaEmail.'.$email);
    }

    public function checkApplicationViaEmail($email)
    {
        $existing_user = DB::table('users')->where('email',$email)->first();
        if($existing_user){
            return [
                'code' => 409,
                'msg'=>'该邮箱已注册。'
            ];
        }

        $blocked_email = ConstantObjects::black_list_emails()->where('email',$email)->first();
        if($blocked_email){
            return [
                'code' => 499,
                'msg'=>'本邮箱'.$email.'存在违规记录，已被拉黑。'
            ];
        }
        return [
            'code' => 200,
            'msg'=>'本邮箱可用。'
        ];
    }

    public function rate_limit_check($function_name, $email=null, $ip=null) {
        /*
        FIXME: comment this out for frontend testing
        please uncomment before move to production
        $item = $email ?? $ip;
        // Temporarily comment this out for testing
        // FIXME: uncomment the following lines
        // if(Cache::has("Ratelimit-regapp-$function_name-$item")){
        //     return abort(498,'访问过于频繁。');
        // }
        Cache::put("Ratelimit-regapp-$function_name-$item", true, 5);
        */
    }
}
