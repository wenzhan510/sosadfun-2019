<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sosadfun\Traits\DonationObjectTraits;
use CacheUser;
use Carbon;
use Cache;
use DB;
use Auth;

class DonationController extends Controller
{
    use DonationObjectTraits;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('admin')->only('patreon_index', 'patreon_approve', 'patreon_upload');
    }

    public function index(Request $request)
    {
        if($request->record_type==='historical'){
            $donation_records = $this->AllDonations(is_numeric($request->page)? $request->page:'1');
        }else{
            $donation_records = $this->RecentDonations(is_numeric($request->page)? $request->page:'1');
        }

        // TODO
        // return view('donations.donate', compact('donation_records'));
    }

    public function donation_update($id, Request $request)
    {
        $record = \App\Models\DonationRecord::on('mysql::write')->find($id);
        if(!$record){abort(404);}
        if($record->user_id!=auth('api')::id()){abort(403);}
        $request->validate([
            'donation_majia' => 'string|nullable|max:20',
            'donation_message' => 'string|nullable|max:200',
        ]);

        $data = $request->only('donation_majia','donation_message');
        $data['show_amount'] = $request->show_amount?true:false;
        $data['is_anonymous'] = $request->is_anonymous?true:false;

        $record->update($data);

        // return redirect()->route('donation.mydonations')->with('success','已修改赞助记录显示');
    }


    public function user_donation($id)
    {
        if(!auth('api')->user()->isAdmin()){$id = auth('api')->id();}
        $user = CacheUser::user($id);
        $patreon = $user->patreon;
        $donation_records = $user->donation_records;
        $reward_tokens = $user->reward_tokens->isPrivate()->isRedeemable();
        // TODO
        // return response()->success([
        //     'user' => '',
        //     'patreon' => '',
        //     'donation_records' => '',
        //     'reward_tokens' => '',
        // ]);
    }

    public function user_reward_tokens($id)
    {
        if(!auth('api')->user()->isAdmin()){$id = auth('api')->id();}
        $user = CacheUser::user($id);
        $reward_tokens = $user->reward_tokens->isPrivate();
        // TODO
        // return view('donations.my_reward_tokens',compact('user', 'reward_tokens'));
    }



    public function reward_token_redeem(Request $request)
    {
        $user = CacheUser::Auser();
        $request->validate([
            'token' => 'required|string|max:30',
        ]);
        $reward_token = \App\Models\RewardToken::where('token',$request->token)->first();

        if(!$reward_token){
            abort(404, '福利码不存在，请检查拼写正确');
        }

        if($reward_token->redeem_limit<=0||$reward_token->redeem_until<Carbon::now()){
            abort(444, '福利码已失效');
        }
        if($reward_token->type==='no_ads'&&$user->no_ads){
            abort('412','你已经具有去广告福利，无需浪费这个福利码');
        }

        DB::transaction(function()use($user, $reward_token){
            $reward_token->redeem_count+=1;
            $reward_token->redeem_limit-=1;
            $reward_token->save();
            \App\Models\RewardTokenRedemption::create([
                'user_id' => $user->id,
                'token_creator_id' => $reward_token->user_id,
                'token_id' => $reward_token->id,
            ]);
            if($reward_token->type=='no_ads'){
                $user->no_ads = 1;
                $user->save();
            }
            if($reward_token->type=='qiandao+'){
                $user->info->qiandao_reward_limit+=1;
                $user->info->save();
            }
        });

        // TODO
        // return redirect()->route('donation.mydonations')->with('success','已兑换福利码');
    }



    public function reward_token_store(Request $request)
    {
        $type = $request->type;
        $user = CacheUser::Auser();
        $info = CacheUser::Ainfo();
        if($info->donation_level<4){abort(403);}
        if($type=='qiandao+'&&$info->qiandao_reward_limit>0){
            $info->qiandao_reward_limit-=1;
            $info->save();
        }
        if($type=='no_ads'&&$info->no_ads_reward_limit>0){
            $info->no_ads_reward_limit-=1;
            $info->save();
        }

        if($type!='no_ads'&&$type!='qiandao+'){abort(422);}
        $reward_token = \App\Models\RewardToken::create([
            'user_id' => $user->id,
            'token' => str_random(15),
            'redeem_limit' => 1,
            'is_public' =>0,
            'type' => $type,
            'redeem_until' => Carbon::now()->addMonth(1),
        ]);
        return redirect()->route('donation.mydonations')->with('success','已成功创建福利码，可以分享给小伙伴了！');
    }

    public function patreon_store(Request $request)
    {
        $user = CacheUser::Auser();
        $patreon = \App\Models\Patreon::onWriteConnection()->where('user_id',$user->id)->first();
        if($patreon){abort(410,'已经存在捐赠记录');}

        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $other_record = \App\Models\Patreon::onWriteConnection()->where('patreon_email', $request->email)->first();
        if($other_record){
            abort(444, '这个Patreon账户已经有人申请，如果不是你本人，请联系站务人员申诉。');
        }
        \App\Models\Patreon::create([
            'user_id' => $user->id,
            'patreon_email' => $request->email,
        ]);
        // TODO
        // return redirect()->route('donation.mydonations')->with('success','已成功提交patreon信息，请等待工作人员完成数据关联。');
    }

    public function patreon_destroy($id)
    {
        $user = CacheUser::Auser();
        $patreon = \App\Models\Patreon::on('mysql::write')->find($id);
        if(!$patreon){abort(404);}
        if($patreon->user_id!=$user->id){abort(403);}
        $patreon->delete();

        $user->cancel_donation_reward();

        DB::table('donation_records')->where('user_id', $user->id)->update(['user_id'=>0]);

        // return redirect()->route('donation.mydonations')->with('success','已删除patreon关联信息。');
    }

    public function patreon_index(Request $request)
    {
        $is_approved = 0;
        if($request->show_review_tab=='approved'){$is_approved = 1;}
        $patreons = \App\Models\Patreon::with('author','donation_records.author')->where('is_approved', $is_approved)->latest()->paginate(20)->appends($request->only('page','show_review_tab'));

        return view('donations.review_patreon', compact('patreons'))->with('show_review_tab', $request->show_review_tab);
    }

    public function patreon_approve($id)
    {
        $patreon = \App\Models\Patreon::on('mysql::write')->find($id);
        if(!$patreon){abort(404);}
        $patreon->sync_records();
        return redirect()->route('donation.review_patreon')->with('success','synced_records');
    }

    public function disapprove(Request $request)
    {

    }

    public function patreon_upload(Request $request)
    {
        $body = $request->body;
        $lines = explode("\n",$body);
        foreach($lines as $line){
            $result = $this->processDonationRecord($line);
            if(!array_key_exists('success',$result)){
                session()->flash('warning', $result['data']);
                return back();
            }
            $record = $this->storeDonationRecord($result['data']);
        }
        return redirect()->route('donation.review_patreon')->with('success','已成功修改赞助记录');
    }


}
