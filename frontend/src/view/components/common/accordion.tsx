import * as React from 'react';
import { classnames } from '../../../utils/classname';
import './accordion.scss';
import { List } from './list';

interface Props {
  title:React.ReactNode;
  children:React.ReactNode;
  arrow?:boolean;
  className?:string;
}
interface State {
  expanded:boolean;
}

export class Accordion extends React.Component<Props, State> {
  public state:State = {
    expanded: false,
  };

  public toggled = false;
  public toggle = () => {
    this.toggled = true;
    this.setState((prevState) => {
      const expanded = !prevState.expanded;
      return { expanded };
    });
  }

  public render () {
    let contentCln = 'comps-common-accordion';
    if (this.toggled) {
      contentCln = classnames(contentCln, `animate${this.state.expanded ? 'In' : 'Out'}`);
      this.toggled = false;
    }
    return <div className={classnames('accordion', this.props.className)}>
      <List.Item className="accordion-title" onClick={this.toggle}>
        {this.props.arrow && <div className="arrow">
          <i className={classnames('fas', `fa-angle-${this.state.expanded ? 'up' : 'down'}`)}></i>
        </div>}
        {this.props.title}
      </List.Item>
      {this.state.expanded &&
        <div className={contentCln}>{this.props.children}</div>
      }
    </div>;
  }
}