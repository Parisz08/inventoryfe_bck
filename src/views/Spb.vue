<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
            <h6 class="mb-0">Data SPPB (Surat Permohonan Permintaan Barang)</h6>
            <div class="d-flex align-items-center" style="gap: 10px;">
              <select class="form-select" style="width: 200px;" v-model="search.status" @change="get()">
                <option value="">Semua Status</option>
                <option>Menunggu Approval</option>
                <option>Ditolak</option>
                <option value="Permintaan Pengadaan">Permintaan / Penawaran ke Vendor</option>
                <option>Disposisi</option>
                <option>PO Diterbitkan</option>
                <option value="Resolusi">Receive Material</option>
                <option>Invoice</option>
                <option>Selesai</option>
              </select>
              <argon-button
                variant="gradient"
                color="success"
                size="sm"
                @click="openCreate()"
              ><i class="fa fa-plus fa-sm" aria-hidden="true"></i> Buat SPPB</argon-button>
            </div>
          </div>

          <div class="card-body px-0 pt-0 pb-2 mt-4">
            <div class="table-responsive p-0 scroll">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No SPPB</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Divisi</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Dibuat Oleh</th>
                    <th class="text-secondary"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in table.data" :key="i">
                    <td class="align-middle text-center text-sm">
                      <span class="badge badge-sm bg-gradient-primary">{{ row.no_spb }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.divisi }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="status-pill" :style="statusPillStyle(row.status)">{{ statusLabel(row.status) }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.request_date }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.created_by }}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <argon-button color="info" size="xs" variant="gradient" @click="openDetail(row.id)">Detail</argon-button>
                      <argon-button v-if="userRole === 'Admin'" color="danger" size="xs" variant="gradient" class="ms-1" @click="doDelete(row.id, row.no_spb)">Hapus</argon-button>
                    </td>
                  </tr>
                  <tr v-if="table.data.length === 0">
                    <td colspan="6" class="text-center text-sm text-secondary py-3">Belum ada data SPPB</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ MODAL BUAT SPPB BARU ============ -->
  <vue-final-modal v-model="formCreate.show" classes="modal-container" content-class="modal-content-width" :z-index="10000">
    <div class="row">
      <div class="col-11 float-left"><span class="modal__title">Buat SPPB Baru</span></div>
      <div class="col-1 float-right">
        <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="formCreate.show = false"></i>
      </div>
    </div><hr>
    <div class="modal__content container">
      <div class="row">
        <div class="col-6">
          <p>Divisi:
            <select class="form-select" v-model="newSpb.divisi">
              <option value="">Pilih Divisi</option>
              <option value="Pipa">Pipa</option>
              <option value="Slitting">Slitting</option>
              <option value="Shearing">Shearing</option>
              <option value="Maintenance">Maintenance</option>
              <option value="Logistic">Logistic</option>
            </select>
          </p>
        </div>
      </div>
      <hr>
      <argon-button color="success" size="xs" class="mb-2" @click="addItemRow()"><i class="fa fa-plus fa-sm"></i> Tambah Barang</argon-button>
      <div class="table-responsive">
        <table class="table align-items-center mb-0">
          <thead>
            <tr style="background-color: #F0F8FF;">
              <th class="text-center text-xxs">Kode Material</th>
              <th class="text-center text-xxs">Nama Barang</th>
              <th class="text-center text-xxs">Kategori</th>
              <th class="text-center text-xxs">Spesifikasi</th>
              <th class="text-center text-xxs">Merek</th>
              <th class="text-center text-xxs">Qty</th>
              <th class="text-center text-xxs">Satuan</th>
              <th class="text-center text-xxs">Keterangan</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in newSpb.items" :key="idx">
              <td>
                <input class="form-control form-control-sm" v-model="item.material_code" readonly placeholder="otomatis">
              </td>
              <td>
                <input class="form-control form-control-sm" list="stockNameList" v-model="item.material_name" @input="onNameInput(item)" placeholder="Ketik/pilih nama barang">
              </td>
              <td>
                <select class="form-select form-select-sm" v-model="item.kategori">
                  <option value="">-</option>
                  <option v-for="(label, code) in kategoriList" :key="code" :value="code">{{ code }}</option>
                </select>
              </td>
              <td><input class="form-control form-control-sm" v-model="item.specification" :readonly="!!item.material_code"></td>
              <td><input class="form-control form-control-sm" v-model="item.merek"></td>
              <td><input type="number" min="1" class="form-control form-control-sm" v-model="item.qty"></td>
              <td><input class="form-control form-control-sm" v-model="item.unit" :readonly="!!item.material_code"></td>
              <td><input class="form-control form-control-sm" v-model="item.note"></td>
              <td><i class="fa fa-times-circle" style="cursor:pointer;" @click="newSpb.items.splice(idx,1)"></i></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="row mt-3">
        <div class="col-8">
          <p class="text-xs font-weight-bold mb-1">KATEGORI :</p>
          <table class="table table-sm table-bordered text-xs" style="max-width: 420px;">
            <tbody>
              <tr v-for="(pair, i) in kategoriPairs" :key="i">
                <td class="font-weight-bold">{{ pair[0].label }}</td>
                <td>{{ pair[0].code }}</td>
                <td class="font-weight-bold">{{ pair[1] ? pair[1].label : '' }}</td>
                <td>{{ pair[1] ? pair[1].code : '' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
         <datalist id="stockNameList">
        <option v-for="s in stockList" :key="s.id" :value="s.material_name"></option>
      </datalist> 
      </div>

      <div class="text-center mt-4">
        <argon-button color="success" variant="gradient" size="sm" :disabled="submitting" @click="submitCreate()">
          <i class="fa fa-check"></i> {{ submitting ? 'Mengirim...' : 'Ajukan SPPB' }}
        </argon-button>
      </div>
    </div>
  </vue-final-modal>

  <!-- ============ MODAL DETAIL & PROSES SPPB ============ -->
  <vue-final-modal v-model="formDetail.show" classes="modal-container" content-class="modal-content-width" :z-index="10000">
    <div class="row">
      <div class="col-11 float-left"><span class="modal__title">Detail SPPB {{ detail.no_spb }}</span></div>
      <div class="col-1 float-right">
        <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="formDetail.show = false"></i>
      </div>
    </div><hr>
    <div class="modal__content container" v-if="detail.id">
      <p>
        <b>Divisi:</b> {{ detail.divisi }} &ensp;|&ensp;
        <b>Status:</b> <span class="status-pill" :style="statusPillStyle(detail.status)">{{ statusLabel(detail.status) }}</span><br>
        <b>Dibuat oleh:</b> {{ detail.created_by }} pada {{ detail.request_date }}
      </p>
      <hr>
      <h6>Barang yang Diminta</h6>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Kode Material</th><th>Nama Barang</th><th>Kategori</th><th>Spesifikasi</th>
              <th>Merek</th><th>Qty</th><th>Satuan</th><th>Stok Aktual / Min</th><th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(it, i) in detail.items" :key="i">
              <td>{{ it.material_code || '-' }}</td>
              <td>{{ it.material_name }}</td>
              <td>{{ it.kategori || '-' }}</td>
              <td>{{ it.specification || '-' }}</td>
              <td>{{ it.merek || '-' }}</td>
              <td>{{ it.qty }}</td>
              <td>{{ it.unit }}</td>
              <td>{{ it.actual_stock !== null && it.actual_stock !== undefined ? it.actual_stock + ' / ' + it.min_stock : '-' }}</td>
              <td>{{ it.note || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TAHAP: Menunggu Approval -->
      <div v-if="detail.status === 'Menunggu Approval'">
        <hr><h6>Approval Atasan</h6>
        <div v-if="userRole === 'Admin'">
          <textarea class="form-control mb-2" placeholder="Catatan approval (opsional)" v-model="actionForm.approval_note"></textarea>
          <argon-button color="success" size="sm" class="me-2" @click="doApprove(true)">Setujui</argon-button>
          <argon-button color="danger" size="sm" @click="doApprove(false)">Tolak</argon-button>
        </div>
        <p v-else class="text-secondary text-sm">Menunggu approval dari Admin/atasan.</p>
      </div>

      <!-- TAHAP: Ditolak -->
      <div v-if="detail.status === 'Ditolak'">
        <hr><p class="text-danger"><b>SPPB ini ditolak.</b> Catatan: {{ detail.approval_note }}</p>
        <div v-if="userRole === 'Admin'">
          <p class="text-secondary text-sm">Admin bisa membatalkan penolakan dan menyetujui ulang SPPB ini:</p>
          <textarea class="form-control mb-2" placeholder="Catatan approval (opsional)" v-model="actionForm.approval_note"></textarea>
          <argon-button color="success" size="sm" @click="doApprove(true)">Setujui Ulang</argon-button>
        </div>
      </div>

      <!-- TAHAP: Permintaan Pengadaan (komparasi & pilih vendor, khusus Admin) -->
      <div v-if="detail.status === 'Permintaan Pengadaan'">
        <hr>
        <div v-if="userRole === 'Admin'">
          <h6>Komparasi Penawaran Vendor</h6>
          <table class="table table-sm" v-if="detail.conditions && detail.conditions.length">
            <thead><tr><th>Pilih</th><th>#</th><th>Vendor</th><th>Harga</th><th>Catatan</th></tr></thead>
            <tbody>
              <tr v-for="(c, i) in detail.conditions" :key="i" :class="c.selected ? 'table-success' : ''">
                <td><input type="radio" name="selectedVendor" :checked="c.selected" @change="doSelectVendor(c.id)"></td>
                <td>{{ c.round }}</td>
                <td>{{ c.supplier }} <span v-if="c.selected" class="badge bg-success">Terpilih</span></td>
                <td>{{ c.price }}</td>
                <td>{{ c.condition_note }}</td>
              </tr>
            </tbody>
          </table>
          <p v-else class="text-secondary text-sm">Belum ada penawaran vendor yang ditambahkan.</p>

          <h6>Tambah Penawaran Vendor</h6>
          <div class="row">
            <div class="col-4">
              <select class="form-select" v-model="actionForm.vendor_id">
                <option value="">Pilih Vendor</option>
                <option v-for="v in vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
              </select>
            </div>
            <div class="col-4"><input type="number" class="form-control" placeholder="Harga Penawaran" v-model="actionForm.price"></div>
            <div class="col-4"><input class="form-control" placeholder="Syarat/Catatan" v-model="actionForm.condition_note"></div>
          </div>
          <argon-button color="info" size="sm" class="mt-2" @click="doAddCondition()">Simpan Penawaran</argon-button>

          <hr><h6>Finalisasi Pilihan Vendor</h6>
          <p class="text-secondary text-sm" v-if="!detail.conditions || !detail.conditions.some(c => c.selected)">
            Pilih (radio) salah satu vendor di atas terlebih dahulu.
          </p>
          <textarea class="form-control mb-2" placeholder="Catatan (opsional)" v-model="actionForm.disposisi_note"></textarea>
          <argon-button color="success" size="sm" class="me-2" :disabled="!detail.conditions || !detail.conditions.some(c => c.selected)" @click="doDisposisi(true)">Konfirmasi & Lanjutkan ke Purchasing</argon-button>
          <argon-button color="warning" size="sm" @click="doDisposisi(false)">Belum Ada yang Sesuai</argon-button>
        </div>
        <p v-else class="text-secondary text-sm">SPPB sedang diproses oleh Admin (komparasi & pemilihan vendor).</p>
      </div>

      <!-- TAHAP: Disposisi (siap terbitkan PO, khusus Purchasing) -->
      <div v-if="detail.status === 'Disposisi'">
        <hr><h6>Terbitkan Purchase Order</h6>
        <div v-if="userRole === 'Purchasing'">
          <div class="row">
            <div class="col-6"><input class="form-control mb-2" placeholder="No. PO" v-model="actionForm.po_number"></div>
            <div class="col-6"><input type="date" class="form-control mb-2" v-model="actionForm.po_date"></div>
            <div class="col-6"><input class="form-control mb-2" placeholder="Supplier" v-model="actionForm.po_supplier"></div>
            <div class="col-6"><input type="number" class="form-control mb-2" placeholder="Total (Rp)" v-model="actionForm.po_total"></div>
          </div>
          <argon-button color="success" size="sm" @click="doIssuePO()">Terbitkan PO</argon-button>
        </div>
        <p v-else class="text-secondary text-sm">Menunggu akun Purchasing menerbitkan PO.</p>
      </div>

      <!-- TAHAP: PO Diterbitkan (Receive Material, khusus Purchasing) -->
      <div v-if="detail.status === 'PO Diterbitkan'">
        <hr><p><b>No. PO:</b> {{ detail.po_number }} | <b>Supplier:</b> {{ detail.po_supplier }} | <b>Total:</b> {{ detail.po_total }}</p>
        <div v-if="userRole === 'Purchasing'">
          <h6>Receive Material (Barang Diterima)</h6>
          <textarea class="form-control mb-2" placeholder="Catatan resolusi" v-model="actionForm.resolusi_note"></textarea>
          <argon-button color="success" size="sm" @click="doResolusi()">Simpan Resolusi</argon-button>
        </div>
        <p v-else class="text-secondary text-sm">Menunggu akun Purchasing menerima barang.</p>
      </div>

      <!-- TAHAP: Resolusi (Invoice, khusus Purchasing) -->
      <div v-if="detail.status === 'Resolusi'">
        <hr>
        <div v-if="userRole === 'Purchasing'">
          <h6>Catat Invoice Supplier</h6>
          <div class="row">
            <div class="col-4"><input class="form-control mb-2" placeholder="No. Invoice" v-model="actionForm.invoice_number"></div>
            <div class="col-4"><input type="date" class="form-control mb-2" v-model="actionForm.invoice_date"></div>
            <div class="col-4"><input type="number" class="form-control mb-2" placeholder="Jumlah (Rp)" v-model="actionForm.invoice_amount"></div>
          </div>
          <argon-button color="success" size="sm" @click="doInvoice()">Simpan Invoice</argon-button>
        </div>
        <p v-else class="text-secondary text-sm">Menunggu akun Purchasing mencatat invoice.</p>
      </div>

      <!-- TAHAP: Invoice (Payment, khusus Purchasing) -->
      <div v-if="detail.status === 'Invoice'">
        <hr><p><b>No. Invoice:</b> {{ detail.invoice_number }} | <b>Jumlah:</b> {{ detail.invoice_amount }}</p>
        <div v-if="userRole === 'Purchasing'">
          <h6>Catat Pembayaran</h6>
          <div class="row">
            <div class="col-4"><input type="date" class="form-control mb-2" v-model="actionForm.payment_date"></div>
            <div class="col-4"><input type="number" class="form-control mb-2" placeholder="Jumlah (Rp)" v-model="actionForm.payment_amount"></div>
            <div class="col-4">
              <select class="form-select mb-2" v-model="actionForm.payment_method">
                <option value="">Metode Pembayaran</option>
                <option>Transfer Bank</option>
                <option>Cash</option>
                <option>Giro</option>
              </select>
            </div>
          </div>
          <argon-button color="success" size="sm" @click="doPayment()">Simpan Pembayaran</argon-button>
        </div>
        <p v-else class="text-secondary text-sm">Menunggu akun Purchasing mencatat pembayaran.</p>
      </div>

      <!-- TAHAP: Selesai -->
      <div v-if="detail.status === 'Selesai'">
        <hr>
        <p class="text-success"><b>SPPB ini sudah selesai.</b></p>
        <p><b>No. PO:</b> {{ detail.po_number }} — {{ detail.po_supplier }} (Rp {{ detail.po_total }})<br>
           <b>Invoice:</b> {{ detail.invoice_number }} (Rp {{ detail.invoice_amount }})<br>
           <b>Dibayar:</b> {{ detail.payment_date }} — Rp {{ detail.payment_amount }} via {{ detail.payment_method }}</p>
      </div>
    </div>
  </vue-final-modal>
</template>

<script>
import ArgonButton from "@/components/ArgonButton.vue";
import { VueFinalModal } from 'vue-final-modal'
import Api from '@/helpers/api';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import spb from '@/services/spb.service';
import akun from '@/services/akun.service';
import vendor from '@/services/vendor.service';
import stockBarang from '@/services/stockBarang.service';

export default {
  name: "sppb",
  components: {
    ArgonButton,
    VueFinalModal,
  },
  data() {
    return {
      submitting: false,
      userRole: '',
      vendors: [],
      stockList: [],
      table: { data: [] },
      search: { status: '' },
      formCreate: { show: false },
      formDetail: { show: false },
      newSpb: { divisi: '', items: [] },
      detail: {},
      actionForm: {},
      kategoriList: {
        A: 'Aset', B: 'Consumable', C: 'Sparepart', D: 'Tools',
        E: 'Jasa', F: 'Maintenance', G: 'Stationary', H: 'Lain-lain',
      },
    };
  },
  computed: {
    kategoriPairs() {
      const entries = Object.entries(this.kategoriList).map(([code, label]) => ({ code, label }));
      const pairs = [];
      for (let i = 0; i < 4; i++) {
        pairs.push([entries[i], entries[i + 4]]);
      }
      return pairs;
    },
  },
  mounted() {
    this.get();
    this.getRole();
    this.getVendors();
    this.getStockList();
  },
  methods: {
    notify(message, type) {
      toast(message, { autoClose: 2000, type: type, position: 'top-right' });
    },
    statusPillStyle(status) {
      const map = {
        'Menunggu Approval': { bg: '#fff3cd', color: '#8a6d00' },
        'Ditolak':            { bg: '#fbdcdc', color: '#a71d2a' },
        'Permintaan Pengadaan': { bg: '#ffe4c4', color: '#8a4b00' },
        'Disposisi':          { bg: '#d4ecff', color: '#0b5ed7' },
        'PO Diterbitkan':     { bg: '#e0d9fb', color: '#5b21b6' },
        'Resolusi':           { bg: '#d9e9fb', color: '#1a4f8a' },
        'Invoice':            { bg: '#d9e9fb', color: '#1a4f8a' },
        'Selesai':            { bg: '#d3f5df', color: '#0f7a3d' },
      };
      const c = map[status] || { bg: '#e9ecef', color: '#495057' };
      return { backgroundColor: c.bg, color: c.color };
    },
    statusLabel(status) {
      const map = {
        'Permintaan Pengadaan': 'Permintaan / Penawaran ke Vendor',
        'Resolusi': 'Receive Material',
      };
      return map[status] || status;
    },
    get() {
      let context = this;
      Api(context, spb.index({ status: context.search.status })).onSuccess(function (response) {
        context.table.data = response.data.data.data;
      }).onError(function () {
        context.table.data = [];
        context.notify('Gagal mengambil data SPPB', 'error');
      }).call();
    },
    getRole() {
      let context = this;
      Api(context, akun.indexProfile()).onSuccess(function (response) {
        context.userRole = response.data.data[0].role;
      }).onError(function () {}).call();
    },
    getVendors() {
      let context = this;
      Api(context, vendor.index()).onSuccess(function (response) {
        context.vendors = response.data.data;
      }).onError(function () {}).call();
    },
    getStockList() {
      let context = this;
      Api(context, stockBarang.index()).onSuccess(function (response) {
        context.stockList = response.data.data.data;
      }).onError(function () {}).call();
    },
    onNameInput(item) {
      const stock = this.stockList.find(s => s.material_name === item.material_name);
      if (stock) {
        item.material_code = stock.material_code;
        item.specification = stock.specification;
        item.unit = stock.unit;
      } else {
        // ketik nama yang belum ada di master -> anggap barang baru, manual
        item.material_code = '';
      }
    },
    openCreate() {
      this.newSpb = { divisi: '', items: [] };
      this.addItemRow();
      this.formCreate.show = true;
    },
    addItemRow() {
      this.newSpb.items.push({ material_name: '', material_code: '', kategori: '', merek: '', specification: '', qty: 1, unit: '', note: '' });
    },
    submitCreate() {
      let context = this;
      if (context.submitting) return;
      if (context.newSpb.items.length === 0) {
        context.notify('Minimal 1 barang wajib diisi', 'error');
        return;
      }
      context.submitting = true;
      Api(context, spb.create(context.newSpb)).onSuccess(function () {
        context.notify('SPPB Berhasil Diajukan', 'success');
        context.formCreate.show = false;
        context.submitting = false;
        context.get();
      }).onError(function () {
        context.notify('Gagal Mengajukan SPPB', 'error');
        context.submitting = false;
      }).call();
    },
    openDetail(id) {
      let context = this;
      context.actionForm = {};
      Api(context, spb.show(id)).onSuccess(function (response) {
        context.detail = response.data.data;
        context.formDetail.show = true;
      }).onError(function () {
        context.notify('Gagal mengambil detail SPPB', 'error');
      }).call();
    },
    refreshDetail() {
      let context = this;
      Api(context, spb.show(context.detail.id)).onSuccess(function (response) {
        context.detail = response.data.data;
        context.actionForm = {};
        context.get();
      }).call();
    },
    doApprove(approve) {
      let context = this;
      Api(context, spb.approve(context.detail.id, { approve: approve, approval_note: context.actionForm.approval_note })).onSuccess(function () {
        context.notify(approve ? 'SPPB Disetujui' : 'SPPB Ditolak', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal memproses approval', 'error');
      }).call();
    },
    doAddCondition() {
      let context = this;
      Api(context, spb.addCondition(context.detail.id, {
        vendor_id: context.actionForm.vendor_id,
        price: context.actionForm.price,
        condition_note: context.actionForm.condition_note,
      })).onSuccess(function () {
        context.notify('Penawaran Vendor Berhasil Ditambahkan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Menambahkan Penawaran', 'error');
      }).call();
    },
    doSelectVendor(conditionId) {
      let context = this;
      Api(context, spb.selectCondition(conditionId)).onSuccess(function () {
        context.notify('Vendor Terpilih', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Memilih Vendor', 'error');
      }).call();
    },
    doDisposisi(setuju) {
      let context = this;
      Api(context, spb.disposisi(context.detail.id, { disposisi: setuju, disposisi_note: context.actionForm.disposisi_note })).onSuccess(function () {
        context.notify(setuju ? 'Lanjut ke Purchasing' : 'Kembali ke Permintaan Pengadaan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal memproses disposisi', 'error');
      }).call();
    },
    doIssuePO() {
      let context = this;
      Api(context, spb.issuePO(context.detail.id, context.actionForm)).onSuccess(function () {
        context.notify('PO Berhasil Diterbitkan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Menerbitkan PO', 'error');
      }).call();
    },
    doResolusi() {
      let context = this;
      Api(context, spb.resolusi(context.detail.id, context.actionForm)).onSuccess(function () {
        context.notify('Resolusi Berhasil Disimpan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Menyimpan Resolusi', 'error');
      }).call();
    },
    doInvoice() {
      let context = this;
      Api(context, spb.invoice(context.detail.id, context.actionForm)).onSuccess(function () {
        context.notify('Invoice Berhasil Disimpan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Menyimpan Invoice', 'error');
      }).call();
    },
    doPayment() {
      let context = this;
      Api(context, spb.payment(context.detail.id, context.actionForm)).onSuccess(function () {
        context.notify('Pembayaran Berhasil Disimpan, SPPB Selesai', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Menyimpan Pembayaran', 'error');
      }).call();
    },
    doDelete(id, noSpb) {
      let context = this;
      if (!confirm('Yakin mau hapus SPPB ' + noSpb + '? Data ini tidak bisa dikembalikan.')) return;
      Api(context, spb.delete(id)).onSuccess(function () {
        context.notify('SPPB Berhasil Dihapus', 'success');
        context.get();
      }).onError(function () {
        context.notify('Gagal Menghapus SPPB', 'error');
      }).call();
    },
  },
};
</script>

<style scoped>
::v-deep .modal-container {
  display: flex;
  justify-content: center;
  align-items: center;
}
::v-deep .modal-content-width {
  position: relative;
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  width: 90%;
  max-width: 950px;
  max-height: 90vh;
  overflow-y: auto;
}
.modal__title {
  font-weight: bold;
  font-size: 1.1rem;
}
.status-pill {
  display: inline-block;
  min-width: 130px;
  padding: 8px 20px;
  margin: 4px 0;
  border-radius: 50px;
  font-size: 0.72rem;
  font-weight: 700;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  white-space: nowrap;
  line-height: 1.2;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
</style>