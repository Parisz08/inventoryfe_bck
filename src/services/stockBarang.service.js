import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('stock-barang/index', {params});
    },
    show(id) {
        return Service().get('stock-barang/show/'+id);
    },
    create(params) {
        return Service().post('stock-barang/create', params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    update(id, params) {
        return Service().post('stock-barang/update/'+id, params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    delete(id) {
        return Service().post('stock-barang/delete/'+id);
    },
}