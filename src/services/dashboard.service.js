import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('dashboard/index', {params});
    },
    getMinStock(params) {
        return Service().get('dashboard/barang-min-stock', {params});
    },
    getMatSerTerpakai(params) {
        return Service().get('dashboard/barang-sering-terpakai', {params});
    },
}