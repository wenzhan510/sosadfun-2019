<?php

namespace Tests\Feature;

use Tests\TestCase;

class ThreadTest extends TestCase
{

    /** @test */
    public function an_authorised_user_can_create_thread()
    {
        $user = factory('App\Models\User')->create([
            'level' => 5,
            'quiz_level' => 3,
        ]);

        $this->actingAs($user, 'api');

        //为channel=1 原创channel插入thread
        $data = [
            'channel_id' => 6,
            'title' => 'test_thread1',
            'brief' => 'brief1',
            'body' => 'body'.$this->faker->paragraph,
            'tags' => [27],
        ];

        $response = $this->post('api/thread/', $data);
        $response->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'title',
                    'brief',
                    'body',
                    //这里还有待补充
                ],
            ],
        ]);

        $response = $this->post('api/thread/', $data);
        $response->assertStatus(410);

    }

    /** @test */
    public function an_authorised_user_can_edit_a_thread()
    {
        $user = factory('App\Models\User')->create([
            'level' => 5,
            'quiz_level' => 3,
        ]);

        $this->actingAs($user, 'api');

        $data = [
            'channel_id' => 6,
            'title' => 'test_thread3',
            'brief' => 'brief3',
            'body' => 'body'.$this->faker->paragraph,
            'tags' => [30,31],
        ];
        $response = $this->post('api/thread/', $data);
        $response->assertStatus(200);

        $content = $response->decodeResponseJson();

        $data = [
            'title' => 'modified_title',
            'brief' => 'modified_brief',
            'body' => 'modified_body',
            'tags' => [34],
        ];

        $response = $this->patch('api/thread/'.$content['data']['id'], $data);
        $response->assertStatus(200);

    }
    //TODO 下面还应该测试：
    //用户不能修改别人创建的thread，
    //每一项可选值，比如说is_anonymous这样的值，是否得到了合理的update并且在模型中反馈出来
    //应该是很繁琐的。。
    //以及修改tag等……
}
