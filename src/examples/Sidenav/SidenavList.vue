<template>
  <div
    class="collapse navbar-collapse w-auto h-auto h-100"
    id="sidenav-collapse-main"
  >
    <ul class="navbar-nav">
      <li class="nav-item">
        <sidenav-item
          url="/dashboard-default"
          :class="getRoute() === 'dashboard-default' ? 'active' : ''"
          navText="Stock Barang"
        >
          <template v-slot:icon>
            <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li class="nav-item">
        <sidenav-item
          url="/barang-masuk"
          :class="getRoute() === 'tables' ? 'active' : ''"
          navText="Barang Masuk"
        >
          <template v-slot:icon>
            <i
              class="ni ni-cloud-download-95 text-success text-sm opacity-10"
            ></i>
          </template>
        </sidenav-item>
      </li>
      <li class="nav-item">
        <sidenav-item
          url="/barang-keluar"
          :class="getRoute() === 'virtual-reality' ? 'active' : ''"
          navText="Barang Keluar"
        >
          <template v-slot:icon>
            <i class="ni ni-delivery-fast text-warning text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li class="nav-item">
        <sidenav-item
          url="/spb"
          :class="getRoute() === 'spb' ? 'active' : ''"
          navText="SPPB"
        >
          <template v-slot:icon>
      <i class="ni ni-single-copy-04 text-info text-sm opacity-10"></i>
    </template>
  </sidenav-item>
</li>
      <li class="nav-item">
        <sidenav-item
          url="/remaind-stock"
          :class="getRoute() === 'billing' ? 'active' : ''"
          navText="Remaind Stock"
        >
          <template v-slot:icon>
            <i class="ni ni-bell-55 text-danger text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li class="nav-item">
        <sidenav-item
          url="/stock-barang"
          :class="getRoute() === 'billing' ? 'active' : ''"
          navText="Master Data"
        >
          <template v-slot:icon>
            <i class="ni ni-collection text-dark text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li class="nav-item" v-if="role == 'Admin'">
        <sidenav-item
          url="/account"
          :class="getRoute() === 'rtl-page' ? 'active' : ''"
          navText="Account"
        >
          <template v-slot:icon>
            <i class="ni ni-satisfied text-primary text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>

    </ul>
  </div>
</template>
<script>
import SidenavItem from "./SidenavItem.vue";
import Api from '@/helpers/api';
import akun from '@/services/akun.service';

export default {
  name: "SidenavList",
  props: {
    cardBg: String
  },
  data() {
    return {
      title: "Argon Dashboard 2",
      controls: "dashboardsExamples",
      isActive: "active",
      role: '',
    };
  },
  components: {
    SidenavItem,
  },
  mounted(){
    this.getRole();
  },
  methods: {
    getRoute() {
      const routeArr = this.$route.path.split("/");
      return routeArr[1];
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
  }
};
</script>
