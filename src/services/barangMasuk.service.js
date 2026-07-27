import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('barang-masuk/index', {params});
    },
    show(id) {
        return Service().get('barang-masuk/show/'+id);
    },
    cekMaterial(params) {
        return Service().get('barang-masuk/cek-material',  {params});
    },
    create(params) {
        return Service().post('barang-masuk/create', params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    update(id, params) {
        return Service().post('barang-masuk/update/'+id, params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    duplicate(params) {
        return Service().post('barang-masuk/duplicate', params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    delete(id) {
        return Service().post('barang-masuk/delete/'+id);
    },
}