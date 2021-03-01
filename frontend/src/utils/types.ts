import * as React from 'react';

export type Styles = {[className:string]:React.CSSProperties};

export function checkType (src:any, target:any) {
  if (typeof target !== typeof src) {
    return false;
  }

  if (typeof target === 'object') {
    if (src instanceof Function || target instanceof Function) { return false; }
    if (src instanceof Array && target instanceof Array) { return true; }
    return checkObject(src, target);
  }

  // normal types
  return true;
}

export function checkObject (src:object, target:object) {
  const targetProps = Object.keys(target);
  let valid = false;
  for (let i = 0; i < targetProps.length; i ++) {
    const prop = targetProps[i];
    const targetValue = target[prop];
    if (typeof targetValue !== typeof src[prop]) { return false; }

    if (isNormalType(targetValue)) {
      continue;
    }

    if (targetValue instanceof Function) {
      return false;
    }

    if (targetValue instanceof Array) {
      valid = src[prop] instanceof Array;
      continue;
    }

    valid = checkObject(src[prop], targetValue);
  }
  return valid;
}

export function isNormalType (v:any) {
  if (typeof v === 'object') { return false; }
  return true;
}

export function checkArray (src:any[], target) {
  return src.every(((v) => {
    if (isNormalType(v)) {
      return typeof v === typeof target;
    }

    if (v instanceof Function) {
      return false;
    }

    if (v instanceof Array) {
      checkArray(v, target);
    }

    return checkObject(v, target);
  }));
}

export function checkNormalType (src:string|number|boolean, target:string|number|boolean) {
  if (typeof src !== typeof target) {
    return false;
  }
  return true;
}