<?php

return [
    //对于受限制的tag，用户最多选择x个
    'sum_limit_count' => 5,

    //所有大类列表
    'types' => [
        '大类'  ,  // sum limit
        '篇幅'  ,  // only one
        '性向'  ,  // only one
        '进度'  ,  // only one
        '结局'  ,  // only one; sum limit
        '文章性质'  , // only one; sum limit
        '同人原著'  ,// only one
        '同人CP'  , // only one
        '同人聚类'  ,// sum limit
        '版权相关'  ,// sum limit
        '故事体裁'  ,// sum limit
        '整体时代'  ,// sum limit
        '故事观感'  ,// sum limit
        '强弱关系'  ,// sum limit
        '伦理关系'  ,// sum limit
        'CP关系'  ,// sum limit
        '视角关系'  ,// sum limit
        '人称'  ,// sum limit
        '人物性格'  ,// sum limit
        '执业范围'  ,// sum limit
        '特殊元素'  ,// sum limit
        '具体情节'  ,// sum limit
        '世界设定'  ,// sum limit
        '生物设置'  ,// sum limit
        '风俗环境'  ,// sum limit
        '性癖'  ,// sum limit
        '编推'  , //专题推荐，当前编推
        '管理'  , //（和post共享的）高亮，精华，置顶，
        '阅读进度'  , //（post only），想读，在读，已读，弃文，养肥
        '阅读反馈'  , //（post only），推荐，多次阅读，
        '语言',//语种
    ],
    'limits' => [
        'only_one' => [
            '篇幅'  ,
            '性向'  ,
            '进度'  ,
            '结局'  ,
            '文章性质'  ,
            '同人原著'  ,
            '同人CP'  ,
            '故事体裁' ,
        ],
        'sum_limit' => [
            '大类'  ,
            '结局'  ,
            '文章性质'  ,
            '同人聚类'  ,
            '故事体裁'  ,
            '整体时代'  ,
            '故事观感'  ,
            '强弱关系'  ,
            '伦理关系'  ,
            'CP关系'  ,
            '视角关系'  ,
            '人称'  ,
            '人物性格'  ,
            '执业范围'  ,
            '特殊元素'  ,
            '具体情节'  ,
            '世界设定'  ,
            '生物设定'  ,
            '风俗环境'  ,
            '性癖'  ,
        ],
        'admin_only' => [//用户没有选择权利
            '编推'  , //
            '管理'  , //
        ],
        'post_only' => [// 只使用于post相关
            '阅读进度'  , //
            '阅读反馈'  , //
        ],
    ],

    'book_info' =>[
        'originality_info' => [
            0 => '同人',
            1 => '原创',
        ],
        'channel_info' => [
            1 => '原创',
            2 => '同人',
        ],
        'book_length_info' => [
            '1' => '短篇',
            '2' => '中篇',
            '3' => '长篇',
            '4' => '大纲',
        ],
        'book_status_info' => [
            '1' => '连载',
            '2' => '完结',
            '3' => '暂停',
        ],
        'sexual_orientation_info' => [ //0:未知，1:BL，2:GL，3:BG，4:GB，5:混合性向，6:无CP，7:其他性向
            '0' => '性向未知',
            '1' => 'BL',
            '2' => 'GL',
            '3' => 'BG',
            '4' => 'GB',
            '5' => '混合性向',
            '6' => '无CP',
            '7' => '其他性向',
        ],
        'rating_info' => [ //1:非边缘, 2:边缘
            '1' => '非边限',
            '2' => '边限',
        ],
        'orderby_info' => [ //1:按最新章节, 2:按最新回贴时间, 3:积分排序, 4.字数均衡积分
            '1' => '最新章节',
            '2' => '最新回复',
            '3' => '总积分',
            '4' => '均字数积分',
        ],
    ],
    'custom_public_none_tongren_tag_types' => [ //不包括“同人原著”，“同人CP”,不包括管理tag，不包括“性癖”……的其他所有书籍自选tag
        '大类'  ,  // sum limit
        '篇幅'  ,  // only one
        '性向'  ,  // only one
        '进度'  ,  // only one
        '结局'  ,  // only one; sum limit
        '文章性质'  , // only one; sum limit
        '同人聚类'  ,// sum limit
        '版权相关'  ,// sum limit
        '故事体裁'  ,// sum limit
        '整体时代'  ,// sum limit
        '故事观感'  ,// sum limit
        '强弱关系'  ,// sum limit
        '伦理关系'  ,// sum limit
        'CP关系'  ,// sum limit
        '视角关系'  ,// sum limit
        '人称'  ,// sum limit
        '人物性格'  ,// sum limit
        '执业范围'  ,// sum limit
        '特殊元素'  ,// sum limit
        '具体情节'  ,// sum limit
        '世界设定'  ,// sum limit
        '生物设置'  ,// sum limit
        '风俗环境'  ,// sum limit
        '语言',//
    ],
    'custom_none_tongren_tag_types' => [ //不包括“同人原著”，“同人CP”,不包括管理tag……的其他所有书籍自选tag
        '大类'  ,  // sum limit
        '篇幅'  ,  // only one
        '性向'  ,  // only one
        '进度'  ,  // only one
        '结局'  ,  // only one; sum limit
        '文章性质'  , // only one; sum limit
        '同人聚类'  ,// sum limit
        '版权相关'  ,// sum limit
        '故事体裁'  ,// sum limit
        '整体时代'  ,// sum limit
        '故事观感'  ,// sum limit
        '强弱关系'  ,// sum limit
        '伦理关系'  ,// sum limit
        'CP关系'  ,// sum limit
        '视角关系'  ,// sum limit
        '人称'  ,// sum limit
        '人物性格'  ,// sum limit
        '执业范围'  ,// sum limit
        '特殊元素'  ,// sum limit
        '具体情节'  ,// sum limit
        '世界设定'  ,// sum limit
        '生物设置'  ,// sum limit
        '风俗环境'  ,// sum limit
        '性癖'  ,// sum limit
        '语言',//
    ],
    'custom_none_tongren_none_book_tag_types' => [ //不包括管理tag，不包括同人tag，不包括篇幅、性向、进度三个总tag的，其他所有书籍自选tag
        '大类'  ,  // sum limit
        '结局'  ,  // only one; sum limit
        '文章性质'  , // only one; sum limit
        '同人聚类'  ,// sum limit
        '版权相关'  ,// sum limit
        '故事体裁'  ,// sum limit
        '整体时代'  ,// sum limit
        '故事观感'  ,// sum limit
        '强弱关系'  ,// sum limit
        '伦理关系'  ,// sum limit
        'CP关系'  ,// sum limit
        '视角关系'  ,// sum limit
        '人称'  ,// sum limit
        '人物性格'  ,// sum limit
        '执业范围'  ,// sum limit
        '特殊元素'  ,// sum limit
        '具体情节'  ,// sum limit
        '世界设定'  ,// sum limit
        '生物设置'  ,// sum limit
        '风俗环境'  ,// sum limit
        '性癖'  ,// sum limit
        '语言',//
    ],
];
