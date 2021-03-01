/**
 * 通用错误提示
 */
export const ErrorMsg = {
  FetchError: '请求后端错误',
  JSONParseError: 'JSON格式解析错误',
  200: 'success', // 成功
  400: 'not found', // 相关指令服务器无法识别resolve
  401: '未登陆', // 未登陆，或未能获得相关频道的发布授权，或不具有修改资格
  403: 'permission denied', // 不允许进行此项操作
  404: 'not found', // post/thread/chapter等内容未找到；api路径错误（相关api路径未定义，比如单复数搞反了）
  405: 'method not allowed',
  409: 'data conflict', // 数据内容重复，比如重复回帖相同的内容
  410: 'number conflict', // 不允许建立更多的同类内容了，需要更长时间间隔或者更高等级或更多余额
  412: 'precondition failed', //前提失败导致动作无效，如取关未关注的人
  422: 'validation failed', // 不符合规则的内容，
  433: 'item is component, has to be a regular post/comment to delete', // 目标物并不是普通的回帖，而是chapter/review/question/answer一类的内容，需要转换成普通post才能正常删除
  481: 'classification data corruption', // 分类性数据冲突，比如大类信息和频道信息不能对应匹配，或不能检索到对应的大类信息，或许可以考虑更新大类信息；或者，选择回复的
  482: 'related item not applicable', // 选择回复/附件的对象并不存在或不可用(比如在讨论帖A中回复讨论帖B的回帖)
  488: 'forbidden word', // 内容中违禁词超过了运作能力（比如标题因违禁词存在变成空白字串）
  495: 'did not store in database', //不知为何，数据未能存储
  499: 'is blocked', // 用户因不当行为被站内封禁
  595: 'database error', // 数据库问题
  599: 'unknown errors', // 其他所有未知的问题
};
export type ErrorCodeKeys = keyof typeof ErrorMsg;