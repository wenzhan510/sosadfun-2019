import React from 'react';
import { Page } from '../../components/common/page';
import { InputText } from '../../components/common/input/text';
import { Core } from '../../../core';

export class SearchPage extends React.Component<{
  // props
  core:Core;
}, {
  // state
  text:string;
}> {
  public state = {
    text: '',
  };

  public render () {
    return <Page className="search-page">
    <div className="search-bar">
        <InputText
          value={this.state.text}
          label={<i className="fa fa-search"></i>}
          onConfirm={() => {}}
          onChange={(text) => this.setState({text})}
        ></InputText>
        <div onClick={() => this.props.core.route.back()} className="cancel">取消</div>
      </div>
    </Page>;
  }
}