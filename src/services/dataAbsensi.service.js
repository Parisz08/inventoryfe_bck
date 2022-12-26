import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('absensi/index', {params});
    },
    show(id) {
        return Service().get('absensi/show/'+id);
    },
    create(params) {
        return Service().post('absensi/create', params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    updateAbsen(params) {
        return Service().post('absensi/update-absen', params);
    },
}