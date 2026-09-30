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
            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
              <div class="d-flex align-items-center" style="gap: 4px;">
                <input
                  type="text"
                  class="form-control form-control-sm"
                  style="width: 180px;"
                  placeholder="Cari No. PO..."
                  v-model="poSearchKeyword"
                  @keyup.enter="searchPo()"
                >
                <argon-button variant="outline" color="dark" size="sm" @click="searchPo()"><i class="fa fa-search"></i></argon-button>
              </div>
              <select class="form-select" style="width: 220px;" v-model="search.status" @change="get()">
                <option value="">Semua Status</option>
                <option>Menunggu Approval</option>
                <option>Ditolak</option>
                <option>Dibatalkan</option>
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

          <!-- HASIL PENCARIAN PO (by No. PO, lintas semua SPPB) -->
          <div class="po-search-results mx-3 mt-3" v-if="poSearchKeyword && (poSearchResults.length > 0 || poSearchDone)">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-sm font-weight-bold text-secondary">Hasil pencarian PO "{{ poSearchKeyword }}"</span>
              <i class="fa fa-times" style="cursor: pointer;" @click="clearPoSearch()" title="Tutup"></i>
            </div>
            <div v-if="poSearchResults.length === 0" class="text-secondary text-sm">Tidak ada PO dengan nomor tersebut.</div>
            <table v-else class="table table-sm align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-xs text-secondary">No. PO</th>
                  <th class="text-xs text-secondary">Vendor</th>
                  <th class="text-xs text-secondary">Status PO</th>
                  <th class="text-xs text-secondary">Dari SPPB</th>
                  <th class="text-xs text-secondary">Tanggal PO</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="po in poSearchResults" :key="po.id">
                  <td class="text-sm font-weight-bold">{{ po.po_number }}{{ po.po_number_suffix ? '/' + po.po_number_suffix : '' }}</td>
                  <td class="text-sm">{{ po.supplier || (po.vendor ? po.vendor.name : '-') }}</td>
                  <td class="text-sm">{{ po.status }}</td>
                  <td class="text-sm">{{ po.spb ? po.spb.no_spb : '-' }}</td>
                  <td class="text-sm">{{ po.po_date }}</td>
                  <td>
                    <argon-button color="info" size="xs" variant="gradient" @click="openPoResult(po)">Lihat SPPB</argon-button>
                  </td>
                </tr>
              </tbody>
            </table>
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
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Barang Dibutuhkan</th>
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
                      <span class="status-pill" :style="statusPillStyle(displayStatusKey(row, false))">{{ statusLabel(displayStatusKey(row, false)) }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.request_date }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-xs font-weight-bold" :class="isNeededDateOverdue(row) ? 'text-danger' : 'text-secondary'">
                        {{ row.needed_date || '-' }}
                        <br v-if="isNeededDateOverdue(row)">
                        <span v-if="isNeededDateOverdue(row)" class="badge badge-sm bg-gradient-danger">Lewat, perlu update</span>
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.sign_diajukan || row.created_by }}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <argon-button color="info" size="xs" variant="gradient" @click="openDetail(row.id)">Detail</argon-button>
                      <argon-button v-if="userRole === 'Admin'" color="danger" size="xs" variant="gradient" class="ms-1" @click="doDelete(row.id, row.no_spb)">Hapus</argon-button>
                    </td>
                  </tr>
                  <tr v-if="table.data.length === 0">
                    <td colspan="7" class="text-center text-sm text-secondary py-3">Belum ada data SPPB</td>
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
      <div class="col-11 float-left"><span class="modal__title">{{ newSpb.editId ? 'Edit SPPB ' + newSpb.no_spb : 'Buat SPPB Baru' }}</span></div>
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
          <i class="fa fa-check"></i> {{ submitting ? 'Mengirim...' : (newSpb.editId ? 'Simpan Perubahan' : 'Ajukan SPPB') }}
        </argon-button>
      </div>
    </div>
  </vue-final-modal>

  <!-- ============ MODAL DETAIL & PROSES SPPB ============ -->
  <vue-final-modal v-model="formDetail.show" classes="modal-container" content-class="modal-content-width" :z-index="10000">
    <div class="row">
      <div class="col-4 float-left">
        <argon-button v-if="canMundurTahap()" color="secondary" size="sm" :title="mundurTahapTitle()" @click="doMundurTahap()"><i class="fa fa-arrow-left"></i></argon-button>
      </div>
      <div class="col-4 float-left"><span class="modal__title">Detail SPPB {{ detail.no_spb }}</span></div>
      <div class="col-3 float-left text-end">
        <argon-button v-if="detail.status && detail.status !== 'Menunggu Approval' && detail.status !== 'Dibatalkan'" color="secondary" size="sm" :disabled="!signaturesComplete()" :title="!signaturesComplete() ? 'Isi dan simpan tanda tangan SPPB terlebih dahulu' : ''" @click="openPrintPreview()">Preview / Print</argon-button>
      </div>
      <div class="col-1 float-right">
        <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="formDetail.show = false"></i>
      </div>
    </div><hr>
    <div class="modal__content container" v-if="detail.id">

      <!-- STEPPER: alur proses SPPB -->
      <div class="stepper-wrap" v-if="detail.status !== 'Ditolak' && detail.status !== 'Dibatalkan'">
        <div class="stepper">
          <div v-for="(label, idx) in stepLabelsList()" :key="idx" class="stepper-step" :class="stepState(idx)">
            <div class="stepper-dot"><i v-if="stepState(idx) === 'done'" class="fa fa-check"></i><span v-else>{{ idx + 1 }}</span></div>
            <div class="stepper-label">{{ label }}</div>
          </div>
        </div>
      </div>
      <div class="rejected-banner" v-else>
        <i class="fa fa-times-circle"></i> SPPB ini ditolak pada tahap approval
      </div>

      <div class="summary-header">
        <div class="summary-main">
          <span class="summary-divisi">{{ detail.divisi }}</span>
          <span class="status-pill" :style="statusPillStyle(displayStatusKey(detail, false))">{{ statusLabel(displayStatusKey(detail, false)) }}</span>
        </div>
        <div class="summary-sub text-secondary text-sm">Diajukan oleh {{ detail.sign_diajukan || detail.created_by }} · {{ detail.request_date }}</div>
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
        <h6 class="section-title collapsible" @click="riwayatExpanded = !riwayatExpanded">
          Riwayat Proses
          <i class="fa" :class="riwayatExpanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        </h6>
        <div class="table-responsive p-0 scroll" v-show="riwayatExpanded">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Tahap</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Oleh</th><th class="text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Pengajuan SPPB</b></td>
                <td>-</td>
                <td>{{ detail.sign_diajukan || detail.created_by }}</td>
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
        <div v-else-if="canEditOrCancel(detail)">
          <div v-if="isNeededDateOverdue(detail)" class="overdue-warning mb-3">
            <i class="fa fa-exclamation-triangle"></i>
            Tanggal barang dibutuhkan (<b>{{ detail.needed_date }}</b>) sudah lewat, sementara SPPB ini belum di-ACC Admin.
            Mohon perbarui tanggal kebutuhan barangnya lewat tombol "Edit SPPB" di bawah ini.
          </div>
          <p class="text-secondary text-sm mb-2">SPPB ini masih menunggu approval Admin. Anda masih bisa mengedit atau membatalkan pengajuan ini.</p>
          <argon-button :color="isNeededDateOverdue(detail) ? 'danger' : 'warning'" size="sm" class="me-2" @click="openEdit(detail)"><i class="fa fa-pencil-alt"></i> {{ isNeededDateOverdue(detail) ? 'Perbarui Tanggal Dibutuhkan' : 'Edit SPPB' }}</argon-button>
          <argon-button color="secondary" size="sm" @click="doCancelSpb(detail)"><i class="fa fa-ban"></i> Batalkan SPPB</argon-button>
        </div>
        <p v-else class="text-secondary text-sm mb-0">Menunggu approval dari Admin/atasan.</p>
      </div>

      <!-- TAHAP: Dibatalkan -->
      <div v-if="detail.status === 'Dibatalkan'" class="section-box">
        <table class="table table-sm table-borderless mb-0">
          <tbody>
            <tr><td class="text-secondary" style="width:160px;"><b>SPPB Dibatalkan</b></td><td>Oleh {{ detail.cancelled_by || '-' }}</td></tr>
          </tbody>
        </table>
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
                <input class="form-control form-control-sm" list="vendorNameListRv" v-model="requestVendorForms[it.id]" placeholder="Ketik nama vendor (bebas, bisa vendor baru)">
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
              Surat untuk {{ v.name }}
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
              <thead><tr><th>Pilih</th><th>#</th><th>Vendor</th><th>Harga</th><th>Catatan</th><th></th></tr></thead>
              <tbody>
                <tr v-for="(c, j) in it.conditions" :key="j" :class="c.selected ? 'table-success' : ''">
                  <td><input type="radio" :name="'vendorItem' + it.id" :checked="c.selected" @change="doSelectItemCondition(c.id)"></td>
                  <td>{{ c.round }}</td>
                  <td>{{ c.supplier }}</td>
                  <td>
                    <span v-if="!conditionEditing[c.id]">Rp {{ formatRupiah(c.price) }}</span>
                    <input v-else type="text" inputmode="numeric" class="form-control form-control-sm" :value="formatRupiah(conditionForms[c.id].price)" @input="onCurrencyInput(conditionForms[c.id], 'price', $event)">
                  </td>
                  <td>
                    <span v-if="!conditionEditing[c.id]">{{ c.condition_note }}</span>
                    <input v-else class="form-control form-control-sm" v-model="conditionForms[c.id].condition_note">
                  </td>
                  <td class="text-nowrap text-center">
                    <i v-if="!conditionEditing[c.id]" class="fa fa-pencil-alt" style="cursor:pointer; color:#8392ab;" title="Edit harga" @click="startEditCondition(c)"></i>
                    <span v-else class="text-xs">
                      <a href="javascript:void(0)" class="text-success font-weight-bold" @click="doUpdateItemCondition(c)">Simpan</a>
                      <span class="text-secondary"> · </span>
                      <a href="javascript:void(0)" class="text-secondary" @click="conditionEditing[c.id] = false">Batal</a>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-else class="text-secondary text-sm">Belum ada penawaran vendor untuk barang ini.</p>

            <div class="row g-2" v-if="itemForms[it.id] && availableVendorsForItem(it).length">
              <div class="col-4">
                <select class="form-select form-select-sm" v-model="itemForms[it.id].vendor_id" @change="onVendorSelectForPrice(it, itemForms[it.id])">
                  <option value="">Pilih vendor yang sudah diminta</option>
                  <option v-for="rv in availableVendorsForItem(it)" :key="'vopt-' + rv.id" :value="rv.vendor_id">{{ rv.vendor ? rv.vendor.name : '-' }}</option>
                </select>
              </div>
              <div class="col-3">
                <input type="text" inputmode="numeric" class="form-control form-control-sm" placeholder="Harga (Rp)" :value="formatRupiah(itemForms[it.id].price)" @input="onCurrencyInput(itemForms[it.id], 'price', $event)">
                <small v-if="lastPriceHint(it, itemForms[it.id].vendor_id) !== null" class="text-secondary">Harga terakhir: Rp {{ formatRupiah(lastPriceHint(it, itemForms[it.id].vendor_id)) }}</small>
              </div>
              <div class="col-3"><input class="form-control form-control-sm" placeholder="Syarat/Catatan" v-model="itemForms[it.id].condition_note"></div>
              <div class="col-2"><argon-button color="info" size="sm" @click="doAddItemCondition(it)">Simpan</argon-button></div>
            </div>
            <p v-else-if="itemForms[it.id]" class="text-secondary text-sm mb-0">Semua vendor yang diminta untuk barang ini sudah memberikan harga.</p>
          </div>
          <datalist id="vendorNameList">
            <option v-for="v in vendors" :key="v.id" :value="v.name"></option>
          </datalist>

          <div class="action-box">
            <h6 class="section-title">Finalisasi Pilihan Vendor</h6>
            <p class="text-secondary text-sm" v-if="!allItemsHaveSelectedVendor()">
              Semua barang harus punya vendor terpilih terlebih dahulu sebelum bisa lanjut.
            </p>
            <textarea class="form-control mb-2" placeholder="Catatan (opsional)" v-model="actionForm.disposisi_note"></textarea>
            <argon-button color="success" size="sm" class="me-2" :disabled="!allItemsHaveSelectedVendor()" @click="doDisposisi(true)">Konfirmasi & Terbitkan PO</argon-button>
            <argon-button color="secondary" size="sm" :disabled="!allItemsHaveSelectedVendor()" :title="!allItemsHaveSelectedVendor() ? 'Pilih vendor untuk semua barang terlebih dahulu' : ''" @click="openPrintPerbandinganHarga()">Preview Perbandingan Harga</argon-button>
          </div>
        </div>
        <p v-else class="text-secondary text-sm">SPPB sedang diproses oleh Purchasing (komparasi & pemilihan vendor per barang).</p>
      </div>

      <!-- PURCHASE ORDER (otomatis kepecah per vendor, tiap PO progress sendiri-sendiri) -->
      <div v-if="detail.purchase_orders && detail.purchase_orders.length">
        <h6 class="section-title">Purchase Order ({{ detail.purchase_orders.length }})</h6>
        <div v-for="(po, i) in detail.purchase_orders" :key="'po-card-' + i" class="section-box po-card">
            <div class="po-card-header" @click="poExpanded[po.id] = !poExpanded[po.id]">
              <div class="po-card-header-main">
                <span class="po-card-number">{{ po.po_number }}{{ po.po_number_suffix ? '/' + po.po_number_suffix : '' }}</span>
                <span class="po-card-vendor">{{ po.supplier }}</span>
              </div>
              <div class="po-card-header-side">
                <span class="status-pill" :style="statusPillStyle(displayStatusKey(po, true))">{{ statusLabel(displayStatusKey(po, true)) }}</span>
                <span class="po-card-total">Rp {{ formatRupiah(po.po_total) }}</span>
                <i class="fa" :class="poExpanded[po.id] ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
              </div>
            </div>

            <div v-show="poExpanded[po.id]" class="po-card-body">
            <div v-if="userRole === 'Admin' || userRole === 'Purchasing'">
            <div v-if="po.status === 'Selesai'" class="po-done-box">
              <div class="po-done-header">
                <i class="fa fa-check-circle"></i>
                <span>PO Ini Sudah Selesai</span>
              </div>

              <table class="table table-sm mb-3">
                <thead><tr><th>Barang</th><th>Qty</th></tr></thead>
                <tbody>
                  <tr v-for="(it, j) in po.items" :key="j"><td>{{ it.material_name }}</td><td>{{ it.qty }} {{ it.unit }}</td></tr>
                </tbody>
              </table>

              <table class="po-info-table mb-3">
                <tbody>
                  <tr class="group-header"><td colspan="2">Tanda Tangan</td></tr>
                  <tr>
                    <td class="label">Dibuat Oleh</td>
                    <td class="value">{{ po.sign_dibuat || '-' }}</td>
                  </tr>
                  <tr>
                    <td class="label">Disetujui Oleh</td>
                    <td class="value">{{ po.sign_disetujui || '-' }}</td>
                  </tr>

                  <tr class="group-header"><td colspan="2">Pajak &amp; Discount</td></tr>
                  <tr>
                    <td class="label">Discount</td>
                    <td class="value">{{ formatPercent(po.discount_percent) }}%</td>
                  </tr>
                  <tr v-if="po.potongan_harga > 0">
                    <td class="label">Potongan Harga</td>
                    <td class="value">Rp {{ formatRupiah(po.potongan_harga) }}</td>
                  </tr>
                  <tr>
                    <td class="label">PPN</td>
                    <td class="value">{{ formatPercent(po.ppn_percent) }}%</td>
                  </tr>
                  <tr>
                    <td class="label">PPh</td>
                    <td class="value">{{ formatPercent(po.pph_percent) }}%</td>
                  </tr>

                  <template v-if="po.up_name || po.no_sppb_manual">
                    <tr class="group-header"><td colspan="2">Info Tambahan</td></tr>
                    <tr v-if="po.up_name">
                      <td class="label">Up</td>
                      <td class="value">{{ po.up_name }}</td>
                    </tr>
                    <tr v-if="po.no_sppb_manual">
                      <td class="label">No. SPPB</td>
                      <td class="value">{{ po.no_sppb_manual }}</td>
                    </tr>
                  </template>

                  <tr class="group-header"><td colspan="2">Progress Pengadaan</td></tr>
                  <tr v-if="po.resolusi_note">
                    <td class="label">Catatan Receipt</td>
                    <td class="value">{{ po.resolusi_note }}</td>
                  </tr>
                  <tr>
                    <td class="label">No. Invoice</td>
                    <td class="value">{{ po.invoice_number || '-' }}</td>
                  </tr>
                  <tr v-if="po.invoice_photo">
                    <td class="label">Foto Invoice</td>
                    <td class="value"><a :href="photoUrl('invoice_photo', po.invoice_photo)" target="_blank" class="photo-link"><i class="fa fa-image"></i> Lihat Foto</a></td>
                  </tr>
                  <tr>
                    <td class="label">Jumlah Invoice</td>
                    <td class="value">Rp {{ formatRupiah(po.invoice_amount) }}</td>
                  </tr>
                  <tr>
                    <td class="label">Tanggal Bayar</td>
                    <td class="value">{{ po.payment_date || '-' }}</td>
                  </tr>
                  <tr>
                    <td class="label">Jumlah Dibayar</td>
                    <td class="value">Rp {{ formatRupiah(po.payment_amount) }}</td>
                  </tr>
                  <tr v-if="po.payment_photo">
                    <td class="label">Foto Pembayaran</td>
                    <td class="value"><a :href="photoUrl('payment_photo', po.payment_photo)" target="_blank" class="photo-link"><i class="fa fa-image"></i> Lihat Foto</a></td>
                  </tr>
                  <tr>
                    <td class="label">Metode</td>
                    <td class="value">{{ po.payment_method || '-' }}</td>
                  </tr>
                </tbody>
              </table>

              <div class="d-flex justify-content-between align-items-center">
                <argon-button color="secondary" size="sm" :disabled="!(po.sign_dibuat && po.sign_disetujui)" @click="openPrintPoPreview(po)">Preview / Print PO</argon-button>
                <argon-button v-if="userRole === 'Purchasing'" color="secondary" size="sm" title="Mundur (Salah Catat Payment)" @click="doMundurPo(po)"><i class="fa fa-arrow-left"></i></argon-button>
              </div>
            </div>

            <div v-else>
            <div class="text-end mb-2">
              <argon-button color="secondary" size="sm" :disabled="!(po.sign_dibuat && po.sign_disetujui)" :title="!(po.sign_dibuat && po.sign_disetujui) ? 'Isi dan simpan tanda tangan PO terlebih dahulu' : ''" @click="openPrintPoPreview(po)">Preview / Print PO</argon-button>
            </div>
            <table class="table table-sm mb-2">
              <thead><tr><th>Barang</th><th>Qty</th></tr></thead>
              <tbody>
                <tr v-for="(it, j) in po.items" :key="j"><td>{{ it.material_name }}</td><td>{{ it.qty }} {{ it.unit }}</td></tr>
              </tbody>
            </table>

            <!-- INFO PO: DISCOUNT, PPN, PPH & INFO MANUAL (satu form, untuk preview print).
                 Tanda tangan PO (Dibuat Oleh: Randy, Disetujui Oleh: Robinan) sudah otomatis
                 terisi dari backend saat PO diterbitkan, jadi tidak perlu diinput manual di sini. -->
            <div class="po-info-box mb-3" v-if="userRole === 'Purchasing' && taxForms[po.id] && (taxEditing[po.id] || !taxIsComplete(po))">
              <div class="po-info-box-title">Info PO (untuk Preview Print)</div>

              <div class="po-info-group-label">No. PO</div>
              <div class="row g-2 mb-3">
                <div class="col-md-3">
                  <label class="text-xs text-secondary">Nomor (otomatis)</label>
                  <input type="text" class="form-control form-control-sm" :value="po.po_number" disabled>
                </div>
                <div class="col-md-9">
                  <label class="text-xs text-secondary">Format (isi manual, mis. BCK-RETAIL/PO/VIII/2026)</label>
                  <input class="form-control form-control-sm" placeholder="BCK-RETAIL/PO/VIII/2026" v-model="taxForms[po.id].po_number_suffix">
                </div>
              </div>

              <div class="po-info-group-label">Pajak &amp; Discount</div>
              <div class="row g-2 mb-3">
                <div class="col-md-3">
                  <label class="text-xs text-secondary">Discount (%)</label>
                  <input type="number" min="0" max="100" step="0.01" class="form-control form-control-sm" v-model="taxForms[po.id].discount_percent">
                </div>
                <div class="col-md-3">
                  <label class="text-xs text-secondary">Potongan Harga (Rp)</label>
                  <input type="number" min="0" step="1" class="form-control form-control-sm" placeholder="0" v-model="taxForms[po.id].potongan_harga">
                </div>
                <div class="col-md-3">
                  <label class="text-xs text-secondary">PPN (%)</label>
                  <input type="number" min="0" max="100" step="0.01" class="form-control form-control-sm" v-model="taxForms[po.id].ppn_percent">
                </div>
                <div class="col-md-3">
                  <label class="text-xs text-secondary">PPh (%)</label>
                  <input type="number" min="0" max="100" step="0.01" class="form-control form-control-sm" v-model="taxForms[po.id].pph_percent">
                </div>
              </div>

              <div class="po-info-group-label">Info Tambahan <span class="text-secondary fw-normal">(opsional, kalau kosong pakai nilai otomatis)</span></div>
              <div class="row g-2 mb-3">
                <div class="col-md-6">
                  <label class="text-xs text-secondary">Nama Up</label>
                  <input class="form-control form-control-sm" placeholder="Nama" v-model="taxForms[po.id].up_name">
                </div>
                <div class="col-md-6">
                  <label class="text-xs text-secondary">No. SPPB</label>
                  <input class="form-control form-control-sm" placeholder="No. SPPB" v-model="taxForms[po.id].no_sppb_manual">
                </div>
              </div>

              <div class="text-end">
                <argon-button color="info" size="sm" @click="doSavePoInfo(po)">Simpan</argon-button>
              </div>
            </div>
            <div class="po-info-box mb-3" v-else-if="taxForms[po.id]">
              <div class="po-info-box-title d-flex justify-content-between align-items-center">
                <span>Info PO</span>
                <i v-if="po.status === 'PO Diterbitkan' && userRole === 'Purchasing'" class="fa fa-pencil-alt" style="cursor:pointer; color:#8392ab;" title="Ubah info PO" @click="taxEditing[po.id] = true"></i>
              </div>
              <p v-if="!taxIsComplete(po)" class="text-secondary text-sm mb-2">Purchasing belum mengisi Discount, PPN &amp; PPh untuk PO ini.</p>
              <table class="po-info-table">
                <tbody>
                  <tr class="group-header"><td colspan="2">Tanda Tangan</td></tr>
                  <tr>
                    <td class="label">Dibuat Oleh</td>
                    <td class="value">{{ po.sign_dibuat || '-' }}</td>
                  </tr>
                  <tr>
                    <td class="label">Disetujui Oleh</td>
                    <td class="value">{{ po.sign_disetujui || '-' }}</td>
                  </tr>

                  <tr class="group-header"><td colspan="2">Pajak &amp; Discount</td></tr>
                  <tr>
                    <td class="label">Discount</td>
                    <td class="value">{{ formatPercent(po.discount_percent) }}%</td>
                  </tr>
                  <tr v-if="po.potongan_harga > 0">
                    <td class="label">Potongan Harga</td>
                    <td class="value">Rp {{ formatRupiah(po.potongan_harga) }}</td>
                  </tr>
                  <tr>
                    <td class="label">PPN</td>
                    <td class="value">{{ formatPercent(po.ppn_percent) }}%</td>
                  </tr>
                  <tr>
                    <td class="label">PPh</td>
                    <td class="value">{{ formatPercent(po.pph_percent) }}%</td>
                  </tr>

                  <template v-if="po.up_name || po.no_sppb_manual">
                    <tr class="group-header"><td colspan="2">Info Tambahan</td></tr>
                    <tr v-if="po.up_name">
                      <td class="label">Up</td>
                      <td class="value">{{ po.up_name }}</td>
                    </tr>
                    <tr v-if="po.no_sppb_manual">
                      <td class="label">No. SPPB</td>
                      <td class="value">{{ po.no_sppb_manual }}</td>
                    </tr>
                  </template>
                </tbody>
              </table>
              <div v-if="po.tax_updated_by" class="text-xs text-secondary mt-2">
                Terakhir diubah oleh {{ po.tax_updated_by }}<template v-if="po.tax_updated_at"> · {{ po.tax_updated_at }}</template><template v-if="po.status !== 'PO Diterbitkan'"> — sudah dikunci (PO lewat tahap PO Diterbitkan)</template>
              </div>
            </div>

            <div v-if="po.status === 'PO Diterbitkan'">
              <div v-if="!taxIsComplete(po)">
                <p class="text-secondary text-sm mb-0">Isi dan simpan Discount, PPN &amp; PPh PO dulu di atas sebelum lanjut ke Receipt.</p>
              </div>
              <div v-else-if="userRole === 'Purchasing'">
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
                  <div class="col-4">
                    <input type="text" inputmode="numeric" class="form-control form-control-sm" placeholder="Jumlah (Rp)" :value="formatRupiah(poForms[po.id].invoice_amount)" @input="onCurrencyInput(poForms[po.id], 'invoice_amount', $event)">
                    <small v-if="po.grand_total" class="text-secondary">Otomatis dari Grand Total PO: Rp {{ formatRupiah(po.grand_total) }}</small>
                  </div>
                </div>
                <div class="row g-2 mt-1">
                  <div class="col-12">
                    <label class="text-xs text-secondary d-block">Foto Invoice (opsional)</label>
                    <input type="file" accept="image/*" class="form-control form-control-sm" @change="onPhotoChange(poForms[po.id], 'invoice_photo', $event)">
                    <small v-if="po.invoice_photo" class="text-secondary">Foto sudah tersimpan (<a :href="photoUrl('invoice_photo', po.invoice_photo)" target="_blank" class="photo-link"><i class="fa fa-image"></i> Lihat Foto</a>). Kosongkan kalau tidak ingin diganti.</small>
                  </div>
                </div>
                <argon-button color="success" size="sm" class="mt-2" @click="doInvoicePo(po)">Simpan Invoice</argon-button>
                <argon-button color="secondary" size="sm" class="mt-2 ms-1" title="Mundur (Salah Catat Receipt)" @click="doMundurPo(po)"><i class="fa fa-arrow-left"></i></argon-button>
              </div>
              <div v-else class="po-info-box">
                <div class="po-info-box-title">Menunggu Purchasing Mencatat Invoice</div>
                <p v-if="po.resolusi_note" class="text-secondary text-sm mb-0">Catatan Receipt: {{ po.resolusi_note }}</p>
                <p class="text-secondary text-sm mb-0">Belum ada invoice yang dicatat untuk PO ini.</p>
              </div>
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
                <div class="row g-2 mt-1">
                  <div class="col-12">
                    <label class="text-xs text-secondary d-block">Foto Bukti Pembayaran (opsional)</label>
                    <input type="file" accept="image/*" class="form-control form-control-sm" @change="onPhotoChange(poForms[po.id], 'payment_photo', $event)">
                    <small v-if="po.payment_photo" class="text-secondary">Foto sudah tersimpan (<a :href="photoUrl('payment_photo', po.payment_photo)" target="_blank" class="photo-link"><i class="fa fa-image"></i> Lihat Foto</a>). Kosongkan kalau tidak ingin diganti.</small>
                  </div>
                </div>
                <argon-button color="success" size="sm" class="mt-2" @click="doPaymentPo(po)">Simpan Pembayaran</argon-button>
                <argon-button color="secondary" size="sm" class="mt-2 ms-1" title="Mundur (Salah Catat Invoice)" @click="doMundurPo(po)"><i class="fa fa-arrow-left"></i></argon-button>
              </div>
              <div v-else class="po-info-box">
                <div class="po-info-box-title">Invoice Sudah Dicatat Purchasing</div>
                <p class="text-secondary text-sm mb-1">No: {{ po.invoice_number || '-' }} · Tanggal: {{ po.invoice_date || '-' }} · Jumlah: Rp {{ formatRupiah(po.invoice_amount) }}</p>
                <a v-if="po.invoice_photo" :href="photoUrl('invoice_photo', po.invoice_photo)" target="_blank" class="photo-link"><i class="fa fa-image"></i> Lihat Foto Invoice</a>
                <p class="text-secondary text-sm mb-0 mt-2">Menunggu Purchasing mencatat pembayaran.</p>
              </div>
            </div>
            </div>
            </div>
            <div v-else>
              <table class="table table-sm mb-2">
                <thead><tr><th>Barang</th><th>Qty</th></tr></thead>
                <tbody>
                  <tr v-for="(it, j) in po.items" :key="j"><td>{{ it.material_name }}</td><td>{{ it.qty }} {{ it.unit }}</td></tr>
                </tbody>
              </table>
              <p class="text-secondary text-sm mb-0">
                PO sudah diterbitkan ke vendor.
              </p>
            </div>
            </div>
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
      formsSpbId: null,
      userRole: '',
      storageUrl: config.apiUrl.trim().replace(/\/$/, ''), // dipakai buat akses file lewat endpoint /file (lihat photoUrl())
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
      poSearchKeyword: '',
      poSearchResults: [],
      poSearchDone: false,
      taxForms: {},
      taxEditing: {},
      conditionEditing: {},
      conditionForms: {},
      poExpanded: {},
      riwayatExpanded: false,
      stepLabels: ['Pengajuan', 'Permintaan Vendor', 'Penawaran Harga', 'PO Diterbitkan', 'Selesai'],
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
  // Kalau user klik notifikasi PAS lagi udah di halaman SPPB (bukan pindah dari halaman
  // lain), Vue Router cuma ganti query di URL tanpa remount komponen ini, jadi mounted()
  // gak kepanggil ulang. Di-watch di sini supaya tetap kebuka otomatis di kedua kasus.
  watch: {
    '$route.query.open'(newId) {
      if (newId) {
        this.openDetail(newId);
      }
    },
  },
  mounted() {
    this.get();
    this.getRole();
    this.getVendors();
    this.getStockList();
    // Kalau dibuka dari klik notifikasi (?open=ID), langsung tampilkan detail SPPB-nya
    // supaya user gak perlu cari manual di daftar.
    const openId = this.$route.query.open;
    if (openId) {
      this.openDetail(openId);
    }
  },
  methods: {
    notify(message, type) {
      toast(message, { autoClose: 2000, type: type, position: 'top-right' });
    },
    statusPillStyle(status) {
      const map = {
        'Menunggu Approval': { bg: '#fff3cd', color: '#8a6d00' },
        'Ditolak':            { bg: '#fbdcdc', color: '#a71d2a' },
        'Dibatalkan':         { bg: '#e9ecef', color: '#495057' },
        'Permintaan Vendor': { bg: '#d4e6ff', color: '#0a4a9e' },
        'Permintaan Pengadaan': { bg: '#ffe4c4', color: '#8a4b00' },
        'Disposisi':          { bg: '#d4ecff', color: '#0b5ed7' },
        'PO Diterbitkan':     { bg: '#e0d9fb', color: '#5b21b6' },
        'Resolusi':           { bg: '#d9e9fb', color: '#1a4f8a' },
        'Invoice':            { bg: '#d9e9fb', color: '#1a4f8a' },
        'Selesai':            { bg: '#d3f5df', color: '#0f7a3d' },
        'Sedang Diajukan':    { bg: '#d4e6ff', color: '#0a4a9e' },
      };
      const c = map[status] || { bg: '#e9ecef', color: '#495057' };
      return { backgroundColor: c.bg, color: c.color };
      
    },
    isRequesterView() {
      return this.userRole !== 'Admin' && this.userRole !== 'Purchasing';
    },
    poIsReceived(po) {
      return ['Resolusi', 'Invoice', 'Selesai'].includes(po.status);
    },
    spbIsDoneForRequester(record) {
      // Buat user biasa, PO itu gak pernah kelihatan detailnya sama sekali, jadi begitu
      // PO udah diterbitkan (SPB masuk status "PO Diterbitkan" atau lebih lanjut),
      // dianggap Selesai dari sisi mereka — gak perlu nunggu Receipt/Invoice/Payment
      // yang memang gak pernah mereka lihat juga.
      if (record.status === 'Selesai') return true;
      const pos = record.purchase_orders || [];
      return pos.length > 0;
    },
    displayStatusKey(record, isPo) {
      if (!this.isRequesterView()) {
        return record.status;
      }
      if (!isPo && record.status === 'Ditolak') return 'Ditolak';
      if (!isPo && record.status === 'Dibatalkan') return 'Dibatalkan';
      // Kartu PO sendiri gak pernah ditampilkan detail ke user biasa, jadi begitu
      // PO ada (apapun status internalnya), dianggap Selesai — konsisten dengan
      // status utama di atas, gak kontradiksi.
      const done = isPo ? true : this.spbIsDoneForRequester(record);
      return done ? 'Selesai' : 'Sedang Diajukan';
    },
    statusLabel(status) {
      const map = {
        'Permintaan Vendor': 'Permintaan ke Vendor',
        'Permintaan Pengadaan': 'Penawaran Harga Vendor',
        'Resolusi': 'Receipt',
      };
      return map[status] || status;
    },
    stepLabelsList() {
      return this.isRequesterView() ? ['Diajukan', 'Selesai'] : this.stepLabels;
    },
    stepState(idx) {
      if (this.isRequesterView()) {
        return this.spbIsDoneForRequester(this.detail) ? 'done' : (idx === 0 ? 'current' : 'upcoming');
      }
      const order = ['Menunggu Approval', 'Permintaan Vendor', 'Permintaan Pengadaan', 'PO Diterbitkan', 'Selesai'];
      const current = order.indexOf(this.detail.status);
      if (current === -1) return 'upcoming';
      if (idx < current) return 'done';
      if (idx === current) return 'current';
      return 'upcoming';
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
      this.newSpb = { editId: null, no_spb: '', divisi: '', needed_date: '', sign_diajukan: '', sign_ditinjau: '', sign_disetujui: '', items: [] };
      this.addItemRow();
      this.formCreate.show = true;
    },
    // ==== EDIT / BATAL SPPB (sebelum di-approve) ====
    // Hanya boleh dilakukan oleh SI PENGAJU sendiri (bukan Admin/Purchasing), dan hanya
    // selama status masih "Menunggu Approval". Pengecekan di sini cuma untuk kontrol
    // tampil/sembunyi tombol di FE — otorisasi yang beneran tetap divalidasi di backend.
    canEditOrCancel(row) {
      if (!row || row.status !== 'Menunggu Approval') return false;
      if (!this.isRequesterView()) return false; // Admin/Purchasing edit lewat alur approval/pengadaan, bukan di sini
      const myName = localStorage.getItem('username');
      return !!myName && row.created_by === myName;
    },
    // SPPB biasanya barangnya dibutuhkan 3 hari setelah diajukan. Kalau sampai tanggal
    // itu SPPB-nya belum di-ACC Admin, tanggal kebutuhan yang lama sudah tidak relevan lagi
    // — pengaju harus masukkan tanggal kebutuhan yang baru lewat "Edit SPPB".
    isNeededDateOverdue(row) {
      if (!row || row.status !== 'Menunggu Approval' || !row.needed_date) return false;
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const needed = new Date(row.needed_date);
      needed.setHours(0, 0, 0, 0);
      return needed < today;
    },
    openEdit(row) {
      let context = this;
      // Kalau dipanggil dari dalam modal Detail (klik "Edit SPPB" di sana), modal Detail
      // harus ditutup dulu SEBELUM modal Edit dibuka. Kalau enggak, dua modal numpuk
      // barengan (Detail masih tampil DI ATAS Edit), jadi kelihatan kayak "macet" —
      // padahal modal Edit-nya udah kebuka, cuma ketutup modal Detail yang belum hilang.
      context.formDetail.show = false;
      Api(context, spb.show(row.id)).onSuccess(function (response) {
        const data = response.data.data;
        context.newSpb = {
          editId: data.id,
          no_spb: data.no_spb,
          divisi: data.divisi || '',
          needed_date: data.needed_date || '',
          keperluan: data.keperluan || '',
          sign_diajukan: data.sign_diajukan || '',
          sign_ditinjau: data.sign_ditinjau || '',
          sign_disetujui: data.sign_disetujui || '',
          items: (data.items || []).map(it => ({
            material_name: it.material_name || '',
            material_code: it.material_code || '',
            kategori: it.kategori || '',
            merek: it.merek || '',
            specification: it.specification || '',
            qty: it.qty,
            unit: it.unit || '',
            note: it.note || '',
            actual_stock: it.actual_stock ?? '',
            min_stock: it.min_stock ?? '',
          })),
        };
        if (context.newSpb.items.length === 0) {
          context.addItemRow();
        }
        context.formCreate.show = true;
      }).onError(function () {
        context.notify('Gagal mengambil data SPPB untuk diedit', 'error');
      }).call();
    },
    doCancelSpb(row) {
      let context = this;
      if (!confirm('Yakin mau membatalkan SPPB ' + row.no_spb + '? SPPB yang sudah dibatalkan tidak bisa diproses lagi.')) return;
      Api(context, spb.cancel(row.id)).onSuccess(function () {
        context.notify('SPPB Berhasil Dibatalkan', 'success');
        context.get();
        if (context.formDetail.show && context.detail.id === row.id) {
          context.refreshDetail();
        }
      }).onError(function (error) {
        const msg = error?.response?.data?.message || 'Gagal membatalkan SPPB';
        context.notify(msg, 'error');
      }).call();
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
      {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const needed = new Date(context.newSpb.needed_date);
        needed.setHours(0, 0, 0, 0);
        if (needed < today) {
          context.notify('Tanggal barang dibutuhkan tidak boleh tanggal yang sudah lewat. Mohon pilih tanggal hari ini atau setelahnya.', 'error');
          return;
        }
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
      const isEdit = !!context.newSpb.editId;
      const request = isEdit ? spb.update(context.newSpb.editId, context.newSpb) : spb.create(context.newSpb);
      Api(context, request).onSuccess(function () {
        context.notify(isEdit ? 'SPPB Berhasil Diubah' : 'SPPB Berhasil Diajukan', 'success');
        context.formCreate.show = false;
        context.submitting = false;
        context.get();
        if (isEdit && context.formDetail.show && context.detail.id === context.newSpb.editId) {
          context.refreshDetail();
        }
      }).onError(function (error) {
        const msg = error?.response?.data?.message || (isEdit ? 'Gagal Mengubah SPPB' : 'Gagal Mengajukan SPPB');
        context.notify(msg, 'error');
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
    availableVendorsForItem(item) {
      // Vendor yang sudah kasih harga (sudah ada di conditions) tidak perlu muncul lagi di pilihan,
      // supaya tidak bisa dobel-input harga untuk vendor yang sama.
      if (!item || !item.requested_vendors) return [];
      const pricedVendorIds = (item.conditions || []).map(c => c.vendor_id);
      return item.requested_vendors.filter(rv => !pricedVendorIds.includes(rv.vendor_id));
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
      const vendorName = (context.requestVendorForms[item.id] || '').trim();
      if (!vendorName) {
        context.notify('Nama vendor wajib diisi', 'error');
        return;
      }
      Api(context, spb.requestVendor(item.id, { vendor_name: vendorName })).onSuccess(function () {
        context.notify('Vendor berhasil diminta penawaran', 'success');
        context.requestVendorForms[item.id] = '';
        context.getVendors();
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
        context.refreshDetail(true);
      }).onError(function (response) {
        context.notify((response.response && response.response.data && response.response.data.message) || 'Gagal lanjut ke tahap penawaran', 'error');
      }).call();
    },
    // Tombol mundur di header modal (khusus Purchasing), mundur 1 tahap per klik, berhenti
    // di tahap paling awal milik Purchasing (Permintaan Vendor). Dicek dari status PO yang
    // PALING MAJU dulu (bukan cuma status SPB), karena status SPB cuma generic "PO Diterbitkan"
    // begitu ada PO yang mundur dari Selesai, walaupun PO itu sendiri sebenarnya sudah di
    // tahap Invoice/Resolusi, bukan benar-benar "PO Diterbitkan" (belum diisi info PO):
    //   Selesai   -> Invoice   (per-PO, lewat mundurPo)
    //   Invoice   -> Resolusi  (per-PO, lewat mundurPo)
    //   Resolusi  -> PO Diterbitkan (per-PO, lewat mundurPo)
    //   PO Diterbitkan (SPB, dan masih ada PO yang beneran di tahap isi info) -> Penawaran Harga Vendor
    //   Permintaan Pengadaan -> Permintaan Vendor
    // Data yang sudah disimpan tidak hilang, dan isian yang sudah diketik tapi belum disimpan
    // dipertahankan (refreshDetail(true)).
    mundurTahapTarget() {
      if (this.userRole !== 'Purchasing') return null;

      const pos = this.detail.purchase_orders || [];
      const order = ['Selesai', 'Invoice', 'Resolusi'];
      for (const status of order) {
        const po = pos.find(p => p.status === status);
        if (po) return { level: 'po', po, from: status };
      }

      if (this.detail.status === 'PO Diterbitkan' && pos.some(p => p.status === 'PO Diterbitkan')) {
        return { level: 'spb', from: 'PO Diterbitkan' };
      }
      if (this.detail.status === 'Permintaan Pengadaan') {
        return { level: 'spb', from: 'Permintaan Pengadaan' };
      }
      return null;
    },
    canMundurTahap() {
      return this.mundurTahapTarget() !== null;
    },
    mundurTahapTitle() {
      return this.canMundurTahap() ? 'Mundur ke Permintaan Vendor' : '';
    },
    doMundurTahap() {
      let context = this;
      if (!context.mundurTahapTarget()) return;

      if (!confirm('SPPB ini akan dimundurkan sampai ke tahap Permintaan Vendor. PO yang belum lanjut ke Receipt/Invoice/Payment akan dibatalkan (isian Info PO-nya disimpan, dipakai lagi kalau vendor yang sama dikonfirmasi ulang); PO yang sudah lanjut akan dimundurkan bertahap dulu sampai bisa ikut dibatalkan. Data penawaran harga & vendor terpilih tidak akan hilang. Lanjutkan?')) return;

      const step = function () {
        const target = context.mundurTahapTarget();
        if (!target) {
          context.notify('Berhasil mundur ke Permintaan Vendor', 'success');
          context.refreshDetail(true);
          return;
        }
        const request = target.level === 'po' ? spb.mundurPo(target.po.id) : spb.mundur(context.detail.id);
        Api(context, request).onSuccess(function () {
          Api(context, spb.show(context.detail.id)).onSuccess(function (res) {
            context.detail = res.data.data;
            step();
          }).call();
        }).onError(function (error) {
          const res = error && error.response;
          const msg = (res && res.data && res.data.message)
            || ('Gagal memundurkan tahap' + (res && res.status ? ' (kode error ' + res.status + ', cek log backend)' : ''));
          context.notify(msg, 'error');
          context.refreshDetail(true);
        }).call();
      };
      step();
    },
    initForms(keepDrafts) {
      // keepDrafts dipakai saat mundur/lanjut tahap: isian yang sudah diketik tapi belum
      // disimpan dipertahankan, supaya tidak perlu ngetik ulang. Cuma berlaku kalau SPPB-nya
      // sama dengan yang formnya sedang terbuka.
      const prevForms = (keepDrafts === true && this.formsSpbId === this.detail.id)
        ? { itemForms: this.itemForms, requestVendorForms: this.requestVendorForms, poForms: this.poForms, taxForms: this.taxForms }
        : null;
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
      const taxForms = {};
      const poExpanded = {};
      (this.detail.purchase_orders || []).forEach(po => {
        // Isi form dari data yang SUDAH tersimpan di PO (bukan dikosongin), supaya kalau PO
        // dimundurkan (mis. dari Invoice balik ke Receipt) Purchasing tidak perlu ngetik ulang.
        // Kalau belum pernah diisi, invoice_amount default ke Grand Total PO.
        const dateOnly = (v) => (v ? String(v).substring(0, 10) : '');
        const moneyOnly = (v) => (v !== null && v !== undefined && v !== '' ? Math.round(Number(v)) : '');
        poForms[po.id] = {
          resolusi_note: po.resolusi_note || '',
          invoice_number: po.invoice_number || '',
          invoice_date: dateOnly(po.invoice_date),
          invoice_amount: po.invoice_amount !== null && po.invoice_amount !== undefined
            ? moneyOnly(po.invoice_amount)
            : (po.grand_total ? Math.round(po.grand_total) : ''),
          invoice_photo: null,
          payment_date: dateOnly(po.payment_date),
          payment_amount: moneyOnly(po.payment_amount),
          payment_method: po.payment_method || '',
          payment_photo: null,
        };
        signPoForms[po.id] = {
          sign_dibuat: po.sign_dibuat || 'Randy',
          sign_disetujui: po.sign_disetujui || 'Robinan',
        };
        taxForms[po.id] = {
          discount_percent: po.discount_percent,
          potongan_harga: po.potongan_harga,
          ppn_percent: po.ppn_percent,
          pph_percent: po.pph_percent,
          up_name: po.up_name,
          no_sppb_manual: po.no_sppb_manual,
          po_number_suffix: po.po_number_suffix,
        };
        // Pertahankan status buka/tutup kartu PO kalau sebelumnya sudah pernah diset
        // (misal user baru saja simpan form), default: buka kalau belum Selesai.
        poExpanded[po.id] = this.poExpanded[po.id] !== undefined ? this.poExpanded[po.id] : (po.status !== 'Selesai');
      });
      this.poForms = poForms;
      this.signPoForms = signPoForms;
      this.taxForms = taxForms;
      this.poExpanded = poExpanded;

      this.signForm = {
        sign_diajukan: this.detail.sign_diajukan,
        sign_ditinjau: this.detail.sign_ditinjau,
        sign_disetujui: this.detail.sign_disetujui,
      };

      if (prevForms) {
        this.restoreDrafts(prevForms);
      }
      this.formsSpbId = this.detail.id;
    },
    restoreDrafts(prev) {
      const filled = (v) => v !== null && v !== undefined && v !== '';
      Object.keys(this.itemForms).forEach(id => {
        const p = prev.itemForms[id];
        if (p && (filled(p.vendor_id) || filled(p.price) || filled(p.condition_note))) {
          this.itemForms[id] = { vendor_id: p.vendor_id, price: p.price, condition_note: p.condition_note };
        }
      });
      Object.keys(this.requestVendorForms).forEach(id => {
        if (filled(prev.requestVendorForms[id])) {
          this.requestVendorForms[id] = prev.requestVendorForms[id];
        }
      });
      ['poForms', 'taxForms'].forEach(group => {
        Object.keys(this[group]).forEach(id => {
          const p = prev[group][id];
          if (!p) return;
          Object.keys(this[group][id]).forEach(key => {
            if (filled(p[key]) && p[key] !== this[group][id][key]) {
              this[group][id][key] = p[key];
            }
          });
        });
      });
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
    // Cari PO lintas semua SPPB berdasarkan No. PO — buat PO yang sudah "tenggelam"
    // ketimbunan SPPB lain, tidak perlu buka satu-satu.
    searchPo() {
      let context = this;
      const keyword = context.poSearchKeyword.trim();
      if (!keyword) {
        context.clearPoSearch();
        return;
      }
      Api(context, spb.searchPo(keyword)).onSuccess(function (response) {
        context.poSearchResults = response.data.data;
        context.poSearchDone = true;
      }).onError(function () {
        context.poSearchResults = [];
        context.poSearchDone = true;
      }).call();
    },
    clearPoSearch() {
      this.poSearchKeyword = '';
      this.poSearchResults = [];
      this.poSearchDone = false;
    },
    openPoResult(po) {
      this.clearPoSearch();
      this.openDetail(po.spb_id);
    },
    refreshDetail(keepDrafts) {
      let context = this;
      Api(context, spb.show(context.detail.id)).onSuccess(function (response) {
        context.detail = response.data.data;
        if (keepDrafts !== true) {
          context.actionForm = {};
        }
        context.initForms(keepDrafts === true);
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
    lastPriceHint(it, vendorId) {
      if (!vendorId || !it.last_prices) return null;
      const p = it.last_prices[vendorId];
      return (p !== undefined && p !== null) ? p : null;
    },
    onVendorSelectForPrice(it, form) {
      // Auto-isi harga kalau vendor yang dipilih pernah kasih penawaran untuk
      // barang yang sama sebelumnya (dari SPB manapun), tinggal dicek/disesuaikan.
      const hint = this.lastPriceHint(it, form.vendor_id);
      if (hint !== null) {
        form.price = hint;
      }
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
    startEditCondition(c) {
      this.conditionForms[c.id] = { price: c.price, condition_note: c.condition_note };
      this.conditionEditing[c.id] = true;
    },
    doUpdateItemCondition(c) {
      let context = this;
      const form = context.conditionForms[c.id];
      if (!form.price) {
        context.notify('Harga penawaran wajib diisi', 'error');
        return;
      }
      Api(context, spb.updateItemCondition(c.id, form)).onSuccess(function () {
        context.notify('Penawaran Vendor Berhasil Diubah', 'success');
        context.conditionEditing[c.id] = false;
        context.refreshDetail();
      }).onError(function (response) {
        context.notify((response.response && response.response.data && response.response.data.message) || 'Gagal Mengubah Penawaran', 'error');
      }).call();
    },
    doDisposisi(setuju) {
      let context = this;
      Api(context, spb.disposisi(context.detail.id, { disposisi: setuju, disposisi_note: context.actionForm.disposisi_note })).onSuccess(function (response) {
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
    doMundurPo(po) {
      let context = this;
      if (!confirm('Yakin mau mundurkan tahap PO ini? Data yang sudah diisi (Receipt/Invoice/Payment) tidak akan hilang, cuma statusnya mundur supaya bisa diisi ulang.')) return;
      Api(context, spb.mundurPo(po.id)).onSuccess(function (response) {
        context.notify((response.data && response.data.message) || 'PO berhasil dimundurkan', 'success');
        context.refreshDetail(true);
      }).onError(function (response) {
        context.notify((response.response && response.response.data && response.response.data.message) || 'Gagal memundurkan tahap PO', 'error');
      }).call();
    },
    onPhotoChange(form, field, event) {
      form[field] = event.target.files[0] || null;
    },
    photoUrl(folder, filename) {
      return this.storageUrl + '/file?folder=' + folder + '&name=' + encodeURIComponent(filename);
    },
    buildFormData(obj) {
      const formData = new FormData();
      Object.keys(obj).forEach(function (key) {
        const value = obj[key];
        if (value !== null && value !== undefined) {
          formData.append(key, value);
        }
      });
      return formData;
    },
    doInvoicePo(po) {
      let context = this;
      Api(context, spb.invoicePo(po.id, context.buildFormData(context.poForms[po.id]))).onSuccess(function () {
        context.notify('Invoice Berhasil Disimpan', 'success');
        context.refreshDetail();
      }).onError(function (error) {
        const data = error && error.response && error.response.data;
        let msg = (data && data.message) || 'Gagal Menyimpan Invoice';
        if (data && data.data && typeof data.data === 'object') {
          const firstError = Object.values(data.data)[0];
          if (firstError) msg = Array.isArray(firstError) ? firstError[0] : firstError;
        }
        context.notify(msg, 'error');
      }).call();
    },
    doPaymentPo(po) {
      let context = this;
      Api(context, spb.paymentPo(po.id, context.buildFormData(context.poForms[po.id]))).onSuccess(function (response) {
        context.notify((response.data && response.data.message) || 'Pembayaran Berhasil Disimpan', 'success');
        context.refreshDetail();
      }).onError(function (error) {
        const data = error && error.response && error.response.data;
        let msg = (data && data.message) || 'Gagal Menyimpan Pembayaran';
        if (data && data.data && typeof data.data === 'object') {
          const firstError = Object.values(data.data)[0];
          if (firstError) msg = Array.isArray(firstError) ? firstError[0] : firstError;
        }
        context.notify(msg, 'error');
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
    doSavePoInfo(po) {
      let context = this;
      const signForm = context.signPoForms[po.id];
      const taxForm = context.taxForms[po.id];
      if (!signForm.sign_dibuat || !signForm.sign_disetujui) {
        context.notify('Nama Dibuat Oleh dan Disetujui Oleh wajib diisi', 'error');
        return;
      }
      // Discount/PPN/PPh yang dikosongin dianggap 0%, bukan "belum diisi" — biar
      // form ini konsisten nganggep sudah lengkap begitu disimpan, gak nyangkut.
      ['discount_percent', 'potongan_harga', 'ppn_percent', 'pph_percent'].forEach(function (field) {
        if (taxForm[field] === '' || taxForm[field] === null || taxForm[field] === undefined) {
          taxForm[field] = 0;
        }
      });
      Api(context, spb.savePoSignature(po.id, signForm)).onSuccess(function () {
        Api(context, spb.updatePoTax(po.id, taxForm)).onSuccess(function () {
          context.notify('Info PO berhasil disimpan', 'success');
          context.taxEditing[po.id] = false;
          context.refreshDetail();
        }).onError(function () {
          context.notify('Tanda tangan tersimpan, tapi gagal menyimpan Discount/PPN/PPh', 'error');
          context.refreshDetail();
        }).call();
      }).onError(function () {
        context.notify('Gagal menyimpan tanda tangan PO', 'error');
      }).call();
    },
    taxIsComplete(po) {
      return !!po.sign_dibuat && !!po.sign_disetujui
        && po.discount_percent !== null && po.discount_percent !== undefined
        && po.ppn_percent !== null && po.ppn_percent !== undefined
        && po.pph_percent !== null && po.pph_percent !== undefined;
    },
    formatPercent(value) {
      if (value === null || value === undefined) return '0';
      const num = Number(value);
      return Number.isInteger(num) ? String(num) : String(num).replace(/0+$/, '').replace(/\.$/, '');
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
    openPrintPerbandinganHarga() {
      let context = this;
      const ppn = prompt('PPN untuk dokumen perbandingan harga ini berapa persen? (kosongkan / isi 0 kalau tidak kena PPN)', '11');
      if (ppn === null) return; // batal
      const baseUrl = config.apiUrl.trim().replace(/\/$/, '');
      const ppnValue = ppn.trim() === '' ? 11 : ppn.trim();
      window.open(baseUrl + '/print-pdf/perbandingan-harga/' + context.detail.id + '?ppn_percent=' + encodeURIComponent(ppnValue), '_blank');
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
.section-title.collapsible {
  cursor: pointer;
  justify-content: space-between;
  margin-bottom: 0;
  user-select: none;
}
.section-title.collapsible:hover {
  color: #0d6efd;
}
.section-box {
  background-color: #ffffff;
  border: 1px solid #eaecef;
  border-radius: 0.65rem;
  padding: 1.1rem 1.35rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.035);
}
.action-box {
  background-color: #f4f9ff;
  border: 1px solid #d9eaff;
  border-left: 4px solid #0d6efd;
  border-radius: 0.65rem;
  padding: 1.1rem 1.35rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.035);
}

/* ===== STEPPER ===== */
.stepper-wrap {
  padding: 0.5rem 0.25rem 1.35rem;
  margin-bottom: 0.25rem;
}
.stepper {
  display: flex;
  align-items: flex-start;
}
.stepper-step {
  flex: 1;
  position: relative;
  text-align: center;
}
.stepper-step:not(:first-child)::before {
  content: "";
  position: absolute;
  top: 13px;
  left: -50%;
  width: 100%;
  height: 2px;
  background: #dfe4ea;
  z-index: 0;
}
.stepper-step.done:not(:first-child)::before {
  background: #2dce89;
}
.stepper-dot {
  position: relative;
  z-index: 1;
  width: 26px;
  height: 26px;
  margin: 0 auto 6px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  font-weight: 700;
  background: #fff;
  border: 2px solid #dfe4ea;
  color: #9aa4b2;
}
.stepper-step.done .stepper-dot {
  background: #2dce89;
  border-color: #2dce89;
  color: #fff;
}
.stepper-step.current .stepper-dot {
  border-color: #0d6efd;
  color: #0d6efd;
  box-shadow: 0 0 0 4px rgba(13,110,253,0.15);
}
.stepper-label {
  font-size: 0.66rem;
  color: #9aa4b2;
  font-weight: 600;
  line-height: 1.2;
}
.stepper-step.done .stepper-label,
.stepper-step.current .stepper-label {
  color: #344767;
}
.rejected-banner {
  background: #fbeaec;
  color: #a71d2a;
  border: 1px solid #f3c6cb;
  border-radius: 0.5rem;
  padding: 0.7rem 1rem;
  font-weight: 600;
  font-size: 0.85rem;
  margin-bottom: 1.25rem;
}
.overdue-warning {
  background: #fff3cd;
  color: #7a5b00;
  border: 1px solid #ffe08a;
  border-radius: 0.5rem;
  padding: 0.7rem 1rem;
  font-size: 0.85rem;
  line-height: 1.4;
}
.overdue-warning i {
  color: #c98a00;
  margin-right: 6px;
}
.po-search-results {
  background: #f8f9fa;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.75rem 1rem;
}

/* ===== SUMMARY HEADER ===== */
.summary-header {
  margin-bottom: 1.25rem;
}
.summary-main {
  display: flex;
  align-items: center;
  gap: 10px;
}
.summary-divisi {
  font-size: 1.05rem;
  font-weight: 700;
  color: #344767;
}
.summary-sub {
  margin-top: 2px;
}

/* ===== PO CARD (collapsible) ===== */
.po-card {
  padding: 0 !important;
  overflow: hidden;
}
.po-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 0.9rem 1.35rem;
  cursor: pointer;
  user-select: none;
}
.po-card-header:hover {
  background-color: #f8f9fb;
}
.po-card-header-main {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.po-card-number {
  font-weight: 700;
  font-size: 0.85rem;
  color: #344767;
}
.po-card-vendor {
  font-size: 0.75rem;
  color: #8392ab;
}
.po-card-header-side {
  display: flex;
  align-items: center;
  gap: 12px;
}
.po-card-total {
  font-weight: 700;
  font-size: 0.8rem;
  color: #344767;
  white-space: nowrap;
}
.po-card-header .status-pill {
  margin: 0;
}
.po-card-body {
  padding: 0 1.35rem 1.1rem;
  border-top: 1px solid #eaecef;
  padding-top: 1.1rem;
}

/* ===== PO INFO BOX (Tanda Tangan / Discount / PPN / PPh / Up / No SPPB) ===== */
.po-info-box {
  background-color: #f8f9fb;
  border: 1px solid #eaecef;
  border-radius: 0.6rem;
  padding: 0.9rem 1.1rem;
}
.po-info-box-title {
  font-weight: 700;
  font-size: 0.8rem;
  color: #344767;
  margin-bottom: 0.85rem;
}
.po-info-group-label {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: #8392ab;
  margin-bottom: 0.4rem;
}
/* ===== PO DONE (status Selesai) ===== */
.po-done-box {
  background-color: #f0faf4;
  border: 1px solid #cdeedb;
  border-left: 4px solid #2dce89;
  border-radius: 0.6rem;
  padding: 0.9rem 1.1rem;
}
.po-done-header {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 0.85rem;
  color: #0f7a3d;
  margin-bottom: 0.85rem;
}
.po-done-header i {
  font-size: 1.1rem;
}

.po-info-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
}
.po-info-chip {
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 0.5rem;
  padding: 0.4rem 0.75rem;
  min-width: 92px;
}
.po-info-chip-label {
  font-size: 0.62rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: #9aa4b2;
  font-weight: 700;
  margin-bottom: 2px;
}
.po-info-chip-value {
  font-size: 0.8rem;
  font-weight: 700;
  color: #344767;
}

.po-info-table {
  width: 100%;
  border-collapse: collapse;
}
.po-info-table td {
  padding: 0.4rem 0.25rem;
  font-size: 0.78rem;
  border-bottom: 1px solid #f1f3f5;
}
.po-info-table tr:last-child td {
  border-bottom: none;
}
.po-info-table td.label {
  color: #8392ab;
  width: 45%;
}
.po-info-table td.value {
  font-weight: 700;
  color: #344767;
  text-align: right;
}
.po-info-table tr.group-header td {
  background-color: #f8f9fb;
  font-size: 0.66rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: #8392ab;
  font-weight: 700;
  border-bottom: none;
  padding-top: 0.7rem;
}
.po-info-table tr.group-header:first-child td {
  padding-top: 0.3rem;
}

.photo-link {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 0.15rem 0.55rem;
  border: 1px solid #b3d7f5;
  border-radius: 0.4rem;
  background-color: #eaf5ff;
  color: #0d6efd;
  font-size: 0.72rem;
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
}
.photo-link:hover {
  background-color: #dcedff;
  color: #0d6efd;
}
.photo-link i {
  font-size: 0.7rem;
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