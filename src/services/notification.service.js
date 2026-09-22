import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('notification/index', {params});
    },
    unreadCount() {
        return Service().get('notification/unread-count');
    },
    markRead(id) {
        return Service().post('notification/read/'+id);
    },
    markAllRead() {
        return Service().post('notification/read-all');
    },
}