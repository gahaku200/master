<template>
  <div>
    <p>グループ参加招待が無効になっています。</p>
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