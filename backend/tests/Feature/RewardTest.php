<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use DB;

class RewardTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function a_user_can_create_reward()
    {


        $author=factory('App\Models\User')->create();

        $thread = factory('App\Models\Thread')->create([
            'channel_id' => 1,
            'user_id' => $author->id,
        ]);

        $post = factory('App\Models\Post')->create([
            'thread_id' => $thread->id,
            'user_id' => $author->id,
        ]);

        $user=factory('App\Models\User')->create();

        $user->info->ham+=20;
        $user->info->salt+=20;
        $user->info->fish+=20;
        $user->info->save();

        $this->actingAs($user, 'api');

        $data = [
            'rewardable_type' => 'thread',
            'rewardable_id' => $thread->id,
            'reward_type' => 'ham',
            'reward_value' => 3,
        ];

        $response = $this->post('api/reward', $data);
        // var_dump($response->decodeResponseJson());
        $response->assertStatus(200);
        $this->assertDatabaseHas('rewards',$data);

        $response = $this->post('api/reward', $data);
        // var_dump($response->decodeResponseJson());
        $response->assertStatus(410);// 一日内对一个内容不能重复打赏多次

        $data = [
            'rewardable_type' => 'post',
            'rewardable_id' => $post->id,
            'reward_type' => 'fish',
            'reward_value' => 5,
        ];

        $response = $this->post('api/reward', $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('rewards',$data);

        $data = [
            'rewardable_type' => 'post',
            'rewardable_id' => '0',
            'reward_type' => 'ham',
            'reward_value' => 1,
        ];

        $response = $this->post('api/reward', $data);
        $response->assertStatus(404);

        // $this->artisan('cache:clear');

        $response = $this->get('api/reward?rewardable_type=thread&rewardable_id='.$thread->id);
        $response->assertStatus(200);
    }

    /** @test */
    public function a_user_can_cancel_reward(){
        $user=factory('App\Models\User')->create();
        $user->info->salt+=20;
        $user->info->save();
        $this->actingAs($user, 'api');
        $thread = factory('App\Models\Thread')->create([
            'channel_id' => 1,
            'user_id' => $user->id,
        ]);

        $post = factory('App\Models\Post')->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);

        $data = [
            'rewardable_type' => 'thread',
            'rewardable_id' => $thread->id,
            'reward_type' => 'salt',
            'reward_value' => 3,
        ];

        $response = $this->post('api/reward', $data);
        //dd($response);
        $response->assertStatus(200);
        $this->assertDatabaseHas('rewards',$data);

        $content = $response->decodeResponseJson();
        $response = $this->delete('api/reward/'.$content['data']['id']);
        //dd($response);
        $response->assertStatus(200);

        $record = DB::table('rewards')->where('rewardable_type', 'thread')->where('rewardable_id', $thread->id)->where('reward_type', 'salt')->where('reward_value',3)->where('user_id', $user->id)->where('deleted_at', '<>', null)->first();

        $this->assertEquals($record->id, $content['data']['id']);

    }

}
