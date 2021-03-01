import React from 'react';
import './number.scss';
import { classnames } from '../../../../utils/classname';
import _ from 'lodash';

interface Props {
  value:number;
  onChange:(value:number) => void;
  disabled?:boolean;
  placeholder?:string;
  min?:number;
  max?:number;
  fractionDigits?:number;
  style?:React.CSSProperties;
  className?:string;
}

export class InputNumber extends React.Component<Props> {

  constructor(props:Props) {
    super(props);
    this.state = {
      value: props.value,
    };
  }

  public inputRef = React.createRef<HTMLInputElement>();

  public validate = () => {
    if (!this.inputRef.current) {
      return;
    }
    let value = parseFloat(this.inputRef.current.value);
    if (_.isNaN(value)) {
      value = this.props.value;
    }

    const fixedValue = parseFloat(value.toFixed(this.props.fractionDigits));
    if (value !== fixedValue) {
      value = fixedValue;
    }

    const min = this.props.min || -Infinity;
    const max = this.props.max || Infinity;
    if (!_.inRange(value, min, max)) {
      value = _.clamp(value, min, max);
    }
    this.inputRef.current.value = value.toString();
    this.props.onChange(value);
  }

  private onKeyDown = (event:React.KeyboardEvent<HTMLInputElement>) => {
    if (event.keyCode === 13  || event.keyCode === 27) {
      this.validate();
    }
  }

  public render() {
    return <input
      type="number"
      defaultValue={'' + this.props.value}
      ref={this.inputRef}
      onKeyDown={this.onKeyDown}
      onBlur={this.validate}
      disabled={this.props.disabled}
      placeholder={this.props.placeholder}
      min={this.props.min}
      max={this.props.max}
      style={this.props.style}
      className={classnames('input-number', this.props.className)}
    />;
  }
}