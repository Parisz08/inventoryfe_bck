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
    addItemCondition(itemId, params) {
        return Service().post('spb/item-condition/'+itemId, params);
    },
    selectItemCondition(conditionId) {
        return Service().post('spb/item-condition/select/'+conditionId);
    },
    requestVendor(itemId, params) {
        return Service().post('spb/item-request-vendor/'+itemId, params);
    },
    unrequestVendor(requestedVendorId) {
        return Service().post('spb/item-request-vendor/remove/'+requestedVendorId);
    },
    lanjutPenawaran(id) {
        return Service().post('spb/lanjut-penawaran/'+id);
    },
    disposisi(id, params) {
        return Service().post('spb/disposisi/'+id, params);
    },
    resolusiPo(poId, params) {
        return Service().post('spb/po/resolusi/'+poId, params);
    },
    invoicePo(poId, params) {
        return Service().post('spb/po/invoice/'+poId, params);
    },
    paymentPo(poId, params) {
        return Service().post('spb/po/payment/'+poId, params);
    },
    delete(id) {
        return Service().post('spb/delete/'+id);
    },
    saveSignature(id, params) {
    return Service().post('spb/save-signature/'+id, params);
   },
   savePoSignature(poId, params) {
    return Service().post('spb/purchase-order/save-signature/'+poId, params);
   },
}