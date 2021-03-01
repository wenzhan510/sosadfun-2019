import * as React from 'react';
import * as ReactDOM from 'react-dom';

import './test.scss';
import { a } from './a';

const testDom = document.createElement('div');
testDom.innerText = 'hello';
testDom.className = 'testDom';
document.body.appendChild(testDom);
a();

const btn = document.createElement('button');
btn.innerText = 'click me';
btn.onclick = (ev) => {
  // tslint:disable-next-line: no-floating-promises
  import(/* webpackChunkName: "pageA" */ './pageA').then((pageA) => {
    console.log('imported pageA');
  });
};
document.body.appendChild(btn);

const root = document.createElement('div');
document.body.appendChild(root);

ReactDOM.render(<div> react part </div>, root);