<template>
  <div>
    <p>GroupMember</p>
    <ul class="list-group">
      <li v-for="user in users" class="list-group-item d-flex justify-content-between align-items-center">
        {{ user.name }}
        <button @click="banishName(user.id, user.name)" type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#exampleModal">
          追放する
        </button>
      </li>
    </ul>

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

    <p>goodGuys!!</p>
    <button @click.stop.prevent="goHome()">戻る</button>
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