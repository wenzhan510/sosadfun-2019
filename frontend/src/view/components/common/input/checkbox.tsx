import React from 'react';
import './checkbox.scss';
// based on https://www.w3schools.com/howto/howto_css_custom_checkbox.asp
type checkboxType = 'radio' | 'checkbox';
type checkboxColor = 'grey' | 'white';
export function Checkbox (props:{
  name?:string;
  value?:string | number;
  checked:boolean;
  onChange?:(() => void);
  disabled?:boolean;
  label?:string;
  type?:checkboxType;
  checkboxColor?:checkboxColor;
  style?:React.CSSProperties;
  className?:string;
}) {
  const type = props.type ? props.type : 'checkbox';
  return (
    <label
      className={`input-checkbox-container${
        props.className ? ' ' + props.className : ''}`}
      onClick={props.onChange}>
      { props.label && props.label }
      <input type={type}
        name={props.name}
        value={props.value}
        checked={props.checked}
        readOnly={true}
        onClick = {(e) => {
          e.stopPropagation();
        }}
      />
      <span className={`checkmark${
        props.checkboxColor ? ' ' + props.checkboxColor : ' grey'}`}/>
    </label>
  );
}