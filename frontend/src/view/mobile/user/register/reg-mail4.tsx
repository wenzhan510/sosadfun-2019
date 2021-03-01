import * as React from 'react';
import { Card } from '../../../components/common/card';
import { DB } from '../../../../config/db-type';

export function RegMail4 (props:{
  email:string;
  essay:DB.Essay;
  className?:string;
  essayAnswer:string;
  changeEssayAnswer:(essay:string) => () => void;
}) {
  return (
    <Card className="reg">
      {/* TODO: use h2 here, after h2 is defined in common.scss */}
      <p className="title">步骤四：完成注册邀请问卷</p>
      <p className="small-warning">你正在使用 {props.email} 进行注册，如果邮箱有误，请勿继续！</p>
      <p>感谢你回答以上问题。废文网欢迎志同道合的朋友加入，但是如果理念不合，我们觉得没有必要强留。一直以来，废文网致力于打造一个比较自由的创作与阅读天地，想要加入这里的你，想必也对文学怀抱着一份热爱，接下来:</p>
      <p>{ props.essay.attributes.body }</p>

      <div id="essay-textarea">
        <textarea
          placeholder="请输入内容"
          value={props.essayAnswer}
          onChange={(e) => props.changeEssayAnswer(e.target.value)()}></textarea>
      </div>

      <p><small>友情提示：简答题回答与注册账号的关系永久存在，如在注册阶段中采用抄袭/代写/参考他人等违规手段，该邮箱将被拉黑。如采用违禁手段侥幸注册成功，注册后账号后依然会被封禁，该类申诉无视等级并不接受申诉。请保证回答的原创性。<br/>为免数据丢失，建议你在其他地方写完问题后，复制粘贴到本页面提交。</small></p>
    </Card>);
}