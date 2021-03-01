import * as React from 'react';
import { Core } from '../core/index';

export interface ContextProps {
  context:{
    core:Core;
  };
}

export const Context = React.createContext({ core: new Core() });

export function contextWrapper<Props extends ContextProps, State extends {}> (Component:React.ComponentClass<Props, State>) {
  return class extends React.Component<Props, State> {
    public render () {
      return <Context.Consumer>
        { (context) => <Component context={context} {...this.props} /> }
      </Context.Consumer>;
    }
  };
}