<template>
  <div>
    <div style="text-align: center;" class="createGroupTitle">
      <h3 style="padding: 10px;">グループ作成</h3>
    </div>
    
    <ValidationObserver　class="form-group" ref="observer" action="/group" id="group" method="post" tag="form" v-slot="{ invalid }">
      <input type="hidden" name="_token" :value="csrf" />
      
      <div class="col-lg-3"></div>
      <div class="col-lg-6">
        <div>
          <validation-provider name="グループ名" rules="required|max:100" v-slot="{ errors }">
            <div class="alert alert-danger" v-show="errors[0]">{{ errors[0] }}</div>
            <div class="input-group input-group-lg">
              <input v-model="name" name="name" type="text" class="form-control" placeholder="グループ名 or 会社名" aria-label="グループ名 or 会社名" aria-describedby="inputGroup-sizing-default" />
              <button type="button" class="btn btn-primary" @click="createG()">作成</button>
            </div>
          </validation-provider>
        </div>
      </div>
      <div class="col-lg-3"></div>
    </ValidationObserver>
    <div style="text-align:center; padding-top: 20px;" class="homeButton">
      <button @click.stop.prevent="goHome()" type="button" class="btn btn-outline-info">ホームへ戻る</button>
    </div>
  </div>
</template>

<script>
import { ValidationProvider, ValidationObserver, extend } from "vee-validate";
import { required, max } from "vee-validate/dist/rules"; 
extend("required", {
  ...required,
  message: "{_field_}は必須です"
});
extend("max", {
  ...max,
  message: "{_field_}は最大でも{length}文字までです"
});

export default {
  components: {
    ValidationProvider,
    ValidationObserver
  },
  data() {
    return {
      name: "",
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
    };
  },
  mounted() {
    var group_id = document.querySelector("meta[name='group-id']").getAttribute('content');
    if (group_id > 0) {
      this.goHome();
    }
   },
  methods: {
    goHome() {
      this.$router.push("/");
    },
    async createG() {
      const isValid = await this.$refs.observer.validate();
      if (isValid) {
        document.querySelector("#group").submit();
      }
    }
  },
};
</script>