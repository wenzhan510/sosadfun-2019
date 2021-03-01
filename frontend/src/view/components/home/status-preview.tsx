import * as React from 'react';
import { DB } from '../../../config/db-type';

interface Props {
  status:DB.Status;
}
interface State {
}

export class StatusPreview extends React.Component<Props, State> {
  public render () {
    return <div>
      {this.props.status.attributes.body}
    </div>;
  }
}