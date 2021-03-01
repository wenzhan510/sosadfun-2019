<?php

namespace Tests\Feature;

//test
use Tests\TestCase;
use StringProcess;
class RegistrationTest extends TestCase
{

    /** @test */
    public function verify_invitiation_token(){
        $data = [ 'invitation_token' => 'SOSAD_invite' ];
        $this->json('POST', 'api/register/by_invitation_token/submit_token', $data)
            ->assertStatus(200);
        $data = [ 'invitation_token' => 'SOSAD_inviteXXX' ];
        $this->json('POST', 'api/register/by_invitation_token/submit_token', $data)
            ->assertStatus(404);
    }

    /** @test */
    public function register_by_invitation_token(){
        $data = [
            'invitation_token' => 'SOSAD_invite',
            'invitation_type' => 'token',
            'invitation_token' => 'SOSAD_invite',
            'name' => StringProcess::toName($this->faker->name),
            'email' => $this->faker->email,
            'password' => 'Pass%26word%261'
        ];
        $this->json('POST', 'api/register_by_invitation', $data)
            ->assertStatus(200);
        $data['invitation_token'] = 'SOSAD_inviteXXX';
        $this->json('POST', 'api/register_by_invitation', $data)
            ->assertStatus(404);
    }

    // TODO: more tests for register

    /** @test */
    public function anyone_can_register_as_a_user()
    {
        $data = [
            'email' => $this->faker->email,
            'name' => StringProcess::toName($this->faker->name),
            'password' => 'password',
        ];
        // 密码格式不符合的时候，不允许注册
        $this->post('api/register', $data)
        ->assertStatus(422);

        $data['password'] = 'Password&1';

        $response = $this->post('api/register', $data);
        $response->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'token',
                'name',
                'id',
            ],
        ])->assertJson([
            'code' => 200,
            'data' => [
                'name' => $data['name'],
            ],
        ]);
        $content = $response->decodeResponseJson();
        // 同一邮箱不允许重复注册
        $this->post('api/register', $data)
        ->assertStatus(422);
        // 已注册之后可以登陆
        $response = $this->post('api/login',$data);
        // var_dump($response->decodeResponseJson());
        $response->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'token',
                'id',
                'name',
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'id' => $content['data']['id'],
                'name'=> $content['data']['name'],
            ],
        ]);

    }
}
