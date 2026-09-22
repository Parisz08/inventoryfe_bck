import Service from '@/helpers/service'

export default {
    index() {
        return Service().get('vendor/index');
    },
    show(id) {
        return Service().get('vendor/show/'+id);
    },
    create(params) {
        return Service().post('vendor/create', params);
    },
    update(id, params) {
        return Service().post('vendor/update/'+id, params);
    },
    delete(id) {
        return Service().post('vendor/delete/'+id);
    },
}