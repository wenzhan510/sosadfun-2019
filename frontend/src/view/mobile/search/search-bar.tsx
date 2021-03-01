import * as React from 'react';
import { Core } from '../../../core';
import { classnames } from '../../../utils/classname';
import { RoutePath } from '../../../config/route-path';
import './search-bar.scss';

export class SearchBar extends React.Component<{
  // props
  core:Core;
}, {
  // state
  newMessages?:boolean;
}> {
  public state = {
    newMessages: false,
  };

  public render = () => {
    return <div className="search-bar">
      <div className="search-input"
        onClick={() => this.props.core.route.go(RoutePath.search)}
      >
        <i className="fa fa-search i00"></i>
        &nbsp;搜索文章、作者
      </div>
      <div className="message-container"
        onClick={() => this.props.core.route.go(RoutePath.messages)}
      >
        <div className="icon-with-bottom-text">
          <i className={classnames('fas', 'fa-bullhorn', {'hasInfo': this.state.newMessages})}></i>
          <p>消息</p>
        </div>
      </div>
    </div>;
  }
}