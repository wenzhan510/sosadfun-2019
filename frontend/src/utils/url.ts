export type BasicType = string|number|boolean;
export type URLQuery = {[key:string]:BasicType|BasicType[]|undefined};
export function parsePath (path:string, query:URLQuery) {
  for (const key in query) {
    if (query[key] === '' || query[key] === undefined) {
      delete query[key];
    }
  }
  const res = path;

  const obj = Object.assign({}, query);
  // const matches = path.match(/:\w+/g);

  // if (matches) {
  //     for (const match of matches) {
  //         const key = match.substr(1);
  //         const value = obj[key];
  //         if (value && (
  //             typeof value === 'string' ||
  //             typeof value === 'number' ||
  //             typeof value === 'boolean'
  //         )) {
  //             res = res.replace(match, value.toString());
  //             delete obj[key];
  //         } else {
  //             res = res.replace(match, '');
  //         }
  //     }
  // }

  const url = new URLParser();
  url.pathname = res;
  url.queries = obj;
  return url.getPathname();
}

export class URLParser {
  public pathname:string;
  public hostname:string;
  public host:string;
  public port:number;
  public href:string;
  public originalSearch:string;
  public queries:{[query:string]:any} = {};
  public hash:string;
  public origin:string;
  public password:string;
  public protocol:string;
  public username:string;

  constructor (url?:string) {
    const u = new URL(url || window.location.href);
    this.pathname = u.pathname;
    this.host = u.host;
    this.hostname = u.hostname;
    this.port = +u.port;
    this.href = u.href;
    this.originalSearch = u.search;
    this.hash = u.hash;
    this.origin = u.origin;
    this.password = u.password;
    this.protocol = u.protocol;
    this.username = u.username;
    if (this.originalSearch) {
      const queries = this.originalSearch.substr(1).split('&');
      for (let i = 0; i < queries.length; i ++) {
        const q = queries[i].split('=');
        if (q.length !== 2) { continue; }
        if (q[1].startsWith('[') || q[1].startsWith['{']) {
          try {
            this.queries[q[0]] = JSON.parse(q[1]);
          } catch (e) {
            console.log(e);
            this.setQuery(q[0], q[1]);
          }
        } else {
          this.setQuery(q[0], q[1]);
        }
      }
    }
  }

  public getQuery (query:string) {
    return this.queries[query];
  }

  public getAllPath () : string[] {
    return this.pathname.substr(1).split('/').map((s) => '/' + s);
  }

  public setQuery (query:string, value:any) {
    if (typeof value === 'undefined') { return this; }
    if (typeof value === 'string') {
      if (!Number.isNaN(+value)) {
        this.queries[query] = +value;
      } else if (value === 'true') {
        this.queries[query] = true;
      } else if (value === 'false') {
        this.queries[query] = false;
      } else {
        this.queries[query] = value;
      }
    } else {
      this.queries[query] = value;
    }
    return this;
  }

  public setArrayQuery (query:string, value:(string|number|boolean)[]) {
    const v = this.queries[query];
    if (!v || !(v instanceof Array)) {
      this.queries[query] = value;
    } else {
      this.queries[query] = v.concat(value.filter((e) => v.indexOf(e) < 0));
    }
    return this;
  }

  public removeQuery (query:string) {
    delete this.queries[query];
    return this;
  }

  public removeArrayQuery (query:string, value:(string|number|boolean)[]) {
    const v = this.queries[query];
    if (!v) { return this; }
    this.queries[query] = v.filter((element) => value.indexOf(element) < 0);
    if (this.queries[query].length === 0) {
      this.removeQuery(query);
    }
    return this;
  }

  public getSearch (_quries?:{[query:string]:any}) {
    const queries = Object.keys(_quries || this.queries);
    if (queries.length === 0) { return ''; }
    const res:string[] = [];
    for (let i = 0; i < queries.length; i ++) {
      const q = queries[i];
      const v = this.queries[q];
      if (typeof v === 'string' || typeof v === 'number' || typeof v === 'boolean') {
        res.push(`${q}=${v}`);
        continue;
      }
      if (typeof v === 'object') {
        res.push(`${q}=${JSON.stringify(v)}`);
        continue;
      }
    }
    return '?' + res.join('&');
  }

  public getPathname (_quries?:{[query:string]:any}) {
    return this.pathname + this.hash + this.getSearch(_quries);
  }
}