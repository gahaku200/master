<template>
  <div>
    <p>グループに参加しました！</p>
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