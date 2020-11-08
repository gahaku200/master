<template>
  <header>
      <div class="bg-dark text-white container-fluid">
        <div class="row">
          <div class="col-lg-12 headerLeftTop">
            <h5>会社名：</h5>
            <h3>{{ group }}</h3>
          </div>
          <div class="col-lg-9 headerLeftBottom">
            <p>ユーザー名：</p>
            <h5>{{ username }}</h5>
          </div>
          <div class="col-lg-3 login">
            <ul class="loginSelected" v-if="auth.length === 0">
              <router-link id="header-nav__register" to="/register" class="registerUser">ユーザー登録</router-link>
              <router-link id="header-nav__login" to="/login" class="userLogin">ログイン</router-link>
            </ul>

            <ul class="loginSelected" v-if="auth.length !== 0">
              <li class="authName">
                  ユーザー名：{{auth.name}}
              </li>
              <svg @click="toggle()" width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
              </svg>
              <drawer @close="toggle" :align="align" :closeable="true" :mask-closable="true">
                <ul class="sideMenu" v-if="open">
                  <p @click="goSchedule()">勤務予定表</p>
                  <p>勤務実績表</p>
                  <p>タスク実績表</p>
                  <p>給与計算</p>
                  <a class="logout" id="header-nav__logout" @click="logout()">ログアウト</a>
                  <form id="logout-form" action="/logout" method="POST" style="display: none;">
                    <input type="hidden" name="_token" :value="csrf" />
                  </form>
                </ul>
              </drawer>
            </ul>

          </div>
        </div>
      </div>

      <div class="ready">
        <transition name="fade">
          <div v-if="isActive" class="col-lg-1 menu">
            <p @click="goSchedule()">勤務予定表</p>
            <p>勤務実績表</p>
            <p>タスク実績表</p>
            <p>給与計算</p>
          </div>
        </transition>
      </div>
      
    </header>
</template>

<script>
import Drawer from "vue-simple-drawer";


export default {
  components: {
    Drawer,
  },
  data() {
    return {
      group: 'YJC',
      isActive: false,
      username: '浅田　剛明',
      open: false,
      align: 'right',
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content")
    };
  },
  props: {
    auth: {
      type: Object | Array
    }
  },
  methods: {
    goSchedule() {
      this.$router.push("/schedule");
    },
    active() {  
      this.isActive = !this.isActive  
    },
    toggle() {
      this.open = !this.open
    },
    logout() {
      document.querySelector("#logout-form").submit();
    },
  }
};
</script>