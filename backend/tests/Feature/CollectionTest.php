<?php

namespace Tests\Feature;

use Tests\TestCase;

class CollectionTest extends TestCase
{
    /** @test */
    public function an_authorised_user_can_collect_a_thread_and_update_and_delete_it()
    {

        $author = factory('App\Models\User')->create();

        $thread = factory('App\Models\Thread')->create([
            'channel_id' => 1,
            'user_id' => $author->id,
            'is_public' => true,
        ]);

        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');
        //增
        $response = $this->post('api/thread/'.$thread->id.'/collect')
        ->assertJson([
            'code' => 200,
            'data' => [
                'type' => 'collection',
                'attributes' => [
                    'user_id' => $user->id,
                    'thread_id' => $thread->id,
                ],
            ],
        ]);
        $content = $response->decodeResponseJson();
        $data = [
            'keep_updated' => false,
        ];
        $response = $this->patch('api/collection/'.$content['data']['id'], $data)
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'attributes' => $data,
            ],
        ]);
        $data = [
            'keep_updated' => true,
        ];
        $response = $this->patch('api/collection/'.$content['data']['id'], $data)
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'attributes' => $data,
            ],
        ]);
        $content = $response->decodeResponseJson();
        $response = $this->delete('api/collection/'.$content['data']['id'], $data)
        ->assertStatus(200);
    }

    /** @test */
    public function an_authorised_user_can_see_his_collections()
    {
        $author = \App\Models\User::inRandomOrder()->first();

        $thread1 = factory('App\Models\Thread')->create([
            'channel_id' => 1,
            'user_id' => $author->id,
            'is_public' => true,
        ]);

        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');
        $response = $this->post('api/thread/'.$thread1->id.'/collect');
        $response = $this->get('api/user/'.$user->id.'/collection')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'collections',
            ],
        ])->assertJson([
            'data' => [
                'collections' => [
                    [
                        'thread' => [
                            'id' => $thread1->id,
                        ]
                    ],
                ],
            ]
        ]);

    }

    /** @test */
    public function an_authorised_user_can_create_and_modify_a_collection_group()
    {
        $user = factory('App\Models\User')->create([
            'level' => 3,
        ]);
        $this->actingAs($user, 'api');
        //增
        $data = [
            'name' => '收藏夹1号',
            'order_by' => 1,
        ];
        $response = $this->post('api/collection_group', $data)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'collection_groups' => [
                    [
                        'id',
                        'type',
                        'attributes' =>[
                            'name',
                            'order_by',
                        ],
                    ],
                ],
            ],
        ])->assertJson([
            'code' => 200,
            'data' => [
                'collection_groups' => [
                    [
                        'type' => 'collection_group',
                        'attributes' =>[
                            'name' => $data['name'],
                            'order_by' => $data['order_by'],
                        ],
                    ],
                ],
            ],
        ]);

        $content = $response->decodeResponseJson();
        $collection_group_id = $content['data']['collection_groups'][0]['id'];

        $data = [
            'name' => '收藏夹修改',
            'order_by' => 2,
        ];

        $response = $this->patch('api/collection_group/'.$collection_group_id, $data)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'collection_groups' => [
                    [
                        'id',
                        'type',
                        'attributes' =>[
                            'name',
                            'order_by',
                        ],
                    ],
                ],
            ],
        ])->assertJson([
            'code' => 200,
            'data' => [
                'collection_groups' => [
                    [
                        'type' => 'collection_group',
                        'attributes' =>[
                            'name' => $data['name'],
                            'order_by' => $data['order_by'],
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this->delete('api/collection_group/'.$collection_group_id)
        ->assertStatus(200);

    }



}
