import * as React from 'react';
import { Card } from './card';
import { classnames } from '../../../utils/classname';

export class Tab extends React.Component<{
  tabs:{name:string, children:React.ReactNode}[];
  className?:string;
  onClickTab?:(tabIndex:number, tabName:string) => void;
}, {
  onTab:number;
}> {
  public state = {
    onTab: 0,
  };

  public render () {
    const onTab = this.props.tabs[this.state.onTab];

    return <Card className={classnames('tab-card', this.props.className)}>
      <div className="tabs is-boxed">
        <ul>
          {this.props.tabs.map((tab, i) =>
            <li key={i}
              onClick={() => this.setState({onTab: i}, () => this.props.onClickTab && this.props.onClickTab(i, tab.name))}
              className={classnames({'is-active': this.state.onTab === i})}>
              <a><span>{tab.name}</span></a>
            </li>)}
        </ul>
      </div>
      <div className="tab-content">
        {onTab.children}
      </div>
    </Card>;
  }
}