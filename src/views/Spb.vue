<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card sppb-list-card">
          <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
            <div>
              <h6 class="mb-0">Data SPPB</h6>
              <p class="text-secondary text-sm mb-0">Surat Permohonan Permintaan Barang</p>
            </div>
            <div class="d-flex align-items-center" style="gap: 10px;">
              <select class="form-select" style="width: 220px;" v-model="search.status" @change="get()">
                <option value="">Semua Status</option>
                <option>Menunggu Approval</option>
                <option>Ditolak</option>
                <option value="Permintaan Vendor">Permintaan ke Vendor</option>
                <option value="Permintaan Pengadaan">Penawaran Harga Vendor</option>
                <option>Disposisi</option>
                <option>PO Diterbitkan</option>
                <option value="Resolusi">Receipt</option>
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
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Aksi</th>
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
          <p>Divisi: <span class="text-danger">*</span>
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
        <div class="col-6">
          <p>Barang Dibutuhkan Pada Tanggal: <span class="text-danger">*</span>
            <input type="date" class="form-control" v-model="newSpb.needed_date">
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-12"><p class="mb-1 text-xs font-weight-bold">Tanda Tangan SPPB:</p></div>
        <div class="col-4">
          <p class="mb-1">Diajukan Oleh <span class="text-danger">*</span></p>
          <input class="form-control" placeholder="Nama" v-model="newSpb.sign_diajukan">
        </div>
        <div class="col-4">
          <p class="mb-1">Ditinjau Oleh <span class="text-danger">*</span></p>
          <input class="form-control" placeholder="Nama" v-model="newSpb.sign_ditinjau">
        </div>
        <div class="col-4">
          <p class="mb-1">Disetujui Oleh <span class="text-danger">*</span></p>
          <input class="form-control" placeholder="Nama" v-model="newSpb.sign_disetujui">
        </div>
      </div>
      <hr>
      <argon-button color="success" size="xs" class="mb-2" @click="addItemRow()"><i class="fa fa-plus fa-sm"></i> Tambah Barang</argon-button>
      <div class="table-responsive">
        <table class="table align-items-center mb-0">
          <thead>
            <tr style="background-color: #F0F8FF;">
              <th class="text-center text-xxs">Kode Material</th>
              <th class="text-center text-xxs">Nama Barang <span class="text-danger">*</span></th>
              <th class="text-center text-xxs">Kategori <span class="text-danger">*</span></th>
              <th class="text-center text-xxs">Spesifikasi <span class="text-danger">*</span></th>
              <th class="text-center text-xxs">Stok Aktual</th>
              <th class="text-center text-xxs">Stok Minimum</th>
              <th class="text-center text-xxs">Merek <span class="text-danger">*</span></th>
              <th class="text-center text-xxs">Qty <span class="text-danger">*</span></th>
              <th class="text-center text-xxs">Satuan <span class="text-danger">*</span></th>
              <th class="text-center text-xxs">Keterangan <span class="text-danger">*</span></th>
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
              <td><input class="form-control form-control-sm" v-model="item.specification"></td>
              <td>
                <input type="number" min="0" class="form-control form-control-sm" v-model="item.actual_stock" placeholder="otomatis">
              </td>
              <td>
                <input type="number" min="0" class="form-control form-control-sm" v-model="item.min_stock" placeholder="otomatis">
              </td>
              <td><input class="form-control form-control-sm" v-model="item.merek"></td>
              <td><input type="number" min="1" class="form-control form-control-sm" v-model="item.qty"></td>
              <td><input class="form-control form-control-sm" v-model="item.unit"></td>
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
      <div class="col-8 float-left"><span class="modal__title">Detail SPPB {{ detail.no_spb }}</span></div>
      <div class="col-3 float-left text-end">
        <argon-button v-if="detail.status && detail.status !== 'Menunggu Approval'" color="secondary" size="sm" :disabled="!signaturesComplete()" :title="!signaturesComplete() ? 'Isi dan simpan tanda tangan SPPB terlebih dahulu' : ''" @click="openPrintPreview()">🖨️ Preview / Print</argon-button>
      </div>
      <div class="col-1 float-right">
        <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="formDetail.show = false"></i>
      </div>
    </div><hr>
    <div class="modal__content container" v-if="detail.id">
      <div class="section-box">
        <table class="table table-sm table-borderless mb-0 info-table">
          <tbody>
            <tr>
              <td class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:140px;">Divisi</td>
              <td>{{ detail.divisi }}</td>
            </tr>
            <tr>
              <td class="text-uppercase text-secondary text-xxs font-weight-bolder">Status</td>
              <td><span class="status-pill" :style="statusPillStyle(detail.status)">{{ statusLabel(detail.status) }}</span></td>
            </tr>
            <tr>
              <td class="text-uppercase text-secondary text-xxs font-weight-bolder">Dibuat oleh</td>
              <td>{{ detail.created_by }} <span class="text-secondary text-sm">— {{ detail.request_date }}</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="section-box">
        <h6 class="section-title">Barang yang Diminta</h6>
        <div class="table-responsive p-0 scroll">
          <table class="table table-sm align-middle mb-0 grid-table">
            <thead>
              <tr>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Material</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama Barang</th><th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kategori</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Spesifikasi</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Merek</th><th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty</th><th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Satuan</th><th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stok Aktual / Min</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Vendor Terpilih</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(it, i) in detail.items" :key="i">
                <td class="text-center">{{ it.material_code || '-' }}</td>
                <td>{{ it.material_name }}</td>
                <td class="text-center">{{ it.kategori || '-' }}</td>
                <td>{{ it.specification || '-' }}</td>
                <td>{{ it.merek || '-' }}</td>
                <td class="text-center">{{ it.qty }}</td>
                <td class="text-center">{{ it.unit }}</td>
                <td class="text-center">{{ it.actual_stock !== null && it.actual_stock !== undefined ? it.actual_stock + ' / ' + it.min_stock : '-' }}</td>
                <td>{{ it.note || '-' }}</td>
                <td>
                  <span v-if="itemSelectedVendor(it)">{{ itemSelectedVendor(it).supplier }}</span>
                  <span v-else class="text-secondary">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="section-box">
        <h6 class="section-title">Riwayat Proses</h6>
        <div class="table-responsive p-0 scroll">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Tahap</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Oleh</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Pengajuan SPPB</b></td>
                <td>-</td>
                <td>{{ detail.created_by }}</td>
                <td>{{ detail.request_date }}</td>
              </tr>
              <tr v-if="detail.approved_by">
                <td><b>Approval</b></td>
                <td>{{ detail.status === 'Ditolak' ? 'Ditolak' : 'Disetujui' }}{{ detail.approval_note ? ' — ' + detail.approval_note : '' }}</td>
                <td>{{ detail.approved_by }}</td>
                <td>{{ detail.approved_at }}</td>
              </tr>
              <tr v-if="detail.disposisi_by">
                <td><b>Disposisi</b></td>
                <td>{{ detail.disposisi_note || '-' }}</td>
                <td>{{ detail.disposisi_by }}</td>
                <td>{{ detail.disposisi_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAHAP: Menunggu Approval -->
      <div v-if="detail.status === 'Menunggu Approval'" class="action-box">
        <h6 class="section-title">Approval Atasan</h6>
        <div v-if="userRole === 'Admin'">
          <textarea class="form-control mb-3" placeholder="Catatan approval (opsional)" v-model="actionForm.approval_note"></textarea>
          <argon-button color="success" size="sm" class="me-2" @click="doApprove(true)">Setujui</argon-button>
          <argon-button color="danger" size="sm" @click="doApprove(false)">Tolak</argon-button>
        </div>
        <p v-else class="text-secondary text-sm mb-0">Menunggu approval dari Admin/atasan.</p>
      </div>

      <!-- TAHAP: Ditolak -->
      <div v-if="detail.status === 'Ditolak'" class="section-box">
        <table class="table table-sm table-borderless mb-2">
          <tbody>
            <tr><td class="text-danger" style="width:160px;"><b>SPPB Ditolak</b></td><td class="text-danger">Ya</td></tr>
            <tr><td class="text-secondary"><b>Catatan</b></td><td>{{ detail.approval_note || '-' }}</td></tr>
          </tbody>
        </table>
        <div v-if="userRole === 'Admin'">
          <p class="text-secondary text-sm">Admin bisa membatalkan penolakan dan menyetujui ulang SPPB ini:</p>
          <textarea class="form-control mb-2" placeholder="Catatan approval (opsional)" v-model="actionForm.approval_note"></textarea>
          <argon-button color="success" size="sm" @click="doApprove(true)">Setujui Ulang</argon-button>
        </div>
      </div>

      <!-- TAHAP: Permintaan Vendor (pilih vendor yg diminta penawaran, belum ada harga) -->
      <div v-if="detail.status === 'Permintaan Vendor'">
        <div v-if="userRole === 'Purchasing'">
          <h6 class="section-title">Pilih Vendor yang Diminta Penawaran</h6>
          <p class="text-secondary text-sm">Tandai vendor mana saja yang akan diminta memberi penawaran untuk tiap barang (belum isi harga). Surat permintaan bisa dicetak per vendor di bawah.</p>

          <div v-for="(it, i) in detail.items" :key="'item-rv-' + i" class="section-box">
            <table class="table table-sm table-borderless mb-2 info-table">
              <tbody>
                <tr>
                  <td class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:140px;">Barang</td>
                  <td><b>{{ it.material_name }}</b></td>
                </tr>
                <tr>
                  <td class="text-uppercase text-secondary text-xxs font-weight-bolder">Qty</td>
                  <td>{{ it.qty }} {{ it.unit }}</td>
                </tr>
              </tbody>
            </table>

            <div class="mb-2">
              <span v-if="!(it.requested_vendors && it.requested_vendors.length)" class="text-secondary text-sm">Belum ada vendor yang diminta untuk barang ini.</span>
              <span v-for="rv in it.requested_vendors" :key="'rv-' + rv.id" class="badge bg-secondary me-2 mb-1" style="font-size:12px;">
                {{ rv.vendor ? rv.vendor.name : '-' }}
                <a href="javascript:void(0)" style="color:#fff; margin-left:4px;" @click="doUnrequestVendor(it, rv.id)">&times;</a>
              </span>
            </div>

            <div class="row g-2" v-if="requestVendorForms[it.id] !== undefined">
              <div class="col-8">
                <input class="form-control form-control-sm" list="vendorNameListRv" v-model="requestVendorForms[it.id]" placeholder="Ketik/pilih nama vendor">
              </div>
              <div class="col-4"><argon-button color="info" size="sm" @click="doRequestVendor(it)">+ Minta Penawaran</argon-button></div>
            </div>
          </div>
          <datalist id="vendorNameListRv">
            <option v-for="v in vendors" :key="'rvopt-' + v.id" :value="v.name"></option>
          </datalist>

          <div class="action-box" v-if="requestedVendorSummary().length">
            <h6 class="section-title">Cetak Surat Permintaan Penawaran</h6>
            <p class="text-secondary text-sm">1 surat per vendor, otomatis berisi semua barang yang diminta ke vendor tersebut.</p>
            <button v-for="v in requestedVendorSummary()" :key="'rfq-' + v.id"
                    class="btn btn-outline-secondary btn-sm me-2 mb-2" @click="printRfq(v.id)">
              🖨️ Surat untuk {{ v.name }}
            </button>
          </div>

          <div class="action-box">
            <h6 class="section-title">Lanjut ke Tahap Penawaran Harga</h6>
            <p class="text-secondary text-sm" v-if="!allItemsHaveRequestedVendor()">
              Semua barang harus punya minimal 1 vendor yang diminta penawaran terlebih dahulu.
            </p>
            <argon-button color="success" size="sm" :disabled="!allItemsHaveRequestedVendor()" @click="doLanjutPenawaran()">Lanjut ke Penawaran Harga</argon-button>
          </div>
        </div>
        <p v-else class="text-secondary text-sm">SPPB sedang diproses oleh Purchasing (memilih vendor yang akan diminta penawaran).</p>
      </div>

      <!-- TAHAP: Permintaan Pengadaan (komparasi vendor PER BARANG, khusus Purchasing) -->
      <div v-if="detail.status === 'Permintaan Pengadaan'">
        <div v-if="userRole === 'Purchasing'">
          <h6 class="section-title">Komparasi Penawaran Vendor per Barang</h6>
          <p class="text-secondary text-sm">Tiap barang bisa punya vendor pemenang yang berbeda-beda. Sistem akan otomatis membuat PO terpisah untuk setiap vendor.</p>

          <div v-for="(it, i) in detail.items" :key="'item-cmp-' + i" class="section-box">
            <table class="table table-sm table-borderless mb-2 info-table">
              <tbody>
                <tr>
                  <td class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:140px;">Barang</td>
                  <td><b>{{ it.material_name }}</b></td>
                </tr>
                <tr>
                  <td class="text-uppercase text-secondary text-xxs font-weight-bolder">Qty</td>
                  <td>{{ it.qty }} {{ it.unit }}</td>
                </tr>
                <tr v-if="itemSelectedVendor(it)">
                  <td class="text-uppercase text-secondary text-xxs font-weight-bolder">Vendor Terpilih</td>
                  <td><span class="badge bg-success">{{ itemSelectedVendor(it).supplier }}</span></td>
                </tr>
              </tbody>
            </table>
            <table class="table table-sm mb-2" v-if="it.conditions && it.conditions.length">
              <thead><tr><th>Pilih</th><th>#</th><th>Vendor</th><th>Harga</th><th>Catatan</th></tr></thead>
              <tbody>
                <tr v-for="(c, j) in it.conditions" :key="j" :class="c.selected ? 'table-success' : ''">
                  <td><input type="radio" :name="'vendorItem' + it.id" :checked="c.selected" @change="doSelectItemCondition(c.id)"></td>
                  <td>{{ c.round }}</td>
                  <td>{{ c.supplier }}</td>
                  <td>Rp {{ formatRupiah(c.price) }}</td>
                  <td>{{ c.condition_note }}</td>
                </tr>
              </tbody>
            </table>
            <p v-else class="text-secondary text-sm">Belum ada penawaran vendor untuk barang ini.</p>

            <div class="row g-2" v-if="itemForms[it.id]">
              <div class="col-4">
                <select class="form-select form-select-sm" v-model="itemForms[it.id].vendor_id">
                  <option value="">Pilih vendor yang sudah diminta</option>
                  <option v-for="rv in (it.requested_vendors || [])" :key="'vopt-' + rv.id" :value="rv.vendor_id">{{ rv.vendor ? rv.vendor.name : '-' }}</option>
                </select>
              </div>
              <div class="col-3"><input type="text" inputmode="numeric" class="form-control form-control-sm" placeholder="Harga (Rp)" :value="formatRupiah(itemForms[it.id].price)" @input="onCurrencyInput(itemForms[it.id], 'price', $event)"></div>
              <div class="col-3"><input class="form-control form-control-sm" placeholder="Syarat/Catatan" v-model="itemForms[it.id].condition_note"></div>
              <div class="col-2"><argon-button color="info" size="sm" @click="doAddItemCondition(it)">Simpan</argon-button></div>
            </div>
          </div>
          <datalist id="vendorNameList">
            <option v-for="v in vendors" :key="v.id" :value="v.name"></option>
          </datalist>

          <div class="action-box">
            <h6 class="section-title">Finalisasi Pilihan Vendor</h6>
            <p class="text-secondary text-sm" v-if="!allItemsHaveSelectedVendor()">
              Semua barang harus punya vendor terpilih terlebih dahulu sebelum bisa lanjut.
            </p>
            <label class="text-xs text-secondary">Diajukan Oleh (Manager Dept.) <span class="text-danger">*</span></label>
            <input class="form-control mb-2" placeholder="Nama Manager Dept." v-model="actionForm.diajukan_oleh">
            <textarea class="form-control mb-2" placeholder="Catatan (opsional)" v-model="actionForm.disposisi_note"></textarea>
            <argon-button color="success" size="sm" class="me-2" :disabled="!allItemsHaveSelectedVendor()" @click="doDisposisi(true)">Konfirmasi & Terbitkan PO</argon-button>
            <argon-button color="warning" size="sm" @click="doDisposisi(false)">Belum Ada yang Sesuai</argon-button>
          </div>
        </div>
        <p v-else class="text-secondary text-sm">SPPB sedang diproses oleh Purchasing (komparasi & pemilihan vendor per barang).</p>
      </div>

      <!-- PURCHASE ORDER (otomatis kepecah per vendor, tiap PO progress sendiri-sendiri) -->
      <div v-if="detail.purchase_orders && detail.purchase_orders.length">
        <h6 class="section-title">Purchase Order ({{ detail.purchase_orders.length }})</h6>
        <div v-for="(po, i) in detail.purchase_orders" :key="'po-card-' + i" class="section-box">
            <table class="table table-sm table-borderless mb-2 info-table">
              <tbody>
                <tr>
                  <td class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:140px;">No. PO</td>
                  <td>{{ po.po_number }}</td>
                </tr>
                <tr>
                  <td class="text-uppercase text-secondary text-xxs font-weight-bolder">Vendor</td>
                  <td>{{ po.supplier }}</td>
                </tr>
                <tr>
                  <td class="text-uppercase text-secondary text-xxs font-weight-bolder">Total</td>
                  <td>Rp {{ formatRupiah(po.po_total) }}</td>
                </tr>
                <tr>
                  <td class="text-uppercase text-secondary text-xxs font-weight-bolder">Status</td>
                  <td>
                    <span class="status-pill" :style="statusPillStyle(po.status)">{{ statusLabel(po.status) }}</span>
                    <argon-button color="secondary" size="sm" class="ms-2" :disabled="!(po.sign_dibuat && po.sign_disetujui)" :title="!(po.sign_dibuat && po.sign_disetujui) ? 'Isi dan simpan tanda tangan PO terlebih dahulu' : ''" @click="openPrintPoPreview(po)">🖨️ Preview / Print PO</argon-button>
                  </td>
                </tr>
              </tbody>
            </table>
            <table class="table table-sm mb-2">
              <thead><tr><th>Barang</th><th>Qty</th></tr></thead>
              <tbody>
                <tr v-for="(it, j) in po.items" :key="j"><td>{{ it.material_name }}</td><td>{{ it.qty }} {{ it.unit }}</td></tr>
              </tbody>
            </table>

            <!-- TANDA TANGAN PO (hilang otomatis kalau sudah tersimpan) -->
            <div class="mb-3" v-if="signPoForms[po.id] && !(po.sign_dibuat && po.sign_disetujui)">
              <p class="text-xs font-weight-bold mb-1">Tanda Tangan PO (untuk cetak)</p>
              <div class="row g-2">
                <div class="col-5">
                  <label class="text-xs text-secondary">Dibuat Oleh</label>
                  <input class="form-control form-control-sm" placeholder="Nama" v-model="signPoForms[po.id].sign_dibuat">
                </div>
                <div class="col-5">
                  <label class="text-xs text-secondary">Disetujui Oleh</label>
                  <input class="form-control form-control-sm" placeholder="Nama" v-model="signPoForms[po.id].sign_disetujui">
                </div>
                <div class="col-2 d-flex align-items-end">
                  <argon-button color="info" size="sm" @click="savePoSignature(po)">Simpan</argon-button>
                </div>
              </div>
            </div>

            <div v-if="po.status === 'PO Diterbitkan'">
              <div v-if="userRole === 'Purchasing'">
                <textarea class="form-control form-control-sm mb-2" placeholder="Catatan Receipt" v-model="poForms[po.id].resolusi_note"></textarea>
                <argon-button color="success" size="sm" @click="doResolusiPo(po)">Simpan Receipt (Barang Diterima)</argon-button>
              </div>
              <p v-else class="text-secondary text-sm mb-0">Menunggu Purchasing menerima barang.</p>
            </div>

            <div v-if="po.status === 'Resolusi'">
              <div v-if="userRole === 'Purchasing'">
                <div class="row g-2">
                  <div class="col-4"><input class="form-control form-control-sm" placeholder="No. Invoice" v-model="poForms[po.id].invoice_number"></div>
                  <div class="col-4"><input type="date" class="form-control form-control-sm" v-model="poForms[po.id].invoice_date"></div>
                  <div class="col-4"><input type="text" inputmode="numeric" class="form-control form-control-sm" placeholder="Jumlah (Rp)" :value="formatRupiah(poForms[po.id].invoice_amount)" @input="onCurrencyInput(poForms[po.id], 'invoice_amount', $event)"></div>
                </div>
                <argon-button color="success" size="sm" class="mt-2" @click="doInvoicePo(po)">Simpan Invoice</argon-button>
              </div>
              <p v-else class="text-secondary text-sm mb-0">Menunggu Purchasing mencatat invoice.</p>
            </div>

            <div v-if="po.status === 'Invoice'">
              <div v-if="userRole === 'Purchasing'">
                <div class="row g-2">
                  <div class="col-4"><input type="date" class="form-control form-control-sm" v-model="poForms[po.id].payment_date"></div>
                  <div class="col-4"><input type="text" inputmode="numeric" class="form-control form-control-sm" placeholder="Jumlah (Rp)" :value="formatRupiah(poForms[po.id].payment_amount)" @input="onCurrencyInput(poForms[po.id], 'payment_amount', $event)"></div>
                  <div class="col-4">
                    <select class="form-select form-select-sm" v-model="poForms[po.id].payment_method">
                      <option value="">Metode Pembayaran</option>
                      <option>Transfer Bank</option>
                      <option>Cash</option>
                      <option>Giro</option>
                    </select>
                  </div>
                </div>
                <argon-button color="success" size="sm" class="mt-2" @click="doPaymentPo(po)">Simpan Pembayaran</argon-button>
              </div>
              <p v-else class="text-secondary text-sm mb-0">Menunggu Purchasing mencatat pembayaran.</p>
            </div>

            <p v-if="po.status === 'Selesai'" class="text-success text-sm mb-0">
              <b>PO ini sudah selesai.</b> Invoice: {{ po.invoice_number }} (Rp {{ formatRupiah(po.invoice_amount) }}) — Dibayar {{ po.payment_date }} Rp {{ formatRupiah(po.payment_amount) }} via {{ po.payment_method }}
            </p>
        </div>
      </div>

      <!-- TAHAP: Selesai -->
      <div v-if="detail.status === 'Selesai'" class="section-box">
        <table class="table table-sm table-borderless mb-0 info-table">
          <tbody>
            <tr><td class="text-success" style="width:220px;"><b>Status Akhir</b></td><td class="text-success">Semua Purchase Order pada SPPB ini sudah selesai</td></tr>
          </tbody>
        </table>
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
import config from '@/configs/config';

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
      itemForms: {},
      requestVendorForms: {},
      poForms: {},
      signForm: {},
      signPoForms: {},
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
        'Permintaan Vendor': { bg: '#d4e6ff', color: '#0a4a9e' },
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
        'Permintaan Vendor': 'Permintaan ke Vendor',
        'Permintaan Pengadaan': 'Penawaran Harga Vendor',
        'Resolusi': 'Receipt',
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
        item.actual_stock = stock.stock_barang;
        item.min_stock = stock.min_stock;
      } else if (item.material_code) {
        // sebelumnya nyambung ke master, sekarang sudah tidak match lagi -> reset semua field otomatis
        item.material_code = '';
        item.specification = '';
        item.unit = '';
        item.actual_stock = '';
        item.min_stock = '';
      }
      // kalau dari awal memang barang baru (material_code sudah kosong),
      // biarkan specification/unit/actual_stock/min_stock yang sudah diketik manual, jangan direset
    },
    openCreate() {
      this.newSpb = { divisi: '', needed_date: '', sign_diajukan: '', sign_ditinjau: '', sign_disetujui: '', items: [] };
      this.addItemRow();
      this.formCreate.show = true;
    },
    addItemRow() {
      this.newSpb.items.push({ material_name: '', material_code: '', kategori: '', merek: '', specification: '', qty: 1, unit: '', note: '', actual_stock: '', min_stock: '' });
    },
    submitCreate() {
      let context = this;
      if (context.submitting) return;
      if (!context.newSpb.divisi) {
        context.notify('Divisi wajib dipilih', 'error');
        return;
      }
      if (!context.newSpb.needed_date) {
        context.notify('Tanggal barang dibutuhkan wajib diisi', 'error');
        return;
      }
      if (!context.newSpb.sign_diajukan || !context.newSpb.sign_ditinjau || !context.newSpb.sign_disetujui) {
        context.notify('Nama Diajukan/Ditinjau/Disetujui Oleh wajib diisi', 'error');
        return;
      }
      if (context.newSpb.items.length === 0) {
        context.notify('Minimal 1 barang wajib diisi', 'error');
        return;
      }
      for (let i = 0; i < context.newSpb.items.length; i++) {
        const it = context.newSpb.items[i];
        if (!it.material_name || !it.kategori || !it.specification || !it.merek || !it.qty || Number(it.qty) < 1 || !it.unit || !it.note) {
          context.notify(`Baris barang #${i + 1}: semua kolom bertanda * wajib diisi`, 'error');
          return;
        }
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
    formatRupiah(value) {
      if (value === null || value === undefined || value === '') return '';
      const num = Number(value);
      if (isNaN(num)) return value;
      return new Intl.NumberFormat('id-ID').format(num);
    },
    onCurrencyInput(obj, field, event) {
      const raw = event.target.value.replace(/\D/g, '');
      obj[field] = raw ? parseInt(raw, 10) : '';
      event.target.value = raw ? this.formatRupiah(raw) : '';
    },
    itemSelectedVendor(item) {
      if (!item || !item.conditions) return null;
      return item.conditions.find(c => c.selected) || null;
    },
    allItemsHaveSelectedVendor() {
      if (!this.detail || !this.detail.items || this.detail.items.length === 0) return false;
      return this.detail.items.every(it => this.itemSelectedVendor(it));
    },
    allItemsHaveRequestedVendor() {
      if (!this.detail || !this.detail.items || this.detail.items.length === 0) return false;
      return this.detail.items.every(it => it.requested_vendors && it.requested_vendors.length > 0);
    },
    requestedVendorSummary() {
      if (!this.detail || !this.detail.items) return [];
      const map = {};
      this.detail.items.forEach(it => {
        (it.requested_vendors || []).forEach(rv => {
          if (rv.vendor && !map[rv.vendor.id]) {
            map[rv.vendor.id] = rv.vendor;
          }
        });
      });
      return Object.values(map);
    },
    doRequestVendor(item) {
      let context = this;
      const vendorName = context.requestVendorForms[item.id];
      const vendor = context.vendors.find(v => v.name === vendorName);
      if (!vendor) {
        context.notify('Pilih vendor yang valid dari daftar (nama harus cocok persis)', 'error');
        return;
      }
      Api(context, spb.requestVendor(item.id, { vendor_id: vendor.id })).onSuccess(function () {
        context.notify('Vendor berhasil diminta penawaran', 'success');
        context.requestVendorForms[item.id] = '';
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal meminta penawaran vendor', 'error');
      }).call();
    },
    doUnrequestVendor(item, requestedVendorId) {
      let context = this;
      Api(context, spb.unrequestVendor(requestedVendorId)).onSuccess(function () {
        context.notify('Permintaan vendor dibatalkan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal membatalkan permintaan vendor', 'error');
      }).call();
    },
    doLanjutPenawaran() {
      let context = this;
      Api(context, spb.lanjutPenawaran(context.detail.id)).onSuccess(function () {
        context.notify('Lanjut ke tahap Penawaran Harga', 'success');
        context.refreshDetail();
      }).onError(function (response) {
        context.notify((response.data && response.data.message) || 'Gagal lanjut ke tahap penawaran', 'error');
      }).call();
    },
    initForms() {
      const itemForms = {};
      const requestVendorForms = {};
      (this.detail.items || []).forEach(it => {
        itemForms[it.id] = { vendor_id: '', price: '', condition_note: '' };
        requestVendorForms[it.id] = '';
      });
      this.itemForms = itemForms;
      this.requestVendorForms = requestVendorForms;

      const poForms = {};
      const signPoForms = {};
      (this.detail.purchase_orders || []).forEach(po => {
        poForms[po.id] = {
          resolusi_note: '',
          invoice_number: '', invoice_date: '', invoice_amount: '',
          payment_date: '', payment_amount: '', payment_method: '',
        };
        signPoForms[po.id] = {
          sign_dibuat: po.sign_dibuat,
          sign_disetujui: po.sign_disetujui,
        };
      });
      this.poForms = poForms;
      this.signPoForms = signPoForms;

      this.signForm = {
        sign_diajukan: this.detail.sign_diajukan,
        sign_ditinjau: this.detail.sign_ditinjau,
        sign_disetujui: this.detail.sign_disetujui,
      };
    },
    openDetail(id) {
      let context = this;
      context.actionForm = {};
      Api(context, spb.show(id)).onSuccess(function (response) {
        context.detail = response.data.data;
        context.initForms();
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
        context.initForms();
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
    onItemVendorNameInput(item) {
      const vendor = this.vendors.find(v => v.name === this.itemForms[item.id].vendor_name);
      this.itemForms[item.id].vendor_id = vendor ? vendor.id : '';
    },
    doAddItemCondition(item) {
      let context = this;
      const form = context.itemForms[item.id];
      if (!form.vendor_id) {
        context.notify('Pilih vendor yang valid dari daftar (nama harus cocok persis)', 'error');
        return;
      }
      if (!form.price) {
        context.notify('Harga penawaran wajib diisi', 'error');
        return;
      }
      Api(context, spb.addItemCondition(item.id, {
        vendor_id: form.vendor_id,
        price: form.price,
        condition_note: form.condition_note,
      })).onSuccess(function () {
        context.notify('Penawaran Vendor Berhasil Ditambahkan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Menambahkan Penawaran', 'error');
      }).call();
    },
    doSelectItemCondition(conditionId) {
      let context = this;
      Api(context, spb.selectItemCondition(conditionId)).onSuccess(function () {
        context.notify('Vendor Terpilih Untuk Barang Ini', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Memilih Vendor', 'error');
      }).call();
    },
    doDisposisi(setuju) {
      let context = this;
      if (setuju && !context.actionForm.diajukan_oleh) {
        context.notify('Nama Diajukan Oleh (Manager Dept.) wajib diisi sebelum PO diterbitkan', 'error');
        return;
      }
      Api(context, spb.disposisi(context.detail.id, { disposisi: setuju, diajukan_oleh: context.actionForm.diajukan_oleh, disposisi_note: context.actionForm.disposisi_note })).onSuccess(function (response) {
        context.notify((response.data && response.data.message) || (setuju ? 'PO Berhasil Diterbitkan' : 'Kembali ke Permintaan Pengadaan'), 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal memproses disposisi', 'error');
      }).call();
    },
    doResolusiPo(po) {
      let context = this;
      Api(context, spb.resolusiPo(po.id, context.poForms[po.id])).onSuccess(function () {
        context.notify('Receipt Berhasil Disimpan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Menyimpan Receipt', 'error');
      }).call();
    },
    doInvoicePo(po) {
      let context = this;
      Api(context, spb.invoicePo(po.id, context.poForms[po.id])).onSuccess(function () {
        context.notify('Invoice Berhasil Disimpan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Menyimpan Invoice', 'error');
      }).call();
    },
    doPaymentPo(po) {
      let context = this;
      Api(context, spb.paymentPo(po.id, context.poForms[po.id])).onSuccess(function (response) {
        context.notify((response.data && response.data.message) || 'Pembayaran Berhasil Disimpan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal Menyimpan Pembayaran', 'error');
      }).call();
    },
    signaturesComplete() {
      return !!(this.detail.sign_diajukan && this.detail.sign_ditinjau && this.detail.sign_disetujui);
    },
    saveSignature() {
      let context = this;
      if (!context.signForm.sign_diajukan || !context.signForm.sign_ditinjau || !context.signForm.sign_disetujui) {
        context.notify('Semua nama tanda tangan SPPB wajib diisi', 'error');
        return;
      }
      Api(context, spb.saveSignature(context.detail.id, context.signForm)).onSuccess(function () {
        context.notify('Tanda tangan SPPB berhasil disimpan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal menyimpan tanda tangan SPPB', 'error');
      }).call();
    },
    savePoSignature(po) {
      let context = this;
      const form = context.signPoForms[po.id];
      if (!form.sign_dibuat || !form.sign_disetujui) {
        context.notify('Nama Dibuat Oleh dan Disetujui Oleh wajib diisi', 'error');
        return;
      }
      Api(context, spb.savePoSignature(po.id, form)).onSuccess(function () {
        context.notify('Tanda tangan PO berhasil disimpan', 'success');
        context.refreshDetail();
      }).onError(function () {
        context.notify('Gagal menyimpan tanda tangan PO', 'error');
      }).call();
    },
    openPrintPreview() {
      let context = this;
      if (!context.signaturesComplete()) {
        context.notify('Isi dan simpan tanda tangan SPPB terlebih dahulu', 'error');
        return;
      }
      const baseUrl = config.apiUrl.trim().replace(/\/$/, '');
      window.open(baseUrl + '/print-pdf/sppb/' + this.detail.id, '_blank');
    },
    openPrintPoPreview(po) {
      let context = this;
      if (!(po.sign_dibuat && po.sign_disetujui)) {
        context.notify('Isi dan simpan tanda tangan PO terlebih dahulu', 'error');
        return;
      }
      const baseUrl = config.apiUrl.trim().replace(/\/$/, '');
      window.open(baseUrl + '/print-pdf/po/' + po.id, '_blank');
    },
    printRfq(vendorId) {
      const baseUrl = config.apiUrl.trim().replace(/\/$/, '');
      window.open(baseUrl + '/print-pdf/rfq?spb_id=' + this.detail.id + '&vendor_id=' + vendorId, '_blank');
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
.item-table-wrap {
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  overflow-x: auto;
  overflow-y: hidden;
}
.item-table thead tr {
  background-color: #F0F8FF;
}
.item-table thead th {
  border-bottom: 2px solid #d7e6f5;
  padding: 0.6rem 0.75rem;
}
.item-table tbody td {
  padding: 0.55rem 0.75rem;
  border-bottom: 1px solid #eef2f7;
}
.item-table tbody tr:last-child td {
  border-bottom: none;
}
.item-table tbody tr:hover {
  background-color: #f8fbff;
}
.section-title {
  font-weight: 700;
  font-size: 0.92rem;
  color: #344767;
  margin-bottom: 0.85rem;
  display: flex;
  align-items: center;
}
.section-title::before {
  content: "";
  display: inline-block;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #adb5bd;
  margin-right: 8px;
}
.section-box, .action-box {
  background-color: #ffffff;
  border: 1px solid #eaecef;
  border-radius: 0.65rem;
  padding: 1.1rem 1.35rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.035);
}
.info-table td {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  vertical-align: middle;
}
.grid-table {
  border: 1px solid #edf0f3;
  border-radius: 0.4rem;
  overflow: hidden;
}
.grid-table thead th {
  background-color: #f8f9fb;
  border-bottom: 1px solid #edf0f3;
  padding: 0.55rem 0.7rem;
}
.grid-table tbody td {
  padding: 0.55rem 0.7rem;
  border-bottom: 1px solid #f1f3f5;
  border-right: 1px solid #f4f5f7;
}
.grid-table tbody td:last-child {
  border-right: none;
}
.grid-table tbody tr:last-child td {
  border-bottom: none;
}
.grid-table tbody tr:hover {
  background-color: #fafbfc;
}
.info-table .status-pill {
  white-space: normal;
  min-width: 0;
  max-width: 100%;
}
</style>