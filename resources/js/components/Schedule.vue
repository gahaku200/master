<template>
  <div>
    <p>Schedule</p>
    <p>goodGuys!!</p>
    <button @click.stop.prevent="goHome()">戻る</button>
  </div>
</template>

<script>
export default {
  methods: {
    goHome() {
      this.$router.push("/");
    }
  }
};
</script>

