import React from 'react';
import { Page } from '../common/page';
import { NavBar } from '../common/navbar';
import { Core } from '../../../core';

export class ForumTags extends React.Component<{
  // props
  core:Core;
  onConfirm:() => void;
}, {
  // state
}> {
  public render () {
  return (
    <Page top={<NavBar
      goBack={() => this.props.core.route.back()}
      menu={NavBar.MenuIcon({
        onClick: () => this.props.onConfirm(),
        icon: 'fa fa-search',
      })}>
        标签列表
      </NavBar>}>
    </Page>);
  }
}