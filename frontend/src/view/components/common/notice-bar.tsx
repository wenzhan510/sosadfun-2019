import * as React from 'react';
import './notice-bar.scss';
import { classnames } from '../../../utils/classname';

interface Props {
  icon?:string;
  closable?:boolean;
  onClick?:() => void;
  customizeLink?:React.ReactNode;
  style?:React.CSSProperties;
}
interface State {
  roll:boolean;
}

export class NoticeBar extends React.Component<Props, State> {
  public state = {
    roll: false,
  };

  public render () {
    return <div className="notice-bar-wrapper" style={this.props.style}>
      {this.props.icon && <i className={classnames('notice-icon', this.props.icon)}></i>}
      <div className="notice-content-wrapper" ref="contentWrapper">
        <div className="notice-content" ref="content" style={{
          animation: this.state.roll ? '20s wordsLoop linear infinite normal' : undefined,
        }}>
          {this.props.children}
        </div>
      </div>
      { (this.props.closable || this.props.onClick) &&
        <div className="action" onClick={this.props.onClick}>
          { this.props.closable &&
            <i className="fas fa-times"></i> ||
            this.props.customizeLink ||
            <i className="fas fa-angle-right"></i>
          }
        </div>
      }
    </div>;
  }

  public componentDidMount () {
    const wrapper = this.refs.contentWrapper as HTMLDivElement;
    const content = this.refs.content as HTMLDivElement;
    if (wrapper && content) {
      if (content.offsetWidth > wrapper.offsetWidth) {
        this.setState({roll: true});
      }
    }
  }
}