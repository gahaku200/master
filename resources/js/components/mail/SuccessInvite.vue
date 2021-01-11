<template>
  <div>
    <div style="text-align: center;" class="theTitle">
      <h3 style="padding: 10px;">グループに参加しました！</h3>
    </div>
    <div style="text-align:center; padding-top: 20px;" class="homeButton">
      <button @click.stop.prevent="goHome()" type="button" class="btn btn-outline-info">ホームへ戻る</button>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    goHome() {
      this.$router.push("/");
    }
  },
};
</script>