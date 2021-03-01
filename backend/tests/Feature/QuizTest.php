<?php

namespace Tests\Feature;

use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\RegistrationApplication;
use App\Models\User;
use App\Models\UserInfo;
use App\Sosadfun\Traits\QuizObjectTraits;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuizTest extends TestCase
{

    /**
     * @test
     */
    public function get_quiz_test()
    {

        // 未登录时报错
        $this->get('api/quiz/get_quiz')
            ->assertStatus(401);

        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        // 不存在对应level时报错
        $this->get('api/quiz/get_quiz?level=100')
            ->assertStatus(404);

        // 验证返回题目和格式
        $response = $this->get('api/quiz/get_quiz');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'data' => [
                    'quizzes' => [
                        '*' => [
                            'id',
                            'type',
                            'attributes' => [
                                'body',
                                'hint',
                                'options' => [
                                    '*' => [
                                        'type',
                                        'id',
                                        'attributes'
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
            ]);

        // 验证是否取到了正确的题目
        $response=$response->decodeResponseJson();
        $id = $user->id;
        $quizzes_questions = UserInfo::find($id)->quiz_questions;
        $returned_quizzes_questions = [];
        $this->assertCount(config('constants.quiz_test_number',5),$response["data"]["quizzes"]);
        foreach ($response["data"]["quizzes"] as $quiz) {
            $returned_quizzes_questions[] = $quiz["id"];
            $this->assertDatabaseHas('quizzes', ["id" => $quiz["id"]]);
            // 这里是验证是否所有选项都被选出来了
            $options_from_database = QuizOption::where('quiz_id',$quiz['id'])->orderBy('id')->pluck('id')->toArray();
            $options_from_returned = [];
            foreach ($quiz["attributes"]["options"] as $option) {
                $options_from_returned[] = $option['id'];
            }
            $this->assertEquals($options_from_database,$options_from_returned);
        }
        $this->assertEquals($quizzes_questions,implode(",",$returned_quizzes_questions));
    }

    /**
     * @test
     */
    public function submit_quiz_test()
    {
        // 未登录时报错
        $this->post('api/quiz/submit_quiz')
            ->assertStatus(401);

        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        // 拒绝缺少quizzes的请求
        $this->post('api/quiz/submit_quiz')
            ->assertStatus(422);

        $this->get('api/quiz/get_quiz?level=1');
        $user_info = UserInfo::find($user->id);

        // 拒绝提交题目不匹配的请求
        $quiz_questions = array_map('intval',explode(',',$user_info->quiz_questions));
        $data['quizzes'] = [
            ['id' => 1, 'answer' => '1']
        ];
        $this->post('api/quiz/submit_quiz', $data)
            ->assertStatus(444);

        // 成功提交，答错数量太多
        unset($data['quizzes']);
        // 直接填充错误答案。填充方式为：如果正确选项数量大于1个，则只填充第一个选项；如果正确选项数量只有1个，则填充所有选项
        foreach ($quiz_questions as $quiz_question) {
            $possible_answers = QuizObjectTraits::find_quiz_set($quiz_question)->quiz_options;
            $correct_answer = $possible_answers->where('is_correct',true)->pluck('id')->toArray();
            if (count($correct_answer) > 1) {
                $data['quizzes'][] = ['id' => $quiz_question, 'answer' => $correct_answer[0]];
            } else {
                $data['quizzes'][] = ['id' => $quiz_question, 'answer' => implode(',',$possible_answers->pluck('id')->toArray())];
            }

        }

        $result = $this->post('api/quiz/submit_quiz', $data)
            ->assertStatus(200)->assertJsonStructure([
                'code',
                'data' => [
                    'id',
                    'type',
                    'attribute' => [
                        'is_passed',
                        'is_quiz_level_up',
                        'current_quiz_level'
                    ],
                    'quizzes' => [
                        '*' => [
                            'type',
                            'id',
                            'attributes' => [
                                'body',
                                'hint',
                                'correct_answer',
                                'options' => [
                                    '*' => [
                                        'type',
                                        'id',
                                        'attributes' => [
                                            'body',
                                            'explanation'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

        // 验证返回的答题正误
        $result->assertJsonFragment([
            'is_passed' => false,
            'is_quiz_level_up' => false,
            'current_quiz_level' => 1
        ]);

        // 验证quiz_question有没有被清空
        $this->assertNull(UserInfo::find($user->id)->quiz_questions);

        // 成功提交，答题全对
        // 先重新刷新一次model
        UserInfo::find($user->id)->update(['quiz_questions' =>'']);

        $this->get('api/quiz/get_quiz?level=1');
        unset($data['quizzes']);
        // 直接填充正确答案
        foreach ($quiz_questions as $quiz_question) {
            $data['quizzes'][] = ['id' => $quiz_question, 'answer' => implode(',',QuizObjectTraits::find_quiz_set($quiz_question)->quiz_options->where('is_correct',true)->pluck('id')->toArray())];
        }
        $response = $this->post('api/quiz/submit_quiz', $data);
        $response->assertStatus(200)->assertExactJson([
            'code' => 200,
            'data' => [
                'id' => $user->id,
                'type' => 'quiz_result',
                'attribute' => [
                    'is_passed' => true,
                    'is_quiz_level_up' => true,
                    'current_quiz_level' => 1
                ]
            ]
        ])->assertDontSee('quizzes'); // 全对时不用出现'quizzes'字段

        // 验证quiz_question有没有被清空
        $this->assertNull(UserInfo::find($user->id)->quiz_questions);

        // 验证用户数据库是否已被更新
        $new_user = User::find($user->id);
        $this->assertGreaterThanOrEqual(1,$new_user->level);
        $this->assertEquals(2, $new_user->quiz_level);
    }

    /**
     * @test
     */
    public function get_all_quiz_test()
    {
        // 未登录时报错
        $this->get('api/quiz')
            ->assertStatus(401);

        // 不是管理员时报错
        $user0 = factory('App\Models\User')->create();
        $this->actingAs($user0, 'api');
        $this->get('api/quiz')
            ->assertStatus(403);

        // 成功
        $user = factory('App\Models\User')->create(['role'=>'admin']);
        $this->actingAs($user, 'api');
        $this->get('api/quiz')->assertStatus(200)
            ->assertJsonStructure([
                "code",
                "data" => [
                    "quizzes" => [
                        '*' => [
                            "type",
                            "id",
                            "attributes" => [
                                "body",
                                "hint",
                                "type",
                                "is_online",
                                "level",
                                "quiz_count",
                                "correct_count",
                                "edited_at"
                            ]
                        ],
                    ],
                    "paginate" => [
                        "total",
                        "count",
                        "per_page",
                        "current_page",
                        "total_pages"
                    ]
                ]
            ]);

        // 测试筛选
        $response = $this->get('api/quiz?quiz_level=1,2&quiz_type=level_up')->assertStatus(200);
        $response->assertJsonStructure([
            "code",
            "data" => [
                "quizzes" => [
                    '*' => [
                        "type",
                        "id",
                        "attributes" => [
                            "body",
                            "hint",
                            "type",
                            "is_online",
                            "level",
                            "quiz_count",
                            "correct_count",
                            "edited_at"
                        ]
                    ],
                ],
                "paginate" => [
                    "total",
                    "count",
                    "per_page",
                    "current_page",
                    "total_pages"
                ]
            ]
        ]);
        $response->assertJsonMissingExact(['type' => 'essay']);
        $response->assertJsonMissingExact(['type' => 'registration']);
        $response->assertJsonMissingExact(['level' => 0]);
    }

    /**
     * @test
     */
    public function create_quiz_test()
    {
        // 未登录时报错
        $this->post('api/quiz')
            ->assertStatus(401);

        // 不是管理员时报错
        $user0 = factory('App\Models\User')->create();
        $this->actingAs($user0, 'api');
        $this->post('api/quiz')
            ->assertStatus(403);

        // 没有quizzes字段
        $user = factory('App\Models\User')->create(['role'=>'admin']);
        $this->actingAs($user, 'api');
        $this->post('api/quiz')->assertStatus(422);

        // type不能为未注册在constants中的类型
        $data['quizzes'] = [
            [
                "type" => "none",
                "hint" => "提示",
                "level" => 1,
                "is_online" => false
            ]
        ];
        $this->post('api/quiz',$data)->assertStatus(422);

        // quiz不能没有body
        $data['quizzes'] = [
            [
                "type" => "level_up",
                "hint" => "提示",
                "level" => 1,
                "is_online" => false
            ]
        ];
        $this->post('api/quiz',$data)->assertStatus(422);

        // 选择题不能没有选项
        $data['quizzes'] = [
            [
                "type" => "level_up",
                "body" => "选择题题干",
                "hint" => "提示",
                "level" => 1,
                "is_online" => false
            ]
        ];
        $this->post('api/quiz',$data)->assertStatus(422);

        // 没有正确答案的选择题会被failed
        $data['quizzes'] = [
            [
                "type" => "level_up",
                "body" => "选择题题干",
                "hint" => "提示",
                "level" => 1,
                "is_online" => false,
                "option" => [
                    [
                        "body" => "错误选项A",
                        "explanation" => "错误解释A"
                    ],
                    [
                        "body" => "错误选项B",
                        "explanation" => "错误解释C"
                    ]
                ]
            ]
        ];
        $response = $this->post('api/quiz',$data)->assertStatus(200);
        $response = $response->decodeResponseJson()["data"];
        $this->assertEmpty($response['quizzes']);
        $this->assertEquals([0],$response['failed']);


        // 成功提交quiz
        $type = 'level_up';
        $body = '选择题题干';
        $hint = '提示';
        $level = 1;
        $is_online = false;
        $option = [
            [
                "body" => "错误选项A",
                "explanation" => "错误解释A",
                "is_correct" => false
            ],
            [
                "body" => "正确选项B",
                "explanation" => "正确解释B",
                "is_correct" => true
            ],
            [
                "body" => "错误选项C",
                "explanation" => "错误解释C",
                "is_correct" => false
            ],
        ];
        $data['quizzes'] = [
            [
                "type" => $type,
                "body" => $body,
                "hint" => $hint,
                "level" => $level,
                "is_online" => $is_online,
                "option" => $option
            ]
        ];
        $response = $this->post('api/quiz',$data)->assertStatus(200);
        $response = $response->decodeResponseJson()["data"];
        $this->assertEmpty($response['failed']);
        $this->assertCount(1,$response["quizzes"]);
        $quiz = $response['quizzes'][0]['attributes'];
        $this->assertEquals($type, $quiz['type']);
        $this->assertEquals($body, $quiz['body']);
        $this->assertEquals($hint, $quiz['hint']);
        $this->assertEquals($level, $quiz['level']);
        $this->assertEquals($is_online, $quiz['is_online']);
        $this->assertEquals($option[0]['body'], $quiz['options'][0]['attributes']['body']);
        $this->assertEquals($option[0]['explanation'], $quiz['options'][0]['attributes']['explanation']);
        $this->assertEquals($option[0]['is_correct'], $quiz['options'][0]['attributes']['is_correct']);
        $this->assertEquals($option[1]['body'], $quiz['options'][1]['attributes']['body']);
        $this->assertEquals($option[1]['explanation'], $quiz['options'][1]['attributes']['explanation']);
        $this->assertEquals($option[1]['is_correct'], $quiz['options'][1]['attributes']['is_correct']);
        $this->assertEquals($option[2]['body'], $quiz['options'][2]['attributes']['body']);
        $this->assertEquals($option[2]['explanation'], $quiz['options'][2]['attributes']['explanation']);
        $this->assertEquals($option[2]['is_correct'], $quiz['options'][2]['attributes']['is_correct']);

        // 验证数据库是否更新
        $last = Quiz::latest()->first();
        $this->assertEquals($body, $last['body']);
        $last_option = $last->quiz_options;
        $this->assertEquals($option[2]['body'],$last_option->last()['body']);


        // 成功提交essay
        $type = 'essay';
        $body = 'essay题干';
        $hint = 'essay提示';
        unset($data);
        $data['quizzes'] = [
            [
                "type" => $type,
                "body" => $body,
                "hint" => $hint,
            ]
        ];
        $response = $this->post('api/quiz',$data)->assertStatus(200);
        $response = $response->decodeResponseJson()["data"];
        $this->assertEmpty($response['failed']);
        $this->assertCount(1,$response["quizzes"]);
        $quiz = $response['quizzes'][0]['attributes'];
        $this->assertEquals($type, $quiz['type']);
        $this->assertEquals($body, $quiz['body']);
        $this->assertEquals($hint, $quiz['hint']);

        // 验证数据库是否更新
        $last = Quiz::orderBy('id','desc')->limit(1)->get()[0];
        $this->assertEquals($body, $last['body']);
    }

    /**
     * @test
     */
    public function update_quiz_test()
    {
        // 先插一道题
        factory('App\Models\Quiz')->create(['type' => 'level_up']);

        // 未登录时报错
        $this->patch('api/quiz/1')
            ->assertStatus(401);

        // 不是管理员时报错
        $user0 = factory('App\Models\User')->create();
        $this->actingAs($user0, 'api');
        $this->patch('api/quiz/1')
            ->assertStatus(403);

        // 没有quizzes字段
        $user = factory('App\Models\User')->create(['role'=>'admin']);
        $this->actingAs($user, 'api');
        $this->patch('api/quiz/1')->assertStatus(422);

        $last_quiz = Quiz::latest()->first();
        $last_id = $last_quiz->id;

        // 成功提交quiz
        $type = $last_quiz->type;
        $body = '新的选择题题干';
        $hint = '新的提示';
        $level = 2;
        $is_online = true;
        $option = [
            [
                "body" => "新的正确选项A",
                "explanation" => "新的正确解释A",
                "is_correct" => true
            ],
            [
                "body" => "新的错误选项B",
                "explanation" => "新的错误解释B",
                "is_correct" => false
            ],
            [
                "body" => "新的正确选项C",
                "explanation" => "新的正确解释C",
                "is_correct" => true
            ],
        ];
        $data = [
            "type" => $type,
            "body" => $body,
            "hint" => $hint,
            "level" => $level,
            "is_online" => $is_online,
            "option" => $option
        ];
        $response = $this->patch("api/quiz/$last_id",$data)->assertStatus(200);
        $response = $response->decodeResponseJson()["data"];
        $quiz = $response['attributes'];
        $this->assertEquals($type, $quiz['type']);
        $this->assertEquals($body, $quiz['body']);
        $this->assertEquals($hint, $quiz['hint']);
        $this->assertEquals($level, $quiz['level']);
        $this->assertEquals($is_online, $quiz['is_online']);
        $this->assertEquals($option[0]['body'], $quiz['options'][0]['attributes']['body']);
        $this->assertEquals($option[0]['explanation'], $quiz['options'][0]['attributes']['explanation']);
        $this->assertEquals($option[0]['is_correct'], $quiz['options'][0]['attributes']['is_correct']);
        $this->assertEquals($option[1]['body'], $quiz['options'][1]['attributes']['body']);
        $this->assertEquals($option[1]['explanation'], $quiz['options'][1]['attributes']['explanation']);
        $this->assertEquals($option[1]['is_correct'], $quiz['options'][1]['attributes']['is_correct']);
        $this->assertEquals($option[2]['body'], $quiz['options'][2]['attributes']['body']);
        $this->assertEquals($option[2]['explanation'], $quiz['options'][2]['attributes']['explanation']);
        $this->assertEquals($option[2]['is_correct'], $quiz['options'][2]['attributes']['is_correct']);

        // 验证数据库是否更新
        $last = Quiz::find($last_quiz->id);
        $this->assertEquals($body, $last['body']);
        $last_option = $last->quiz_options;
        $this->assertEquals($option[2]['body'],$last_option->last()['body']);

    }
}
