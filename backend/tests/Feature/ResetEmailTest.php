<?php

namespace Tests\Feature;

use Tests\TestCase;
use DB;
use App\Models\User;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Hash;
use Redis;
use Cache;

class ResetEmailTest extends TestCase
{
    /** @test */
    public function anyone_can_reset_password_by_email()
    {
        $user = factory('App\Models\User')->create();
        Cache::flush();
        $data=['email' => $user->email];
        $response = $this->post('api/password/email', $data)
        ->assertStatus(412);//当天注册用户

        /* FIXME: uncomment
        $response = $this->post('api/password/email', $data)
        ->assertStatus(498);//当前ip已于10分钟内提交过重置密码请求。
        */

        Cache::flush();
        $response = $this->post('api/password/email',['email' => '111'] )
        ->assertStatus(422);//邮箱格式错

        $response = $this->post('api/password/email',['email' => '111@163.com'] )
        ->assertStatus(404);//邮箱账户不存在;

        $user->forceFill(['created_at' => Carbon::now()->subDays(2)])->save();
        Cache::flush();
        $response = $this->post('api/password/email', $data)
        ->assertStatus(200);

        $token = str_random(40);

        $pass_reset = \App\Models\PasswordReset::where('email',$user->email)->first();

        //TODO in future test if email is succesfully sent with correct token. Now change it to what we need.

        $pass_reset->forceFill([
            'token' => bcrypt($token)
        ])->save();

        $response = $this->post('api/password/reset_via_email', [
          'token' => $token,
          'password' => '111',
          'email' => $user->email,
        ])
        ->assertStatus(422);    //密码格式错误

        // array_set($request, 'password', 'Aa1aa#%a01A11saAD');
        $response = $this->post('api/password/reset_via_email', [
            'token' => 'token',
            'password' => 'Aa1aa#%a01A11saAD',
            'email' => $user->email,
        ])->assertStatus(444);

        $response = $this->post('api/password/reset_via_email', [
        'token' => 'token',
        'password' => 'Aa1aa#%a01A11saAD',
        'email' => $user->email,
        ])
        ->assertStatus(410);

        $data = [
            'token' => $token,
            'password' => 'Aa1aa#%a01A11saAD',
            'email' => $user->email,
        ];
        Cache::flush();
        $response = $this->post('api/password/reset_via_email', $data)
        ->assertStatus(200)
        ->assertJson([
            'code' => 200,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
            ]
        ]);
        $response = $this->post('api/password/reset_via_email', $data)
        ->assertStatus(410);
        Cache::flush();
        $response = $this->post('api/password/reset_via_email', $data)
        ->assertStatus(404);

        $response = $this->post('api/login', [
            'email' => $user->email,
            'password' => $data['password'],
        ])
        ->assertStatus(200);
    }
  }
