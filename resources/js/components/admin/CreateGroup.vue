<template>
  <div>
    <p>CreateGroup</p>
    <p>hey!!createGroup!!</p>
    
    <ValidationObserver　class="form-group" ref="observer" action="/group" id="group" method="post" tag="form" v-slot="{ invalid }">
      <input type="hidden" name="_token" :value="csrf" />
      <div class="input-group mb-3">
        <span class="input-group-text" id="inputGroup-sizing-default">グループ or 会社名</span>
        <validation-provider name="グループ名" rules="required|max:100" v-slot="{ errors }">
          <input v-model="name" name="name" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" />
          <div class="alert alert-danger" v-show="errors[0]">{{ errors[0] }}</div>
        </validation-provider>
      </div>
      <button type="button" class="btn btn-primary" @click="createG()">作成</button>
    </ValidationObserver>

    <button @click.stop.prevent="goHome()">戻る</button>
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