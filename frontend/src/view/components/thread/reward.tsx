import * as React from 'react';
import { Popup } from '../common/popup';
import './reward.scss';
import { Button } from '../common/button';
import { Colors } from '../../theme/theme';
import { InputNumber } from '../common/input/number';

type RewardType = 'salt'|'fish'|'ham';

interface Props {
  onClose:() => void;
  salt:number;
  fish:number;
  ham:number;
  onReward:(type:RewardType, value:number) => void;
}

interface State {
  value:number;
  selected?:RewardType;
}

export class Reward extends React.Component<Props, State> {
  public state:State = {
    value:1,
  };

  public rewards:[RewardType, string][] = [['salt', '盐粒'], ['fish', '咸鱼'], ['ham', '火腿']];

  public onSelect = (type:RewardType) => {
    this.setState({
      selected: type,
      value:1,
    });
  }

  public onInput = (value:number) => {
    this.setState({
      value,
    });
  }

  public validate = () => (this.state.selected && this.props[this.state.selected] !== 0
    && this.rewards.findIndex((v) => v[0] === this.state.selected) >= 0)

  public onConfirm = () => {
    if (this.validate()) {
      this.props.onReward(this.state.selected!, this.state.value);
    }
  }

  public render () {
    const maxValue = this.state.selected ? Math.min(100, this.props[this.state.selected]) : 100;
    return <Popup onClose={this.props.onClose} className="reward-content"
      style={{
        backgroundColor: 'var(--color-bg-base)',
      }} width="327px">
      <div className="title"> 打赏 </div>
      <div className="tip"> 对同一贴一天内只能打赏一次哦！ </div>
      <div className="reward">
        {
          this.rewards.map(
            (value) => {
              const [rewardType, name] = value;
              return <div className="reward-item" key={rewardType}
                onClick={() => this.onSelect(rewardType)}>
                <input type="radio" name="reward" value={rewardType}
                  checked={this.state.selected === rewardType} readOnly/>
                {name} (余额 <span> {this.props[rewardType]} </span> )
              </div>;
            },
          )
        }
      </div>
      <InputNumber className="count" placeholder={`填写数量（1 - ${maxValue}）`}
        disabled={this.state.selected === undefined || maxValue === 0}
        value={this.state.value} onChange={this.onInput}
        min={1} max={maxValue} fractionDigits={0}
      />
      <Button disabled={!this.validate()}
        onClick={this.onConfirm}
        color={Colors.primary}>
        确认
      </Button>
    </Popup>;
  }
}