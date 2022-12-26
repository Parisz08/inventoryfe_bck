import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('karyawan/index', {params});
    },
    show(id) {
        return Service().get('karyawan/show/'+id);
    },
    create(params) {
        return Service().post('karyawan/create', params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    update(id, params) {
        return Service().post('karyawan/update/'+id, params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    delete(id) {
        return Service().post('karyawan/delete/'+id);
    },
}