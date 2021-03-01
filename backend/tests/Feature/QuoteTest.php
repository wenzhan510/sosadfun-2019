<?php

namespace Tests\Feature;

use Tests\TestCase;

class QuoteTest extends TestCase
{

    /** @test */
    public function an_authorised_user_can_create_quote()
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        $body = $this->faker->sentence;

        $response = $this->post('api/quote', ['body' => $body])
        ->assertStatus(412);

        $user->forceFill([
            'level' => 5
        ])->save();

        $response = $this->post('api/quote', ['body' => $body])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'type',
                'attributes' => [
                    'body',
                    'is_anonymous',
                ],
                'author',
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'type' => 'quote',
                'attributes' => [
                    'body' => $body,
                    'is_anonymous' => false,
                ],
                'author' => [
                    'type' => 'user',
                    'id' => $user->id,
                ],
            ],
        ]);

        $response = $this->post('api/quote', ['body' => $body])
        ->assertStatus(422);
    }

    /** @test */
    public function an_authorised_user_can_create_quote_anonymously()//用户可匿名发表题头
    {
        $user = factory('App\Models\User')->create();
        $user->forceFill([
            'level' => 5
        ])->save();
        $this->actingAs($user, 'api');
        $body = $this->faker->sentence;
        $majia = 'niming';
        $response = $this->post('api/quote', ['body' => $body, 'is_anonymous' => 1, 'majia' => $majia])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'type',
                'attributes' => [
                    'body',
                    'is_anonymous',
                    'majia',
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'type' => 'quote',
                'attributes' => [
                    'body' => $body,
                    'is_anonymous' => true,
                    'majia' => $majia,
                ],
                'author' => [
                    'type' => 'user',
                    'id' => $user->id,
                ],
            ],
        ]);

        $response = $this->post('api/quote', ['body' => $body])
        ->assertStatus(422);
    }

    /** @test */
    public function a_guest_can_not_create_quote()
    {
        $body = $this->faker->sentence;
        $response = $this->post('api/quote', ['body' => $body])
        ->assertStatus(401);
    }

    /** @test */
    public function an_authorised_user_can_delete_quote()
    {
        $user = factory('App\Models\User')->create();
        $user->forceFill([
            'level' => 5
        ])->save();
        $quote = factory('App\Models\Quote')->create();
        $response = $this->delete('api/quote/'.$quote->id)
        ->assertStatus(401);
        $this->actingAs($user, 'api');
        $response = $this->delete('api/quote/'.$quote->id)
        ->assertStatus(403);
        $quote->user_id=$user->id;
        $quote->save();
        $response = $this->delete('api/quote/'.$quote->id)
        ->assertStatus(200);
    }
    /** @test */
    public function a_guest_can_not_delete_quote()
    {
        $quote = factory('App\Models\Quote')->create();
        $response = $this->delete('api/quote/'.$quote->id)
        ->assertStatus(401);
    }
        /** @test */
    public function a_admin_can_review_quote()
    {
        $user = factory('App\Models\User')->create();
        $quote = factory('App\Models\Quote')->create();
        $response = $this->patch('api/quote/'.$quote->id.'/review',['attitude'=>'approve'])
        ->assertStatus(401);//未登录
        $this->actingAs($user, 'api');
        $response = $this->patch('api/quote/'.$quote->id.'/review',['attitude'=>'approve'])
        ->assertStatus(403);//不是管理员
        $user->role='admin';
        $response = $this->patch('api/quote/'.$quote->id.'/review',['attitude'=>'disapprove'])
        ->assertStatus(200);
        $response = $this->patch('api/quote/'.$quote->id.'/review',['attitude'=>'disapprove'])
        ->assertStatus(404);//已经不通过的 不能再次不通过
        $response = $this->patch('api/quote/'.$quote->id.'/review',['attitude'=>'approve'])
        ->assertStatus(200);
        $response = $this->patch('api/quote/'.$quote->id.'/review',['attitude'=>'approve'])
        ->assertStatus(404);//已经通过的 不能再次通过
        $response = $this->patch('api/quote/111/review',['attitude'=>'approve'])
        ->assertStatus(404);
    }
        /** @test */
    public function anyone_can_browse_quotes()
    {
        $response = $this->get('api/quote/',['ordered'=>'latest_created'])
        ->assertStatus(200);
        $response = $this->get('api/quote/',['ordered'=>'max_fish'])
        ->assertStatus(200);
        $response = $this->get('api/quote/',['ordered'=>'latest_created','page'=>2])
        ->assertStatus(200);
        // TODO 这里可能还需要考虑到是否会显示某个题头，题头格式是否符合要求等问题

    }
            /** @test */
    public function an_authorised_user_can_browse_his_own_quotes()
    {
        $user = factory('App\Models\User')->create();
        $user->forceFill([
            'level' => 5
        ])->save();
        $response = $this->get('api/user/'.$user->id.'/quote',['ordered'=>'latest_created'])
        ->assertStatus(401);
        $this->actingAs($user, 'api');
        $response = $this->get('api/user/'.$user->id.'/quote')
        ->assertStatus(200);
        // TODO 这里需要检查，比如某用户之前创建了一个题头，能否通过这个api获得这个刚创建的内容
    }
}
