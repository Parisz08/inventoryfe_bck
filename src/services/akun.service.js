import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('akun/index', {params});
    },
    indexProfile() {
        return Service().get('akun/index-profile');
    },
    show(id) {
        return Service().get('akun/show/'+id);
    },
    create(params) {
        return Service().post('akun/create', params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    update(id, params) {
        return Service().post('akun/update/'+id, params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    delete(id) {
        return Service().post('akun/delete/'+id);
    },
}