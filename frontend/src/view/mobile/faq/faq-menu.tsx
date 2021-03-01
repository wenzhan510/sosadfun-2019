import * as React from 'react';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';
import { NavBar } from '../../components/common/navbar';
import '../message/style.scss'; // TODO: extract common scss out
import './style.scss';
import { Menu, MenuItem } from '../../components/common/menu';
import { RoutePath } from '../../../config/route-path';
import { Constant } from '../../../config/constant';

interface State {
}

export class FAQMenu extends React.Component<MobileRouteProps, State> {

  // TODO: save faq data in localStorage

  private renderFAQGroup(faqGroup, id) {
    const history = this.props.core.history;
    return (
      <div key={id} className="faqgroup">
        <div className="faqgroup-title">{`【${faqGroup.title}】`}</div>
        <Menu>
          { faqGroup.children.map((subType, i) => (
            <MenuItem title={subType.title}
              onClick={() =>
                history.push(
                  RoutePath.FAQContent.replace(':key', `${id}-${i}`))}
              key={i} />))}
        </Menu>
      </div>
    );
  }
  public render () {
    return (<Page
        top={<NavBar goBack={this.props.core.route.back}>
          帮助FAQ
        </NavBar>}>

        { Constant.FAQTypes.map((type, i) => (
          (this.renderFAQGroup(type, i))
        ))}
      </Page>);
  }
}