import * as React from 'react';

import './dropdown.scss';
import { isMobile } from '../../../utils/mobile';
import { classnames } from '../../../utils/classname';

interface Props<T> {
  list:{text:string, value:T, icon?:string}[];
  onClick:(value:T, index:number) => void;
  onIndex?:number;
  title?:string;
  setState?:(callback:(onIndex:number) => void) => void;
  className?:string;
}
interface State {
  active:boolean;
  onIndex:number;
}

export class Dropdown<T extends string|number> extends React.Component<Props<T>, State> {
  public selectEl:HTMLSelectElement|null = null;
  public state:State = {
    active: false,
    onIndex: this.props.onIndex === undefined ? -1 : this.props.onIndex,
  };

  public componentDidMount () {
    if (this.props.setState) {
      this.props.setState((onIndex) => {
        this.setState({onIndex});
      });
    }
  }

  public render () {
    // TODO: render mobile
    return this.renderWeb();
    // return isMobile() ? this.renderMobile() : this.renderWeb();
  }

  public renderMobile () {
    // todo: css
    return <select className={classnames('dropdown-mobile', this.props.className)}
      ref={(el) => this.selectEl = el}
      onChange={(ev) => {
        for (let i = 0; i < this.props.list.length; i ++) {
          const item = this.props.list[i];
          if (item.value === ev.target.value) {
            this.setState({ onIndex: i });
            this.props.onClick(item.value, i);
            break;
          }
        }
      }}>
      {this.props.list.map((item, i) => <option
        key={i}
        value={item.value}>
        {item.text}
      </option>)}
    </select>;
  }

  public renderTrigger (onClick:() => void) {
    let text = '';
    if (this.state.onIndex < 0) {
      text = this.props.title || '';
    } else {
      text = this.props.list[this.state.onIndex].text;
    }
    return (
      <div className={classnames('dropdown-trigger', {'is-active': this.state.active})}>
        <div className="item"
          onClick={onClick}
          onBlur={() => this.setState({active: false})}
          aria-haspopup="true"
          aria-controls="dropdown-menu">
          <span>{text}</span>
          <span className="icon">
            <i className={classnames('fas', `fa-angle-${this.state.active ?   'up' : 'down'}`)} aria-hidden="true"></i>
          </span>
        </div>
      </div>);
  }

  // TODO: only fixed the css for renderWeb (renderMobile is still broken)
  // This component still needs clean up
  public renderWeb () {
    return (
      <div className={classnames('dropdown', {'is-active': this.state.active}, this.props.className)}>
      {this.renderTrigger(() => this.setState((prevState) => ({active: !prevState.active})))}
      {this.state.active && (
        <div className="dropdown-menu" role="menu">
        <div className="dropdown-content">
          {this.props.list.map((item, i) => (
            <div
              key={i}
              onClick={() => {
                this.setState({onIndex: i});
                this.props.onClick(item.value, i);
                }}
              className={
                classnames(
                  'dropdown-item',
                  'item',
                  {'selected' : this.state.onIndex == i},
                  )}>
                    {item.text}
            </div>))}
        </div>
      </div>
      )}
    </div>
    );
  }
}