import * as React from 'react';
import { Link } from 'react-router-dom';
import { classnames } from '../../../utils/classname';
import './card.scss';

export class Card extends React.Component<{
  // props
  children?:React.ReactNode;
  style?:React.CSSProperties;
  className?:string;
  title?:string|{text:string, link:string};
  onClick?:() => void;
}, {
  // state
  }> {
    public rootElement:HTMLDivElement|null = null;

    public render () {
      return <div className={classnames('card', this.props.className)}
        onClick={this.props.onClick}
        ref={(el) => this.rootElement = el}
        style={this.props.style}>
          {this.props.title && (typeof this.props.title !== 'string' ?
        <Link to={this.props.title.link} className="title">{this.props.title.text}</Link> :
        <span className="title">{this.props.title}</span>)}
        {this.props.children}
      </div>;
    }
}