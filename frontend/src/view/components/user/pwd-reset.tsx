import * as React from 'react';
import { validEmail } from '../../../utils/validates';
import { Card } from '../common/card';
import { NoticeBar } from '../common/notice-bar';

interface Props {
  resetPassword:(email:string) => Promise<boolean>;
}
interface State {
  email:string;
  errorMsg:string;
}

export class PasswordReset extends React.Component<Props, State> {
  public placeholder = '邮箱地址';
  public state = {
    email: '',
    errorMsg: '',
  };

  public render () {
    return <Card>
      <div className="card-content">

      {this.state.errorMsg && <NoticeBar>{this.state.errorMsg}</NoticeBar>}

      <input className="input is-normal is-fullwidth"
        type="email"
        value={this.state.email}
        placeholder={this.placeholder}
        onChange={(ev) => this.setState({email: ev.target.value})} />

      <br />

      <a className="button is-normal is-fullwidth" onChange={async(ev) => {
        if (this.state.email === '') {
          this.setState({ errorMsg: '邮箱不能为空' });
          return;
        }
        if (!validEmail(this.state.email)) {
          this.setState({ errorMsg: '邮箱格式不正确' });
          return;
        }
        await this.props.resetPassword(this.state.email);
      }}>发送重置邮件</a>

      </div>
    </Card>;
  }
}