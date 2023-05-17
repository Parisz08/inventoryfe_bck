import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('dashboard/index', {params});
    },
    showEhp(params) {
        return Service().get('dashboard/show-ehp', {params});
    },
}