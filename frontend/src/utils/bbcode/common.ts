export type BBCODETag = 'blockquote' | 'url' | 'email' | 'anchor' | 'b' | 'i' | 'size'
  | 'color' | 'highlight' | 'fly' | 'move' | 'indent' | 'font' | 'li'
  | 'ul' | 'ol' | 'code' | 'php' | 'java' | 'javascript' | 'cpp' | 'ruby'
  | 'python' | 'html' | 'mention' | 'span' | 'h1' | 'h2' | 'h3' | 'h4'
  | 'h5' | 'h6' | 'table' | 'tr' | 'td' | 's' | 'u' | 'sup' | 'sub'
  | 'youtube' | 'gvideo' | 'google' | 'baidu' | 'wikipedia' | 'img';

export type HTMLTag = 'div' | 'blockquote' | 'a' | 'strong' | 'em' | 'span'
  | 'marquee' | 'li' | 'ul' | 'ol' | 'pre' | 'h1' | 'h2' | 'h3' | 'h4'
  | 'h5' | 'h6' | 'table' | 'tr' | 'td' | 's' | 'u' | 'sup' | 'sub'
  | 'object' | 'embed' | 'img' | 'br'
  | 'p' | 'code' | '';

// TODO: comment out unsupported bbcode tags
export const BBCDOE_HTML_TAG_MAP:{[key in BBCODETag]:HTMLTag} = {
  'blockquote': 'blockquote',
  'url': 'a',
  'email': 'a',
  'anchor': 'a',
  'b': 'strong',
  'i': 'em',
  'size': 'span',
  'color': 'span',
  'highlight': 'span',
  'fly': 'marquee',
  'move': 'marquee',
  'indent': 'blockquote',
  'font': 'span',
  'li': 'li',
  'ul': 'ul',
  'ol': 'ol',
  'code': 'code',
  'php': 'code',
  'java': 'code',
  'javascript': 'code',
  'cpp': 'code',
  'ruby': 'code',
  'python': 'code',
  'html': '',
  'mention': 'span',
  'span': 'span',
  'h1': 'h1',
  'h2': 'h2',
  'h3': 'h3',
  'h4': 'h4',
  'h5': 'h5',
  'h6': 'h6',
  'table': 'table',
  'tr': 'tr',
  'td': 'td',
  's': 's',
  'u': 'u',
  'sup': 'sup',
  'sub': 'sub',
  'youtube': 'object',
  'gvideo': 'embed',
  'google': 'a',
  'baidu': 'a',
  'wikipedia': 'a',
  'img': 'img',
};

export const fontSizes = {
  'ql-size-small': 'small',
  'ql-size-large': 'large',
  'ql-size-huge':  'huge',
};