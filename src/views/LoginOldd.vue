<template>
  <div class="container top-0 position-sticky z-index-sticky">
    <div class="row">
      <div class="col-12">
        <!-- <navbar isBtn="bg-gradient-light" /> -->
      </div>
    </div>
  </div>
  <main class="main-content mt-0">
    <div
      class="page-header align-items-start min-vh-50 pt-5 pb-11 m-3 border-radius-lg"
      style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signup-cover.jpg'); background-position: top;"
    >
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-5 text-center mx-auto">
            <h1 class="text-white mb-2 mt-5">Welcome!</h1>
            <p
              class="text-lead text-white"
            >This is an employee management system PT. Buana Centra Karya</p>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row mt-lg-n10 mt-md-n11 mt-n10 justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
          <div class="card z-index-0">
            <div class="card-header text-center pt-4">
              <h5>Please Login</h5>
              <p class="text-sm font-weight-bold mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3"></p>
            </div>
            <div class="card-body">
              <!-- <form role="form"> -->
                <input type="text" class="form-control mb-3" placeholder="Username" v-model="username" v-on:keyup.enter="login()" required>
                <input type="password" class="form-control mb-3" placeholder="Password" v-model="password" v-on:keyup.enter="login()" required>
                <div class="text-center">
                  <button type="primary" class="my-4 btn btn-primary btn-outline-dark" @click="login()" :disabled="onLoading">
                      <span v-if="onLoading"><i class="fa fa-spinner fa-spin"></i> Please Wait...</span>
                      <span v-else>
                          <span>Login</span>
                      </span>
                  </button>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <app-footer />
</template>
<script>
// import Navbar from "@/examples/PageLayout/Navbar.vue";
import AppFooter from "@/examples/PageLayout/Footer.vue";
// import ArgonButton from "@/components/ArgonButton.vue";
// const body = document.getElementsByTagName("body")[0];
import config from '@/configs/config';
import axios from 'axios';
import Api from '@/helpers/api';
import akun from '@/services/akun.service';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

export default {
  components: {
    // Navbar,
    AppFooter,
    // ArgonButton,
  },
  created() {
    // this.$store.state.hideConfigButton = true;
    this.$store.state.showNavbar  = false;
    this.$store.state.showSidenav = false;
    this.$store.state.showFooter  = false;
    // body.classList.remove("bg-gray-100");
  },
  beforeUnmount() {
    // this.$store.state.hideConfigButton = false;
    this.$store.state.showNavbar  = true;
    this.$store.state.showSidenav = true;
    this.$store.state.showFooter  = true;
    // body.classList.add("bg-gray-100");
  },
  data() {
    return {
      username: '',
      password: '',
      onLoading: false,
    };
  },
  methods: {
    login() {             
      if (this.username && this.password) {
        var formData = new FormData()
        formData.append('username',  this.username)
        formData.append('password', this.password)

        axios.post(config.apiUrl +"auth/login", formData)
        .then(response => {
          localStorage.setItem('token', response.data.access_token)                                        
          localStorage.setItem('authenticated', true) 

            // ======== untuk menentukan admin ============
            let context = this;     
            context.onLoading = true;          
            Api(context, akun.indexProfile()).onSuccess(function(response) {
                var dataRole = response.data.data[0];
                // context.notifyVue('Selamat anda berhasil login '+dataRole.full_name, 'top', 'right', 'info')
                localStorage.setItem('role', dataRole.role)
                localStorage.setItem('username', dataRole.full_name)
                if (dataRole.role == 'Admin') {
                    context.$router.push('dashboard-default')
                }else{
                    context.$router.push('/detail-profile/'+dataRole.employee_id)
                }
            }).onError(function(error) {  
            })
            .call() 
            // ==============================================                                      
            context.onLoading = true;
        })
        .catch(err => {
         this.notify('Username atau Password Salah', 'error')
        })
      } else {
        this.notify('Error Username Password Required', 'error')
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
  }
};
</script>
