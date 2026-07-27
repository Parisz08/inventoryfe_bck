import Service from '@/helpers/service'

export default {

    index(params) {
        return Service().get('spb/index', {params});
    },
    show(id) {
        return Service().get('spb/show/'+id);
    },
    create(params) {
        return Service().post('spb/create', params);
    },
    approve(id, params) {
        return Service().post('spb/approve/'+id, params);
    },
    addCondition(id, params) {
        return Service().post('spb/condition/'+id, params);
    },
    selectCondition(conditionId) {
        return Service().post('spb/condition/select/'+conditionId);
    },
    disposisi(id, params) {
        return Service().post('spb/disposisi/'+id, params);
    },
    issuePO(id, params) {
        return Service().post('spb/issue-po/'+id, params);
    },
    resolusi(id, params) {
        return Service().post('spb/resolusi/'+id, params);
    },
    invoice(id, params) {
        return Service().post('spb/invoice/'+id, params);
    },
    payment(id, params) {
        return Service().post('spb/payment/'+id, params);
    },
    delete(id) {
        return Service().post('spb/delete/'+id);
    },
}