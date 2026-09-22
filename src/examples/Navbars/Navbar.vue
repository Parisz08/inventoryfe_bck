<template>
  <nav
    class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl"
    :class="
      this.$store.state.isRTL ? 'top-0 position-sticky z-index-sticky' : ''
    "
    v-bind="$attrs"
    id="navbarBlur"
    data-scroll="true"
  >
    <div class="px-3 py-1 container-fluid">
      <!-- <breadcrumbs :currentPage="currentRouteName" textWhite="text-white" /> -->

      <div
        class="mt-2 collapse navbar-collapse mt-sm-0 me-md-0 me-sm-4"
        :class="this.$store.state.isRTL ? 'px-0' : 'me-sm-4'"
        id="navbar"
      >
        <div
          class="pe-md-3 d-flex align-items-center"
          :class="this.$store.state.isRTL ? 'me-md-auto' : 'ms-md-auto'"
        >
          <div class="input-group">
            <!-- <span class="input-group-text text-body">
              <i class="fas fa-search" aria-hidden="true"></i>
            </span>
 -->            <!-- <input
              type="text"
              class="form-control"
              :placeholder="
                this.$store.state.isRTL ? 'أكتب هنا...' : 'Type here...'
              "
            /> -->
          </div>
        </div>
        <ul class="navbar-nav justify-content-end">
          
          <!-- <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
            <a
              href="#"
              @click="toggleSidebar"
              class="p-0 nav-link text-white"
              id="iconNavbarSidenav"
            >
              <div class="sidenav-toggler-inner">
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
              </div>
            </a>
          </li> -->
          <!-- <li class="px-3 nav-item d-flex align-items-center">
            <a class="p-0 nav-link text-white" @click="toggleConfigurator">
              <i class="cursor-pointer fa fa-cog fixed-plugin-button-nav"></i>
            </a>
          </li> -->
          <li
            class="nav-item dropdown d-flex align-items-center"
            :class="this.$store.state.isRTL ? 'ps-2' : 'pe-2'"
          >
            <a
              href="#"
              class="p-0 nav-link text-white position-relative"
              :class="[showMenu ? 'show' : '']"
              id="dropdownMenuButton"
              data-bs-toggle="dropdown"
              aria-expanded="false"
              @click="toggleNotifications()"
            >
              <i class="cursor-pointer fa fa-bell"></i>
              <span
                v-if="unreadCount > 0"
                class="badge badge-sm bg-gradient-danger border-radius-lg position-absolute"
                style="top: -6px; right: -8px; font-size: 10px; padding: 2px 5px;"
              >{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
            </a>
            <ul
              class="px-2 py-3 dropdown-menu dropdown-menu-end me-sm-n4"
              :class="showMenu ? 'show' : ''"
              aria-labelledby="dropdownMenuButton"
              style="min-width: 340px; max-height: 420px; overflow-y: auto;"
            >
              <li class="d-flex justify-content-between align-items-center px-2 mb-2">
                <h6 class="mb-0 text-dark">Notifikasi</h6>
                <a
                  v-if="unreadCount > 0"
                  href="javascript:;"
                  class="text-xs text-primary"
                  @click.stop="markAllRead()"
                >Tandai semua dibaca</a>
              </li>
              <li v-if="notifications.length === 0" class="px-2">
                <p class="text-xs text-secondary mb-1">Belum ada notifikasi.</p>
              </li>
              <li v-for="n in notifications" :key="n.id" class="mb-1">
                <a
                  class="dropdown-item border-radius-md"
                  href="javascript:;"
                  :style="!n.is_read ? 'background-color:#f0f6ff;' : ''"
                  @click="openNotification(n)"
                >
                  <div class="py-1 d-flex">
                    <div class="my-auto">
                      <i
                        class="fa avatar avatar-sm me-3 d-flex align-items-center justify-content-center"
                        :class="notifIcon(n.type)"
                        style="background-color:#e9ecef; color:#344767; border-radius:50%;"
                      ></i>
                    </div>
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-1 text-sm font-weight-normal">
                        <span v-if="!n.is_read" class="unread-dot" title="Belum dibaca"></span>
                        <span class="font-weight-bold">{{ n.title }}</span>
                      </h6>
                      <p class="mb-0 text-xs text-secondary">{{ n.message }}</p>
                      <p class="mb-0 text-xs text-secondary">
                        <i class="fa fa-clock me-1"></i>
                        {{ timeAgo(n.created_at) }}
                      </p>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item d-flex align-items-center text-white">
            <!-- <i class="fa fa-user" ></i> -->
            <span class="d-sm-inline d-none ms-sm-2 me-sm-2">Hallo {{ full_name }}</span>
            <i class="fas fa-sign-out-alt ms-2" @click="logout()" style="cursor: pointer; font-size: 1.1rem;" title="Logout"></i>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>
<script>
// import Breadcrumbs from "../Breadcrumbs.vue";
import { mapMutations, mapActions } from "vuex";
import Api from '@/helpers/api';
import akun from '@/services/akun.service';
import notification from '@/services/notification.service';

export default {
  name: "navbar",
  data() {
    return {
      showMenu: false,
      full_name: '',
      notifications: [],
      unreadCount: 0,
      pollTimer: null,
    };
  },
  props: ["minNav", "textWhite"],
  created() {
    this.minNav;
  },
  mounted() {
    this.getRole();
    this.getUnreadCount();
    // Polling ringan tiap 20 detik supaya badge notifikasi update otomatis tanpa
    // harus reload halaman — user gak perlu buka & cek manual apakah ada progress baru.
    this.pollTimer = setInterval(() => {
      this.getUnreadCount();
    }, 20000);
    document.addEventListener('click', this.handleOutsideClick);
  },
  beforeUnmount() {
    if (this.pollTimer) clearInterval(this.pollTimer);
    document.removeEventListener('click', this.handleOutsideClick);
  },
  methods: {
    ...mapMutations(["navbarMinimize", "toggleConfigurator"]),
    ...mapActions(["toggleSidebarColor"]),

    toggleSidebar() {
      this.toggleSidebarColor("bg-white");
      this.navbarMinimize();
    },
    logout(){
      localStorage.removeItem('token');
      localStorage.setItem('authenticated', false)  
      this.$router.push('/login')
    },
    getRole(){
      let context = this;     
      context.onLoading = true;          
      Api(context, akun.indexProfile()).onSuccess(function(response) {
          context.full_name = response.data.data[0].full_name;
      }).onError(function(error) {  
      })
      .call() 
    },
    getUnreadCount() {
      let context = this;
      Api(context, notification.unreadCount()).onSuccess(function (response) {
        context.unreadCount = response.data.data.unread_count;
      }).onError(function (error) {
        console.error('Gagal ambil unread count notifikasi:', error?.response?.data || error);
      }).call();
    },
    getNotifications() {
      let context = this;
      Api(context, notification.index({ per_page: 15 })).onSuccess(function (response) {
        context.notifications = response.data.data.data;
      }).onError(function (error) {
        console.error('Gagal ambil daftar notifikasi:', error?.response?.data || error);
      }).call();
    },
    toggleNotifications() {
      this.showMenu = !this.showMenu;
      if (this.showMenu) {
        this.getNotifications();
      }
    },
    handleOutsideClick(event) {
      if (this.showMenu && this.$el && !this.$el.contains(event.target)) {
        this.showMenu = false;
      }
    },
    openNotification(n) {
      let context = this;
      if (!n.is_read) {
        Api(context, notification.markRead(n.id)).onSuccess(function () {
          n.is_read = true;
          context.getUnreadCount();
        }).onError(function () {}).call();
      }
      this.showMenu = false;
      if (n.spb_id) {
        this.$router.push({ path: '/spb', query: { open: n.spb_id } });
      }
    },
    markAllRead() {
      let context = this;
      Api(context, notification.markAllRead()).onSuccess(function () {
        context.notifications.forEach(n => { n.is_read = true; });
        context.unreadCount = 0;
      }).onError(function () {}).call();
    },
    notifIcon(type) {
      const map = {
        spb_created:   'fa-file-alt',
        spb_updated:   'fa-pencil-alt',
        spb_cancelled: 'fa-ban',
        spb_approved:  'fa-check-circle',
        spb_rejected:  'fa-times-circle',
        po_issued:     'fa-file-invoice',
        po_received:   'fa-box-open',
        po_invoiced:   'fa-receipt',
        po_paid:       'fa-money-bill-wave',
        spb_selesai:   'fa-flag-checkered',
      };
      return map[type] || 'fa-bell';
    },
    timeAgo(dateStr) {
      if (!dateStr) return '';
      const then = new Date(dateStr.replace(' ', 'T'));
      const diffMs = Date.now() - then.getTime();
      const mins = Math.floor(diffMs / 60000);
      if (mins < 1) return 'Baru saja';
      if (mins < 60) return mins + ' menit lalu';
      const hours = Math.floor(mins / 60);
      if (hours < 24) return hours + ' jam lalu';
      const days = Math.floor(hours / 24);
      return days + ' hari lalu';
    },
  },
  components: {
    // Breadcrumbs
  },
  computed: {
    currentRouteName() {
      return this.$route.name;
    }
  }
};
</script>
<style scoped>
.unread-dot {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #ea0606;
  margin-right: 6px;
  vertical-align: middle;
}
</style>