import * as React from 'react';
import { Core } from '../core';
import { MobileRouter } from './mobile/router';
import { Router } from 'react-router-dom';

import '@fortawesome/fontawesome-free-webfonts/css/fontawesome.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-regular.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-solid.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-brands.css';
import './common.scss';
import './theme/index.scss';
import { loadStorage } from '../utils/storage';

interface Props {
  core:Core;
}

interface State {
}

export class App extends React.Component<Props, State> {
  public renderApp () {
    return <MobileRouter core={this.props.core} />;
    // if (isMobile()) {
    //     return <Main_m core={this.props.core} />
    // } else {
    //     return <Main_pc core={this.props.core} />
    // }
  }

  public render () {
    const theme = loadStorage('theme');
    return (<div className={`theme-${theme}`} data-theme={theme} id="app">
      <Router history={this.props.core.history}>
        { this.renderApp() }
      </Router>
    </div>);
  }
}