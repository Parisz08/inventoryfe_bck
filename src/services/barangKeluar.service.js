import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('barang-keluar/index', {params});
    },
    searchMaterial(params) {
        return Service().get('barang-keluar/search-material',  {params});
    },
    getMaterial(params) {
        return Service().get('barang-keluar/get-material', {params});
    },
    create(params) {
        return Service().post('barang-keluar/create', params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    update(id, params) {
        return Service().post('barang-keluar/update/'+id, params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    updateDesc(id, params) {
        return Service().post('barang-keluar/update-description/'+id, params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    delete(id) {
        return Service().post('barang-keluar/delete/'+id);
    },
}