<template>
  <div>
    <p>既にグループに参加しています。</p>
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