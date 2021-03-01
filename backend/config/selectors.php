<?php

return [
    'thread_filter' => [
        'withType' => [
            'post' => '只看回帖',
        ],

        'withComponent' => [
            'include_comment' => '显示点评',
        ],

        'withFolded' => [
            'include_folded' => '显示折叠内容',
            'folded_only' => '只显示折叠内容',
        ],

        'ordered' => [
            'default' => '最早回复',
            'latest_created' => '最新发布',
            'most_upvoted' => '最高赞',
        ],
    ],
    'book_filter' => [
        'withType' => [
            'chapter' => '只看章节',
            'post' => '只看回帖',
        ],
        'withComponent' => [
            'component_only' => '只显示正文',
            'post_N_comment' => '只显示回帖和点评',
            'include_comment' => '显示点评',
        ],

        'withFolded' => [
            'include_folded' => '显示折叠内容',
        ],

        'ordered' => [
            'default' => '最早发布',
            'latest_created' => '最新发布',
            'most_upvoted' => '最高赞',
        ],
    ],
    'list_filter' => [
        'withType' => [
            'post' => '只看回帖',
            'review' => '只看书评',
        ],
        'withComponent' => [
            'component_only' => '只显示书评',
            'post_N_comment' => '只显示回帖和点评',
            'include_comment' => '显示点评',
        ],

        'withFolded' => [
            'include_folded' => '显示折叠内容',
        ],

        'ordered' => [
            'default' => '最早发布',
            'latest_created' => '最新发布',
            'most_upvoted' => '最高赞',
        ],
    ],
    'box_filter' => [
        'withType' => [
            'post' => '只看回帖',
            'question' => '只看提问',
            'answer' => '只看回答',
        ],
        'withComponent' => [
            'component_only' => '只显示问+答',
            'post_N_comment' => '只显示回帖和点评',
            'include_comment' => '显示点评',
        ],

        'withFolded' => [
            'include_folded' => '显示折叠内容',
        ],

        'ordered' => [
            'default' => '时间顺序',
            'latest_created' => '最新发布',
            'most_upvoted' => '最高赞',
        ],
    ],
    'homework_filter' => [
        'withType' => [
            'post' => '回帖',
            'work' => '作业正文',
            'critique' => '作业批评',
        ],

        'withComponent' => [
            'include_comment' => '显示点评',
        ],

        'withFolded' => [
            'include_folded' => '显示折叠内容',
        ],

        'ordered' => [
            'default' => '最早回复',
            'latest_created' => '最新发布',
            'most_upvoted' => '最高赞',
        ],
    ],
    'book_index_filter' => [
        'inChannel' => [
            '1' => '原创小说',
            '2' => '同人小说'
        ],
        'ordered' => [
            'default' => '最后回复',
            'latest_add_component' => '最新更新',
            'total_char' => '总字数',
            'jifen' => '总积分',
            'weighted_jifen' => '均字数积分',
            'latest_created' => '最新创建',
            'collection_count' => '最多收藏',
            'random' => '随机乱序',
        ],
    ],
    'thread_index_filter' => [
        'isPublic' => [
            'include_private' => '包含未公开',
            'private_only' => '只看未公开'
        ],
        'ordered' => [
            'default' => '最后回复',
            'latest_add_component' => '最新更新',
            'total_char' => '总字数',
            'jifen' => '总积分',
            'weighted_jifen' => '均字数积分',
            'latest_created' => '最新创建',
            'collection_count' => '最多收藏',
            'random' => '随机乱序',
        ],
        'withBianyuan' => [
            'include_bianyuan' => '包含边限',
            'bianyuan_only' => '只看边限'
        ],
    ],

    'collection_filter' => [
        'order_by' => [
            0 => '最新收藏',
            1 => '最新回复',
            2 => '最新章节',
            3 => '最新创立',
         ]
    ],

    'review_filter' => [

        'reviewType' => [
            'all' => '全部书评',
            'sosad_only' => '站内文评',
            'none_sosad_only' => '非站内文评',
        ],

        'withLength' => [
            'short' => '短评',
            'medium' => '中评',
            'long' => '长评',
            'no_limit' => '不限长度',
        ],

        'reviewRecommend' => [
            'recommend_only' => '推荐',
            'none_recommend_only' => '未推荐',
        ],

        'reviewEditor' => [
            'editor_only' => '编推',
            'none_editor_only' => '非编推',
        ],

        'ordered' => [
            'default' => '最早回复',
            'latest_created' => '最新发布',
            'most_upvoted' => '最高赞',
        ],
    ],

];
