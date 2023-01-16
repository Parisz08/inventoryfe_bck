import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import Tables from "../views/Tables.vue";
import Billing from "../views/Billing.vue";
import VirtualReality from "../views/VirtualReality.vue";
import RTL from "../views/Rtl.vue";
import Profile from "../views/Profile.vue";
import Signup from "../views/Signup.vue";
import Signin from "../views/Signin.vue";
import Login from "../views/Login.vue";

// ADMIN
import DataKaryawan from "../views/admin/DataKaryawan.vue";
import DetailProfile from "../views/admin/DetailProfile.vue";
import DataAbsensi from "../views/admin/DataAbsensi.vue";
import DataPayroll from "../views/admin/DataPayroll.vue";
import DetailSlip from "../views/admin/DetailSlip.vue";

const routes = [
  {
    path: "/",
    name: "/",
    redirect: "/dashboard-default",
  },
  {
    path: "/data-karyawan",
    name: "DataKaryawan",
    component: DataKaryawan,
  },
  {
    path: '/detail-profile/:id_karyawan',
    name: 'DetailProfile',
    component: DetailProfile
  },
  {
    path: "/data-absensi",
    name: "DataAbsensi",
    component: DataAbsensi,
  },
  {
    path: "/data-payroll",
    name: "DataPayroll",
    component: DataPayroll,
  },
  {
    path: '/detail-slip/:id_karyawan/:periode_start/:periode_end',
    name: 'DetailSlip',
    component: DetailSlip
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
  },

  {
    path: "/dashboard-default",
    name: "Dashboard",
    component: Dashboard,
  },
  {
    path: "/tables",
    name: "Tables",
    component: Tables,
  },
  {
    path: "/billing",
    name: "Billing",
    component: Billing,
  },
  {
    path: "/virtual-reality",
    name: "Virtual Reality",
    component: VirtualReality,
  },
  {
    path: "/rtl-page",
    name: "RTL",
    component: RTL,
  },
  {
    path: "/profile",
    name: "Profile",
    component: Profile,
  },
  {
    path: "/signin",
    name: "Signin",
    component: Signin,
  },
  
  
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  linkActiveClass: "active",
});

router.beforeEach((to, from, next) => {
  const publicPages = ['Login'];
  const authRequired = !publicPages.includes(to.name);
  const authenticated = JSON.parse(localStorage.getItem('authenticated'));  

  if (authRequired && !authenticated) {
      alert('Session Kamu Habis Ayo Login Lagi !!!')
      return next({
          name: 'Login',
          query: {redirect: to.fullPath}
      });
  }

  if (authenticated) {
      const auth = JSON.parse(authenticated);
      if (to.name == 'Login') {
          return next({
              name: '/'
          });
      }
      if (to.name != 'Relogin') {
          if (auth.expired) {
            alert('Session Kamu Habis Ayo Login Lagi !!!')
            localStorage.removeItem('token');
            localStorage.setItem('authenticated', false)  
            return next({
                name: 'Login'
            });
          }
      }
  }
  next();
})

export default router;
