<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserInfoTest extends TestCase
{
    /** @test*/
    public function updateIntro() {
        $user = factory('App\Models\User')->create();

        // 未登录用户不能修改intro
        $this->patch('/api/user/'.$user->id.'/intro')
        ->assertStatus(401);

        $user2 = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        // 非管理员登录用户不能修改非本人intro
        $this->patch('/api/user/'.$user2->id.'/intro')
        ->assertStatus(403);

        // 需同时提交body和brief_intro
        $body = 'this is intro body';
        $this->patch('/api/user/'.$user->id.'/intro', ['body' => $body])
        ->assertStatus(422);

        $brief_intro = 'this is info brief_intro';
        $this->patch('/api/user/'.$user->id.'/intro', ['body' => $body, 'brief_intro' => $brief_intro])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'body',
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'type' => 'user_intro',
                'attributes' => [
                    'body' => $body,
                ],
            ],
        ]);

        // 管理员可修改用户intro
        $admin = factory('App\Models\User')->create(['role' => 'admin']);
        $this->actingAs($admin, 'api');
        $this->patch('/api/user/'.$user->id.'/intro', ['body' => $body, 'brief_intro' => $brief_intro])
        ->assertStatus(200);
    }

    /** @test*/
    public function updateInfo() {
        $user = factory('App\Models\User')->create();

        // 未登录用户不能修改preference
        $this->patch('/api/user/'.$user->id.'/preference')
        ->assertStatus(401);

        $user2 = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        // 不能修改非本人preference
        $this->patch('/api/user/'.$user2->id.'/preference')
        ->assertStatus(403);

        $this->patch('/api/user/'.$user->id.'/preference', ['no_upvote_reminders' => 1])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'no_upvote_reminders',
                    'no_reward_reminders',
                    'no_message_reminders',
                    'no_reply_reminders',
                    'no_stranger_msg',
                    'default_list_id',
                    'default_box_id',
                    'default_collection_group_id',
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'type' => 'user_info',
                'id' => $user->id,
                'attributes' => [
                    'no_upvote_reminders' => true,
                ],
            ],
        ]);

        // 管理员不可修改用户preference
        $admin = factory('App\Models\User')->create(['role' => 'admin']);
        $this->actingAs($admin, 'api');
        $this->patch('/api/user/'.$user->id.'/preference')
        ->assertStatus(403);
    }

    /** @test*/
    public function updateReminder() {
        $user = factory('App\Models\User')->create();

        // 未登录用户不能修改reminder
        $this->patch('/api/user/'.$user->id.'/reminder')
        ->assertStatus(401);

        $user2 = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        // 不能修改非本人reminder
        $this->patch('/api/user/'.$user2->id.'/reminder')
        ->assertStatus(403);

        $info = \App\Models\UserInfo::find($user->id);
        $info->update(['upvote_reminders' => 5]);
        $this->patch('/api/user/'.$user->id.'/reminder', ['upvote_reminders' => 1])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'unread_reminders',
                    'unread_updates',
                    'message_reminders',
                    'reply_reminders',
                    'upvote_reminders',
                    'reward_reminders',
                    'administration_reminders',
                    'default_collection_updates',
                    'public_notice_id',
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'type' => 'user_info',
                'id' => $user->id,
                'attributes' => [
                    'upvote_reminders' => 0,
                ],
            ],
        ]);

        // 管理员不可修改用户reminder
        $admin = factory('App\Models\User')->create(['role' => 'admin']);
        $this->actingAs($admin, 'api');
        $this->patch('/api/user/'.$user->id.'/reminder')
        ->assertStatus(403);
    }

    /** @test*/
    public function destroyUser() {
        $user = factory('App\Models\User')->create();

        // 未登录用户注销用户
        $this->delete('/api/user/'.$user->id)
        ->assertStatus(401);

        $user2 = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        // 不能注销非本人账号
        $this->delete('/api/user/'.$user2->id)
        ->assertStatus(403);

        // 管理员不可注销用户
        $admin = factory('App\Models\User')->create(['role' => 'admin']);
        $this->actingAs($admin, 'api');
        $this->delete('/api/user/'.$user->id)
        ->assertStatus(403);

        $this->actingAs($user, 'api');
        $this->delete('/api/user/'.$user->id)
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'success',
                'user_id',
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'user_id' => $user->id,
            ],
        ]);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}
