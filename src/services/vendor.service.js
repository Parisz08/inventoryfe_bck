import Service from '@/helpers/service'

export default {
    index() {
        return Service().get('vendor/index');
    },
}