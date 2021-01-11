<template>
  <div>
    <div style="text-align: center;" class="showUserTitle">
      <h3 style="padding: 10px;">ユーザー情報</h3>
    </div>

    <div class="row">
      <div class="col-lg-3"></div>
      <div class="col-lg-6">
        <div class="form-group has-error" v-if="error !== ''">
          <div class="alert alert-danger text-center">
            <div>{{error}}</div>
          </div>
        </div>
        <ul class="list-group">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <td>ユーザー名：{{ userName }}</td>
            <td>
              <button v-if="!isEditName" @click="nameEdit()" type="button" class="btn btn-outline-primary">編集する</button>
              <button v-if="isEditName" @click="nameEdit()" type="button" class="btn btn-outline-primary">　×　</button>
            </td>
          </li>
          <li v-if="isEditName" class="list-group-item d-flex justify-content-between align-items-center">
            <td>
              <input v-model="editName" name="editName" type="editName" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" />
            </td>
            <td>
              <button @click="postEditName()" type="button" class="btn btn-outline-primary">更新する</button>
            </td>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <td>ユーザーアドレス：{{ userEmail }}</td>
            <td>
              <button v-if="!isEditEmail" @click="emailEdit()" type="button" class="btn btn-outline-primary">編集する</button>
              <button v-if="isEditEmail" @click="emailEdit()" type="button" class="btn btn-outline-primary">　×　</button>
            </td>
          </li>
          <li v-if="isEditEmail" class="list-group-item d-flex justify-content-between align-items-center">
            <td>
              <input v-model="editEmail" name="editEmail" type="editEmail" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" />
            </td>
            <td>
              <button @click="postEditEmail()" type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#exampleModal">更新する</button>
            </td>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <td>パスワード</td>
            <td>
              <button v-if="!isEditPassword" @click="passwordEdit()" type="button" class="btn btn-outline-primary">編集する</button>
              <button v-if="isEditPassword" @click="passwordEdit()" type="button" class="btn btn-outline-primary">　×　</button>
            </td>
          </li>
          <li v-if="isEditPassword" class="list-group-item d-flex justify-content-between align-items-center">
            <ValidationObserver class="form-horizontal" ref="observer" tag="form" @submit.prevent="register()" v-slot="{ invalid }" style="width: -webkit-fill-available">
              <input type="hidden" name="_token" :value="csrf" />

              <div class="form-group">
                <label for="oldpassword-confirm" class="col-md-4 control-label">現在のパスワード</label>

                <div class="col-md-6">
                  <validation-provider name="今のパスワード" rules="required|min:8" v-slot="{ errors }">
                    <input v-model="oldpassword" name="oldpassword" type="password" class="form-control"/>
                    <div class="alert alert-danger" v-show="errors[0]">{{ errors[0] }}</div>
                  </validation-provider>
                </div>
              </div>

              <div class="form-group">
                <label for="newpassword" class="col-md-4 control-label">新しいパスワード</label>

                <div class="col-md-6">
                  <validation-provider name="新しいパスワード" rules="required|min:8|confirmed:password_confirmation" v-slot="{ errors }">
                    <input v-model="newpassword" name="newpassword" type="password" class="form-control"/>
                    <div class="alert alert-danger" v-show="errors[0]">{{ errors[0] }}</div>
                  </validation-provider>
                </div>
              </div>

              <div class="form-group">
                <label for="newpassword-confirm" class="col-md-4 control-label">新しいパスワード再確認</label>

                <div class="col-md-6">
                  <validation-provider name="新しいパスワード再確認" rules="required|min:8" vid="password_confirmation" v-slot="{ errors }">
                    <input v-model="newpassword_confirmation" name="newpassword_confirmation" type="password" class="form-control"/>
                    <div class="alert alert-danger" v-show="errors[0]">{{ errors[0] }}</div>
                  </validation-provider>
                </div>
              </div>

              <div class="form-group-register">
                <div>
                  <button type="submit" class="btn btn-outline-primary" style="float: right">更新する</button>
                </div>
              </div>
            </ValidationObserver>
          </li>
        </ul>
      </div>
      <div class="col-lg-3"></div>
    </div>

    <div style="text-align:center; padding-top: 20px;" class="homeButton">
      <button @click.stop.prevent="goHome()" type="button" class="btn btn-outline-info">ホームへ戻る</button>
    </div>
  </div>
</template>

<script>
import { ValidationProvider, ValidationObserver, extend } from "vee-validate";
import { required, max, min, email, confirmed } from "vee-validate/dist/rules"; 
extend("required", {
  ...required,
  message: "{_field_}は必須です"
});
extend("min", {
  ...min,
  message: "{_field_}は最低でも{length}文字入力してください"
});
extend("confirmed", {
  ...confirmed,
  message: "新しい再確認パスワードと入力が一致していません"
});

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  data() {
    return {
      userId: 0,
      userName: '',
      editName: '',
      userEmail: '',
      editEmail: '',
      isEditName: false,
      isEditEmail: false,
      isEditPassword: false,
      oldpassword: '',
      newpassword: '',
      newpassword_confirmation: '',
      error: '',
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content")
    }
  },
  mounted() {
    var id = document.querySelector("meta[name='user-id']").getAttribute('content');
    axios.get('/api/getUserInfo/' + id)
      .then(res =>  {
        this.userId = res.data.id;
        this.userName = res.data.name;
        this.editName = res.data.name;
        this.userEmail = res.data.email;
        this.editEmail = res.data.email;
      }).catch( error => { console.log(error); })
   },
  methods: {
    goHome() {
      this.$router.push("/");
    },
    nameEdit() {
      if (this.isEditName == true) {
        this.isEditName = false;
      } else {
        this.isEditName = true;
      }
    },
    postEditName() {
      const data = {
      name: this.editName
      }
      var id = document.querySelector("meta[name='user-id']").getAttribute('content');
      axios.post('/api/editUserName/' + id, data)
      .then(res =>  {
        this.userName = res.data.name;
        this.editName = res.data.name;
      })
      .catch( error => { console.log(error); });
      this.isEditName = false;
    },
    emailEdit() {
      if (this.isEditEmail == true) {
        this.isEditEmail = false;
      } else {
        this.isEditEmail = true;
      }
    },
    postEditEmail() {
      const data = {
      email: this.editEmail
      }
      var id = document.querySelector("meta[name='user-id']").getAttribute('content');
      axios.post('/api/editUserEmail/' + id, data)
      .then(res =>  {
        this.userEmail = res.data.email;
        this.editEmail = res.data.email;
      })
      .catch( error => { console.log(error); });
      this.isEditEmail = false;
    },
    passwordEdit() {
      if (this.isEditPassword == true) {
        this.isEditPassword = false;
      } else {
        this.isEditPassword = true;
      }
    },
    async register() {
      const isValid = await this.$refs.observer.validate();
      if (isValid) {
        const data = {
          oldpassword: this.oldpassword,
          newpassword: this.newpassword,
          }
          var id = document.querySelector("meta[name='user-id']").getAttribute('content');
          axios.post('/api/updatePassword/' + id, data)
          .then(res =>  {
            this.error = res.data;
            if (res.data == 'パスワードが正しく変更されました') {
              this.isEditPassword = false;
            }
          })
          .catch( error => { console.log(error); });
          this.oldpassword = '';
          this.newpassword = '';
          this.newpassword_confirmation= '';
      }
    },
  }
};
</script>

