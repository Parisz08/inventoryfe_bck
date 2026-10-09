<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card sppb-page">

          <!-- Judul + tombol utama -->
          <div class="sppb-head">
            <div class="sppb-head-title">
              <div class="sppb-head-icon"><i class="fa fa-file-alt"></i></div>
              <div>
                <h5 class="mb-0 sppb-head-name">Data SPPB</h5>
                <p class="text-secondary text-sm mb-0">
                  Surat Permohonan Permintaan Barang<span v-if="table.meta.total"> &middot; {{ table.meta.total }} data</span>
                </p>
              </div>
            </div>
            <argon-button variant="gradient" color="success" size="sm" @click="openCreate()"><i class="fa fa-plus fa-sm" aria-hidden="true"></i> Buat SPPB</argon-button>
          </div>

          <!-- Pencarian & filter -->
          <div class="sppb-toolbar">
            <div class="sppb-search">
              <i class="fa fa-search"></i>
              <input type="text" class="form-control" placeholder="Cari No. SPPB (mis. SPPB-0023)" v-model="sppbSearch">
            </div>
            <div class="sppb-search">
              <i class="fa fa-file-invoice"></i>
              <input type="text" class="form-control" placeholder="Cari No. PO, lalu Enter" v-model="poSearchKeyword" @keyup.enter="searchPo()">
            </div>
            <select class="form-select sppb-status-select" v-model="search.status" @change="applyFilter()">
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
            <a v-if="hasListFilter" href="javascript:void(0)" class="sppb-reset" @click="resetListFilter()"><i class="fa fa-times fa-sm" aria-hidden="true"></i> Reset</a>
          </div>

          <!-- HASIL PENCARIAN PO (by No. PO, lintas semua SPPB) -->
          <div class="po-search-results mx-4 mb-3" v-if="poSearchKeyword && (poSearchResults.length > 0 || poSearchDone)">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-sm font-weight-bold text-secondary"><i class="fa fa-file-invoice me-1"></i> Hasil pencarian PO "{{ poSearchKeyword }}"</span>
              <i class="fa fa-times" style="cursor: pointer;" @click="clearPoSearch()" title="Tutup"></i>
            </div>
            <div v-if="poSearchResults.length === 0" class="text-secondary text-sm">Tidak ada PO dengan nomor tersebut.</div>
            <div class="table-responsive" v-else>
              <table class="table table-sm align-items-center mb-0">
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
                    <td class="text-sm">{{ po.spb ? sppbShortNo(po.spb) : '-' }}</td>
                    <td class="text-sm">{{ formatTanggal(po.po_date) }}</td>
                    <td class="text-end">
                      <argon-button color="info" size="xs" variant="gradient" @click="openPoResult(po)">Lihat SPPB</argon-button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Tabel -->
          <div class="table-responsive p-0 scroll">
            <table class="sppb-table">
              <thead>
                <tr>
                  <th>No SPPB</th>
                  <th>Barang</th>
                  <th class="text-center">Status</th>
                  <th>Tanggal</th>
                  <th>Dibuat Oleh</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in table.data" :key="row.id || i" class="sppb-row" @click="openDetail(row.id)">
                  <td>
                    <div class="sppb-no">
                      <span class="sppb-no-icon"><i class="fa fa-file-alt"></i></span>
                      <div>
                        <div class="sppb-no-text">{{ sppbShortNo(row) }}</div>
                        <span class="divisi-pill">{{ row.divisi }}</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="items-cell">
                      <span class="items-first" :title="itemsSummary(row).first">{{ itemsSummary(row).first }}</span>
                      <span v-if="itemsSummary(row).more" class="items-more">+{{ itemsSummary(row).more }} lainnya</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="status-pill status-pill-compact" :style="statusPillStyle(displayStatusKey(row, false))">{{ statusLabel(displayStatusKey(row, false)) }}</span>
                  </td>
                  <td>
                    <div class="date-stack">
                      <div class="date-line"><span class="date-label">Diajukan</span>{{ formatTanggal(row.request_date) }}</div>
                      <div class="date-line" :class="isNeededDateOverdue(row) ? 'is-overdue' : ''"><span class="date-label" title="Barang dibutuhkan pada tanggal">Butuh</span>{{ formatTanggal(row.needed_date) }}</div>
                      <span v-if="isNeededDateOverdue(row)" class="due-badge">Lewat, perlu update</span>
                    </div>
                  </td>
                  <td>
                    <div class="by-cell">
                      <span class="avatar-initial" :style="avatarStyle(row.sign_diajukan || row.created_by)">{{ initials(row.sign_diajukan || row.created_by) }}</span>
                      <span class="by-name">{{ row.sign_diajukan || row.created_by }}</span>
                    </div>
                  </td>
                  <td class="text-center cell-actions" @click.stop>
                    <argon-button color="info" size="xs" variant="gradient" @click="openDetail(row.id)"><i class="fa fa-eye fa-sm" aria-hidden="true"></i> Detail</argon-button>
                    <argon-button v-if="userRole === 'Admin'" color="danger" size="xs" variant="gradient" class="ms-1" title="Hapus SPPB" @click="doDelete(row.id, sppbShortNo(row))"><i class="fa fa-trash fa-sm" aria-hidden="true"></i></argon-button>
                  </td>
                </tr>
                <tr v-if="table.data.length === 0">
                  <td colspan="6">
                    <div class="sppb-empty">
                      <div class="sppb-empty-icon"><i class="fa fa-folder-open"></i></div>
                      <div class="sppb-empty-title">{{ hasListFilter ? 'Tidak ada SPPB yang cocok' : 'Belum ada data SPPB' }}</div>
                      <div class="text-secondary text-sm">{{ hasListFilter ? 'Coba ubah kata kunci atau status yang dipilih.' : 'Klik "Buat SPPB" untuk mengajukan permintaan barang.' }}</div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Navigasi halaman -->
          <div class="sppb-pager" v-if="table.meta.total > 0">
            <div class="text-secondary text-sm">
              Menampilkan {{ pageFrom }}&ndash;{{ pageTo }} dari {{ table.meta.total }} SPPB
            </div>
            <div class="sppb-pager-buttons" v-if="table.meta.last_page > 1">
              <button type="button" class="pg-btn" :disabled="table.meta.current_page <= 1" @click="goToPage(table.meta.current_page - 1)" title="Sebelumnya"><i class="fa fa-chevron-left"></i></button>
              <template v-for="(n, k) in pageNumbers" :key="'pg-' + k">
                <span v-if="n === '...'" class="pg-gap">&hellip;</span>
                <button v-else type="button" class="pg-btn" :class="{ 'is-active': n === table.meta.current_page }" @click="goToPage(n)">{{ n }}</button>
              </template>
              <button type="button" class="pg-btn" :disabled="table.meta.current_page >= table.meta.last_page" @click="goToPage(table.meta.current_page + 1)" title="Berikutnya"><i class="fa fa-chevron-right"></i></button>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- ============ MODAL BUAT SPPB BARU ============ -->
  <vue-final-modal v-model="formCreate.show" classes="modal-container" content-class="modal-content-width" :z-index="10000">
    <div class="sppb-modal-head">
      <div class="sppb-modal-head-left">
        <div class="sppb-modal-icon"><i class="fa" :class="newSpb.editId ? 'fa-pencil-alt' : 'fa-plus'"></i></div>
        <div>
          <div class="sppb-modal-title">{{ newSpb.editId ? 'Edit SPPB' : 'Buat SPPB Baru' }}<span v-if="newSpb.editId" class="sppb-no-badge">{{ sppbShortNo({ id: newSpb.editId }) }}</span></div>
          <div class="sppb-modal-sub">{{ newSpb.editId ? 'Perbarui data SPPB selagi masih menunggu approval.' : 'Isi data permintaan barang, lalu ajukan untuk di-approve.' }}</div>
        </div>
      </div>
      <button type="button" class="sppb-icon-btn" title="Tutup" @click="formCreate.show = false"><i class="fa fa-times" aria-hidden="true"></i></button>
    </div>

    <div class="sppb-modal-body">
      <div class="form-card">
        <div class="form-card-title"><i class="fa fa-info-circle"></i> Informasi SPPB</div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-control-label">Divisi <span class="text-danger">*</span></label>
            <select class="form-select" v-model="newSpb.divisi">
              <option value="">Pilih Divisi</option>
              <option value="Pipa">Pipa</option>
              <option value="Slitting">Slitting</option>
              <option value="Shearing">Shearing</option>
              <option value="Maintenance">Maintenance</option>
              <option value="Logistic">Logistic</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-control-label">Barang Dibutuhkan Pada Tanggal <span class="text-danger">*</span></label>
            <input type="date" class="form-control" v-model="newSpb.needed_date">
          </div>
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title"><i class="fa fa-file-signature"></i> Tanda Tangan SPPB</div>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-control-label">Diajukan Oleh <span class="text-danger">*</span></label>
            <input class="form-control" placeholder="Nama" v-model="newSpb.sign_diajukan">
          </div>
          <div class="col-md-4">
            <label class="form-control-label">Ditinjau Oleh <span class="text-danger">*</span></label>
            <input class="form-control" placeholder="Nama" v-model="newSpb.sign_ditinjau">
          </div>
          <div class="col-md-4">
            <label class="form-control-label">Disetujui Oleh <span class="text-danger">*</span></label>
            <input class="form-control" placeholder="Nama" v-model="newSpb.sign_disetujui">
          </div>
        </div>
      </div>

      <div class="form-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-2" style="gap: 8px;">
          <div class="form-card-title mb-0"><i class="fa fa-box"></i> Daftar Barang <span class="po-count">{{ newSpb.items.length }}</span></div>
          <argon-button color="success" size="xs" class="mb-0" @click="addItemRow()"><i class="fa fa-plus fa-sm"></i> Tambah Barang</argon-button>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center mb-0 item-form-table">
            <thead>
              <tr>
                <th class="text-center">Kode Material</th>
                <th class="text-center">Nama Barang <span class="text-danger">*</span></th>
                <th class="text-center">Kategori <span class="text-danger">*</span></th>
                <th class="text-center">Spesifikasi <span class="text-danger">*</span></th>
                <th class="text-center">Stok Aktual</th>
                <th class="text-center">Stok Minimum</th>
                <th class="text-center">Merek <span class="text-danger">*</span></th>
                <th class="text-center">Qty <span class="text-danger">*</span></th>
                <th class="text-center">Satuan <span class="text-danger">*</span></th>
                <th class="text-center">Keterangan <span class="text-danger">*</span></th>
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
                <td class="text-center"><i class="fa fa-times-circle remove-row" title="Hapus baris" @click="newSpb.items.splice(idx,1)"></i></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="kategori-legend">
          <span class="kategori-legend-title">Kategori:</span>
          <span v-for="(label, code) in kategoriList" :key="'kat-' + code" class="kat-chip"><b>{{ code }}</b> {{ label }}</span>
        </div>
        <datalist id="stockNameList">
          <option v-for="s in stockList" :key="s.id" :value="s.material_name"></option>
        </datalist>
      </div>

      <div class="sppb-modal-foot">
        <argon-button color="secondary" size="sm" class="mb-0" @click="formCreate.show = false">Batal</argon-button>
        <argon-button color="success" variant="gradient" size="sm" class="mb-0" :disabled="submitting" @click="submitCreate()">
          <i class="fa fa-check"></i> {{ submitting ? 'Mengirim...' : (newSpb.editId ? 'Simpan Perubahan' : 'Ajukan SPPB') }}
        </argon-button>
      </div>
    </div>
  </vue-final-modal>

  <!-- ============ MODAL DETAIL & PROSES SPPB ============ -->
  <vue-final-modal v-model="formDetail.show" classes="modal-container" content-class="modal-content-width" :z-index="10000">
    <div class="sppb-modal-head">
      <div class="sppb-modal-head-left">
        <argon-button v-if="canMundurTahap()" color="secondary" size="sm" class="mb-0" :title="mundurTahapTitle()" @click="doMundurTahap()"><i class="fa fa-arrow-left"></i></argon-button>
        <div v-else class="sppb-modal-icon"><i class="fa fa-file-alt"></i></div>
        <div>
          <div class="sppb-modal-title">Detail SPPB <span class="sppb-no-badge">{{ sppbShortNo(detail) }}</span></div>
          <div class="sppb-modal-sub">No. sistem {{ detail.no_spb }}</div>
        </div>
      </div>
      <div class="sppb-modal-head-right">
        <argon-button v-if="detail.status && detail.status !== 'Menunggu Approval' && detail.status !== 'Dibatalkan'" color="secondary" size="sm" class="mb-0" :disabled="!signaturesComplete()" :title="!signaturesComplete() ? 'Isi dan simpan tanda tangan SPPB terlebih dahulu' : ''" @click="openPrintPreview()"><i class="fa fa-print fa-sm"></i> Preview / Print</argon-button>
        <button type="button" class="sppb-icon-btn" title="Tutup" @click="formDetail.show = false"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>
    </div>
    <div class="sppb-modal-body" v-if="detail.id">

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

      <div class="summary-card">
        <div class="summary-main">
          <span class="summary-divisi">{{ detail.divisi }}</span>
          <span class="status-pill" :style="statusPillStyle(displayStatusKey(detail, false))">{{ statusLabel(displayStatusKey(detail, false)) }}</span>
        </div>
        <div class="summary-meta">
          <span class="meta-chip"><i class="fa fa-user"></i> {{ detail.sign_diajukan || detail.created_by }}</span>
          <span class="meta-chip"><i class="fa fa-calendar-alt"></i> Diajukan {{ formatTanggal(detail.request_date) }}</span>
          <span class="meta-chip" :class="isNeededDateOverdue(detail) ? 'meta-chip-danger' : ''"><i class="fa fa-clock"></i> Dibutuhkan {{ formatTanggal(detail.needed_date) }}</span>
        </div>
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
            <p class="text-secondary text-sm" v-if="hasSavedPoDraft()">
              <i class="fa fa-info-circle"></i> Info PO dan progres PO yang sudah disimpan tetap tersimpan. Kalau vendornya sama, PO dilanjutkan lagi dengan isian dan tahap yang sama.
            </p>
            <textarea class="form-control mb-2" placeholder="Catatan (opsional)" v-model="actionForm.disposisi_note"></textarea>
            <argon-button color="success" size="sm" class="me-2" :disabled="!allItemsHaveSelectedVendor()" @click="doDisposisi(true)">Konfirmasi & Terbitkan PO</argon-button>
            <argon-button color="secondary" size="sm" :disabled="!allItemsHaveSelectedVendor()" :title="!allItemsHaveSelectedVendor() ? 'Pilih vendor untuk semua barang terlebih dahulu' : ''" @click="openPrintPerbandinganHarga()">Preview Perbandingan Harga</argon-button>
          </div>
        </div>
        <p v-else class="text-secondary text-sm">SPPB sedang diproses oleh Purchasing (komparasi & pemilihan vendor per barang).</p>
      </div>

      <!-- PURCHASE ORDER (otomatis kepecah per vendor, tiap PO progress sendiri-sendiri) -->
      <div v-if="visiblePurchaseOrders().length">
        <h6 class="section-title">Purchase Order ({{ visiblePurchaseOrders().length }})</h6>
        <div v-for="(po, i) in visiblePurchaseOrders()" :key="'po-card-' + i" class="section-box po-card" :class="{ 'po-card-done': po.status === 'Selesai' }">
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
            <div v-if="po.status === 'Selesai'" class="po-done">

              <!-- Banner ringkasan -->
              <div class="po-done-hero">
                <div class="po-done-hero-icon"><i class="fa fa-check"></i></div>
                <div class="po-done-hero-text">
                  <div class="po-done-hero-title">PO Selesai</div>
                  <div class="po-done-hero-sub">
                    Dibayar {{ formatTanggal(po.payment_date) }}<template v-if="po.payment_method"> &middot; {{ po.payment_method }}</template>
                  </div>
                </div>
                <div class="po-done-hero-amount">
                  <div class="po-done-hero-amount-label">Total Dibayar</div>
                  <div class="po-done-hero-amount-value">{{ rp(po.payment_amount) }}</div>
                </div>
              </div>

              <!-- Alur proses (semua tahap sudah terlewati) -->
              <div class="po-flow">
                <div v-for="(step, k) in poFlowSteps(po)" :key="'flow-' + k" class="po-flow-step">
                  <div class="po-flow-dot"><i class="fa" :class="step.icon"></i></div>
                  <div class="po-flow-label">{{ step.label }}</div>
                  <div class="po-flow-date">{{ step.date }}</div>
                </div>
              </div>

              <!-- Angka ringkas -->
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <div class="po-stat">
                    <div class="po-stat-label">Grand Total PO</div>
                    <div class="po-stat-value">{{ rp(po.grand_total) }}</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="po-stat">
                    <div class="po-stat-label">Jumlah Invoice</div>
                    <div class="po-stat-value">{{ rp(po.invoice_amount) }}</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="po-stat po-stat-highlight">
                    <div class="po-stat-label">Jumlah Dibayar</div>
                    <div class="po-stat-value">{{ rp(po.payment_amount) }}</div>
                    <span v-if="poPaymentDiff(po) === 0" class="po-chip po-chip-ok"><i class="fa fa-check"></i> Sesuai Grand Total</span>
                    <span v-else-if="poPaymentDiff(po) !== null" class="po-chip po-chip-warn">Selisih {{ rp(Math.abs(poPaymentDiff(po))) }} ({{ poPaymentDiff(po) > 0 ? 'lebih' : 'kurang' }})</span>
                  </div>
                </div>
              </div>

              <!-- Barang -->
              <div class="po-done-items">
                <div class="po-kv-title"><i class="fa fa-box"></i> Barang <span class="po-count">{{ (po.items || []).length }}</span></div>
                <div class="table-responsive p-0">
                  <table class="table table-sm align-middle mb-0 grid-table">
                    <thead>
                      <tr>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width:50px;">No</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama Barang</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width:130px;">Qty</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(it, j) in po.items" :key="j">
                        <td class="text-center">{{ j + 1 }}</td>
                        <td>{{ it.material_name }}</td>
                        <td class="text-center">{{ it.qty }} {{ it.unit }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Catatan receipt (kalau ada) -->
              <div v-if="po.resolusi_note" class="po-note">
                <i class="fa fa-info-circle"></i> <b>Catatan Receipt:</b> {{ po.resolusi_note }}
              </div>

              <!-- Detail -->
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="po-kv-card">
                    <div class="po-kv-title"><i class="fa fa-receipt"></i> Invoice</div>
                    <div class="po-kv"><span class="po-kv-label">No. Invoice</span><span class="po-kv-value">{{ po.invoice_number || '-' }}</span></div>
                    <div class="po-kv"><span class="po-kv-label">Tanggal Invoice</span><span class="po-kv-value">{{ formatTanggal(po.invoice_date) }}</span></div>
                    <div class="po-kv"><span class="po-kv-label">Jumlah Invoice</span><span class="po-kv-value">{{ rp(po.invoice_amount) }}</span></div>
                    <div class="po-kv" v-if="po.invoice_photo">
                      <span class="po-kv-label">Foto Invoice</span>
                      <span class="po-kv-value"><a :href="photoUrl('invoice_photo', po.invoice_photo)" target="_blank" class="photo-link"><i class="fa fa-image"></i> Lihat Foto</a></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="po-kv-card">
                    <div class="po-kv-title"><i class="fa fa-money-bill-wave"></i> Pembayaran</div>
                    <div class="po-kv"><span class="po-kv-label">Tanggal Bayar</span><span class="po-kv-value">{{ formatTanggal(po.payment_date) }}</span></div>
                    <div class="po-kv"><span class="po-kv-label">Jumlah Dibayar</span><span class="po-kv-value">{{ rp(po.payment_amount) }}</span></div>
                    <div class="po-kv"><span class="po-kv-label">Metode</span><span class="po-kv-value">{{ po.payment_method || '-' }}</span></div>
                    <div class="po-kv" v-if="po.payment_photo">
                      <span class="po-kv-label">Foto Pembayaran</span>
                      <span class="po-kv-value"><a :href="photoUrl('payment_photo', po.payment_photo)" target="_blank" class="photo-link"><i class="fa fa-image"></i> Lihat Foto</a></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="po-kv-card">
                    <div class="po-kv-title"><i class="fa fa-percent"></i> Pajak &amp; Discount</div>
                    <div class="po-kv"><span class="po-kv-label">Discount</span><span class="po-kv-value">{{ formatPercent(po.discount_percent) }}%</span></div>
                    <div class="po-kv" v-if="po.potongan_harga > 0"><span class="po-kv-label">Potongan Harga</span><span class="po-kv-value">{{ rp(po.potongan_harga) }}</span></div>
                    <div class="po-kv"><span class="po-kv-label">PPN</span><span class="po-kv-value">{{ formatPercent(po.ppn_percent) }}%</span></div>
                    <div class="po-kv"><span class="po-kv-label">PPh</span><span class="po-kv-value">{{ formatPercent(po.pph_percent) }}%</span></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="po-kv-card">
                    <div class="po-kv-title"><i class="fa fa-file-signature"></i> Tanda Tangan &amp; Info</div>
                    <div class="po-kv"><span class="po-kv-label">Dibuat Oleh</span><span class="po-kv-value">{{ po.sign_dibuat || '-' }}</span></div>
                    <div class="po-kv"><span class="po-kv-label">Disetujui Oleh</span><span class="po-kv-value">{{ po.sign_disetujui || '-' }}</span></div>
                    <div class="po-kv" v-if="po.up_name"><span class="po-kv-label">Up</span><span class="po-kv-value">{{ po.up_name }}</span></div>
                    <div class="po-kv" v-if="po.no_sppb_manual"><span class="po-kv-label">No. SPPB</span><span class="po-kv-value">{{ po.no_sppb_manual }}</span></div>
                  </div>
                </div>
              </div>

              <div class="po-done-actions">
                <argon-button color="secondary" size="sm" :disabled="!(po.sign_dibuat && po.sign_disetujui)" @click="openPrintPoPreview(po)"><i class="fa fa-print fa-sm"></i> Preview / Print PO</argon-button>
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
                 Tanda tangan PO (Dibuat Oleh: Randy, Disetujui Oleh: Robinand) sudah otomatis
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
                  <div class="col-4">
                    <input type="text" inputmode="numeric" class="form-control form-control-sm" placeholder="Jumlah (Rp)" :value="formatRupiah(poForms[po.id].payment_amount)" @input="onCurrencyInput(poForms[po.id], 'payment_amount', $event)">
                    <small v-if="po.grand_total" class="text-secondary">Otomatis dari Grand Total PO: Rp {{ formatRupiah(po.grand_total) }}</small>
                  </div>
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
      table: { data: [], meta: { current_page: 1, last_page: 1, total: 0, per_page: 10 } },
      page: 1,
      sppbSearch: '',
      sppbSearchTimer: null,
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
    hasListFilter() {
      return !!(this.sppbSearch.trim() || this.search.status);
    },
    pageFrom() {
      const m = this.table.meta;
      return m.total ? (m.current_page - 1) * m.per_page + 1 : 0;
    },
    pageTo() {
      const m = this.table.meta;
      return Math.min(m.current_page * m.per_page, m.total);
    },
    // Nomor halaman yang ditampilkan, mis. [1, '...', 4, 5, 6, '...', 12]
    pageNumbers() {
      const last = this.table.meta.last_page;
      const cur = this.table.meta.current_page;
      const pages = [];
      for (let n = 1; n <= last; n++) {
        if (n === 1 || n === last || Math.abs(n - cur) <= 1) pages.push(n);
        else if (pages[pages.length - 1] !== '...') pages.push('...');
      }
      return pages;
    },
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
    // Cari No. SPPB berjalan sendiri setelah berhenti mengetik sebentar
    sppbSearch() {
      clearTimeout(this.sppbSearchTimer);
      this.sppbSearchTimer = setTimeout(() => this.applyFilter(), 400);
    },
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
      // PO draft bisa tetap tersimpan waktu SPPB dimundurkan ke Penawaran Harga, jadi yang
      // dicek status SPPB-nya, bukan cuma ada/tidaknya PO.
      const pos = record.purchase_orders || [];
      return record.status === 'PO Diterbitkan' && pos.length > 0;
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
    get(page) {
      let context = this;
      if (page) context.page = page;
      const params = { status: context.search.status, page: context.page };
      if (context.sppbSearch.trim()) params.search = context.sppbSearch.trim();
      Api(context, spb.index(params)).onSuccess(function (response) {
        const result = response.data.data;
        // Kalau halaman yang dibuka sudah kosong (mis. data terakhirnya baru dihapus), mundur ke halaman terakhir
        if (result.data.length === 0 && result.current_page > 1) {
          context.get(result.last_page);
          return;
        }
        context.table.data = result.data;
        context.table.meta = {
          current_page: result.current_page || 1,
          last_page: result.last_page || 1,
          total: result.total || 0,
          per_page: result.per_page || 10,
        };
      }).onError(function () {
        context.table.data = [];
        context.table.meta = { current_page: 1, last_page: 1, total: 0, per_page: 10 };
        context.notify('Gagal mengambil data SPPB', 'error');
      }).call();
    },
    applyFilter() {
      this.page = 1;
      this.get();
    },
    resetListFilter() {
      clearTimeout(this.sppbSearchTimer);
      this.sppbSearch = '';
      this.search.status = '';
      this.page = 1;
      this.get();
    },
    goToPage(n) {
      if (n < 1 || n > this.table.meta.last_page || n === this.table.meta.current_page) return;
      this.get(n);
    },
    // Nomor SPPB yang sama dengan "Req. No." di preview (SPPB-0023). Dari backend (no_spb_short),
    // atau dihitung dari ID dengan rumus yang sama kalau backend belum diperbarui.
    sppbShortNo(rec) {
      if (!rec) return '-';
      if (rec.no_spb_short) return rec.no_spb_short;
      const id = rec.id || rec.spb_id;
      return id ? 'SPPB-' + String(id).padStart(4, '0') : (rec.no_spb || '-');
    },
    initials(name) {
      const parts = (name || '?').toString().trim().split(/\s+/).filter(Boolean);
      if (!parts.length) return '?';
      return (parts[0][0] + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
    },
    avatarStyle(name) {
      const palette = [['#e0e7ff', '#4338ca'], ['#dcfce7', '#15803d'], ['#fee2e2', '#b91c1c'], ['#fef3c7', '#b45309'], ['#e0f2fe', '#0369a1'], ['#fae8ff', '#a21caf']];
      let hash = 0;
      (name || '').toString().split('').forEach((ch) => { hash = (hash * 31 + ch.charCodeAt(0)) % 997; });
      const c = palette[hash % palette.length];
      return { backgroundColor: c[0], color: c[1] };
    },
    itemsSummary(row) {
      const items = (row && row.items) || [];
      return { first: items.length ? items[0].material_name : '-', more: Math.max(items.length - 1, 0) };
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
      if (!confirm('Yakin mau membatalkan SPPB ' + this.sppbShortNo(row) + '? SPPB yang sudah dibatalkan tidak bisa diproses lagi.')) return;
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
    formatTanggal(value) {
      if (!value) return '-';
      const m = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);
      if (!m) return String(value);
      const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      return parseInt(m[3], 10) + ' ' + bulan[parseInt(m[2], 10) - 1] + ' ' + m[1];
    },
    rp(value) {
      if (value === null || value === undefined || value === '') return '-';
      return 'Rp ' + this.formatRupiah(value);
    },
    // Alur proses PO (dipakai di tampilan PO Selesai)
    poFlowSteps(po) {
      return [
        { label: 'PO Diterbitkan', date: this.formatTanggal(po.po_date), icon: 'fa-file-invoice' },
        { label: 'Receipt', date: this.formatTanggal(po.resolusi_at), icon: 'fa-box-open' },
        { label: 'Invoice', date: this.formatTanggal(po.invoice_date), icon: 'fa-receipt' },
        { label: 'Payment', date: this.formatTanggal(po.payment_date), icon: 'fa-money-bill-wave' },
      ];
    },
    // Selisih jumlah dibayar vs Grand Total PO (0 = sesuai, null = datanya belum lengkap)
    poPaymentDiff(po) {
      const empty = (v) => v === null || v === undefined || v === '';
      if (empty(po.grand_total) || empty(po.payment_amount)) return null;
      return Math.round(Number(po.payment_amount)) - Math.round(Number(po.grand_total));
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
    // Tombol mundur di header modal (khusus Purchasing): memundurkan SPPB-nya SAJA, satu tahap
    // per klik:
    //   PO Diterbitkan / Selesai -> Penawaran Harga (Permintaan Pengadaan)
    //   Penawaran Harga          -> Permintaan Vendor
    // Progres tiap PO (info PO, Receipt, Invoice, Payment) TIDAK ikut dimundurkan atau
    // di-reset, jadi begitu vendor dikonfirmasi lagi, PO kembali ke tahap terakhirnya.
    // Kalau memang perlu mengulang tahap sebuah PO, pakai tombol panah di kartu PO-nya.
    mundurTahapTarget() {
      if (this.userRole !== 'Purchasing') return null;
      if (['PO Diterbitkan', 'Selesai', 'Permintaan Pengadaan'].includes(this.detail.status)) {
        return { level: 'spb', from: this.detail.status };
      }
      return null;
    },
    // Selama SPPB ada di Permintaan Vendor / Penawaran Harga (misal habis dimundurkan), semua
    // kartu PO disembunyikan. Datanya TIDAK hilang: Info PO dan progres PO (Receipt/Invoice/
    // Payment) tetap tersimpan dan muncul lagi begitu vendor dikonfirmasi ulang.
    visiblePurchaseOrders() {
      const rolledBack = this.detail.status === 'Permintaan Vendor' || this.detail.status === 'Permintaan Pengadaan';
      return rolledBack ? [] : (this.detail.purchase_orders || []);
    },
    hasSavedPoDraft() {
      return (this.detail.purchase_orders || []).length > 0;
    },
    // Tujuan mundur per klik: dari PO Diterbitkan/seterusnya -> Penawaran Harga,
    // dari Penawaran Harga -> Permintaan Vendor. Cuma satu tahap SPPB per klik.
    mundurGoalStatus() {
      return this.detail.status === 'Permintaan Pengadaan' ? 'Permintaan Vendor' : 'Permintaan Pengadaan';
    },
    canMundurTahap() {
      return this.mundurTahapTarget() !== null;
    },
    mundurTahapTitle() {
      if (!this.canMundurTahap()) return '';
      return this.mundurGoalStatus() === 'Permintaan Vendor' ? 'Mundur ke Permintaan Vendor' : 'Mundur ke Penawaran Harga';
    },
    doMundurTahap() {
      let context = this;
      if (!context.mundurTahapTarget()) return;

      const goal = context.mundurGoalStatus();
      const goalLabel = goal === 'Permintaan Vendor' ? 'Permintaan Vendor' : 'Penawaran Harga';

      let message = 'SPPB ini akan dimundurkan ke tahap ' + goalLabel + '. Data penawaran harga & vendor terpilih tidak akan hilang.';
      if (goal === 'Permintaan Pengadaan') {
        message += ' Info PO yang sudah disimpan dan progres PO (Receipt/Invoice/Payment) juga tidak di-reset: setelah vendor dikonfirmasi lagi, PO kembali ke tahap terakhirnya.';
      }
      message += ' Lanjutkan?';
      if (!confirm(message)) return;

      Api(context, spb.mundur(context.detail.id)).onSuccess(function () {
        context.notify('Berhasil mundur ke ' + goalLabel, 'success');
        context.refreshDetail(true);
      }).onError(function (error) {
        const res = error && error.response;
        const msg = (res && res.data && res.data.message)
          || ('Gagal memundurkan tahap' + (res && res.status ? ' (kode error ' + res.status + ', cek log backend)' : ''));
        context.notify(msg, 'error');
        context.refreshDetail(true);
      }).call();
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
          // Jumlah pembayaran otomatis = Grand Total PO (sama persis dengan preview PO), kecuali
          // sudah pernah disimpan sebelumnya. Tetap bisa diubah manual kalau bayarnya beda.
          payment_amount: po.payment_amount !== null && po.payment_amount !== undefined
            ? moneyOnly(po.payment_amount)
            : (po.grand_total ? Math.round(po.grand_total) : ''),
          payment_method: po.payment_method || '',
          payment_photo: null,
        };
        signPoForms[po.id] = {
          sign_dibuat: po.sign_dibuat || 'Randy',
          sign_disetujui: po.sign_disetujui || 'Robinand',
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
  border-radius: 16px;
  padding: 0;
  width: 92%;
  max-width: 1000px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
}
.modal__title {
  font-weight: bold;
  font-size: 1.1rem;
}
.status-pill {
  display: inline-block;
  min-width: 118px;
  padding: 6px 14px;
  margin: 2px 0;
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
.status-pill-compact {
  min-width: 0;
  max-width: 150px;
  padding: 5px 12px;
  font-size: 0.62rem;
  line-height: 1.2;
  white-space: normal;
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
  flex-wrap: wrap;
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
/* ===== PO SELESAI ===== */
.po-card-done {
  border-left: 4px solid #2dce89;
}
.po-done-hero {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 14px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.1rem;
  border-radius: 0.8rem;
  background: linear-gradient(135deg, #eafaf1 0%, #d3f5df 100%);
  border: 1px solid #bfeccf;
}
.po-done-hero-icon {
  flex-shrink: 0;
  width: 46px;
  height: 46px;
  border-radius: 50%;
  background: #2dce89;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  box-shadow: 0 4px 10px rgba(45, 206, 137, 0.35);
}
.po-done-hero-text {
  flex: 1;
  min-width: 150px;
}
.po-done-hero-title {
  font-weight: 800;
  font-size: 1.05rem;
  line-height: 1.2;
  color: #0f7a3d;
}
.po-done-hero-sub {
  margin-top: 2px;
  font-size: 0.78rem;
  color: #4f8f6a;
}
.po-done-hero-amount {
  text-align: right;
}
.po-done-hero-amount-label {
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.4px;
  text-transform: uppercase;
  color: #4f8f6a;
}
.po-done-hero-amount-value {
  font-size: 1.25rem;
  font-weight: 800;
  white-space: nowrap;
  color: #0f7a3d;
}

.po-flow {
  display: flex;
  margin-bottom: 1.2rem;
}
.po-flow-step {
  position: relative;
  flex: 1;
  text-align: center;
}
.po-flow-step:not(:first-child)::before {
  content: "";
  position: absolute;
  top: 15px;
  left: -50%;
  width: 100%;
  height: 2px;
  background: #2dce89;
  z-index: 0;
}
.po-flow-dot {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  margin: 0 auto 6px;
  border-radius: 50%;
  background: #2dce89;
  color: #fff;
  font-size: 0.72rem;
  box-shadow: 0 0 0 4px rgba(45, 206, 137, 0.15);
}
.po-flow-label {
  font-size: 0.7rem;
  font-weight: 700;
  line-height: 1.2;
  color: #344767;
}
.po-flow-date {
  margin-top: 1px;
  font-size: 0.66rem;
  color: #8392ab;
}

.po-stat {
  height: 100%;
  padding: 0.8rem 1rem;
  background: #fff;
  border: 1px solid #eaecef;
  border-radius: 0.65rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.035);
}
.po-stat-highlight {
  background: #f4fcf7;
  border-color: #bfeccf;
}
.po-stat-label {
  margin-bottom: 3px;
  font-size: 0.64rem;
  font-weight: 700;
  letter-spacing: 0.35px;
  text-transform: uppercase;
  color: #8392ab;
}
.po-stat-value {
  font-size: 1rem;
  font-weight: 800;
  white-space: nowrap;
  color: #344767;
}
.po-chip {
  display: inline-block;
  margin-top: 6px;
  padding: 2px 9px;
  border-radius: 50px;
  font-size: 0.64rem;
  font-weight: 700;
}
.po-chip-ok {
  background: #d3f5df;
  color: #0f7a3d;
}
.po-chip-warn {
  background: #ffe8c7;
  color: #8a4b00;
}

.po-done-items {
  margin-bottom: 1rem;
}
.po-count {
  display: inline-block;
  min-width: 20px;
  padding: 0 6px;
  border-radius: 50px;
  background: #e9ecef;
  color: #495057;
  font-size: 0.64rem;
  line-height: 18px;
  text-align: center;
}
.po-note {
  margin-bottom: 1rem;
  padding: 0.65rem 0.9rem;
  background: #fffbea;
  border: 1px solid #ffe08a;
  border-radius: 0.6rem;
  font-size: 0.78rem;
  color: #7a5b00;
}
.po-kv-card {
  height: 100%;
  padding: 0.8rem 1rem;
  background: #f8f9fb;
  border: 1px solid #eaecef;
  border-radius: 0.65rem;
}
.po-kv-title {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 0.45rem;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.35px;
  text-transform: uppercase;
  color: #8392ab;
}
.po-kv {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 0.32rem 0;
  border-bottom: 1px dashed #e6e9ee;
  font-size: 0.78rem;
}
.po-kv:last-child {
  border-bottom: none;
}
.po-kv-label {
  color: #8392ab;
}
.po-kv-value {
  font-weight: 700;
  text-align: right;
  color: #344767;
}
.po-done-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
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

/* =====================================================================
   HALAMAN DAFTAR SPPB
   ===================================================================== */
.sppb-page {
  border-radius: 1rem;
  overflow: hidden;
}
.sppb-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding: 1.25rem 1.5rem 1rem;
}
.sppb-head-title {
  display: flex;
  align-items: center;
  gap: 14px;
}
.sppb-head-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: linear-gradient(135deg, #5e72e4, #825ee4);
  box-shadow: 0 6px 14px rgba(94, 114, 228, 0.35);
  color: #fff;
  font-size: 1.05rem;
}
.sppb-head-name {
  font-weight: 800;
  color: #344767;
}
.sppb-toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  padding: 0 1.5rem 1rem;
}
.sppb-search {
  position: relative;
  flex: 1 1 220px;
  max-width: 300px;
}
.sppb-search i {
  position: absolute;
  top: 50%;
  left: 12px;
  transform: translateY(-50%);
  color: #9aa4b2;
  font-size: 0.8rem;
  pointer-events: none;
}
.sppb-search .form-control {
  height: 38px;
  padding-left: 34px;
}
.sppb-status-select {
  width: 210px;
  height: 38px;
}
.sppb-reset {
  font-size: 0.75rem;
  font-weight: 700;
  color: #8392ab;
}
.sppb-reset:hover {
  color: #344767;
}

.sppb-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}
.sppb-table thead th {
  padding: 0.7rem 0.6rem;
  background: #f8f9fe;
  border-top: 1px solid #eef0f5;
  border-bottom: 1px solid #eef0f5;
  color: #8392ab;
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  white-space: nowrap;
}
.sppb-table tbody td {
  padding: 0.75rem 0.6rem;
  border-bottom: 1px solid #f1f3f7;
  vertical-align: middle;
  font-size: 0.8rem;
  color: #344767;
}
/* Jarak dari tepi kartu: kolom pertama & terakhir (Aksi) tidak menempel ke pinggir */
.sppb-table th:first-child,
.sppb-table td:first-child {
  padding-left: 1.25rem;
}
.sppb-table th:last-child,
.sppb-table td:last-child {
  padding-right: 1.25rem;
}
.sppb-row {
  cursor: pointer;
  transition: background 0.15s;
}
.sppb-row:hover {
  background: #f8f9ff;
}
.sppb-row:last-child td {
  border-bottom: none;
}
.sppb-no {
  display: flex;
  align-items: center;
  gap: 10px;
}
.sppb-no-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 30px;
  height: 30px;
  border-radius: 8px;
  background: #eef0ff;
  color: #5e72e4;
  font-size: 0.85rem;
}
.sppb-no-text {
  font-weight: 800;
  letter-spacing: 0.2px;
  white-space: nowrap;
  color: #344767;
}
.divisi-pill {
  display: inline-block;
  margin-top: 2px;
  padding: 1px 9px;
  border-radius: 50px;
  background: #f1f3f9;
  color: #5a6a85;
  font-size: 0.7rem;
  font-weight: 700;
}
.items-cell {
  display: flex;
  flex-direction: column;
  max-width: 135px;
}
.items-first {
  overflow: hidden;
  font-weight: 600;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.items-more {
  font-size: 0.68rem;
  color: #8392ab;
}
.date-stack {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.date-line {
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
  color: #5a6a85;
}
.date-line.is-overdue {
  color: #dc2626;
}
.date-label {
  display: inline-block;
  width: 56px;
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.3px;
  text-transform: uppercase;
  color: #9aa4b2;
}
.due-badge {
  display: block;
  width: max-content;
  margin-top: 2px;
  padding: 1px 8px;
  border-radius: 50px;
  background: #fee2e2;
  color: #b91c1c;
  font-size: 0.6rem;
  font-weight: 700;
}
.cell-actions {
  white-space: nowrap;
}
.by-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}
.avatar-initial {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  font-size: 0.65rem;
  font-weight: 800;
}
.by-name {
  max-width: 76px;
  overflow: hidden;
  font-weight: 600;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.sppb-empty {
  padding: 2.5rem 1rem;
  text-align: center;
}
.sppb-empty-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  margin: 0 auto 0.75rem;
  border-radius: 50%;
  background: #f1f3f9;
  color: #9aa4b2;
  font-size: 1.3rem;
}
.sppb-empty-title {
  margin-bottom: 2px;
  font-weight: 700;
  color: #344767;
}
.sppb-pager {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 0.9rem 1.5rem;
  border-top: 1px solid #eef0f5;
}
.sppb-pager-buttons {
  display: flex;
  align-items: center;
  gap: 4px;
}
.pg-btn {
  min-width: 34px;
  height: 34px;
  padding: 0 8px;
  border: 1px solid #e3e6ee;
  border-radius: 8px;
  background: #fff;
  color: #5a6a85;
  font-size: 0.75rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s;
}
.pg-btn:hover:not(:disabled) {
  border-color: #5e72e4;
  color: #5e72e4;
}
.pg-btn.is-active {
  border-color: transparent;
  background: linear-gradient(135deg, #5e72e4, #825ee4);
  box-shadow: 0 4px 10px rgba(94, 114, 228, 0.35);
  color: #fff;
}
.pg-btn:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}
.pg-gap {
  padding: 0 4px;
  color: #9aa4b2;
}

/* Layar lebar: teks nama barang & pembuat tidak perlu dipotong */
@media (min-width: 1300px) {
  .items-cell {
    max-width: 220px;
  }
  .by-name {
    max-width: 130px;
  }
}

/* =====================================================================
   JENDELA (MODAL): BUAT/EDIT & DETAIL
   ===================================================================== */
.sppb-modal-head {
  position: sticky;
  top: 0;
  z-index: 5;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 0.9rem 1.5rem;
  background: rgba(255, 255, 255, 0.97);
  border-bottom: 1px solid #eef0f5;
}
.sppb-modal-head-left,
.sppb-modal-head-right {
  display: flex;
  align-items: center;
  gap: 12px;
}
.sppb-modal-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: linear-gradient(135deg, #5e72e4, #825ee4);
  box-shadow: 0 5px 12px rgba(94, 114, 228, 0.3);
  color: #fff;
}
.sppb-modal-title {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
  font-size: 1rem;
  font-weight: 800;
  line-height: 1.2;
  color: #344767;
}
.sppb-modal-sub {
  margin-top: 1px;
  font-size: 0.72rem;
  color: #8392ab;
}
.sppb-no-badge {
  padding: 2px 10px;
  border-radius: 50px;
  background: #eef0ff;
  color: #4338ca;
  font-size: 0.8rem;
  font-weight: 800;
}
.sppb-icon-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border: 0;
  border-radius: 50%;
  background: #f1f3f9;
  color: #5a6a85;
  cursor: pointer;
  transition: background 0.15s;
}
.sppb-icon-btn:hover {
  background: #e4e7f1;
  color: #344767;
}
.sppb-modal-body {
  padding: 1.25rem 1.5rem 1.5rem;
}
.sppb-modal-foot {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.form-card {
  margin-bottom: 1rem;
  padding: 1rem 1.25rem;
  background: #fff;
  border: 1px solid #eaecef;
  border-radius: 0.8rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
}
.form-card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 0.8rem;
  font-size: 0.72rem;
  font-weight: 800;
  letter-spacing: 0.4px;
  text-transform: uppercase;
  color: #5a6a85;
}
.form-card-title i {
  color: #5e72e4;
}
.item-form-table thead th {
  padding: 0.55rem 0.5rem;
  background: #f8f9fe;
  border-bottom: 1px solid #eef0f5;
  color: #8392ab;
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.4px;
  text-transform: uppercase;
  white-space: nowrap;
}
.item-form-table td {
  padding: 0.4rem 0.35rem;
  vertical-align: middle;
}
.item-form-table .form-control,
.item-form-table .form-select {
  min-width: 90px;
}
.remove-row {
  color: #c0c6d3;
  cursor: pointer;
  transition: color 0.15s;
}
.remove-row:hover {
  color: #dc2626;
}
.kategori-legend {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
  margin-top: 0.85rem;
}
.kategori-legend-title {
  font-size: 0.68rem;
  font-weight: 700;
  color: #8392ab;
}
.kat-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 3px 10px;
  border-radius: 50px;
  background: #f1f3f9;
  color: #5a6a85;
  font-size: 0.68rem;
}
.kat-chip b {
  color: #5e72e4;
}

.summary-card {
  margin-bottom: 1.25rem;
  padding: 0.9rem 1.1rem;
  background: linear-gradient(135deg, #f8f9fe, #fff);
  border: 1px solid #eaecef;
  border-radius: 0.8rem;
}
.summary-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 0.6rem;
}
.meta-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 11px;
  border-radius: 50px;
  background: #fff;
  border: 1px solid #e6e9f2;
  font-size: 0.72rem;
  font-weight: 600;
  color: #5a6a85;
}
.meta-chip i {
  font-size: 0.7rem;
  color: #9aa4b2;
}
.meta-chip-danger {
  background: #fff1f1;
  border-color: #f8c9c9;
  color: #b91c1c;
}
.meta-chip-danger i {
  color: #b91c1c;
}
</style>