import * as React from 'react';
import './mark.scss';

interface Props {
  length:number;
  mark?:number;
  onClick?:(mark:number) => void;
  className?:string;
}
interface State {
  currentValue:number;
  value:number;
}

export class Mark extends React.Component<Props, State> {

  constructor(props:Props) {
    super(props);

    this.state = {
      currentValue: props.length,
      value: props.length,
    };
  }

  private selectValue(value:number) {
    const {onClick, mark} = this.props;
    if (mark) {
      return;
    }
    if (this.state.currentValue == value) {
      value -= 0.5;
    }
    this.setState({
      currentValue: value,
      value,
    },            () => {
      onClick && onClick(value + 1);
    });
  }

  public render () {
    const {length, mark} = this.props;
    const { currentValue } = this.state;
    const value = mark === undefined ? currentValue : mark - 1;

    const getClassName = (k) => {
      if (k == value + 0.5) {
        return 'fa fa-heart half';
      } else {
        return k <= value ? 'fa fa-heart full' : 'fa fa-heart';
      }
    };
    return (
      <div className={`comps-common-mark${ this.props.className ? ' ' + this.props.className : '' }`}>
        {/* It's necessary to have two layers, otherwise we will have trouble with padding/margin for the inner absolute div */}
        <div className="comps-common-mark-container">
          {(new Array(length)).fill('').map((v, k) => (
            <span
              style={{cursor: mark ? 'auto' : 'pointer'}}
              onClick={() => this.selectValue(k)}
              key={k}
            >
              <i className={getClassName(k)}></i>
            </span>
          ))}
          <span className="frame">{(new Array(length)).fill('').map((v, k) => (
              <i key={k} className="far fa-heart"></i>
          ))}</span>
        </div>
      </div>
    );
  }
}