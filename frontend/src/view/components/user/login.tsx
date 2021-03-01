import * as React from 'react';
import { Link } from 'react-router-dom';
import { validEmail } from '../../../utils/validates';
import { Card } from '../common/card';
import { NoticeBar } from '../common/notice-bar';
import { RoutePath } from '../../../config/route-path';
import { Button } from '../common/button';
import { Colors } from '../../theme/theme';
import { Core } from '../../../core';
import './login.scss';

// FIXME: internal server error will be treated as incorrect password 
interface Props {
  login:(email:string, pwd:string) => Promise<boolean>;
  core: Core;
}
interface State {
  email:string;
  password:string;
  errMsg:string;
}

export class Login extends React.Component<Props, State> {
  public state = {
    email: '',
    password: '',
    errMsg: '',
  };

  public render () {
    return <Card className = "login">
        <div className="card-content">
        <i className="fas fa-times close" onClick = {this.props.core.route.back}></i>     
        <div className="login-logo"> </div>


        <div className="inputbox">
          <div className = "textfield">账号</div> <div className = "dividing-line">|</div> 
          <input className="input is-normal inputfield"
            type="email"
            placeholder="邮箱/用户名"
            maxLength={255}
            value={this.state.email}
            onChange={(ev) => this.setState({email:ev.target.value})} />
        </div>

        <div className="inputbox">
          <div className = "textfield">密码</div> <div className = "dividing-line">|</div> 
          <input
            className="input is-normal inputfield"
            type="password"
            maxLength={32}
            value={this.state.password}
            onChange={(ev) => this.setState({password:ev.target.value})} />
        </div>

        { this.state.errMsg && <div className = "error">{this.state.errMsg}</div> }

        <div className="button-div">
        <Button type='high' color = {Colors.primary} onClick={async() => {
          if (this.state.email === '') {
            this.setState({errMsg: '邮箱 不能为空。'});
          } else if (this.state.password === '') {
            this.setState({errMsg: '密码 不能为空。'});
          } else if (!validEmail(this.state.email)) {
            this.setState({errMsg: '邮箱格式不符'});
          } else {
            try {
              await this.props.login(this.state.email, this.state.password);
            } catch (e) {
              this.setState({errMsg: '用户名或密码错误。'});
            }
          }
        }}>登录</Button></div> 

        <div className = "regisbox">
          <Link to={RoutePath.register} className="register">注册</Link>

          <Link to={RoutePath.reset_password} className="forgot-password">忘记密码?</Link>
        </div>

      </div>

      <div className="card-footer">
      废文1.0｜copyright xxx
      </div>
    </Card>
  }
}

// <label className="checkbox"><input type="checkbox" />记住我</label>