import * as React from 'react';
import './chat-bubble.scss';
import { bbcode2html } from '../../../utils/text-formater';
// support bbcode

export class ChatBubble extends React.Component<{
  // props
  fromMe:boolean;
  content:string;
  style?:React.CSSProperties;
  className?:string;
}, {
  // state
}> {

  public render () {
    const htmlContent = bbcode2html(this.props.content);
    const style = this.props.fromMe ? 'talk-bubble from-me-color from-me' : 'talk-bubble from-other-color from-other';

    return (
      <div className={style}>
        {/* FIXIME: dangerouslySetInnerHTML is not good practice. we use it as our bbcode converter only converts bbcode to html string for now (not react element). We may modify the converter in future */}
        <div className="talktext"  dangerouslySetInnerHTML={{__html:htmlContent}}>
        </div>
      </div>);

  }
}