export function setIdleTimeout (cb:() => void, ms:number) {
    if ('requestIdleCallback' in window) {
        const tick = Date.now();
        const event = () => {
            if (Date.now() - tick < ms) {
                (window as any).requestIdleCallback(event);
            } else {
                cb();
            }
        };
        (window as any).requestIdleCallback(event);
    } else {
        setTimeout(cb, ms);
    }
}

export function setFrameTimeout (cb:() => void, ms:number) {
    if ('requestAnimationFrame' in window) {
        const tick = Date.now();
        const event = () => {
            if (Date.now() - tick < ms) {
                (window as any).requestAnimationFrame(event);
            } else {
                cb();
            }
        };
        (window as any).requestAnimationFrame(event);
    } else {
        setTimeout(cb, ms);
    }
}