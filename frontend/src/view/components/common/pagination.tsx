import * as React from 'react';
import { Card } from './card';
import { Link } from 'react-router-dom';
import { URLParser } from '../../../utils/url';
import { classnames } from '../../../utils/classname';

export class Anchor extends React.Component <{
  className?:string;
  isDisabled?:boolean;
  to:string;
  children?:React.ReactNode;
  role?:string;
}, {}> {
  public el:HTMLAnchorElement|null = null;
  public componentDidUpdate () {
    if (!this.el) { return; }
    if (this.props.isDisabled) {
      this.el.setAttribute('disabled', '');
    } else {
      this.el.removeAttribute('disabled');
    }
  }
  public render () {
    return <Link
      className={this.props.className}
      to={this.props.to}
      role={this.props.role}
      innerRef={(el) => this.el = el}>{this.props.children}</Link>;
  }
}

export class Pagination extends React.Component <{
  style?:React.CSSProperties,
  className?:string,
  currentPage:number,
  lastPage:number,
}, {}> {
  public FirstShowPages = 3;
  public MiddleShowPages = 10;
  public LastShowPages = 3;

  public render () {
    const pages = new Array(this.props.lastPage);
    pages.fill(0);

    return <Card className={this.props.className}
      style={Object.assign({}, this.props.style || {})}>
      <nav className="pagination is-centered is-small" role="navigation" aria-label="pagination">
        <Anchor className="pagination-previous"
          to={(new URLParser()).setQuery('page', this.props.currentPage - 1).getPathname()}
          isDisabled={this.props.currentPage === 1}>&lt;</Anchor>
        <Anchor className="pagination-next"
          to={(new URLParser()).setQuery('page', this.props.currentPage + 1).getPathname()}
          isDisabled={this.props.currentPage === this.props.lastPage}>&gt;</Anchor>
        <ul className="pagination-list">
        { pages.map((_, idx) => {
          const page = idx + 1;
          if (page < this.FirstShowPages ||
            page > this.props.lastPage - this.LastShowPages ||
            page < this.props.currentPage + this.MiddleShowPages / 2 ||
            page > this.props.currentPage - this.MiddleShowPages / 2 ) {
            return <li key={page}>
              <Link
                className={classnames('pagination-link', {'is-current': page === this.props.currentPage})}
                to={(new URLParser()).setQuery('page', page).getPathname()}>{page}</Link>
            </li>;
          }
          if ((page === this.FirstShowPages + 1 && this.props.currentPage - this.MiddleShowPages / 2 > this.FirstShowPages + 1) ||
            (page === this.props.lastPage - 1 - this.LastShowPages && this.props.currentPage + this.MiddleShowPages / 2 < this.props.lastPage - 1 - this.LastShowPages)) {
            return <li key={page}>
              <span className="pagination-ellipsis">&hellip;</span>
            </li>;
          }
          return null;
        })}
        </ul>
      </nav>
    </Card>;
  }
}