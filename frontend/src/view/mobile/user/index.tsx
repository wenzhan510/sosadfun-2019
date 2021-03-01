import * as React from 'react';
import { MobileRouteProps } from '../router';
import { Profile } from './profile';
import { Redirect } from 'react-router';
import { Page } from '../../components/common/page';
import { MainMenu } from '../main-menu';

interface State {

}

export class User extends React.Component<MobileRouteProps, State> {
  public renderProfile() {
    return (<div>
      <button onClick={() => {
        this.props.core.user.logout();
      }}>log out</button>
      <Profile {...this.props}></Profile>
    </div>);
  }
  public render() {
    const isLogin = this.props.core.user.isLoggedIn();
    return (
      <Page bottom={<MainMenu />}>
        {isLogin ? this.renderProfile() : <Redirect to={{ pathname: './login', state: { from: this.props.location } }}></Redirect>}
      </Page>

    );
  }
}