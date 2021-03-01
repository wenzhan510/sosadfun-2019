import React from 'react';
import './text.scss';

type inputTextType = 'default' | 'wide';

interface Props {
  // props
  value:string;
  onChange:(text:string) => void;

  label?:React.ReactNode;
  placeholder?:string;
  placeholderCentered?:boolean;
  style?:React.CSSProperties;
  type?:inputTextType;
  onConfirm?:() => void;
  onKeyDown?:(ev:React.KeyboardEvent<HTMLInputElement>) => void;
  onClick?:() => void;
  maxLength?:number;
  warning?:string;
  info?:string;
}

export class InputText extends React.Component<Props, {}> {
  private inputRef:HTMLInputElement | undefined;

  private setInputRef = (e:HTMLInputElement) => {
    this.inputRef = e;
  }

  public focus(options?:FocusOptions) {
    this.inputRef && this.inputRef.focus(options);
  }

  public blur() {
    this.inputRef && this.inputRef.blur();
  }

  public select() {
    this.inputRef && this.inputRef.select();
  }

  public render() {
    const { placeholderCentered, type, maxLength, info, warning } = this.props;
    return (
      <div className="components-common-input-text">
        <div
          className={`container${placeholderCentered ?
            ' placeholder-center' : ''}
            ${(!!type && type != 'default') ? ` input-text-style-${type}` : ''}`}
          style={this.props.style}>
            {this.props.label ? <div className="label">{this.props.label}</div> : <div />}
          <input
            className="text-box"
            value={this.props.value}
            placeholder={this.props.placeholder}
            onChange={(ev) => this.props.onChange(ev.target.value)}
            onKeyDown={(ev) => {
              if (ev.keyCode === 13) {
                this.props.onConfirm && this.props.onConfirm();
              }
              this.props.onKeyDown && this.props.onKeyDown(ev);
            }}
            onClick={this.props.onClick}
            onTouchStart={this.props.onClick}
            ref={this.setInputRef}
            maxLength={maxLength}
          />
        </div>
        {warning && <p className="note warning">{warning}</p>}
        {info && <p className="note">{info}</p>}
      </div>);
  }
}