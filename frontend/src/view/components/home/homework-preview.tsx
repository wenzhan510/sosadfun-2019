import * as React from 'react';
import { DB } from '../../../config/db-type';
import { classnames } from '../../../utils/classname';
import './homework-preview.scss';

interface Props {
  homeworks:DB.BriefHomework[];
  onMoreClick:() => void;
  onHomeworkClick:(id:number) => void;
}

export class HomeworkPreview extends React.Component<Props, {}> {
  public render() {
    const { homeworks, onMoreClick, onHomeworkClick } = this.props;
    return <div className="homework-preview">
      <div className="title">
        <div className="title-left">作业专区</div>
        <div className="title-right" onClick={onMoreClick}>查看更多</div>
      </div>
      <div className="homeworks">
        {homeworks.map((homework) =>
          <div className="homework" key={homework.id} onClick={() => onHomeworkClick(homework.id)}>
            <div className="active-tag">
              <span className={classnames(
                'tag-text',
                homework.attributes.is_active ? 'is-active-tag' : 'no-active-tag',
              )
              }>{homework.attributes.is_active ? '进行中' : '已结束'}</span>
            </div>
            <div className="detail">
              <div className="title">No.{homework.id}</div>
              <div className="topic-container">
                <span className="topic">{`[${homework.attributes.topic}]`}</span>
              </div>
              <div className="footer">
                <i className="fa fa-user" />
                <span> {homework.attributes.worker_count} </span>
                <i className="fa fa-list-ul" />
                <span> {homework.attributes.critic_count} </span>
              </div>
            </div>
          </div>,
        )}
      </div>
    </div>;
  }
}