<template>
  <div>
    {{users.name}}
    <p>InviteMember</p>
    <p>Hey!!InviteMember!!</p>
    
    <form　class="form-group" ref="observer" action="/invite" id="inviteMember" method="post" tag="form">
      <input type="hidden" name="_token" :value="csrf" />
      <div class="input-group mb-3">
        <span class="input-group-text" id="inputGroup-sizing-default">招待するユーザーのメールアドレスを入力</span>
        <div name="メンバーアドレス">
          <input v-model="memberEmail" name="memberEmail" type="email" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" />
        </div>
      </div>
      <button v-if="!button" type="button" class="btn btn-primary" disabled>招待</button>
      <button v-if="button" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">招待</button>

      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">グループに招待</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              入力したメールアドレスに招待メールを送りますか？
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
              <button type="button" class="btn btn-primary" @click="inviteM()">メール送信</button>
            </div>
          </div>
        </div>
      </div>
    </form>
    <div v-model="isOKmessage">
      <p v-if="isOK">グループに招待出来ます</p>
      <p v-if="alreadyG">既にグループに所属しています</p>
      <p v-if="!isOK && memberEmail.length > 0 && !alreadyG">現在入力しているメールアドレスは登録しているユーザーと一致しません</p>
    </div>
    <button @click.stop.prevent="goHome()">戻る</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      memberEmail: '',
      button: false,
      isOK: false,
      alreadyG: false,
      users: [],
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
    };
  },
  mounted() {
    axios.get('/users').then(response => this.users = response.data) 
   },
  computed: {
    isOKmessage() {
      var targets = this.users.filter(e => e.email == this.memberEmail);
      var target = targets.filter(e => e.group_id == null);

      if(target.length > 0) {
        this.button = true;
        this.isOK = true;
        this.alreadyG = false; 
      } else if(targets.length > 0) {
        this.button = false;
        this.isOK = false;
        this.alreadyG = true;
      } else {
        this.button = false;
        this.isOK = false;
        this.alreadyG = false;
      }
    }
  },
  methods: {
    goHome() {
      this.$router.push("/");
    },
    async inviteM() {
        document.querySelector("#inviteMember").submit();
    },
  },
};
</script>