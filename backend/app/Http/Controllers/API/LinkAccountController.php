<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Hash;
use App\Models\User;
use Auth;
use DB;
use Cache;
use Carbon;
use App\Models\Linkaccount;
use CacheUser;

class LinkAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $user = auth('api')->user();
        if(!$user){abort(404);}
        // $branchaccounts = $user->branchaccounts;
        // TODO return response with branchaccounts and make a proper resource for it.
        // return view('users.linkedaccounts', compact('user', 'branchaccounts'));
    }

    public function store(Request $request)
    {
        // $user = auth('api')->user();
        // $this->validate($request, [
        //     'email' => 'required',
        //     'password'  => 'required'
        // ]);
        // if( $user->branchaccounts()->count() >= ($user->level-4)&&!$user->isAdmin()&&!$user->isEditor() ){
        //     return redirect()->back()->with("warning","你的等级限制了你能够关联的账户上限，请升级后再关联更多账户。");
        // }
        // $newaccount = User::where('email',request('email'))->first();
        // if(!$newaccount){
        //     return redirect()->back()->with("danger","你输入的账号不存在。");
        // }
        // if(!Hash::check(request('password'), $newaccount->password)){
        //     return redirect()->back()->with("danger","你输入的账号信息不匹配。");
        // }
        // if($newaccount->id === $user->id){
        //     return redirect()->back()->with("danger","抱歉，你不能关联自己的账号。");
        // }
        // if ($user->linked($newaccount->id)){
        //     return redirect()->back()->with("warning","你已经关联该账号，请勿重复关联。");
        // }
        //
        // Linkaccount::create(['master_account'=>$user->id, 'branch_account'=>$newaccount->id]);
        // return redirect()->back()->with("success","你已成功关联账号。");
    }

    public function switch($id)
    {
        // if(auth('api')->user()->linked($id)){
        //     Auth::loginUsingId($id);
        //     $user = User::findOrFail($id);
        //
        //     $user->save();
        //     return redirect()->back()->with("success","你已成功切换账号");
        // }else{
        //     return redirect()->back()->with("danger","你并未关联该账号");
        // }
    }

    public function destroy(Request $request)
    {
        $user = auth('api')->user();// TODO CacheUser
        if(request()->master_account==$user->id||request()->branch_account==$user->id){
            $link = Linkaccount::where('master_account','=',request()->master_account) // TODO change to on write connection
            ->where('branch_account','=',request()->branch_account)->first();
            if ($link){
                $link->delete();
                return ['success' => '你已成功取消关联账号!'];
            }
            return ['warning' => '未找到关联记录！'];
            //成功删除之前，还需要清理master和branch账户所有的token，然后再用新token登陆目前登陆的账户。
        }
        return ['danger' => '身份信息失误'];
    }
}
