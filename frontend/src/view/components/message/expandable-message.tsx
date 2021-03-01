import * as React from 'react';
import './expandable-message.scss';
import ClampLines from 'react-clamp-lines';

interface Props {
  title:React.ReactNode;
  uid:string;
  footer?:string;
  boldTitle?:boolean;
  content:string;
}
interface State {
  expanded:boolean;
}

// TODO: support bbcode
export class ExpandableMessage extends React.Component<Props, State> {
  public state:State = {
    expanded: false,
  };

  public toggle = () => {
    this.setState({expanded: !this.state.expanded});
  }

  private getBrief () {
    const contentWithOutLineBreak = this.props.content.replace(/\s+/g, ' ');
    return <ClampLines
              text={contentWithOutLineBreak}
              lines={2}
              id={`ct-em-${this.props.uid}`}
              ellipsis="..."
              buttons={false}
              innerElement="p"/>;
  }

  private getFullContent () {
    return this.props.content.split ('\n').map ((line, i) => <p key={i}>{line}</p>); // otherwise the '\n' in string will be ignored in <p/>
  }

  public render () {
    return (
    <div className="expandable-message" onClick={this.toggle}>
      <div className="expandable-message-title" onClick={this.toggle}>
        <span className={`expandable-message-title-text${this.props.boldTitle ? ' bold-title' : ''}`}>{this.props.title}</span>
        <span className="icon">
          <i className={`fas fa-caret-${this.state.expanded ? 'up' : 'down'}`} />
        </span>
      </div>
      <div className="expandable-message-content">
        {this.state.expanded ? this.getFullContent() : this.getBrief()}
        {this.props.footer && this.state.expanded && (<div className="expandable-message-footer"><span>{this.props.footer}</span></div>)}
      </div>
    </div>);
  }
}