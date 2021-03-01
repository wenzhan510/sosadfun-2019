import React from 'react';
import './empty-state.scss';
import { classnames } from '../../../utils/classname';

interface Props {
  size?:'small' | 'middle' | 'large';
  tip?:string;
}

export class EmptyState extends React.Component<Props> {
  public render() {
    return <div className="empty-state">
      <div className={classnames('logo', this.props.size || 'small')}/>
      <div className="tip">
        { this.props.tip || '这里什么也没有'}
      </div>
    </div>;
  }
}