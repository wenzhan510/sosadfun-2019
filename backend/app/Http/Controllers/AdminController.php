<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Thread;
use App\Models\Post;
use App\Models\Status;
use App\Models\User;
use App\Models\Administration;
use DB;
use Auth;
use Carbon;
use ConstantObjects;
use StringProcess;
use CacheUser;
use Cache;
use App\Sosadfun\Traits\ThreadObjectTraits;
use App\Sosadfun\Traits\PostObjectTraits;
use App\Sosadfun\Traits\MessageObjectTraits;
use App\Sosadfun\Traits\ThreadQueryTraits;

class AdminsController extends Controller
{
    use ThreadObjectTraits;
    use ThreadQueryTraits;
    use PostObjectTraits;
    use MessageObjectTraits;
    // use ThreadQueryTraits;

    //所有这些都需要用transaction，以后再说
    public function __construct()
    {
        $this->middleware('admin');
    }
    public function index()
    {
        return view('admin.index');
    }

    public function searchrecordsform(){
        return view('admin.searchrecordsform');
    }
    public function searchrecords(Request $request){
        $this->validate($request, [
            'name' => 'nullable|string|min:1|max:191',
            'name_type' => 'required|string',
        ]);
        $name = $request->name;
        $users = [];
        $email_modification_records = [];
        $password_reset_records = [];
        $donation_records = [];
        $application_records = [];
        $black_list_emails = [];
        $quotes = [];

        if($request->name_type && $request->name_type=='user_id'){
            $users = User::with('emailmodifications', 'passwordresets', 'registrationapplications.reviewer', 'donations', 'info', 'usersessions')
            ->where('id',$request->name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type=='is_forbidden'){
            $users = User::with('emailmodifications', 'passwordresets', 'registrationapplications.reviewer', 'donations', 'info', 'usersessions')
            ->join('user_infos','users.id','=','user_infos.user_id')
            ->where('user_infos.is_forbidden', 1)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='username'){
            $users = User::with('emailmodifications', 'passwordresets', 'registrationapplications.reviewer', 'donations', 'info', 'usersessions')
            ->nameLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='email'){
            $users = User::with('emailmodifications', 'passwordresets', 'registrationapplications.reviewer', 'donations', 'info', 'usersessions')
            ->emailLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));

            $email_modification_records = \App\Models\HistoricalEmailModification::with('user.info')
            ->emailLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));

            $donation_records = \App\Models\DonationRecord::with('user.info')
            ->emailLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));

            $application_records = \App\Models\RegistrationApplication::with('user.info','reviewer','owner')
            ->emailLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));

            $black_list_emails = \App\Models\FirewallEmail::emailLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='ip_address'){
            $users = User::with('emailmodifications', 'passwordresets', 'registrationapplications.reviewer', 'donations', 'info', 'usersessions')
            ->creationIPLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));

            $email_modification_records = \App\Models\HistoricalEmailModification::with('user.info')
            ->creationIPLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));

            $application_records = \App\Models\RegistrationApplication::with('user.info','reviewer','owner')
            ->creationIPLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='latest_created_user'){
            $users = User::with('emailmodifications', 'passwordresets', 'registrationapplications.reviewer', 'donations', 'info', 'usersessions')
            ->latest()
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='latest_invited_user'){
            $users = User::whereHas('info', function ($query){
                $query->where('invitor_id', '>', 0);
            })
            ->with('emailmodifications', 'passwordresets', 'registrationapplications.reviewer', 'donations', 'info', 'usersessions')
            ->latest()
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='latest_email_modification'){
            $email_modification_records = \App\Models\HistoricalEmailModification::with('user.info')
            ->latest()
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='latest_password_reset'){
            $password_reset_records = \App\Models\HistoricalPasswordReset::with('user.info')
            ->latest()
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='max_suspicious_sessions'){
            $users = User::with('emailmodifications', 'passwordresets', 'registrationapplications.reviewer', 'donations', 'info', 'usersessions')
            ->join('historical_user_sessions','users.id','=','historical_user_sessions.user_id')
            ->orderby('historical_user_sessions.mobile_count', 'desc')
            ->select('users.*')
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='active_suspicious_sessions'){
            $users = User::with('emailmodifications', 'passwordresets', 'registrationapplications.reviewer', 'donations', 'info', 'usersessions')
            ->join('historical_user_sessions','users.id','=','historical_user_sessions.user_id')
            ->where('historical_user_sessions.created_at','>', Carbon::now()->subDay(1))
            ->where('users.no_logging',0)
            ->orderby('historical_user_sessions.mobile_count', 'desc')
            ->select('users.*')
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='application_essay_like'){
            $application_records = \App\Models\RegistrationApplication::with('user.info','reviewer','owner')
            ->essayLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='application_record_id'){
            $application_records = \App\Models\RegistrationApplication::with('user.info','reviewer','owner')
            ->where('id',$name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        if($request->name_type && $request->name_type=='quote_like' && $request->name){
            $quotes = \App\Models\Quote::with('author','reviewer','admin_reviews.author')
            ->bodyLike($name)
            ->paginate(config('preference.records_per_part'))
            ->appends($request->only('page','name','name_type'));
        }

        return view('admin.searchrecords', compact('users','name','email_modification_records','donation_records','application_records','black_list_emails','password_reset_records','quotes'));
    }

    public function convert_to_old_email(User $user, $record)
    {
        $records = $user->emailmodifications;
        $this_record = $records->keyBy('id')->get($record);
        if(!$this_record){abort(403);}
        $application = \App\Models\RegistrationApplication::where('email',$this_record->new_email)->first();
        DB::transaction(function()use($user, $this_record, $application){
            $user->forceFill([
                'password' => str_random(60),
                'remember_token' => str_random(60),
                'activated' => 0,
                'email' => $this_record->old_email,
                'no_logging' => 1,
            ])->save();
            $this_record->admin_revoked_at = Carbon::now();
            $this_record->save();
            \App\Models\FirewallEmail::firstOrCreate(['email'=>$this_record->new_email]);
            if($application){
                $application->update([
                    'is_passed' => false,
                    'is_forbidden' => true,
                    'reviewed_at' => Carbon::now(),
                    'reviewer_id' => Auth::id(),
                ]);
            }
        }, 2);

        return back()->with('success','已经将邮箱复原');
    }

    public function forbid_shared_account(User $user)
    {
        $user->no_log(1000);
        $user->admin_reset_password();
        $operation = $this->add_admin_record('user', $user, '1000'.'天'.'|'.$user->name, '系统监测到多人共用账户禁封', 53, false);
        return back();
    }

    public function reset_password(User $user)
    {
        $user->admin_reset_password();
        $operation = $this->add_admin_record('user', $user, $user->name, '系统监测到多人共用账户重置', 52, false);
        return back();
    }

    public function manageblacklistform()
    {
        return view('admin.manageblacklistform');
    }

    public function manageblacklist_submit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'name_type' => 'required|string',
        ]);

        if($request->name_type==='email'&&$request->remove_from_blacklist){
            $black_list_email = \App\Models\FirewallEmail::where('email', $request->name)->first();
            if(!$black_list_email){
                return back()->with('warning','未找到与'.$request->name.'有关的黑名单邮箱记录。');
            }
            $black_list_email->delete();
            return back()->with('success','已去除'.$request->name.'的黑名单邮箱记录。');
        }

        if($request->name_type==='email'&&$request->add_to_blacklist){
            $black_list_email = \App\Models\FirewallEmail::where('email', $request->name)->first();
            if($black_list_email){
                return back()->with('warning','已存在'.$request->name.'的黑名单邮箱记录。');
            }
            \App\Models\FirewallEmail::create(['email'=>$request->name]);

            return back()->with('success','已添加'.$request->name.'的黑名单邮箱记录。');
        }

        if($request->name_type==='ip_address'&&$request->remove_from_blacklist){
            $black_list_ip = \App\Models\Firewall::where('ip_address', $request->name)->first();
            if(!$black_list_ip){
                return back()->with('warning','未找到与'.$request->name.'有关的黑名单IP记录。');
            }
            $black_list_ip->delete();
            return back()->with('success','已去除'.$request->name.'的黑名单IP记录。');
        }

        if($request->name_type==='ip_address'&&$request->add_to_blacklist){
            $black_list_ip = \App\Models\Firewall::where('ip_address', $request->name)->first();
            if($black_list_ip){
                return back()->with('warning','已存在'.$request->name.'的黑名单IP记录。');
            }
            \App\Models\Firewall::create([
                'ip_address'=>$request->name,
                'admin_id'=>Auth::id(),
            ]);

            return back()->with('success','已添加'.$request->name.'的黑名单记录。');
        }

        return back()->with('warning','什么都没做');
    }

    public function resetuser_form($id)
    {
        $user = \App\Models\User::find($id);
        if(!$user){abort(404);}
        $user_info = \App\Models\UserInfo::find($id);
        if(!$user_info){abort(404);}
        if(!$user_info->is_forbidden){abort(403);}

        return view('admin.resetuserform', compact('user', 'user_info'));
    }

    public function resetuser_submit($id, Request $request)
    {
        $user = \App\Models\User::find($id);
        if(!$user){abort(404);}
        $user_info = \App\Models\UserInfo::find($id);
        if(!$user_info){abort(404);}
        if(!$user_info->is_forbidden){abort(403);}

        $this->validate($request, [
            'name' => 'required|string|alpha_dash|unique:users|display_length:2,8',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ]);

        $user->forceFill([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt(request('password')),
            'level' => $request->level,
            'quiz_level' => $request->quiz_level,
            'no_logging' => 0,
            'activated' => 1,
        ])->save();

        $user_info->update([
            'fish' => $request->fish,
            'salt' => $request->salt,
            'ham' => $request->ham,
            'token_limit' => $request->token_limit,
            'no_ads_reward_limit' => $request->no_ads_reward_limit,
            'qiandao_reward_limit' => $request->qiandao_reward_limit,
            'qiandao_max' => $request->qiandao_max,
            'qiandao_continued' => $request->qiandao_continued,
            'qiandao_all' => $request->qiandao_all,
            'qiandao_last' => $request->qiandao_last,
            'no_logging_until' => null,
            'email_verified_at' => Carbon::now(),
        ]);

        return back()->with('success', '成功重置该用户');
    }

}
