import React from 'react';
import { Animate } from './animate';
import './popup-menu.scss';
import './picker.scss';

export interface Item {
  label:React.ReactNode;
  value:string;
}

interface ColumnProp {
  itemHeight:number;
  height:number;
  items:Item[];
  onChange:(value?:string) => void;
}

class Cloumn extends React.Component<ColumnProp> {
  private timer:number=0;
  private ref = React.createRef<HTMLDivElement>();

  private onScroll = () => {
    clearTimeout(this.timer);
    this.timer = setTimeout(this.scrollEnd as TimerHandler, 350);
  }

  private scrollEnd = () => {
    if (this.ref.current) {
      const index = this.ref.current.scrollTop / this.props.itemHeight;
      if (!Number.isInteger(index)) {
        this.ref.current.scrollTo(0, Math.round(index) * this.props.itemHeight);
        return;
      }
      if (index >= 0 && index < this.props.items.length) {
        this.props.onChange(this.props.items[index].value);
      } else {
        this.props.onChange(undefined);
      }
    }
  }

  public render() {
    const topBottomPadding = `${(this.props.height - this.props.itemHeight) / 2}px`;
    return <div className="column" style={{height: this.props.height}}
      onScroll={this.onScroll} ref={this.ref}>
        {this.props.items.map((item, index) => <div className="item"
          style={{
            height: this.props.itemHeight,
            lineHeight: `${this.props.itemHeight}px`,
            paddingTop: index === 0 ? topBottomPadding :'0',
            paddingBottom: index === this.props.items.length - 1 ? topBottomPadding :'0',
          }} key={item.value}>
            {item.label}
        </div>)}
    </div>;
  }
}

type SelectedValueType = {[key:string]:string};

interface ColumnOpt {
  key:string;
  items:(selectedValue:SelectedValueType) => Item[];
}

interface Props {
  itemHeight?:number;
  height?:number;
  onCancel:() => void;
  onConfirm:(selectedValue:SelectedValueType) => void;
  columnOpts:ColumnOpt[];
}

interface State {
  onClosing:boolean;
  selectedValue:SelectedValueType;
}

export class Picker extends React.Component<Props, State> {
  public readonly timeout = 500;

  public state:State = {
    onClosing:false,
    selectedValue:{},
  };

  public constructor(props:Props) {
    super(props);
    const initState = {};
    this.props.columnOpts.reduce((prevValue:SelectedValueType, current) => {
      const items = current.items(prevValue);
      if (items.length > 0) {
        prevValue[current.key] = items[0].value;
      }
      return prevValue;
    },                           initState);
    this.state.selectedValue = initState;
  }

  public onColumnChange = (key:string) => {
    return (value?:string) => {
      const newSelectedValue = {...this.state.selectedValue};
      if (value) {
        newSelectedValue[key] = value;
      } else {
        delete newSelectedValue[key];
      }
      this.setState({
        selectedValue:newSelectedValue,
      });
    };
  }

  public render() {
    const name = this.state.onClosing ? 'slideOutDown' : 'slideInUp';
    const itemHeight = this.props.itemHeight || 30;
    const height = this.props.height || 300;
    return <div className="popupMenu-wrapper picker">
      <div className="background" onClick={this.onClose}></div>
      <Animate name={name}  className="picker-animate" speed="faster">
        <div className="content">
          <div className="title">
            <div onClick={this.onClose}> 取消 </div>
            <div onClick={this.onConfirmClick}> 完成 </div>
          </div>
          <div className="columns-container"  style={{height: height}}>
            <div className="indicator-container">
              <div className="top"/>
              <div className="indicator" style={{height: itemHeight}}/>
              <div className="bottom"/>
            </div>
            <div className="columns">
              {this.props.columnOpts.map((opt) => {
                return <Cloumn items={opt.items(this.state.selectedValue)} height={height}
                  itemHeight={itemHeight} onChange={this.onColumnChange(opt.key)} key={opt.key} />;
              })}
            </div>
          </div>
        </div>
      </Animate>
    </div>;
  }

  public isConfrim = false;

  public onConfirmClick = () => {
    this.isConfrim = true;
    this.onClose();
  }

  public onClose = () => {
    this.setState({ onClosing: true }, () => requestAnimationFrame(this.waitClose));
  }

  public tick = 0;
  public waitClose = () => {
    this.tick += 1000 / 60;
    if (this.tick >= this.timeout) {
      if (this.isConfrim) {
        this.props.onConfirm(this.state.selectedValue);
      } else {
        this.props.onCancel();
      }
      return;
    }
    requestAnimationFrame(this.waitClose);
  }
}