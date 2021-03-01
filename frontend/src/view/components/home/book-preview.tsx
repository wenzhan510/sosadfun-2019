import * as React from 'react';
import { DB } from '../../../config/db-type';
import { Link } from 'react-router-dom';
import { parseDate } from '../../../utils/date';

// todo: mini version
interface Props {
  data:DB.Thread;
  mini?:boolean;
}
interface State {
}

export class BookPreview extends React.Component<Props, State> {
  public render () {
    const { attributes, id, author, tags, last_component } = this.props.data;
    const mini = this.props.mini;

    const cid = attributes.last_post_id;

    return <div className="book-item" key={id}>
      <Link className="title" to={`/book/${id}`}>{ attributes.title }</Link>
      {!mini && attributes.is_bianyuan && <span className="tag" style={{color: 'red'}}>边</span>}

      <div className="biref">{ attributes.brief }</div>
      { !mini && last_component &&
        <Link className="latest-chapter" to={`/book/${id}/chapter/${cid}`}>最新章节: { last_component.attributes.title }</Link>
      }
      { !mini && tags &&
        <div className="tags">
          { tags.map((tag, i) =>
            <Link className="tag" key={i} to={`/books/?tags=[${tag.id}]`}>{tag.attributes.tag_name}</Link>,
          )}
        </div>
      }
      <div className="meta">
        <Link className="author" to={`/user/${author.id}`}>{author.attributes.name}</Link>

        { mini && attributes.created_at && attributes.edited_at &&
        <span className="date">
          {parseDate(attributes.created_at)}/{parseDate(attributes.edited_at)}
        </span>
        }

        {
          !mini &&  <div className="counters">
          <span><i className="fas fa-pencil-alt"></i>{attributes.total_char}</span> /
          <span><i className="fas fa-eye"></i>{attributes.view_count}</span> /
          <span><i className="fas fa-comment-alt"></i>{attributes.reply_count}</span> /
        </div>
        }
      </div>
    </div>;
  }
}