// Based on jaredpalmer's npm module react-simple-infinite-scroll
// https://github.com/jaredpalmer/react-simple-infinite-scroll

import * as React from 'react';
const throttle = require('lodash.throttle');

export interface InfiniteScrollProps {
  /**
   * Does the resource have more entities
   */
  hasMore:boolean;

  /**
   * Should show loading
   */
  isLoading:boolean;

  /**
   * Callback to load more entities
   */
  onLoadMore:() => void;

  /**
   * Scroll threshold
   */
  threshold?:number;

  /**
   * Throttle rate
   */
  throttle?:number;

  /** Children */
  children?:any;
}

export class InfiniteScroll extends React.Component<InfiniteScrollProps, {}> {
  public static defaultProps:Pick<InfiniteScrollProps, 'threshold' | 'throttle'> = {
    threshold: 50,
    throttle: 100,
  };
  private sentinel!:HTMLDivElement | null;
  private resizeHandler!:() => void;
  private height:number = 0;
  private divElement!:HTMLDivElement | null;

  public componentDidMount() {
    this.height = this.divElement ? this.divElement.getBoundingClientRect().bottom : 0;
    this.resizeHandler = throttle(this.checkWindowScroll, this.props.throttle);

    window.addEventListener('resize', this.resizeHandler);
  }

  public componentWillUnmount() {
    window.removeEventListener('resize', this.resizeHandler);
  }

  public componentDidUpdate() {
    // This fixes edge case where initial content is not enough to enable scrolling on a large screen.
    this.checkWindowScroll();
  }

  private checkWindowScroll = () => {
    if (this.props.isLoading) {
      return;
    }

    if (
      this.props.hasMore &&
      this.sentinel &&
      this.sentinel.getBoundingClientRect().top - this.height <=
      this.props.threshold!
    ) {
      this.props.onLoadMore();
    }
  }

  public render() {
    const sentinel = <div ref={(i) => this.sentinel = i} />;

    return (
      <div ref={ (divElement) => this.divElement = divElement }
          style={{height:'100%', overflow:'scroll'}}
          onScroll={throttle(this.checkWindowScroll, this.props.throttle)}>
        <div>
          {this.props.children}
          {sentinel}
        </div>
      </div>
    );
  }
}

export default React.createFactory(InfiniteScroll);
