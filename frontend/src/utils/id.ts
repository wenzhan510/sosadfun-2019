export interface TaggedId {
  id:number;
}

export function compareId (a:TaggedId, b:TaggedId) {
  return a.id - b.id;
}

function interpolate (objects:TaggedId[], l:number, h:number, id:number) {
  const a = objects[l].id;
  const b = objects[h].id;
  const t = (id - a) / (b - a);
  return Math.max(l, Math.min(h, Math.round((1 - t) * l + t * h) | 0));
}

export function indexEq (objects:TaggedId[], id:number) : number {
  let l = 0;
  let h = objects.length - 1;
  while (l < h) {
    const m = interpolate(objects, l, h, id);
    const x = objects[m].id;
    console.log('l, h', l, h, m, x);
    if (x === id) {
      return m;
    } else if (x < id) {
      l = m + 1;
    } else {
      h = m - 1;
    }
  }
  if (l === h && objects[l].id === id) {
    return l;
  }
  return -1;
}

export function indexPred (objects:TaggedId[], id:number) : number {
  let l = 0;
  let h = objects.length - 1;
  let i = l - 1;
  while (l < h) {
    const m = interpolate(objects, l, h, id);
    const x = objects[m].id;
    if (x <= id) {
      i = m;
      l = m + 1;
    } else {
      h = m - 1;
    }
  }
  if (l === h && objects[l].id <= id) {
    return l;
  }
  return i;
}