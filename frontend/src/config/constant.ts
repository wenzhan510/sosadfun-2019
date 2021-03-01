// THIS CONFIG FILE SHOULD BE CONSISTENT WITH BACKEND CONFIG /backend/config
// IF YOU UPDATE SOMETHING, PLZ UPDATE BOTH FRONTEND AND BACKEND
export namespace Constant {
  export enum Reward {
    salt = '盐粒',
    fish = '咸鱼',
    ham = '火腿',
  }

  export enum ContentType {
    thread = '主题',
    post = '帖子',
    status = '动态',
    quote = '题头',
  }

  export interface FAQType {
    title:string;
    children?:FAQType[];
  }
  // BASED ON backend/config/faq.php
  export const FAQTypes:FAQType[] = [
    {
      title: '基本问题/账户问题',
      children: [
        { title: '打不开、进不去、报错了' },
        { title: '邀请码/邀请链接' },
        { title: '邮箱错误、重置密码、更改邮箱' },
        { title: '用户名修改、用户名登陆' },
        { title: 'APP？' },
      ],
    },
    {
      title: '个人使用',
      children: [
        { title: '签到、答题' },
        { title: '等级' },
        { title: '关联账户' },
        { title: '虚拟物（盐粒、咸鱼、火腿）' },
        { title: '个人简介' },
      ],
    },
    {
      title: '看文',
      children: [
        { title: '首页、目录、排序' },
        { title: '筛选、搜索' },
        { title: '标签' },
        { title: '编推' },
        { title: '收藏、收藏夹' },
        { title: '清单' },
        { title: '书签、历史、版面美化' },
      ],
    },
    {
      title: '发文发帖',
      children: [
        { title: '发文发帖' },
        { title: '边限设置' },
        { title: '隐藏、禁止回复' },
        { title: '评论' },
        { title: '格式' },
        { title: '恢复数据' },
        { title: '删帖、删文' },
      ],
    },
    {
      title: '论坛交互',
      children: [
        { title: '提醒、私信、问题箱' },
        { title: '打赏、评票' },
        { title: '粉丝、关注、动态' },
        { title: '题头' },
        { title: '作业活动' },
      ],
    },
    {
      title: '站务相关',
      children: [
        { title: '当前版规' },
        { title: '违规处理' },
        { title: '举报' },
        { title: '仲裁' },
        { title: '当前站内活动' },
        { title: '其他快捷入口' },
      ],
    },
    {
      title: '社区建设',
      children: [
        { title: 'bug反馈' },
        { title: '建议意见' },
        { title: '社区未来' },
        { title: '加入废文' },
        { title: '商业化/经济支持' },
      ],
    },
  ];

  // BASED ON backend/config/constants
  export const invitation_token_prefix = 'SOSAD_';
}