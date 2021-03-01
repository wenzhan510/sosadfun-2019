import React from 'react';
import { Page } from '../../components/common/page';
import { MobileRouteProps } from '../router';
import { NavBar } from '../../components/common/navbar';

interface Props extends MobileRouteProps {

}

interface State {

}

export class Thread extends React.Component<Props, State> {
    public render () {
        return <Page
            top={<NavBar goBack={this.props.core.route.back}>帖子详情</NavBar>}
        >

        </Page>;
    }
}