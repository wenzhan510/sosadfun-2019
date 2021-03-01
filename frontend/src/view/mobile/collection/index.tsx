import React from 'react';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';
import { SearchBar } from '../search/search-bar';
import { MainMenu } from '../main-menu';

interface State {

}

export class Collection extends React.Component<MobileRouteProps, State> {
  public render () {
    return <Page bottom={<MainMenu />}>
      <SearchBar core={this.props.core} />
    </Page>;
  }
}