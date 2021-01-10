<template>
  <header>
    <div class="bg-dark text-white container-fluid">
      <div class="rowRow" style="display: flex; justify-content: space-between">
        <div class="headerLeft" style="align-self: center">
          <h2 @click="goHome()">勤怠アプリ</h2>
        </div>
        <div class="login" style="display: flex; justify-content: space-between">
          <div style="align-self: center">
            <ul class="userGroupName" v-for="group in groups.filter(e => e.id == auth.group_id)" style="margin: auto; justify-content: center">
              グループ名：{{ group.name }}
            </ul>

            <ul class="loginSelected" v-if="auth.length === 0">
              <router-link id="header-nav__register" to="/register" class="btn userLoginSelected">ユーザー登録</router-link>
              
            </ul>
            <ul class="loginSelected" v-if="auth.length === 0">
              <router-link id="header-nav__login" to="/login" class="btn userLoginSelected">ログイン</router-link>
            </UL>

            <ul class="loginSelected" v-if="auth.length !== 0">
              <li class="authName">
                  ユーザー名：{{auth.name}}
              </li>
            </ul>
          </div>
          <div style="align-self: center">
            <svg @click="toggle()" width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
            </svg>
            <drawer @close="toggle" :align="align" :closeable="true" :mask-closable="true">
              <ul class="sideMenu" v-if="open">
                <p @click="showUser()">ユーザー情報</p>
                <p @click="goResultOfAttendance()">勤務実績表</p>
                <p>タスク実績表</p>
                <p>給与計算</p>
                <p>
                  <a class="logout" id="header-nav__logout" @click="logout()">ログアウト</a>
                  <form id="logout-form" action="/logout" method="POST" style="display: none;">
                    <input type="hidden" name="_token" :value="csrf" />
                  </form>
                </p>
                <p v-if="auth.group_id === null" @click="goCreateGroup()">グループ作成</p>
                <p v-if="auth.is_admin === '1'" @click="goInviteMember()">メンバー招待</p>
                <p v-if="auth.is_admin === '1'" @click="goGroupMember()">メンバー情報</p>
                <p v-if="auth.is_admin === '1'" @click="goKindOfTasks()">タスク種類</p>
              </ul>
            </drawer>
          </div>
        </div>
      </div>
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
      groupEX: 'YJC',
      isActive: false,
      username: '浅田　剛明',
      open: false,
      align: 'right',
      groups: [],
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
  mounted() {
    axios.get('/groups').then(response => this.groups = response.data) 
   },
  methods: {
    goHome() {
      this.$router.push("/");
    },
    goResultOfAttendance() {
      this.$router.push("/resultOfAttendance");
      this.open = !this.open
    },
    showUser() {
      this.$router.push("/showUser");
      this.open = !this.open
    },
    goCreateGroup() {
      this.$router.push("/group");
      this.open = !this.open
    },
    goInviteMember() {
      this.$router.push("/inviteMember");
      this.open = !this.open
    },
    goGroupMember() {
      this.$router.push("/groupMember");
      this.open = !this.open
    },
    goKindOfTasks() {
      this.$router.push("/kindOfTasks");
      this.open = !this.open
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