import * as React from 'react';
import { Card } from '../../../components/common/card';
import { Account } from './register';
import { Checkbox } from '../../../components/common/input/checkbox';
import { validEmail, validPwd, validUserName } from '../../../../utils/validates';
import { InvitationType } from './register';

interface Props {
  email:string;
  account:Account;
  registrationOption:InvitationType;
  changeAccount:(account:Account) => () => void;
}

interface State {
  email:string;
  username:string;
  password:string;
  passwordConfirm:string;
  declarationOfGoodFish:string;
  check1:boolean;
  check2:boolean;
  check3:boolean;
  emailInvalid:boolean;
  usernameInvalid:boolean;
  passwordInvalid:boolean;
  passwordConfirmInvalid:boolean;
  declarationOfGoodFishInvalid:boolean;
}
type checkBoxes = 'check1' | 'check2' | 'check3';
const declaration = '我保证在废文好好做鱼看版规帮助不扒马不骂人不盗文';
export class CreateAccount extends React.Component<Props, State> {
  public state:State = {
    email:this.props.email,
    username:'',
    password:'',
    passwordConfirm:'',
    declarationOfGoodFish:'',
    check1:false,
    check2:false,
    check3:false,
    emailInvalid:false,
    usernameInvalid:false,
    passwordInvalid:false,
    passwordConfirmInvalid:false,
    declarationOfGoodFishInvalid:false,
  };

  private setStateAndCheckReady<Key extends keyof State> (key:keyof State, value:State[Key]) {
    this.setState({[key]:value} as any, this.checkReady);
  }

  private checkReady = () => {
    const { email, username, password, passwordConfirm, declarationOfGoodFish, check1, check2, check3, emailInvalid, passwordInvalid, passwordConfirmInvalid, declarationOfGoodFishInvalid } = this.state;
    const ready = !!(email && username && password && passwordConfirm && declarationOfGoodFish && check1 && check2 && check3 && !emailInvalid && !passwordInvalid && !passwordConfirmInvalid && !declarationOfGoodFishInvalid);
    if (ready) {
      this.props.changeAccount({
        email,
        username,
        password,
      })();
    } else if (this.props.account.username) {
      this.props.changeAccount({
        email:'',
        username: '',
        password:'',
      })();
    }
  }

  private renderCheckBox(key:checkBoxes, label:string) {
    return (
      <Checkbox
        className="checkbox"
        value={key}
        checked={this.state[key]}
        onChange={() => this.setStateAndCheckReady(key, !this.state[key])}
        label={label} />);
  }

  public headQuote = (
    <p>丧病之家，你的精神墓园<br/>
      比欲哭无泪更加down，不抑郁不要钱<br/>
      本站禁抄袭，禁人身攻击，禁人肉，禁恋童<br/>
      请不要发布侵犯他人版权的文字<br/>
      请确保你已年满十八岁<br/>
      祝你玩得愉快
    </p>
  );

  private validateEmail = () => {
    if (this.state.email) {
      this.setState({emailInvalid: !validEmail(this.state.email)}, this.checkReady);
    }
  }
  private validateUsername = () => {
    if (this.state.email) {
      this.setState({usernameInvalid: !validUserName(this.state.username)}, this.checkReady);
    }
  }
  private validatePassword = () => {
    if (this.state.password) {
      this.setState({passwordInvalid: !validPwd(this.state.password)}, this.checkReady);
    }
    this.validatePasswordConfirm();
  }
  private validatePasswordConfirm = () => {
    const { passwordConfirm, password } = this.state;
    if (passwordConfirm && password) {
      this.setState({passwordConfirmInvalid: password != passwordConfirm }, this.checkReady);
    }
  }
  private validateDeclarationOfGoodFish = () => {
    const { declarationOfGoodFish } = this.state;
    if (declarationOfGoodFish) {
      this.setState({declarationOfGoodFishInvalid: declarationOfGoodFish != declaration }, this.checkReady);
    }
  }

  public render () {
    const { registrationOption } = this.props;
    const { password, username, passwordConfirm, declarationOfGoodFish, check1, check2, check3, email, passwordConfirmInvalid, passwordInvalid, emailInvalid, declarationOfGoodFishInvalid, usernameInvalid } = this.state;
    return (
      <Card className="reg">
        { this.headQuote }

        <div className="creat-account-input">
          <p>邮箱</p>
          <div className="input-text">
            <input type="email"
              minLength={2}
              maxLength={255}
              value={email}
              disabled={registrationOption == 'email'}
              onChange={(e) => this.setStateAndCheckReady('email',   e.target.value) }
              placeholder="请输入邮箱"
              onBlur={this.validateEmail}></input>
          </div>
          { emailInvalid && <p className="note warning"><small>邮箱不合格，您可能输入了错误邮箱地址，或用了QQ邮箱</small></p>}
        </div>

        <div className="creat-account-input">
          <p>用户名</p>
          <div className="input-text">
            <input type="username"
              minLength={2}
              maxLength={8}
              value={username}
              onChange={(e) => this.setStateAndCheckReady('username',   e.target.value) }
              placeholder="注册后暂时无法更改"
              onBlur={this.validateUsername}></input>
          </div>
          { usernameInvalid && <p className="note warning"><small>用户名不合格，最短应有两个字符</small></p>}
        </div>

        <div className="creat-account-input">
          <p>密码</p>
          <div className="input-text">
            <input type="password"
              minLength={10}
              maxLength={32}
              value={password}
              onChange={(e) => this.setStateAndCheckReady('password',   e.target.value) }
              placeholder="至少10位密码"
              onBlur={this.validatePassword}></input>
          </div>
          { passwordInvalid && <p className="note warning"><small>密码不合格</small></p>}
          <p className="note"><small>{`(需包含至少一个大写字母，至少一个小写字母，至少一个数字，至少一个特殊字符。常用特殊字符：#?!@$%^&*-_)`}</small></p>
        </div>

        <div className="creat-account-input">
          <p>确认密码</p>
          <div className="input-text">
            <input type="password"
              minLength={10}
              maxLength={32}
              value={passwordConfirm}
              onChange={(e) => this.setStateAndCheckReady ('passwordConfirm', e.target.value) }
              placeholder="请重复输入密码"
              onBlur={this.validatePasswordConfirm}></input>
          </div>
          { passwordConfirmInvalid && <p className="note warning"><small>密码不一样</small></p>}
        </div>

        <div className="creat-account-input">
          <p>注册担保（请手动输入下面一句话）</p>
          <p className="note no-select">{ declaration }</p>
          <div className="input-text">
            <input type="text"
              minLength={declaration.length}
              maxLength={declaration.length}
              value={declarationOfGoodFish}
              onChange={(e) => this.setStateAndCheckReady ('declarationOfGoodFish', e.target.value) }
              placeholder="请输入注册担保"
              onBlur={this.validateDeclarationOfGoodFish}></input>
          </div>
          { declarationOfGoodFishInvalid && <p className="note warning"><small>注册担保不完全一致</small></p>}
        </div>

        <div className="checkboxes">
          { this.renderCheckBox('check1', '我知道可以左上角搜索关键词获取使用帮助') }
          { this.renderCheckBox('check2', '我已阅读《版规》中约定的社区公约，同意遵守版规') }
          { this.renderCheckBox('check3', '保证自己年满十八周岁，神智健全清醒，承诺为自己的言行负责。') }
        </div>

        <p><small>友情提示：本页面含有IP访问频率限制，为了你的正常注册，注册时请不要刷新或倒退网页。</small></p>
    </Card>
    );
   }
}