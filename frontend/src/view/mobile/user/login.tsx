import * as React from 'react';
import { Login } from '../../components/user/login';
import { PasswordReset } from '../../components/user/pwd-reset';
import { Register } from '../../components/user/register';
import { NavBar } from '../../components/common/navbar';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';

interface State {
}

export class LoginRoute extends React.Component<MobileRouteProps, State> {
  public location = '';

  public render () {
    const content = this.renderContent();

    return <Page>
      { content }
      </Page>

  }

  public renderContent () {
    // FIXME: location.state.from is '/user', we should probably use from.from
    const fromUrl = '/';
    switch (window.location.pathname) {
      case '/login':
        this.location = 'login';
        return <Login core = {this.props.core} login={async (email, pwd) =>
            this.props.core.api.login(email, pwd, fromUrl) }></Login>;
      case '/register':
        this.location = 'register';
        return <Page top={<NavBar goBack={this.props.core.route.back}>Login</NavBar>} >
          <Register register={async (name, email, pwd) =>
          this.props.core.api.register(name, pwd, email, fromUrl)}></Register>
          </Page>;
      case '/reset_password':
        this.location = 'reset password';
        return  <Page top={<NavBar goBack={this.props.core.route.back}>Login</NavBar>} >
          <PasswordReset resetPassword={(email) => this.props.core.api.resetPassword(email)}></PasswordReset>;
          </Page>;
      default:
        return <div>wrong pathname {window.location.pathname}</div>;
    }
  }
}