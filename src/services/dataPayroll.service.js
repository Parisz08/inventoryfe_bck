import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('payroll/index', {params});
    },
    show(params) {
        return Service().get('payroll/show', {params});
    },
    create(params) {
        return Service().post('payroll/create', params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    update(id, params) {
        return Service().post('payroll/update/'+id, params, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        });
    },
    delete(id) {
        return Service().post('payroll/delete/'+id);
    },
}