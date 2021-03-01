<?php

namespace Tests\Feature;

use Tests\TestCase;

class StatusTest extends TestCase
{
    /** @test */
    public function an_authorised_user_can_create_a_status()
    {

        $user = factory('App\Models\User')->create(['level' => 5]);
        $this->actingAs($user, 'api');

        $status_data=[
            'body' => '首先是饥荒，接着是劳苦和疾病，争执和创伤，还有破天荒可怕的死亡；他颠倒着季侯的次序，轮流地降下了，狂雪和猛火，把那些无遮无盖的人们',
        ];
        $response = $this->post('api/status', $status_data)
        ->assertStatus(200);

        $content = $response->decodeResponseJson();
        $status_id = $content['data']['status']['id'];

        $response = $this->post('api/status/', $status_data)
        ->assertStatus(409);

        $response = $this->get('api/status/'.$status_id)
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'status' => [
                    'attributes' => [
                        'no_reply' => false,
                    ],
                ],
            ],
        ]);

        $response = $this->patch('api/status/'.$status_id.'/no_reply')
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'status' => [
                    'attributes' => [
                        'no_reply' => true,
                    ],
                ],
            ],
        ]);

        $response = $this->delete('api/status/'.$status_id)
        ->assertStatus(200);

    }

    /** @test */
    public function any_one_can_see_status_index()
    {
        $response = $this->get('api/status')
        ->assertStatus(200);
    }

    /** @test */
    public function a_registered_user_can_see_follower_status()
    {
        $user = factory('App\Models\User')->create(['level' => 5]);
        $this->actingAs($user, 'api');

        $status_data=[
            'body' => '首先是饥荒，接着是劳苦和疾病，争执和创伤，还有破天荒可怕的死亡；他颠倒着季侯的次序，轮流地降下了，狂雪和猛火，把那些无遮无盖的人们',
        ];

        $response = $this->post('api/status', $status_data)
        ->assertStatus(200);

        $follower = factory('App\Models\User')->create(['level' => 5]);
        $this->actingAs($follower, 'api');

        $response = $this->get('api/follow_status')
        ->assertStatus(200)
        ->assertJsonCount(0,'data.statuses');

        $response = $this->post('api/user/'.$user->id.'/follow')
        ->assertStatus(200);

        $response = $this->get('api/follow_status')
        ->assertStatus(200)
        ->assertJsonCount(1,'data.statuses');
    }

}
