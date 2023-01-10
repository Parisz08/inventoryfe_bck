import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('payroll/index', {params});
    },
    show(params) {
        return Service().get('payroll/show', {params});
    },
    updatePayroll(params) {
        return Service().post('payroll/update-payroll', params);
    },
    sendSlip(params) {
        return Service().get('payroll/send-slip', {params});
    },
}