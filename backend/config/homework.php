<?php

return [
    'description' => "
    作业区是废文网为了鼓励高质量的文学创作与交流而长期设置的可匿名评写互动交流区，分别在溪流组、湖泊组、深海组三组展开。
    ",
    'levels' => [
        '0' => [
            'name' => '溪流组',
            'description' => '一方清浅通透的水域，即使是阅历较浅的鱼鱼也能轻松畅游，在友好的环境中提高自己的技巧，并享受初次下水的愉悦。当你向广阔的天地进发，最初的旅程总是令人振奋——请体味这段美好的时光，这里是你寻觅知音的好地方。',
            'reward_base' => 1,
            'worker' => [
                'ham_price' => 8,
                'level_limit' => 4,
                'ham_reward' => 10,
                'invitation_required' => false,
            ],
            'critic' => [
                'ham_price' => 2,
                'level_limit' => 4,
                'ham_reward' => 5,
                'invitation_required' => false,
            ],
            'watcher' => [
                'ham_price' => 2,
                'level_limit' => 3,
                'ham_reward' => 0,
                'invitation_required' => false,
            ],
            'reader' => [
                'ham_price' => 1,
                'level_limit' => 1,
                'ham_reward' => 0,
                'invitation_required' => false,
            ],
        ],
        '1' => [
            'name' => '湖泊组',
            'description' => '一片神秘的水域，湖面上寒烟笼罩，不见飞鸟，只有经验足够、实力得到承认的鱼才能够来到这里，与资质优秀的鱼一起磨炼技艺。水面之下，鱼鱼将面对新的挑战。涌动的暗流消耗鱼鱼的体力；茂盛的水草遮蔽天光，令鱼迷失方向。在这里，你需要锻炼体魄，在冒险中寻求突破。',
            'reward_base' => 2,
            'worker' => [
                'ham_price' => 15,
                'level_limit' => 1,
                'ham_reward' => 20,
                'invitation_required' => true,
            ],
            'critic' => [
                'ham_price' => 2,
                'level_limit' => 1,
                'ham_reward' => 5,
                'invitation_required' => true,
            ],
            'watcher' => [
                'ham_price' => 3,
                'level_limit' => 4,
                'ham_reward' => 0,
                'invitation_required' => false,
            ],
            'reader' => [
                'ham_price' => 2,
                'level_limit' => 3,
                'ham_reward' => 0,
                'invitation_required' => false,
            ],
        ],
        '5' => [
            'name' => '深海组',
            'description' => '传说中才存在的水域。水中幽深寒冷，被划分成大块大块的阳光，碎玉般在遥远的头顶浮动，偶尔有微弱光线能照射到在这片水域中蛰伏的庞然巨物。这不是等闲之辈能涉足的场所，也不适合作为遥想的目标，你必须承认深海游曳绝非易事，需要长久积累下的战斗技巧、坚韧的内心、一往无前的气势以及一点灵光。',
            'reward_base' => 3,
            'worker' => [
                'ham_price' => 20,
                'level_limit' => 1,
                'ham_reward' => 30,
                'invitation_required' => true,
            ],
            'critic' => [
                'ham_price' => 2,
                'level_limit' => 1,
                'ham_reward' => 5,
                'invitation_required' => true,
            ],
            'watcher' => [
                'ham_price' => 4,
                'level_limit' => 4,
                'ham_reward' => 0,
                'invitation_required' => true,
            ],
            'reader' => [
                'ham_price' => 3,
                'level_limit' => 4,
                'ham_reward' => 0,
                'invitation_required' => false,
            ],
        ],
    ],

    'roles' => [
        'worker' => '作业员',
        'critic' => '评论员',
        'watcher' => '围观者',
        'reader' => '读者',
    ],
    'requirements' => [
        'assigned_critique' => 1,
        'other_critique' => 3,
    ],
    'summary' => [
        0 => '未完成',
        1 => '完成',
        2 => '优秀',
        -1 => '作业禁止轻微',
        -2 => '作业禁止严重',
    ],

    'no_homework_base_days' => 15,
    'homework_invitation_base_days' => 120,
    'critique_char_min' => 200,

];
