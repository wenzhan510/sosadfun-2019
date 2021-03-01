<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Vote;
use DB;


class VoteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    public function test_a_user_can_create_a_vote_with_attitude()
    {
        $user = factory('App\Models\User')->create();

        $this->actingAs($user, 'api');
        $quote = factory('App\Models\Quote')->create(['user_id' => $user->id]);

        $data = [
            'votable_type' => 'quote',
            'votable_id' => $quote->id,
            'attitude_type' => 'upvote',
        ];

        $response = $this->post('api/vote', $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('votes',$data);

        $response = $this->post('api/vote', $data);
        $response->assertStatus(409);//重复投票

        $data = [
            'votable_type' => 'quote',
            'votable_id' => $quote->id,
            'attitude_type' => 'downvote',
        ];

        $response = $this->post('api/vote', $data);
        $response->assertStatus(411);//检查无效投票，不能即赞又踩
        $this->assertDatabaseMissing('votes',$data);

        $data = [
            'votable_type' => 'status',
            'votable_id' => '0',
            'attitude_type' => 'upvote',
        ];

        $response = $this->post('api/vote', $data);
        $response->assertStatus(404);
        $response = $this->get('api/vote?votable_type=status&votable_id=2');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_upvote_has_userid(){
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');
        $quote = factory('App\Models\Quote')->create(['user_id' => $user->id]);

        $data = [
            'votable_type' => 'quote',
            'votable_id' => $quote->id,
            'attitude_type' => 'upvote',
        ];

        $response = $this->post('api/vote', $data);
        $response->assertStatus(200)
        ->assertSee('author');
        // TODO: 这里需要重新写一下如何看见model resource。之前的版本里，oteResource没有遵循和前端约定的数值返回格式。

        $this->assertDatabaseHas('votes',$data);


    }

    // TODO: 重写测试管理员相关能看见的vote内容
    // /** @test */
    // public function test_other_votes_have_no_userid(){
    //     $user = factory('App\Models\User')->create();
    //     $this->actingAs($user, 'api');
    //     $quote = factory('App\Models\Quote')->create(['user_id' => $user->id]);
    //
    //     $data = [
    //         'votable_type' => 'quote',
    //         'votable_id' => $quote->id,
    //         'attitude_type' => 'downvote',
    //     ];
    //
    //     $response = $this->post('api/vote', $data);
    //     $response->assertStatus(200)
    //     ->assertJsonMissing(['user_id' => $user->id,]);
    //
    //     $this->assertDatabaseHas('votes',$data);
    //
    //
    // }
    // /** @test */
    // public function test_admin_can_see_userid(){
    //     $user = factory('App\Models\User')->create();
    //     $this->actingAs($user, 'api');
    //     $quote = factory('App\Models\Quote')->create(['user_id' => $user->id]);
    //     $data = [
    //         'votable_type' => 'quote',
    //         'votable_id' => $quote->id,
    //         'attitude_type' => 'downvote',
    //     ];
    //     $response = $this->post('api/vote', $data);
    //
    //     $admin = factory('App\Models\User')->create();
    //     DB::table('role_user')->insert([
    //         'user_id' => $admin->id,
    //         'role' => 'admin',
    //     ]);
    //     $this->actingAs($admin, 'api');
    //     $response = $this->get('api/vote?votable_type=Quote&votable_id='.$quote->id);
    //     $response->assertStatus(200)
    //     ->assertSee('user_id');
    //
    // }
    /** @test */
    public function test_a_user_can_cancel_vote(){
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');
        $quote = factory('App\Models\Quote')->create(['user_id' => $user->id]);

        $data = [
            'votable_type' => 'quote',
            'votable_id' => $quote->id,
            'attitude_type' => 'upvote',
        ];

        $response = $this->post('api/vote', $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('votes',$data);

        $content = $response->decodeResponseJson();
        $response = $this->delete('api/vote/'.$content['data']['id']);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('votes',$data);

    }
}
