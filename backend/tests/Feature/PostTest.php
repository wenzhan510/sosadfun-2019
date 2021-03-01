<?php

namespace Tests\Feature;

use Tests\TestCase;

class PostTest extends TestCase
{
    /** @test */
    public function an_authorised_user_can_create_a_post()
    {

        $user = factory('App\Models\User')->create([
            'level' => 5,
            'quiz_level' =>3,
        ]);
        $this->actingAs($user, 'api');

        $thread = factory('App\Models\Thread')->create([
            'channel_id' => 1,
            'user_id' => $user->id,
            'is_bianyuan' => false,
        ]);
        $chapter_post = factory('App\Models\Post')->create([
            'thread_id' => $thread->id,
            'user_id' => $thread->user_id,
            'type' => 'chapter',
        ]);
        $chapter_info = factory('App\Models\PostInfo')->create([
            'post_id' => $chapter_post->id,
        ]);

        $user2 = factory('App\Models\User')->create([
            'level' => 3,
            'quiz_level' =>3,
        ]);
        $this->actingAs($user2, 'api');

        $post_data=[
            'type' => 'post',
            'body' => '首先是饥荒，接着是劳苦和疾病，争执和创伤，还有破天荒可怕的死亡；他颠倒着季侯的次序，轮流地降下了，狂雪和猛火，把那些无遮无盖的人们',
            'brief' => '首先是饥荒，接着是劳苦和疾病，争执和创伤',
            'is_anonymous' => 'yes',
            'majia' => 'majia',
        ];
        $response = $this->post('api/thread/'.$thread->id.'/post/', $post_data)
        ->assertStatus(200)
        ->assertJson([
            'code' => 200,
            'data' => [
                'type'=> 'post',
                'attributes' => [
                    'post_type' => "post",
                    'thread_id' => $thread->id,
                    'body' => $post_data['body'],
                    'brief' => $post_data['brief'],
                    'is_anonymous' => true,
                    'majia' => $post_data['majia'],
                ],
                'author' => [
                    'type'=> 'user',
                    'id'=> $user2->id,
                    'attributes' => [
                        'name' =>$user2->name,
                    ],
                ]
            ],
        ]);

        $response = $this->post('api/thread/'.$thread->id.'/post/', $post_data)
        ->assertStatus(409);

        $post_data2=[
            'type' => 'post',
            'body' => '首先是饥荒，接着是劳苦和疾病，争执和创伤，还有破天荒可怕的死亡；他颠倒着季侯的次序，轮流地降下了，狂雪和猛火，把那些无遮无盖的人们2',
            'brief' => '首先是饥荒，接着是劳苦和疾病，争执和创伤',
            'reply_to_id' => $chapter_post->id,
            'reply_to_brief' => $chapter_post->brief,
            'reply_to_position' => 0,
            'is_comment'=>true,
        ];
        $response = $this->post('api/thread/'.$thread->id.'/post/', $post_data2)
        ->assertStatus(200)
        ->assertJson([
            'code' => 200,
            'data' => [
                'type'=> 'post',
                'attributes' => [
                    'post_type' => 'post',
                    'thread_id' => $thread->id,
                    'body' => $post_data2['body'],
                    'brief' => $post_data2['brief'],
                    'is_anonymous' => false,
                    'reply_to_id' => $post_data2['reply_to_id'],
                    'reply_to_brief' => $post_data2['reply_to_brief'],
                    'reply_to_position' => $post_data2['reply_to_position'],
                    'is_comment' => true,
                ],
                'author' => [
                    'type'=> 'user',
                    'id'=> $user2->id,
                    'attributes' => [
                        'name' =>$user2->name,
                    ],
                ]
            ],
        ]);

        // $content = $response->decodeResponseJson();
        // $post2 = $content['data'];

        $post_data3=[
            'type' => 'post',
            'body' => '首先是饥荒，接着是劳苦和疾病，争执和创伤，还有破天荒可怕的死亡；他颠倒着季侯的次序，轮流地降下了，狂雪和猛火，把那些无遮无盖的人们3',
            'brief' => '首先是饥荒，接着是劳苦和疾病，争执和创伤',
            'reply_to_id' => $chapter_post->id,
            'reply_to_brief' => $chapter_post->brief,
            'reply_to_position' => 10,
            'is_comment' => true,
        ];

        $response = $this->post('api/thread/'.$thread->id.'/post/', $post_data3)
        ->assertStatus(200)
        ->assertJson([
            'code' => 200,
            'data' => [
                'type'=> 'post',
                'attributes' => [
                    'post_type' => 'post',
                    'thread_id' => $thread->id,
                    'body' => $post_data3['body'],
                    'brief' => $post_data3['brief'],
                    'is_anonymous' => false,
                    'reply_to_id' => $post_data3['reply_to_id'],
                    'reply_to_brief' => $post_data3['reply_to_brief'],
                    'reply_to_position' => $post_data3['reply_to_position'],
                    'is_comment' => true,
                ],
                'author' => [
                    'type'=> 'user',
                    'id'=> $user2->id,
                    'attributes' => [
                        'name' =>$user2->name,
                    ],
                ]
            ],
        ]);

        $response = $this->post('api/thread/'.$thread->id.'/post/', $post_data3)
        ->assertStatus(409);

    }


    /** @test */
    public function an_authorised_user_can_update_own_post()
    {

        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        $thread = factory('App\Models\Thread')->create([
            'channel_id' => 1,
            'user_id' => $user->id,
        ]);
        $user2 = factory('App\Models\User')->create();
        $this->actingAs($user2, 'api');

        $post_data=[
            'type' => 'post',
            'body' => '首先是饥荒，接着是劳苦和疾病，争执和创伤，还有破天荒可怕的死亡；他颠倒着季侯的次序，轮流地降下了，狂雪和猛火，把那些无遮无盖的人们',
            'brief' => '首先是饥荒，接着是劳苦和疾病，争执和创伤',
        ];
        $response = $this->post('api/thread/'.$thread->id.'/post/', $post_data)
        ->assertStatus(200);

        $content = $response->decodeResponseJson();

        $data = [
            'title' => 'modifiedchapt',
            'brief' => 'modifiedchapt',
            'body' => 'modifiedchapt',
            'type' => 'chapter',
            'annotation' => 'modified_annotation',
            'warning' => 'modified_warning',
        ];

        $response = $this->patch('api/post/'.$content['data']['id'], $data)

        ->assertStatus(200);

    }
}
