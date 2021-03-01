export function randomCnWords (length:number, commaWeight = 0.2) {
    const comma = '，';
    const period = '。';
    const range = [19968, 20500]; // all: 40869
    let res = '';
    for (let i = 0; i < length; i ++) {
        res += String.fromCharCode(Math.floor(Math.random() * (range[1] - range[0]) + range[0]));
        if (Math.random() < commaWeight) {
            res += comma;
        }
    }
    if (res.length > 20) {
        res += period;
    }
    return res;
}