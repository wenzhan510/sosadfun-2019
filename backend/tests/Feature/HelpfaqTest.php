<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Helpfaq;

class HelpfaqTest extends TestCase
{
    /** @test */
    public function anyone_can_view_faq()
    {

        $response = $this->get('api/helpfaq')
            ->assertStatus(200)
            ->assertJsonStructure([
                "code",
                "data" => [
                    '*' => [
                        "type",
                        "id",
                        "attributes" => [
                            "key",
                            "question",
                            "answer",
                        ]
                    ],
                ]
            ]);
    }

    /** @test */
    public function admin_can_create_a_faq()
    {
        $data = [
            "key" => "1-4",
            "question" => "能否修改用户名？",
            "answer" => "目前不支持修改个人用户名"
        ];
        
        // 未登录时报错
        $this->post('api/helpfaq', $data)
            ->assertStatus(401);

        // 不是管理员时报错
        $user0 = factory('App\Models\User')->create();
        $this->actingAs($user0, 'api');
        $this->post('api/helpfaq', $data)
            ->assertStatus(403);

        // 成功
        $user = factory('App\Models\User')->create(['role'=>'admin']);
        $this->actingAs($user, 'api');
        $this->post('api/helpfaq', $data)->assertStatus(200)
            ->assertJsonStructure([
                "code",
                "data" => [
                    "type",
                    "id",
                    "attributes" => [
                        "key",
                        "question",
                        "answer"
                    ]
                ] 
            ]);
            
        
        // you can only create faq with valid key
        $data["key"] = "123";
        $this->json('POST', 'api/helpfaq', $data)->assertStatus(422);
        $data["key"] = "";
        $this->json('POST', 'api/helpfaq', $data)->assertStatus(422);
    }

    /** @test */
    public function admin_can_edit_a_faq()
    {
        $oldfaq = [
            "key" => "1-4",
            "question" => "能否修改用户名？",
            "answer" => "目前不支持修改个人用户名"
        ];

        $newfaq = [
            "question" => "能否修改用户名？",
            "answer" => "现在可以啦。"
        ];

        $admin = factory('App\Models\User')->create(['role'=>'admin']);
        $this->actingAs($admin, 'api');
        $response = $this->post('api/helpfaq', $oldfaq);
        $response->assertStatus(200);
        $content = $response->decodeResponseJson();

        $id = $content['data']['id'];
        $key = $content['data']['attributes']['key'];

        // 不是管理员时报错
        $user0 = factory('App\Models\User')->create();
        $this->actingAs($user0, 'api');
        $this->patch('api/helpfaq/'.$id, $newfaq)
            ->assertStatus(403);

        // 成功
        $this->actingAs($admin, 'api');  
        $response = $this->patch('api/helpfaq/'.$id, $newfaq)
            ->assertStatus(200)
            ->assertJsonStructure([
                "code",
                "data" => [
                    "type",
                    "id",
                    "attributes" => [
                        "key",
                        "question",
                        "answer"
                    ]
                ] 
            ]);
        $response = $response->decodeResponseJson()["data"];
        $this->assertEquals("faq", $response["type"]);
        $this->assertEquals($id, $response["id"]);
        $this->assertEquals($key, $response["attributes"]["key"]);
        $this->assertEquals($newfaq["question"], $response["attributes"]["question"]);
        $this->assertEquals($newfaq["answer"], $response["attributes"]["answer"]);

        // 验证数据库是否更新
        $faq = Helpfaq::find($id);
        $this->assertEquals($newfaq["answer"], $faq["answer"]);
    }

    /** @test */
    public function admin_can_delete_faq(){
        $faq = [
            "key" => "1-4",
            "question" => "能否修改用户名？",
            "answer" => "目前不支持修改个人用户名"
        ];

        $admin = factory('App\Models\User')->create(['role'=>'admin']);
        $this->actingAs($admin, 'api');
        $response = $this->post('api/helpfaq', $faq);
        $response->assertStatus(200);
        $content = $response->decodeResponseJson();

        $id = $content['data']['id'];
        $key = $content['data']['attributes']['key'];

        // 不是管理员时报错
        $user0 = factory('App\Models\User')->create();
        $this->actingAs($user0, 'api');
        $this->delete('api/helpfaq/'.$id)
            ->assertStatus(403);

        // 成功
        $this->actingAs($admin, 'api');  
        $response = $this->delete('api/helpfaq/'.$id)
            ->assertStatus(200);

        $this->assertDatabaseMissing('helpfaqs', [
            'id' => $id
        ]);

        // you cannot delete a faq that does not exist
        $this->delete('api/helpfaq/'.$id)
        ->assertStatus(404);

        // you cannot update a faq that does not exist
        $this->patch('api/helpfaq/'.$id, $faq)
            ->assertStatus(404);
        
    }
}
