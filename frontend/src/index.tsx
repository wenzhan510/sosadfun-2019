import * as ReactDOM from 'react-dom';
import * as React from 'react';
import { App } from './view';
import { Core } from './core';

async function run () {
  const core = new Core();
  await core.init();

  const root = document.createElement('div');
  document.body.appendChild(root);

  ReactDOM.render(
    <App core={core} />,
    root,
  );
}

run().catch(console.error);