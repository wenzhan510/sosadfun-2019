import * as React from 'react';
import { Dropdown } from '../../components/common/dropdown';

// toolbar
export function Toolbar (props:{
  filterOptions?:{text:string, value:any, icon?:string}[];
  setFilterOption?:(opt:string, i:number) => void;
  className?:string;
}) {
  return (
    <div className="blank-block">
      <div className="left">
        {props.filterOptions && props.setFilterOption &&
          <Dropdown
          className="left"
          list={props.filterOptions}
          onIndex={0}
          onClick={props.setFilterOption} />}
      </div>
      <span className="right">全部标记已读</span>
    </div>);
}