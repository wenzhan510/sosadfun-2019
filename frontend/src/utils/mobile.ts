export const touchable = !!(typeof window !== 'undefined' && 'ontouchstart' in window);

export function preventTouchDefault () {
  // prevent default touch actions
  document.body.addEventListener('touchmove', preventDefaultEv, <any>{passive: false});
}

export function enableTouchDefault () {
  document.body.removeEventListener('touchmove', preventDefaultEv);
}

export function preventDefaultEv (ev) {
  ev.preventDefault();
}

export function isMobile () : boolean {
  return navigator.userAgent.indexOf('Mobile') > -1;
}