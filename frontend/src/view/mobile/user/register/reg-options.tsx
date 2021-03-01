import * as React from 'react';
import { Card } from '../../../components/common/card';
import { Checkbox } from '../../../components/common/input/checkbox';
import { InvitationType } from './register';

export function RegOptions (props:{
  className?:string;
  regOption:InvitationType;
  changeRegOption:(o:InvitationType) => () => void;
}) {
  return (
    <Card className="reg">
      {/* TODO: use h2 here, after h2 is defined in common.scss */}
      <p className="title">请选择一种注册方式</p>
      {/* 邀请码 */}
      <div className="reg-option">
        <div className="sub-title">
          <Checkbox
            type="radio"
            checked={props.regOption == 'token'}
            onChange={props.changeRegOption('token')}
            label="通过邀请码注册"
          />
        </div>
        <p>获得邀请码的渠道:</p>
        <div className="option-info">
          <p>
            <b>【公共邀请码】通过废文网微博、微信公众号等渠道，获得公共邀请码（数量有限）</b><br/>
            微博、微信公众号会不定期开放少量公共邀请码，数量有限，先到先得。
          </p>
          <p>
            <b>【私人邀请码】通过已经注册废文的好友，获得私人邀请码</b><br/>
            资深废文用户，可以在“个人中心-邀请好友”处创建私人邀请码，分享给好友。
          </p>
        </div>
      </div>

      {/* 小作文 */}
      <div className="reg-option">
        <div className="sub-title">
          <Checkbox
            type="radio"
            checked={props.regOption == 'email'}
            onChange={props.changeRegOption('email')}
            label="邮件注册及进度查询（测试中）"
          />
        </div>
        <p>注册流程:</p>
        <div className="option-info">
          <p>
            <b>提交邮箱</b><br/>
            不可以用QQ邮箱哦。
          </p>
          <p>
            <b>完成问卷</b><br/>
            问卷由11道单选题组成,答对7题即可。
          </p>
          <p>
            <b>验证邮箱</b><br/>
            完成问卷后,我们会给您发一封含有验证码的邮件,来核实您的邮箱。
          </p>
          <p>
            <b>提交作文</b><br/>
            根据题目,写一篇500字左右的小作文。
          </p>
          <p>
            <b>进度查询</b><br/>
            我们会记录同一个邮箱的申请进度,您不需要在同一天内完成问卷和作文。如果您申请了一半,下一次输入邮箱会继续之前的申请。如果当前邮箱已完成所有申请步骤,您会看到您的申请记录。请耐心等待管理员审核,不要重复申请。
          </p>
        </div>
      </div>
    </Card>);
}