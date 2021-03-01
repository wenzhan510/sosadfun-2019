import * as React from 'react';
import { DB } from '../../../../config/db-type';
import { MobileRouteProps } from '../../router';
import { Page } from '../../../components/common/page';
import { NavBar } from '../../../components/common/navbar';
import { PreRegInfo } from './pre-reg-info';
import '../../message/style.scss';  // TODO: extract common css
import './style.scss';
import { RegOptions } from './reg-options';
import { RegMail1 } from './reg-mail1';
import { RegMail2 } from './reg-mail2';
import { RegMail3 } from './reg-mail3';
import { RegMail4 } from './reg-mail4';
import { Popup } from '../../../components/common/popup';
import { RegMail4Confirm } from './reg-mail4-confirm';
import { RegMailInfo } from './reg-mail-info';
import { RegMailProgress } from './reg-mail-progress';
import { RegCode } from './reg-code';
import { CreateAccount } from './create-account';
import { Constant } from '../../../../config/constant';
import { notice } from '../../../components/common/notice';
import { saveStorage } from '../../../../utils/storage';

const regMailTokenLength = 10;
const essayMinLength = 500;
export type Account = {
  username:string;
  password:string;
  email:string;
};
export type QuizQuestionAnswer = {id:number, answer:string};
interface State {
  registrationOption:InvitationType;
  email:string;
  registrationStatus:DB.RegistrationApplication;
  quiz:DB.QuizQuestion[];
  quizAnswer:QuizQuestionAnswer[];
  regMailToken:string;
  regCode:string;
  essay:DB.Essay;
  essayAnswer:string;
  account:Account;
  step:Step;
  showPopup:boolean;
}

export type InvitationType = 'token'|'email';

// we put all registration pages in one component,
// and use internal component state to navigate between steps, instead of providing urls for different steps
// thus we can make sure user cannot skip any steps
// and attackers cannot get to final registration page without clicking through all the previous steps
// if user refreshes page, he just has start from the very beginning
export type Step = 'info' | 'choose-reg-option' | 'reg-mail-1' |
  'reg-mail-2' | 'reg-mail-3' | 'reg-mail-4' | 'reg-mail-info' |
  'reg-mail-progress' | 'reg-code' | 'create-account';

export class Register extends React.Component<MobileRouteProps, State> {
  public state:State = {
    // internal state
    registrationOption: 'token',
    email: '',
    registrationStatus: DB.allocRegistrationApplication(),
    quiz: [],
    quizAnswer:[],      // reg-mail2
    regMailToken: '',   // reg-mail3
    regCode: '',
    essay: DB.allocEssay(),
    essayAnswer: '',
    account:{
      email: '',
      username: '',
      password: '',
    },
    step: 'info',
    showPopup:false,
  };

  public async componentDidMount() {
  }

  public updateState = <Key extends keyof State>(key:Key) => (value:State[Key]) => () => {
    this.setState({
      [key]: value,
    } as any);
  }

