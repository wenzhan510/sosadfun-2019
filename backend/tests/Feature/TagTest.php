<?php
namespace Tests\Feature;

use Tests\TestCase;
use ConstantObjects;

class TagTest extends TestCase
{
    public $user;
    public $admin;
    public $createTagData;

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Models\User')->create();
        $this->admin = factory('App\Models\User')->create(['role' => 'admin']);
        $this->createTagData = [
			'tag_name' => '',
			'tag_explanation' => 'test test',
			'tag_type' => '人称',
			'is_bianyuan' => false,
			'is_primary' => false,
			'channel_id' => 0,
			'parent_id' => 0
		];
    }

    // if we use faker here, we may get a lot duplicate name
    private function getUniqueName()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0;$i < 10;$i++)
        {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    private function getCreateTagData()
    {
        $this->createTagData['tag_name'] = $this->getUniqueName();
        return $this->createTagData;
    }

    /**  @test */
	public function anyoneCanGetTag()
	{
        $this->get('/api/tag')
            ->assertStatus(200)
            ->assertJsonStructure([
				'code',
				'data' => [
					'tags' => [
						'book_length_tags',
						'book_status_tags',
						'sexual_orientation_tags',
						'editor_tags',
						'book_public_custom_Tags',
						'tongren_primary_tags',
						'tongren_yuanzhu_tags'
						]]]);

        // make sure you have run DefaultSettingsSeeder before
        $this->get('api/tag/1')
            ->assertStatus(200)
            ->assertJsonStructure([
				'code',
				'data' => [
					'type',
					'id',
					'attributes' => [
						'tag_name',
						'tag_explanation',
						'tag_type',
						'is_bianyuan',
						'is_primary',
						'channel_id',
						'parent_id',
						'book_count'
						]]]);
    }

    /** @test */
	public function adminCanCreateTag()
	{

        $data = $this->getCreateTagData();

        // 未登录时报错
        $this->json('POST', 'api/tag', $data)->assertStatus(401);

        // 不是管理员时报错
        $this->actingAs($this->user, 'api');
        $this->json('POST', 'api/tag', $data)->assertStatus(403);

        $this->actingAs($this->admin, 'api');
        $response = $this->json('POST', 'api/tag', $data)->assertStatus(200);
        $response = $response->decodeResponseJson() ["data"];
        $this->assertEquals($response, [
			'type' => 'tag',
			'id' => $response['id'],
			'attributes' => [
				'tag_name' => $data['tag_name'],
				'tag_explanation' => 'test test',
				'tag_type' => '人称',
				'is_bianyuan' => false,
				'is_primary' => false,
				'channel_id' => 0,
				'parent_id' => 0,
				'book_count' => 0,
				]]);

        // cannot create a tag with same name
        $this->json('POST', 'api/tag', $data)->assertStatus(422);
        // cannot create a child tag if the parent tag does not exist
        $dataChild = $this->getCreateTagData();
        $dataChild['parent_id'] = 666;
        $this->json('POST', 'api/tag', $dataChild)->assertStatus(412);

        // check db
        $tag = ConstantObjects::findTagProfile($response['id']);
        $this->assertNotNull($tag);
        $this->assertEquals($tag->tag_name, $data['tag_name']);
    }

    /** @test */
	public function adminCanEditTag()
	{
        // create tag
        $data = $this->getCreateTagData();
        $this->actingAs($this->admin, 'api');
        $response = $this->json('POST', 'api/tag', $data);
        $response->assertStatus(200);
        $id = $response->decodeResponseJson()['data']['id'];

        // update tag
        $data['tag_name'] = $this->getUniqueName();
        $this->actingAs($this->user, 'api');
        $this->patch('api/tag/' . $id, $data)->assertStatus(403);

        $this->actingAs($this->admin, 'api');
        $response = $this->patch('api/tag/' . $id, $data)->assertStatus(200);
        $response = $response->decodeResponseJson()["data"];
        $this->assertEquals($response, [
			'type' => 'tag',
			'id' => $id,
			'attributes' =>
			['tag_name' => $data['tag_name'],
			'tag_explanation' => 'test test',
			'tag_type' => '人称',
			'is_bianyuan' => false,
			'is_primary' => false,
			'channel_id' => 0,
			'parent_id' => 0,
			'book_count' => 0,
			]]);

        // check db
        $tag = ConstantObjects::findTagProfile($id);
        $this->assertNotNull($tag);
        $this->assertEquals($tag->tag_name, $data['tag_name']);
    }

    /** @test */
	public function adminCanDeleteTag()
	{
        // create parent tag
        $data = $this->getCreateTagData();
        $this->actingAs($this->admin, 'api');
        $response = $this->json('POST', 'api/tag', $data)->assertStatus(200);
        $id = $response->decodeResponseJson()['data']['id'];

        // create child tag
        $dataChild = $this->getCreateTagData();
        $dataChild['parent_id'] = $id;
        $response = $this->json('POST', 'api/tag', $dataChild)->assertStatus(200);
        $idChild = $response->decodeResponseJson()['data']['id'];

        // delete parent
        $this->actingAs($this->user, 'api');
        $this->delete('api/tag/' . $id)->assertStatus(403);
        $this->actingAs($this->admin, 'api');
        // must delete child tag first
        $this->delete('api/tag/' . $id)->assertStatus(412);

        // delete child then parent
        $this->delete('api/tag/' . $idChild)->assertStatus(200);
        $this->delete('api/tag/' . $id)->assertStatus(200);

        // check db
        $tag = ConstantObjects::findTagProfile($id);
        $this->assertNull($tag);
        $this->assertSoftDeleted('tags', ['id' => $id]);
        $tagChild = ConstantObjects::findTagProfile($idChild);
        $this->assertNull($tagChild);
        $this->assertSoftDeleted('tags', ['id' => $idChild]);

    }
}

