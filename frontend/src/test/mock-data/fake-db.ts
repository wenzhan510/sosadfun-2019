import { API } from '../../core/api';
import { DB } from '../../config/db-type';
import { randomCnWords } from '../../utils/fake';

export function fakeMessages (count:number) {
    const res:DB.Message[] = [];
    for (let i = 1; i < count; i ++) {
        const message = DB.allocMessage();
        message.id = count;
        message.attributes.poster_id = 1;
        message.attributes.receiver_id = 2;
        message.attributes.body_id = count;
        message.attributes.created_at = Date.now().toString();
        if (message.message_body) {
            message.message_body.id = count;
            message.message_body.attributes.body = randomCnWords(20);
        }
        res.push(message);
    }
    return res;
}

export function fakeDB (db:API) {
    db.getMessages = async (query, id) => {
        return {
            messages: fakeMessages(10),
            paginate: DB.allocThreadPaginate(),
            style: 'sendbox',
        };
    };
    db.sendMessage = async (uid, _msg) => {
        const msg = DB.allocMessage();
        msg.id = 1;
        if (msg.message_body) {
            msg.message_body.id = 1;
            msg.message_body.attributes.body = _msg;
        }
        return {message: msg};
    };
}