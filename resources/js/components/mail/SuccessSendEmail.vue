<template>
  <div>
    <p>招待メールが送信されました！</p>
    <button @click.stop.prevent="goHome()">ホームへ戻る</button>
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