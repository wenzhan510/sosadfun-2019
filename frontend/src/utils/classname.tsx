type clnBasicType = string|undefined;
type clnType = {[cln:string]:boolean|undefined}|clnBasicType;

export function classnames (firstName:clnType, ...args:clnType[]) {
  const cln:string[] = [];
  addToClassname(cln, firstName);
  for (let i = 0; i < args.length; i ++) {
    addToClassname(cln, args[i]);
  }
  return cln.join(' ');
}

function addToClassname (cln:string[], v:clnType) {
  if (v) {
    if (typeof v === 'object') {
      const keys = Object.keys(v);
      for (let i = 0; i < keys.length; i ++) {
        const _v = v[keys[i]];
        if (_v) {
          cln.push(keys[i]);
        }
      }
    } else {
      cln.push(v.toString());
    }
  }
}