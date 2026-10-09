<template>
  <div class="py-4 container-fluid">
    <div class="mb-3">
      <argon-button
        :variant="activeTab === 'barang' ? 'gradient' : 'outline'"
        color="dark"
        size="sm"
        class="me-2"
        @click="activeTab = 'barang'"
      >Barang</argon-button>
      <argon-button
        :variant="activeTab === 'vendor' ? 'gradient' : 'outline'"
        color="dark"
        size="sm"
        @click="switchToVendorTab()"
      >Vendor</argon-button>
    </div>

    <div v-if="activeTab === 'barang'">
    <a class="btn btn-sm btn-primary" style="margin-right: 10px;" :href="apiUrl+'export-excel/stock-barang?material_code='+search.material_code+'&material_name='+search.material_name+'&type='+search.type+'&unit='+search.unit+'&storage_location='+search.storage_location+'&date='+search.date+''" target="_BLANK"><i class="fa fa-download fa-sm"></i> Export</a>
    <a class="btn btn-sm btn-warning" style="margin-left: 0px;" :href="apiUrl+'print-pdf/stock-barang-qr-code?material_code='+search.material_code+'&material_name='+search.material_name+'&type='+search.type+'&unit='+search.unit+'&storage_location='+search.storage_location+''" target="_BLANK"><i class="fa fa-qrcode fa-sm"></i> Print QR Code</a>
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-4">
                <div class="card-header pb-0">
                  <h6>Master Data</h6>
                </div>
              </div>
              <div class="col-4">
              </div>
              <div class="col-4 float-right">
                <argon-button
                  style="margin-right: 10px; margin-left: 60px;"
                  class="mt-4"
                  variant="gradient"
                  color="secondary"
                  size="sm"
                  @click="filter()"
                ><i class="fa fa-filter fa-sm" aria-hidden="true"></i> Filter</argon-button>
                <argon-button
                  class="mt-4"
                  variant="gradient"
                  color="success"
                  size="sm"
                  @click="create()"
                  v-if="role == 'Admin'"
                ><i class="fa fa-plus fa-sm" aria-hidden="true"></i> Add New</argon-button>
              </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2 mt-4">
              <div class="table-responsive p-0 scroll">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">No</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Image</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Code</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Specification</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Type</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Unit</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Actual Stock</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Stock In</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Stock Out</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Min Stock</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Storage Location</th>
                      <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Unit Price</th> -->
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Created At</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Created By</th>
                      <th class="text-secondary"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in table.data" :key="i">
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ i + 1 }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <img
                          v-if="row.image"
                          :src="fotoBase+encodeURIComponent(row.image)"
                        @error="$event.target.onerror=null; $event.target.src=apiUrl+'file?folder=image_barang&name='+encodeURIComponent(row.image)"
                          class="avatar avatar-lg me-1"
                          alt="foto barang"
                        />
                      </td>
                      <td class="align-middle text-center text-sm">
                        <router-link :to="/detail-barang/+row.material_code">
                          <span class="badge badge-sm bg-gradient-primary">{{ row.material_code }}</span>
                        </router-link>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.material_name}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.specification }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.type}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.unit}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="badge badge-sm bg-gradient-success" v-if="row.stock_barang >= row.min_stock">{{ row.stock_barang }}</span>
                        <span class="badge badge-sm bg-gradient-danger" v-if="row.stock_barang < row.min_stock">{{ row.stock_barang }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_barang_masuk_count }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_barang_keluar_count }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.min_stock }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.storage_location }}</span>
                      </td>
                      <!-- <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.unit_price) }}</span>
                      </td> -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ moment(row.created_at).locale('id').format('LL') }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.created_by }}</span>
                      </td>
                      <td>
                        <i class="fas fa-edit fa-sm" aria-hidden="true" style="cursor: pointer; margin-right: 20px;" @click="edit(row.id)" title="Edit"></i>
                        <i class="fa fa-trash-o fa-sm" aria-hidden="true" title="Hapus" style="cursor: pointer; margin-right: 20px;" @click="remove(row.id)"></i>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
              <!-- <div>
                <argon-pagination class="float-right">
                  <argon-pagination-item prev />
                  <argon-pagination-item label="1" active />
                  <argon-pagination-item label="2" disabled />
                  <argon-pagination-item label="3" />
                  <argon-pagination-item next />
                 </argon-pagination>
              </div> -->
            </div>
          </div>
      </div>
    </div>
    </div>

    <div v-if="activeTab === 'vendor'">
      <a class="btn btn-sm btn-primary" :href="vendorExportUrl" target="_BLANK"><i class="fa fa-download fa-sm"></i> Export</a>
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="row">
              <div class="col-4">
                <div class="card-header pb-0">
                  <h6>Master Data - Vendor</h6>
                </div>
              </div>
            </div>

            <div class="row px-3 align-items-center">
              <div class="col-12 col-md-5">
                <input
                  type="text"
                  class="form-control"
                  placeholder="Cari vendor (nama, PIC, produk, telepon, email)..."
                  v-model="vendorSearch"
                >
              </div>
              <div class="col-12 col-md-auto mt-2 mt-md-0">
                <argon-button
                  class="mb-0 me-2"
                  variant="gradient"
                  color="secondary"
                  size="sm"
                  @click="openVendorFilter()"
                ><i class="fa fa-filter fa-sm" aria-hidden="true"></i> Filter<span v-if="vendorFilterCount"> ({{ vendorFilterCount }})</span></argon-button>
                <argon-button
                  class="mb-0"
                  variant="gradient"
                  color="success"
                  size="sm"
                  @click="createVendor()"
                ><i class="fa fa-plus fa-sm" aria-hidden="true"></i> Tambah Vendor</argon-button>
              </div>
            </div>

            <div class="px-4 mt-2 text-secondary text-xs d-flex align-items-center" v-if="vendorTable.data.length">
              <span>Menampilkan {{ filteredVendors.length }} dari {{ vendorTable.data.length }} vendor</span>
              <a v-if="hasVendorFilter" href="javascript:void(0)" class="ms-3 text-xs font-weight-bold" @click="resetVendorFilter()"><i class="fa fa-times fa-sm" aria-hidden="true"></i> Reset filter</a>
            </div>

            <div class="card-body px-0 pt-0 pb-2 mt-3">
              <div class="table-responsive p-0 scroll">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Nama Vendor</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Produk/Barang</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">PIC</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Telepon</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Email</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Termin Bayar</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Alamat</th>
                      <th class="text-secondary"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="vendorTable.data.length === 0">
                      <td colspan="9" class="text-center text-secondary text-sm py-3">
                        Belum ada vendor. Vendor juga otomatis tersimpan di sini kalau diinput baru lewat tahap "Permintaan Vendor" di SPPB.
                      </td>
                    </tr>
                    <tr v-else-if="filteredVendors.length === 0">
                      <td colspan="9" class="text-center text-secondary text-sm py-3">
                        Tidak ada vendor yang cocok dengan pencarian / filter yang dipilih.
                      </td>
                    </tr>
                    <tr v-for="(row, i) in filteredVendors" :key="row.id">
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ i + 1 }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-dark text-xs font-weight-bold">{{ row.name }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs">{{ row.product || '-' }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs">{{ row.pic || '-' }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs">{{ row.phone || '-' }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs">{{ row.email || '-' }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs">{{ row.payment_term || '-' }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs">{{ row.address || '-' }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <i class="fas fa-edit fa-sm" aria-hidden="true" style="cursor: pointer; margin-right: 20px;" @click="editVendor(row.id)" title="Edit"></i>
                        <i class="fa fa-trash-o fa-sm" aria-hidden="true" title="Hapus" style="cursor: pointer;" @click="removeVendor(row.id)"></i>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- =======  MODAL ADD DATA ======= -->
  <div class="container">
    <vue-final-modal v-model="form.show" classes="modal-container" content-class="modal-content" :z-index="10000">
      <!-- header -->
      <div class="row">
        <div class="col-11 float-left">
          <span class="modal__title">{{form.title}}</span>
        </div>
        <div class="col-1 float-right">
          <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="form.show = false"></i>
        </div>
      </div><hr>
      <!-- end header -->
      <div class="modal__content container">
        <label for="example-text-input" class="form-control-label mt-3" v-if="form.title == 'Edit Data'">Material Code <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Material Code" v-model="dataBarangMasuk.material_code" required @change="cekMaterial()" :disabled="form.title == 'Edit Data'" v-if="form.title == 'Edit Data'">

        <label for="example-text-input" class="form-control-label mt-3">Material Name <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Material Name" v-model="dataBarangMasuk.material_name" required >

        <label for="example-text-input" class="form-control-label mt-3">Specification</label>
        <input type="text" class="form-control" placeholder="Specification" v-model="dataBarangMasuk.specification">
        
        <label for="example-text-input" class="form-control-label mt-3">Type <span style="color: red;">*</span></label>
        <select class="form-select" aria-label="Default select example" v-model="dataBarangMasuk.type">
          <option selected>Select Type</option>
          <option value="Consumable">Consumable</option>
          <option value="Sparepart">Sparepart</option>
          <option value="Tools">Tools</option>
          <option value="Other">Other</option>
        </select>

        <label for="example-text-input" class="form-control-label mt-3">Unit <span style="color: red;">*</span></label>
        <select class="form-select" aria-label="Default select example" v-model="dataBarangMasuk.unit">
          <option selected>Select Unit</option>
          <option value="Batang">Batang</option>
          <option value="Drum">Drum</option>
          <option value="Kg">Kg</option>
          <option value="Kaleng">Kaleng</option>
          <option value="Lembar">Lembar</option>
          <option value="Liter">Liter</option>
          <option value="Lusin">Lusin</option>
          <option value="Meter">Meter</option>
          <option value="M3">M3</option>
          <option value="Pack">Pack</option>
          <option value="Pasang">Pasang</option>
          <option value="Pcs">Pcs</option>
          <option value="Rim">Rim</option>
          <option value="Roll">Roll</option>
          <option value="Set">Set</option>
          <option value="Tabung">Tabung</option>
        </select>

        <!-- <label for="example-text-input" class="form-control-label mt-3">Qty <span style="color: red;">*</span></label>
        <input type="number" class="form-control" placeholder="Qty" v-model="dataBarangMasuk.qty"> -->

        <label for="example-text-input" class="form-control-label mt-3">Min Stock <span style="color: red;">*</span></label>
        <input type="number" class="form-control" placeholder="Min Stock" v-model="dataBarangMasuk.min_stock">

        <label for="example-text-input" class="form-control-label mt-3">Storage Location <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Storage Location" v-model="dataBarangMasuk.storage_location">

        <label for="example-text-input" class="form-control-label mt-3">Image <span style="color: red;">*</span></label>
        <input type="file" class="form-control" placeholder="Image" @change="filesChange">

        <!-- <label for="example-text-input" class="form-control-label mt-3">Note </label>
        <input type="text" class="form-control" placeholder="Note" v-model="dataBarangMasuk.note" required> -->
      </div>
      <!-- footer -->
      <div class="row float-right mt-3">
        <div class="col-6"> 
        </div>
        <div class="col-2" style="margin-right: 20px;">
          <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="form.show = true">Close</argon-button>
        </div>
        <div class="col-1">
          <argon-button variant="gradient" color="success" size="sm" width="1" @click="save()">Save</argon-button>
        </div>
      </div>
      <!-- end footer -->
    </vue-final-modal>
   </div>

  <!-- =======  MODAL FILTER ======= -->
  <div class="container">
    <vue-final-modal v-model="formFilter.show" classes="modal-container" content-class="modal-content" :z-index="10000">
      <!-- header -->
      <div class="row">
        <div class="col-11 float-left">
          <span class="modal__title">{{formFilter.title}}</span>
        </div>
        <div class="col-1 float-right">
          <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="formFilter.show = false"></i>
        </div>
      </div><hr>
      <!-- end header -->
      <div class="modal__content container">
        <label for="example-text-input" class="form-control-label mt-3">Material Code</label>
        <input type="text" class="form-control" placeholder="Material Code" v-model="search.material_code">
        <label for="example-text-input" class="form-control-label mt-3">Material Name</label>
        <input type="text" class="form-control" placeholder="Material Name" v-model="search.material_name" required>
        <label for="example-text-input" class="form-control-label mt-3">Type</label>
        <select class="form-select" aria-label="Default select example" v-model="search.type">
          <option selected>Select Type</option>
          <option value="Consumable">Consumable</option>
          <option value="Sparepart">Sparepart</option>
          <option value="Tools">Tools</option>
          <option value="Other">Other</option>
        </select>
        <label for="example-text-input" class="form-control-label mt-3">Unit</label>
        <select class="form-select" aria-label="Default select example" v-model="search.unit">
          <option selected>Select Unit</option>
          <option value="Batang">Batang</option>
          <option value="Drum">Drum</option>
          <option value="Kg">Kg</option>
          <option value="Kaleng">Kaleng</option>
          <option value="Lembar">Lembar</option>
          <option value="Liter">Liter</option>
          <option value="Lusin">Lusin</option>
          <option value="Meter">Meter</option>
          <option value="M3">M3</option>
          <option value="Pack">Pack</option>
          <option value="Pasang">Pasang</option>
          <option value="Pcs">Pcs</option>
          <option value="Rim">Rim</option>
          <option value="Roll">Roll</option>
          <option value="Set">Set</option>
          <option value="Tabung">Tabung</option>
        </select>
        <label for="example-text-input" class="form-control-label mt-3">Storage Location</label>
        <input type="text" class="form-control" placeholder="Storage Location" v-model="search.storage_location" required>
      </div>
      <!-- footer -->
      <div class="row float-right mt-3">
        <div class="col-6"> 
        </div>
        <div class="col-2" style="margin-right: 20px;">
          <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="formFilter.show = true">Close</argon-button>
        </div>
        <div class="col-1">
          <argon-button variant="gradient" color="success" size="sm" width="1" @click="get()">Filter</argon-button>
        </div>
      </div>
      <!-- end footer -->
    </vue-final-modal>
   </div>

  <!-- =======  MODAL ADD/EDIT VENDOR ======= -->
  <div class="container">
    <vue-final-modal v-model="vendorForm.show" classes="modal-container" content-class="modal-content" :z-index="10000">
      <div class="row">
        <div class="col-11 float-left">
          <span class="modal__title">{{ vendorForm.title }}</span>
        </div>
        <div class="col-1 float-right">
          <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="vendorForm.show = false"></i>
        </div>
      </div><hr>
      <div class="modal__content container">
        <label class="form-control-label mt-3">Nama Vendor <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Nama Vendor" v-model="dataVendor.name">

        <label class="form-control-label mt-3">Produk/Barang yang Ditawarkan</label>
        <input type="text" class="form-control" placeholder="misal: Sparepart mesin, ATK, dll" v-model="dataVendor.product">

        <label class="form-control-label mt-3">PIC (Contact Person)</label>
        <input type="text" class="form-control" placeholder="Nama PIC" v-model="dataVendor.pic">

        <label class="form-control-label mt-3">Telepon</label>
        <input type="text" class="form-control" placeholder="No. Telepon" v-model="dataVendor.phone">

        <label class="form-control-label mt-3">Email</label>
        <input type="email" class="form-control" placeholder="Email" v-model="dataVendor.email">

        <label class="form-control-label mt-3">Termin Pembayaran</label>
        <input type="text" class="form-control" placeholder="misal: NET 30, Cash, dll" v-model="dataVendor.payment_term">

        <label class="form-control-label mt-3">Alamat</label>
        <textarea class="form-control" placeholder="Alamat" v-model="dataVendor.address"></textarea>
      </div><hr>
      <div class="row">
        <div class="col-12 text-center">
          <argon-button variant="gradient" color="success" size="sm" @click="saveVendor()">Simpan</argon-button>
        </div>
      </div>
    </vue-final-modal>

    <!-- =======  MODAL FILTER VENDOR ======= -->
    <vue-final-modal v-model="vendorFilterModal.show" classes="modal-container" content-class="modal-content" :z-index="10000">
      <div class="row">
        <div class="col-11 float-left">
          <span class="modal__title">Filter</span>
        </div>
        <div class="col-1 float-right">
          <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="vendorFilterModal.show = false"></i>
        </div>
      </div><hr>
      <div class="modal__content container">
        <label class="form-control-label mt-3">Termin Bayar</label>
        <input type="text" class="form-control" list="vendorPaymentTermList" autocomplete="off" placeholder="Ketik untuk mencari, mis. Cash" v-model="vendorFilterDraft.payment_term">
        <datalist id="vendorPaymentTermList">
          <option v-for="opt in vendorPaymentTermOptions" :key="'pt-' + opt.label" :value="opt.label"></option>
        </datalist>

        <label class="form-control-label mt-3">Produk/Barang</label>
        <input type="text" class="form-control" list="vendorProductList" autocomplete="off" placeholder="Ketik untuk mencari, mis. Sparepart" v-model="vendorFilterDraft.product">
        <datalist id="vendorProductList">
          <option v-for="opt in vendorProductOptions" :key="'pr-' + opt.label" :value="opt.label"></option>
        </datalist>

        <label class="form-control-label mt-3">Kelengkapan Data</label>
        <select class="form-select" v-model="vendorFilterDraft.completeness">
          <option value="">Semua Kelengkapan Data</option>
          <option value="lengkap">Data lengkap</option>
          <option value="tanpa_telepon">Telepon kosong</option>
          <option value="tanpa_email">Email kosong</option>
          <option value="tanpa_pic">PIC kosong</option>
          <option value="tanpa_alamat">Alamat kosong</option>
        </select>
      </div><hr>
      <div class="row">
        <div class="col-12 text-center">
          <argon-button variant="gradient" color="secondary" size="sm" class="me-2" @click="vendorFilterModal.show = false">Close</argon-button>
          <argon-button variant="gradient" color="success" size="sm" @click="applyVendorFilter()">Filter</argon-button>
        </div>
      </div>
    </vue-final-modal>
  </div>

</template>

<script>
import ArgonButton from "@/components/ArgonButton.vue";
import { VueFinalModal } from 'vue-final-modal'
// import ArgonPagination from "@/components/ArgonPagination.vue";
// import ArgonPaginationItem from "@/components/ArgonPaginationItem.vue";
import Api from '@/helpers/api';
import config from '@/configs/config';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
var moment = require('moment');
import stockBarang from '@/services/stockBarang.service';
import barangMasuk from '@/services/barangMasuk.service';
import akun from '@/services/akun.service';
import vendor from '@/services/vendor.service';

// Penanda untuk filter "(Belum diisi)" di Master Data Vendor. Nilai yang sama dipakai
// backend (VendorExport) supaya hasil export = yang tampil di layar.
const VENDOR_EMPTY = '__kosong__';
const VENDOR_EMPTY_LABEL = '(Belum diisi)';

export default {
  name: "tables",
  components: {
    ArgonButton,
    VueFinalModal,
    // ArgonPagination,
    // ArgonPaginationItem,
  },
  data() {
    return {
      moment:moment,
      table: {
        data: []
      },
      form: {
        add: true,
        title: "Add Data",
        show: false
      },
      formFilter: {
        add: true,
        title: "Filter",
        show: false
      },
      onLoading: false,
      storageUrl : config.storageUrl,
      dataBarangMasuk: {},
      search: {
        material_code: '',
        material_name: '',
        type: '',
        unit: '',
        storage_location: '',
        date: '',
      },
      apiUrl :config.apiUrl,
      fotoBase : process.env.BASE_URL + 'foto_barang/',
      role: '',
      activeTab: 'barang',
      vendorTable: { data: [] },
      vendorForm: { show: false, title: 'Tambah Vendor' },
      dataVendor: {},
      vendorLoaded: false,
      vendorSearch: '',
      vendorFilter: { payment_term: '', product: '', completeness: '' },
      vendorFilterDraft: { payment_term: '', product: '', completeness: '' },
      vendorFilterModal: { show: false },
    };
  },
  computed: {
    filteredVendors() {
      const keyword = this.vendorSearch.trim().toLowerCase();
      const f = this.vendorFilter;
      const isEmpty = (v) => (v === null || v === undefined ? '' : v).toString().trim() === '';
      // "(Belum diisi)" = yang kosong; selain itu cocok kalau isinya MENGANDUNG teks yang diketik
      const same = (v, target) => (target === VENDOR_EMPTY
        ? isEmpty(v)
        : (v || '').toString().toLowerCase().includes(target.toLowerCase()));
      const emptyField = { tanpa_telepon: 'phone', tanpa_email: 'email', tanpa_pic: 'pic', tanpa_alamat: 'address' };

      return this.vendorTable.data.filter((row) => {
        if (f.payment_term && !same(row.payment_term, f.payment_term)) return false;
        if (f.product && !same(row.product, f.product)) return false;
        if (f.completeness === 'lengkap' && ['phone', 'email', 'pic', 'address'].some((k) => isEmpty(row[k]))) return false;
        if (emptyField[f.completeness] && !isEmpty(row[emptyField[f.completeness]])) return false;
        if (keyword) {
          const found = [row.name, row.product, row.pic, row.phone, row.email, row.payment_term, row.address]
            .some((field) => (field || '').toString().toLowerCase().includes(keyword));
          if (!found) return false;
        }
        return true;
      });
    },
    vendorPaymentTermOptions() {
      return this.buildVendorOptions('payment_term');
    },
    vendorProductOptions() {
      return this.buildVendorOptions('product');
    },
    vendorFilterCount() {
      const f = this.vendorFilter;
      return [f.payment_term, f.product, f.completeness].filter(Boolean).length;
    },
    hasVendorFilter() {
      const f = this.vendorFilter;
      return !!(this.vendorSearch.trim() || f.payment_term || f.product || f.completeness);
    },
    // Export mengikuti pencarian + filter yang sedang aktif
    vendorExportUrl() {
      const f = this.vendorFilter;
      const params = [];
      if (this.vendorSearch.trim()) params.push('search=' + encodeURIComponent(this.vendorSearch.trim()));
      if (f.payment_term) params.push('payment_term=' + encodeURIComponent(f.payment_term));
      if (f.product) params.push('product=' + encodeURIComponent(f.product));
      if (f.completeness) params.push('completeness=' + encodeURIComponent(f.completeness));
      return this.apiUrl + 'export-excel/vendor' + (params.length ? '?' + params.join('&') : '');
    },
  },
  mounted(){
    this.get();
    this.tokenApi = 'Bearer '+localStorage.getItem('token');
    this.getRole();
  },
  methods: {
    // Daftar pilihan filter diambil dari data vendor yang ada (unik, tidak peduli huruf besar/kecil)
    buildVendorOptions(field) {
      const seen = {};
      let hasEmpty = false;
      this.vendorTable.data.forEach((row) => {
        const value = (row[field] === null || row[field] === undefined ? '' : row[field]).toString().trim();
        if (!value) { hasEmpty = true; return; }
        const key = value.toLowerCase();
        if (!seen[key]) seen[key] = value;
      });
      const options = Object.keys(seen)
        .sort((a, b) => a.localeCompare(b))
        .map((key) => ({ value: seen[key], label: seen[key] }));
      if (hasEmpty) options.push({ value: VENDOR_EMPTY, label: VENDOR_EMPTY_LABEL });
      return options;
    },
    // Seperti filter di Barang: pilihan diisi dulu di jendela Filter, baru berlaku setelah klik "Filter"
    openVendorFilter() {
      const f = this.vendorFilter;
      this.vendorFilterDraft = {
        payment_term: f.payment_term === VENDOR_EMPTY ? VENDOR_EMPTY_LABEL : f.payment_term,
        product: f.product === VENDOR_EMPTY ? VENDOR_EMPTY_LABEL : f.product,
        completeness: f.completeness,
      };
      this.vendorFilterModal.show = true;
    },
    applyVendorFilter() {
      // Teks yang diketik dirapikan; "(Belum diisi)" diubah jadi penanda internal
      const toFilterValue = (text) => {
        const value = (text || '').toString().trim();
        return value.toLowerCase() === VENDOR_EMPTY_LABEL.toLowerCase() ? VENDOR_EMPTY : value;
      };
      this.vendorFilter = {
        payment_term: toFilterValue(this.vendorFilterDraft.payment_term),
        product: toFilterValue(this.vendorFilterDraft.product),
        completeness: this.vendorFilterDraft.completeness,
      };
      this.vendorFilterModal.show = false;
    },
    resetVendorFilter() {
      this.vendorSearch = '';
      this.vendorFilter = { payment_term: '', product: '', completeness: '' };
    },
    get(param){
      let context = this;               
      Api(context, stockBarang.index({material_code: context.search.material_code, material_name: context.search.material_name, type: context.search.type, unit: context.search.unit, storage_location: context.search.storage_location, date: context.search.date})).onSuccess(function(response) {    
          context.table.data = response.data.data.data;
          context.notify('Data Retrieved Successfully', 'success')
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
          context.notify('Data Retrieved Failed', 'error')
      }).onFinish(function() { 
         context.formFilter.show  = false;
      })
      .call()
    },
    getRole(){
      let context = this;     
      context.onLoading = true;
      Api(context, akun.indexProfile()).onSuccess(function(response) {
          context.role = response.data.data[0].role;
      }).onError(function(error) {  
      })
      .call() 
    },
    filter() {
      this.formFilter.add   = true;
      this.formFilter.show  = true;
      this.formFilter.title = "Filter";
      this.onLoading = false;
    },
    create() {
      this.form.add   = true;
      this.form.show  = true;
      this.form.title = "Add Data";
      this.dataBarangMasuk   = {};
      // this.defaultDate()
    },
    edit(id) {
      let context = this;               
      Api(context, stockBarang.show(id)).onSuccess(function(response) {
          context.dataBarangMasuk         = response.data.data;
          context.dataBarangMasuk.qty_old = response.data.data.qty;
          context.form.show               = true;
          context.form.title              = 'Edit Data';       
      })
      .call()        
    },
    filesChange(e) {
      this.dataBarangMasuk.image = e.target.files[0];
    },
    save(){
      let api      = null;
      let context  = this;
      let formData = new FormData();

      if (context.dataBarangMasuk.material_name != undefined && context.dataBarangMasuk.type != undefined && context.dataBarangMasuk.unit != undefined && context.dataBarangMasuk.min_stock != undefined && context.dataBarangMasuk.storage_location != undefined && context.dataBarangMasuk.image != undefined) {
        formData.append('material_code', context.dataBarangMasuk.material_code);
        formData.append('material_name', context.dataBarangMasuk.material_name);
        formData.append('type', context.dataBarangMasuk.type);
        formData.append('unit', context.dataBarangMasuk.unit);
        if (context.form.title == 'Add Data') {
          formData.append('stock_barang', (context.dataBarangMasuk.qty + ((context.dataBarangMasuk.stock_barang == undefined) ? 0 : context.dataBarangMasuk.stock_barang) ));
        }else{
          formData.append('stock_barang', ((context.dataBarangMasuk.stock_barang - context.dataBarangMasuk.qty_old) + context.dataBarangMasuk.qty) );
        }
        formData.append('min_stock', context.dataBarangMasuk.min_stock);
        formData.append('storage_location', context.dataBarangMasuk.storage_location);
        formData.append('specification', (this.dataBarangMasuk.specification == undefined) ? '' : this.dataBarangMasuk.specification);
        formData.append('image', (this.dataBarangMasuk.image == undefined) ? '' : this.dataBarangMasuk.image);
        formData.append('note', (this.dataBarangMasuk.note == undefined) ? '' : this.dataBarangMasuk.note);
      }else{
        return alert('Field Bintang Merah Wajib Di Isi')
      }

      if (context.form.title == 'Add Data') {
          api = Api(context, stockBarang.create(formData));
      } else {
          api = Api(context, stockBarang.update(this.dataBarangMasuk.id, formData));
      }
      // eslint-disable-next-line no-unused-vars
      api.onSuccess(function(response) {
        context.get();
        context.form.show = false;
        context.notify((context.form === 'Add Data') ? 'Data Berhasil di Simpan' : 'Data Berhasil di Update', 'success')
      // eslint-disable-next-line no-unused-vars
      }).onError(function(error) { 
        context.notify((context.form != 'Add Data') ? 'Data Gagal di Simpan' : 'Data Gagal di Update', 'error')                   
      }).onFinish(function() {  
      })
      .call();
    },
    remove(id) {
      var r = confirm("Anda yakin ingin menghapus data ini ?");
      if (r == true) {
        let context = this;

        Api(context, stockBarang.delete(id)).onSuccess(function(response) {
            context.get();
            context.notify('Data Successfully Deleted', 'success')
        }).call();
      }
    },
    notify(message, type) {
      if (type == 'success') {
        toast.success(message, {
          autoClose: 2000,
        }); // ToastOptions
      }else{
        toast.error(message, {
          autoClose: 2000,
        }); // ToastOptions
      }
    },
    defaultDate(){
      var date  = new Date();
      var day   = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var today = date.getFullYear() + "-" + (month) + "-" + (day);

      this.dataBarangMasuk.date = today
    },
    convertRp(bilangan) {
      if (bilangan) {
        var number_string = bilangan.toString(),
            sisa    = number_string.length % 3,
            rupiah  = number_string.substr(0, sisa),
            ribuan  = number_string.substr(sisa).match(/\d{3}/g);

        if(ribuan){
          var separator = sisa ? '.' : '';
          rupiah += separator + ribuan.join('.');
          return rupiah
        }else{
          return bilangan
        }
      }
    },
    // ==== TAB VENDOR (Master Data) ====
    switchToVendorTab() {
      this.activeTab = 'vendor';
      if (!this.vendorLoaded) {
        this.getVendorList();
      }
    },
    getVendorList() {
      let context = this;
      Api(context, vendor.index()).onSuccess(function (response) {
        context.vendorTable.data = response.data.data;
        context.vendorLoaded = true;
      }).onError(function () {
        context.notify('Gagal mengambil daftar vendor', 'error');
      }).call();
    },
    createVendor() {
      this.dataVendor        = {};
      this.vendorForm.title  = 'Tambah Vendor';
      this.vendorForm.show   = true;
    },
    editVendor(id) {
      let context = this;
      Api(context, vendor.show(id)).onSuccess(function (response) {
        context.dataVendor        = response.data.data;
        context.vendorForm.title  = 'Edit Vendor';
        context.vendorForm.show   = true;
      }).onError(function () {
        context.notify('Gagal mengambil data vendor', 'error');
      }).call();
    },
    saveVendor() {
      let context = this;
      if (!context.dataVendor.name) {
        return alert('Nama Vendor wajib diisi');
      }

      const isEdit = context.vendorForm.title === 'Edit Vendor';
      const request = isEdit
        ? vendor.update(context.dataVendor.id, context.dataVendor)
        : vendor.create(context.dataVendor);

      Api(context, request).onSuccess(function () {
        context.vendorForm.show = false;
        context.getVendorList();
        context.notify(isEdit ? 'Vendor Berhasil Diubah' : 'Vendor Berhasil Ditambahkan', 'success');
      }).onError(function (error) {
        const msg = error?.response?.data?.message || 'Gagal menyimpan data vendor';
        context.notify(msg, 'error');
      }).call();
    },
    removeVendor(id) {
      var r = confirm("Anda yakin ingin menghapus vendor ini?");
      if (r == true) {
        let context = this;
        Api(context, vendor.delete(id)).onSuccess(function () {
          context.getVendorList();
          context.notify('Vendor Berhasil Dihapus', 'success');
        }).onError(function (error) {
          const msg = error?.response?.data?.message || 'Gagal menghapus vendor';
          context.notify(msg, 'error');
        }).call();
      }
    },
  }
};
</script>
<style scoped>
::v-deep .modal-container {
  display: flex;
  justify-content: center;
  align-items: center;
  overflow-y: auto;

}
::v-deep .modal-content {
  display: flex;
  flex-direction: column;
  margin: 0 1rem;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.25rem;
  background: #fff;
  width: 500px;
  margin-top: auto;
  margin-bottom: auto;
}
.modal__title {
  font-size: 1rem;
  font-weight: 600;
}
</style>

<style scoped>
.dark-mode div::v-deep .modal-content {
  border-color: #2d3748;
  background-color: #1a202c;
}
.scroll { 
  overflow: auto; 
  height: 500px; 
}
.scroll thead th { 
  position: sticky; 
  top: 0; 
  z-index: 100; 
  background-color: #F0F8FF;
}
</style>