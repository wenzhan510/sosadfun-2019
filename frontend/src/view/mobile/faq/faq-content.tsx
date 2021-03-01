import * as React from 'react';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';
import { NavBar } from '../../components/common/navbar';
import '../message/style.scss'; // TODO: extract common scss out
import './style.scss';
import { DB } from '../../../config/db-type';
import { ExpandableMessage } from '../../components/message/expandable-message';
import { Constant } from '../../../config/constant';

interface State {
  typeName:string;
  filteredFaqs:DB.FAQ[];
}

export class FAQContent extends React.Component<MobileRouteProps, State> {
  public state:State = {
    typeName: '',
    filteredFaqs: [],
  };

  public async componentDidMount() {
    const faqs = await this.props.core.cache.FAQ.get();
    console.log(faqs);
    const typeKey:string = this.props.match.params.key;

    // get type name
    const [ t1, t2] = typeKey.split('-');
    const k1 = Number(t1);
    const k2 = Number(t2);
    const faqType = Constant.FAQTypes[k1];
    let subType:null | Constant.FAQType = null;
    if (faqType && faqType.children) { subType = faqType.children[k2]; }
    if (!subType) {
      // error
      console.error('invalid key');
    }

    // get filtered faqs
    // backend key starts from 1
    // frontend starts from 0.
    const filteredFaqs = faqs.filter(
      (f) => f.attributes.key == `${(Number(k1)) + 1}-${(Number(k2)) + 1}` );
    this.setState({ typeName: subType ? subType.title : '', filteredFaqs });
  }

  public render() {
    const { typeName, filteredFaqs } = this.state;
    return (<Page
        top={<NavBar goBack={this.props.core.route.back}>
          {typeName}
        </NavBar>}>

        { filteredFaqs.map((faq) => (
          <ExpandableMessage
            key={'faq' + faq.id}
            title={`Q: ${faq.attributes.question}`}
            uid={'pn' + faq.id}
            content={`A: ${faq.attributes.answer}`}
          />
        ))}
      </Page>);
  }
}