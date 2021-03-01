<?php

return [
    //0级可以看所有编推文

    'level_up' => [
       1 => [//1级可看边限相关的讨论贴内容，可以发文。
          'salt' => 20,
       ],
       2 => [//2级可看非边限的边限章，可以留评论
          'salt' => 50,
          'fish' => 10,
          'quiz_level' => 1,
       ],
       3 => [//3级可看普通边限文，看边限目录
           'salt' => 100,
           'fish' => 25,
           'quiz_level' => 2,
       ],
       4 => [//4级可以发布主题贴，发布动态
           'salt' => 500,
           'fish' => 50,
           'qiandao_all' => 10,
           'quiz_level' => 2,
       ],
       5 => [// 5级可以关联马甲号，发布收藏页，给陌生人发私信，建立问题箱
           'salt' => 1000,
           'fish' => 100,
           'qiandao_all' => 20,
           'quiz_level' => 2,
       ],
       6 => [//
           'salt' => 2000,
           'fish' => 200,
           'qiandao_all' => 50,
           'quiz_level' => 3,
       ],
       7 => [//
           'fish' => 500,
           'ham' =>50,
           'qiandao_all' => 100,
           'quiz_level' => 3,
       ],
       8 => [//
           'fish' => 1000,
           'ham' =>100,
           'qiandao_all' => 100,
           'quiz_level' => 3,
       ],
       9 => [//
           'fish' => 5000,
           'ham' =>200,
           'qiandao_all' => 100,
           'quiz_level' => 3,
       ],
    ],
    'values' => [
        'salt' => '盐粒',
        'fish' => '咸鱼',
        'ham' => '火腿',
        'qiandao_all' => '总签到天数',
        'quiz_level' => '答题等级',
    ],
];
