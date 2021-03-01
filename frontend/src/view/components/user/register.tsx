import * as React from 'react';
import { validEmail, validPwd } from '../../../utils/validates';
import { NoticeBar } from '../common/notice-bar';
import { Card } from '../common/card';
import './register.scss';

interface Props {
  register:(name:string, email:string, pwd:string) => Promise<boolean>;
}
interface State {
  username:string;
  email:string;
  pwd:string;
  pwd2:string;
  token:string;
  accept:boolean;
  errMsg:string;
}

export class Register extends React.Component<Props, State> {
  public state = {
    username: '',
    email: '',
    pwd: '',
    pwd2: '',
    token: '',
    accept: false,
    errMsg: '',
  };

  public inputStyle = 'input is-normal inputbox-reg';

  public render () {
    return <Card className="register">
      <div className="card-header" style={{boxShadow: 'none'}}>
        <h1 className="title">注册</h1>
      </div>
      <div className="card-content">
        {this.state.errMsg && <NoticeBar>{this.state.errMsg}</NoticeBar>}

        用户名（笔名）：
        <input className={this.inputStyle}
          type="text"
          onChange={(ev) => this.setState({username: ev.target.value})}
        />

        邮箱：
        <input className={this.inputStyle}
          type="email"
          onChange={(ev) => this.setState({email: ev.target.value})}
        />

        密码：
        <input className={this.inputStyle}
          type="password"
          onChange={(ev) => this.setState({pwd: ev.target.value})}
        />

        确认密码：
        <input className={this.inputStyle}
          type="password"
          onChange={(ev) => this.setState({pwd2: ev.target.value})}
        />

        邀请码：
        <input className={this.inputStyle}
          type="text"
          onChange={(ev) => this.setState({token: ev.target.value})}
        />

        <div style={{
          textAlign: 'center',
          fontSize: '90%',
          margin: '15px 0',
        }}>
          <h3 style={{
            fontWeight: 'bold',
            fontSize: '100%',
          }}>注册协议</h3>
          {[
            '丧病之家，您的精神墓园',
            '比欲哭无泪更加down，不抑郁不要钱',
            '本站禁抄袭，禁人身攻击，禁人肉，禁恋童',
            '请不要发布侵犯他人版权的文字',
            '请确保您已年满十八岁',
            '祝您玩得愉快',
          ].map((text, i) => <p key={i} style={{ margin: '8px 0' }}>{text}</p>)}
        </div>

        <div className="checkbox"
          onClick={(ev) => this.setState((prevState) => ({accept: !prevState.accept}))}
          style={{ textAlign: 'center', width: '100%', margin: '10px 0' }}>
          <input type="checkbox"
            checked={this.state.accept}
          />
          我已阅读并同意注册协议 更多内容
        </div>

        <a className="button is-normal color-primary is-fullwidth register-button" onClick={async (ev) => {
          if (this.state.email === '') {
            this.setState({errMsg: '邮箱 不能为空。'});
          } else if (this.state.pwd === '') {
            this.setState({errMsg: '密码 不能为空。'});
          } else if (this.state.username === '') {
            this.setState({errMsg: '名称 不能为空。'});
          } else if (this.state.token === '') {
            this.setState({errMsg: '邀请码 不能为空。'});
          } else if (!this.state.accept) {
            this.setState({errMsg: '注册协议勾选 不能为空。'});
          } else if (!validEmail(this.state.email)) {
            this.setState({errMsg: '邮箱格式不符'});
          } else if (!validPwd(this.state.pwd)) {
            this.setState({errMsg: '密码格式不符'});
          } else if (this.state.pwd !== this.state.pwd2) {
            this.setState({errMsg: '密码和确认密码不相匹配'});
          } else {
            const success = await this.props.register(this.state.username, this.state.email, this.state.pwd);
            if (!success) {
              // todo:
              this.setState({errMsg: '注册失败'});
            }
          }
        }}>注册</a>
      </div>
    </Card>;
  }
}