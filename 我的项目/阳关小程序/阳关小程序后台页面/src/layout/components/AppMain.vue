<template>
  <section class="app-main">
    <transition name="fade-transform" mode="out-in">
      <router-view />
    </transition>
  </section>
</template>

<script>
export default {
  name: 'AppMain',
  computed: {
	//   <router-view :key="key"/>  
	// 不同路由使用相同组件时，切换这两个路由并不会触发组件的created或者mounted钩子。可以在router-view上加上唯一一个key来解决，缺点就是组件中有嵌套路由切换时，总是会重载这个组件。不能实时获取组件中最新的data数据。
	// https://panjiachen.gitee.io/vue-element-admin-site/zh/guide/essentials/layout.html#app-main
	key() {
      return this.$route.path
    }
  }
}
</script>

<style scoped>
.app-main {
  /*50 = navbar  */
  min-height: calc(100vh - 50px);
  width: 100%;
  position: relative;
  overflow: hidden;
}
.fixed-header+.app-main {
  padding-top: 50px;
}
</style>

<style lang="scss">
// fix css style bug in open el-dialog
.el-popup-parent--hidden {
  .fixed-header {
    padding-right: 15px;
  }
}
</style>
