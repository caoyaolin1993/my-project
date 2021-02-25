<template>
  <div class="dashboard-container">
	  <div class="dashboard-logo"></div>
    <div class="dashboard-text">欢迎进入阳光心情小程序管理后台</div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Dashboard',
  computed: {
    ...mapGetters([
      'name'
    ])
  }
}
</script>

<style lang="scss" scoped>
.dashboard {
  &-container {
    margin: 160px;
  }
  &-logo {
    display: block;
    width: 200px;
    height: 184px;
    background: url("../../assets/imgs/logo.png") no-repeat;
    background-size: 100%;
    text-indent: -9999px;
    margin: 0 auto 40px;
  }
  &-text {
    font-size: 46px;
    text-align: center;
    line-height: 54px;
  }
}
</style>