  public nextStep = async () => {
    const { registerByInvitationEmailSubmitEmail, registerByInvitationEmailSubmitQuiz, registerByInvitationConfirmToken, registerByInvitationSubmitEssay, registerVerifyInvitationToken, registerByInvitation } = this.props.core.api;

    const { registrationOption, step, email, registrationStatus, quiz, quizAnswer, essay, essayAnswer, regMailToken, regCode, account } = this.state;
    switch (step) {
      case 'info':
        this.setState({ step: 'choose-reg-option' });
        break;
      case 'choose-reg-option': {
        this.setState({ step:
          registrationOption == 'token' ? 'reg-code' : 'reg-mail-1' });
        break;
      }
      case 'reg-mail-1': {
        // submit email, request quiz
        try {
          const res = await registerByInvitationEmailSubmitEmail(email);
          const newState = {
            registrationStatus: res.registration_application,
            quiz,
            essay,
            step: step as Step,
          };
          if (res.quizzes) { newState.quiz = res.quizzes; }
          if (res.essay) { newState.essay = res.essay; }

          const { has_quizzed, email_verified_at, submitted_at } = res.registration_application.attributes;
          if (!has_quizzed) {
            // user has not finish quiz, redirect user to take quiz
            newState.step = 'reg-mail-2';
          } else if (!email_verified_at) {
            // user has finished quiz, but has not verify email
            // redirect user to verify email
            newState.step = 'reg-mail-3';
          } else if (!submitted_at) {
            // user has finished quiz and verified email, but has not submit essay
            // redirect user to submit essay
            newState.step = 'reg-mail-4';
          } else {
            // everything is done! redirect user to check application progress page
            newState.step = 'reg-mail-progress';
          }
          this.setState(newState);
        } catch (e) {
          notice.requestError(e);
        }
        break;
      }
      case 'reg-mail-2': {
        // submit quiz
        // format quiz answer
        try {
          const res = await registerByInvitationEmailSubmitQuiz(email, quizAnswer);
          const newState = {
            registrationStatus: res.registration_application,
            essay,
            quizAnswer,
            step: step as Step,
          };
          if (!res.registration_application.attributes.has_quizzed) {
            alert('考试挂科,回去重考');
            newState.quizAnswer = [];
            newState.step = 'reg-mail-1';
          } else {
            if (!res.essay) {
              console.error('这是后端bug,没有返回essay');
            } else {
              // next step: confirm your email
              newState.essay = res.essay;
              newState.step = 'reg-mail-3';
            }
          }
          this.setState(newState);
        } catch (e) {
          notice.requestError(e)
        }
        break;
      }
      case 'reg-mail-3': {
        try {
          await registerByInvitationConfirmToken(email, regMailToken);
          this.setState({ step: 'reg-mail-4' });
        } catch (e) {
          notice.requestError(e);
        }
        break;
      }
      case 'reg-mail-4': {
        try {
          const { registration_application } = await registerByInvitationSubmitEssay(email, essay.id, essayAnswer);
          this.setState({
            step: 'reg-mail-progress',
            registrationStatus: registration_application,
          });
        } catch (e) {
          notice.requestError(e);
        }
        break;
      }
      case 'reg-mail-info':
        this.setState({ step: 'reg-mail-1' });
        break;
      case 'reg-mail-progress':
        break;
      case 'reg-code': {
        // verify token
        if (regCode.substr(0, Constant.invitation_token_prefix.length) != Constant.invitation_token_prefix) {
          notice.warning(`邀请码应以${Constant.invitation_token_prefix}开头，请注意大小写和空格`);
        } else {
          try {
            await registerVerifyInvitationToken(regCode);
            this.setState({ step: 'create-account' });
          } catch (e) {
            notice.requestError(e);
            notice.info('五分钟内只能提交一次邀请码，您已失败一次，请于五分钟后重试。');
          }
        }
        break;
      }
      case 'create-account': {
        try {
          if (registrationOption == 'token') {
            const { user, history } = this.props.core;
            const { token, name, id } = await registerByInvitation  (registrationOption, regCode, account.username, account.email, account.password);
            user.login(name, id, token);
            saveStorage('auth', {
              token: token, username: name, userId: id,
            });
            history.goBack();
          } else {
            // TODO:
            alert('小作文申请注册还在等api~');
          }
        } catch (e) {
          notice.requestError(e);
        }
        break;
      }
    }
  }
  // 通过邮件注册的详细步骤
  private getMenuButton() {
    const { registrationOption, step, email } = this.state;
    let text = '';
    switch (step) {
      case 'info':
      case 'choose-reg-option':
        text = '下一步';
        break;
      case 'reg-mail-1':
      case 'reg-mail-2':
        text = '提交';
        break;
      case 'reg-mail-3':
      case 'reg-mail-info':
        text = '确认';
        break;
      case 'reg-mail-4':
      case 'reg-code':
      case 'create-account':
        text = '提交';
        break;
      case 'reg-mail-progress':
        break;
    }
    return (
      NavBar.MenuText({
        value: text,
        onClick: this.nextStep,
        disabled: this.getMenuButtonIsInvalid(),
      }));
  }

