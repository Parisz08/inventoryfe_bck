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
          navText="Dashboard"
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
          url="/stock-barang"
          :class="getRoute() === 'billing' ? 'active' : ''"
          navText="Stock Barang"
        >
          <template v-slot:icon>
            <i class="ni ni-collection text-dark text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li class="nav-item">
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

export default {
  name: "SidenavList",
  props: {
    cardBg: String
  },
  data() {
    return {
      title: "Argon Dashboard 2",
      controls: "dashboardsExamples",
      isActive: "active"
    };
  },
  components: {
    SidenavItem,
  },
  methods: {
    getRoute() {
      const routeArr = this.$route.path.split("/");
      return routeArr[1];
    }
  }
};
</script>
