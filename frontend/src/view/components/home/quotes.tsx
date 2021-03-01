import * as React from 'react';
import { Link } from 'react-router-dom';
import { Carousel } from '../common/carousel';
import { DB } from '../../../config/db-type';
import { Core } from '../../../core/index';
import './quotes.scss';

interface Props {
  quotes:DB.Quote[];
  core:Core;
}

interface State {
  index:number;
}

export class Quotes extends React.Component<Props, State> {
  public state:State={
    index: 0,
  };

  public setIndex = (index:number) => {
    this.setState({index});
  }

  public render () {
    return <div className="quotes">
      <Carousel
        windowResizeEvent={this.props.core.windowResizeEvent}
        slides={this.props.quotes.map((quote) =>
          <div key={quote.id} className="content">
            <div className="body">
              <div>{quote.attributes.body}</div>
              <div className="author">—— {quote.attributes.is_anonymous ? quote.attributes.majia : quote.author.attributes.name}</div>
            </div>
          </div>)}
        getIndex={this.setIndex}
        indicator
      />

      <div className="btn left" onClick={() => {/* TODO: 投掷咸鱼 */}}>
        <i className="fa fa-gift"/>
        {` ${this.props.quotes[this.state.index] ? this.props.quotes[this.state.index].attributes.fish : 0} `}
      </div>
      {this.props.core.user.isLoggedIn() &&
        <Link to="/createquote" className="btn right">贡献题头</Link>
      }
    </div>;
  }
}