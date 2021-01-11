<template>
  <div>
    <div style="text-align: center;" class="theTitle">
      <h3 style="padding: 10px;">グループメンバー情報</h3>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
          <table class="table">
            <thead>
              <tr>
                <th scope="col"></th>
                <th scope="col">ユーザー名</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users">
                <td scope="row"></td>
                <td>{{ user.name }}</td>
                <td style="text-align: right;">
                  <router-link type="button" class="btn btn-outline-primary" :to="{name:'memberAttendance',params:{id: user.id, name: user.name}}">
                    勤怠詳細
                  </router-link>
                  <router-link type="button" class="btn btn-outline-primary" :to="{name:'memberTask',params:{id: user.id, name: user.name}}">
                    タスク詳細
                  </router-link>
                  <button @click="banishName(user.id, user.name)" type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#exampleModal">
                    追放する
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-lg-3"></div>
      </div>
    </div>

    <!--modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">メンバーを追放する</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            '{{ banishUser }}'さんをグループから本当に追放しますか？
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
            <button @click="banishM()" type="button" class="btn btn-primary">追放する</button>
          </div>
        </div>
      </div>
    </div>

    <div style="text-align:center; padding-top: 20px;" class="homeButton">
      <button @click.stop.prevent="goHome()" type="button" class="btn btn-outline-info">ホームへ戻る</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      banishUser: '',
      banishId: 0,
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
    }
  },
  mounted() {
    var isAdmin = document.querySelector("meta[name='is-admin']").getAttribute('content');
    if (isAdmin != '1') {
      this.goHome();
    }
    var authId = document.querySelector("meta[name='user-id']").getAttribute('content');
    var group_id = document.querySelector("meta[name='group-id']").getAttribute('content');

    axios.get('/api/getGroup/' + group_id)
      .then(res =>  {
        this.users = [];
        res.data.forEach((user) => {
          if (user.id != authId) {
            this.users.push({id: user.id, name: user.name});
          }
        })
      }).catch( error => { console.log(error); })
   },
  methods: {
    goHome() {
      this.$router.push("/");
    },
    banishName(userID, userName) {
      this.banishId = userID;
      this.banishUser = userName;
    },
    async banishM() {
      axios.post('/api/banishMember/' + this.banishId)
      .catch( error => { console.log(error); });
      this.$router.go({path: this.$router.currentRoute.path, force: true})
    },
  },
};
</script>