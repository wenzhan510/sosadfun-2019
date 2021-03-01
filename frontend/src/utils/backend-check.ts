/**
 * 鉴于后端当没有数据的时候传来的数据为空的Array, 有数据的时候传来的为Object或其他
 * 此函数用于检验数据是否存在
 * @param data
 */
export function hasData (data:any) {
    if (data instanceof Array) {
        return data.length > 0;
    }
    return !!data;
}