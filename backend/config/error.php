<?php
return [
    '200' => 'success',
    '400' => 'not found',//相关指令服务器无法识别resolve
    '401' => 'unauthorised',//未登陆用户，不能验证用户身份
    '403' => 'permission denied',//已登陆用户，但权限不足，不允许进行此项操作（如试图修改非本人创建的内容，试图修改非本人账户的信息，旧密码不等于当前密码），非管理试图访问私密版块的内容，非管理试图修改禁止修改的版块的内容
    '404' => 'not found',//post/thread/chapter等内容未找到
    '405' => 'method not allowed',//
    '408' => 'content duplication conflict: report from database', //数据库宣布数据内容重复，不能写入
    '409' => 'content duplication conflict', //数据内容重复，邀请链接已经成功注册（可直接登陆），新旧密码重复，已经关注
    '410' => 'allowed number limit conflict', //不允许建立更多的同类内容了，需要更长时间间隔或者更高等级或更多余额，一日只能打赏一个东西一次，同一账户几小时内只能重置密码一次。
    '411' => 'precondition failed due to self input', //由于自身输入的内容，前提失败导致动作无效，不能关注或取关自己，不能给自己发私信，不能即赞又踩
    '412' => 'precondition failed due to self setting', //由于自身设置或状态，前提失败导致动作无效，不能取关未关注的人,不能修改对未关注人的关注偏好，不能在私信余额不足的时候发送私信, 不能佩戴不具有的头衔，不能打赏超过个人收入的虚拟物，新注册的用户当天不能重置密码。已免广告的不能再免广告，封禁管理中的用户不能重置密码
    '413' => 'precondition failed due to other side setting', //由于对方设置，前提失败导致动作无效，不能发私信给拒绝接收私信的用户，不能访问作者设置私密的讨论，不能收藏申请删除中的书籍/讨论，不能折叠管理员的留言
    '414' => 'precondition failed due to system setting', //由于系统设置，前提失败导致动作无效.
    '416' => 'user further action reqired: needs authentification', // 用户需要激活邮箱才能访问内容
    '417' => 'user further action reqired: needs purchase', // 作业需购买才能阅读
    '420' => 'precondition failed, nothing really done', // 因为输入的内容不可操作，实际上什么都没做
    '422' => 'validation failed',//输入的数据不符合规则，不能validate内容，
    '429' => 'too many attempts', // throttle
    '433' => 'item is component, has to be a regular post/comment to delete', //目标物并不是普通的回帖，而是chapter/review/question/answer一类的内容，需要转换成普通post才能正常删除
    '444' => 'item or command expired, unable to perform operation', //操作已失效，比如邀请码已经使用或已失效，邀请链接已经使用，福利码已使用或已失效
    '481' => 'classification data corruption', //分类性数据冲突，比如大类信息和频道信息不能对应匹配，或不能检索到对应的大类信息，或许可以考虑更新大类信息；或者，选择回复的
    '482' => 'related item not applicable', //选择回复/附件的对象并不存在或不可用(比如在讨论帖A中回复讨论帖B的回帖)
    '488' => 'forbidden word',//内容中违禁词超过了运作能力（比如标题因违禁词存在变成空白字串）
    '495' => 'did not store in database', //不知为何，数据未能存储
    '497' => 'user temporarily prohibited from certain services',//用户暂时禁言或禁止访问作业区
    '498' => 'request frequency limit on IP address', // 短时间内，同IP对安全相关页面（注册、重置密码等）的访问过于频繁
    '499' => 'is blocked',//用户因不当行为被站内封禁
    '595' => 'database error',//数据库连接出现问题
    '599' => 'unknown errors',//其他所有未知的问题
];