  private getMenuButtonIsInvalid() {
    const { step, email, quiz, quizAnswer, regMailToken, essayAnswer, regCode, account } = this.state;
    if (step == 'reg-mail-1' && !email) {
      // TODO: check if email valid
      return true;
    }
    if (step == 'reg-mail-2') {
      if (!email) { return true; }
      if (Object.keys(quizAnswer).length != quiz.length) { return true; }
    }
    if (step == 'reg-mail-3' &&
      regMailToken.length != regMailTokenLength) {
        return true;
    }
    if (step == 'reg-mail-4' && essayAnswer.length < essayMinLength) {
      return true;
    }
    if (step == 'reg-mail-progress') { return true; }
    if (step == 'reg-code' && !regCode) { return true; }
    if (step == 'create-account') {
      return !account.username;
    }
    return false;
  }

  private getMenuTitle() {
    const { step } = this.state;
    switch (step) {
      case 'info':
      case 'choose-reg-option':
        return '注册';
      case 'reg-mail-1':
      case 'reg-mail-2':
      case 'reg-mail-3':
      case 'reg-mail-4':
        return '通过邮件注册';
      case 'reg-mail-info':
        return '通过邮件注册的详细步骤';
      case 'reg-mail-progress':
        return '查询注册申请进度';
      case 'reg-code':
        return '通过邀请码注册';
      case 'create-account':
        return '填写注册信息';
    }
  }

  private getPageContent() {
    const { registrationOption, step, email, quiz, essay, essayAnswer, regMailToken, regCode, registrationStatus, account } = this.state;
    switch (step) {
      case 'info':
        return <PreRegInfo />;
      case 'choose-reg-option':
        return (
          <RegOptions
            regOption={this.state.registrationOption}
            changeRegOption={this.updateState('registrationOption')} />);
      case 'reg-mail-1':
        return (
          <RegMail1
            email={email}
            changeMailAddress={this.updateState('email')}
            redirectToRegMailInfo={() => this.setState({step:'reg-mail-info'})}/>);
      case 'reg-mail-2':
        return (
          <RegMail2
            email={email}
            quiz={quiz}
            changeQuizAnswer={this.updateState('quizAnswer')}
          />
        );
      case 'reg-mail-3':
        return (
          <RegMail3
            regMailToken={regMailToken}
            resendEmail={this.props.core.api.registerByInvitationEmailResendEmailVerification}
            changeRegMailToken={this.updateState('regMailToken')}
            email={email}/>
        );
      case 'reg-mail-4':
        return (
          <RegMail4
            email={email}
            essay={essay}
            essayAnswer={essayAnswer}
            changeEssayAnswer={this.updateState('essayAnswer')}/>
        );
      case 'reg-mail-info':
        return <RegMailInfo />;
      case 'reg-mail-progress':
        return <RegMailProgress
          email={email}
          registrationStatus={registrationStatus}/>;
      case 'reg-code':
        return (
          <RegCode
            regCode={regCode}
            changeRegCode={this.updateState('regCode')}/>);
      case 'create-account':
        return (
          <CreateAccount
            account={account}
            registrationOption={registrationOption}
            email={email}
            changeAccount={this.updateState('account')}/>
        );
    }
  }

  public render () {
    return (
      <Page
        top={<NavBar
          goBack={this.props.core.route.back}
          menu={this.getMenuButton()}>
          {this.getMenuTitle()}</NavBar>}
      >
        {this.getPageContent()}
        {this.state.showPopup && <Popup
          className="reg"
          onClose={() => {}}>
          <RegMail4Confirm onClick={() => console.log(1)}/>
        </Popup>}
      </Page>);
  }
}