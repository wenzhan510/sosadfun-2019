import * as React from 'react';
import { Page } from '../view/components/common/page';

export const CardDecorator = (story) => <div style={{
  boxShadow: '1px 0px 1px 0px rgba(0, 0, 0, 0.3);',
}}>{story()}</div>;

export const pageDecorator = (story) => <Page
  children={story()}
/>;