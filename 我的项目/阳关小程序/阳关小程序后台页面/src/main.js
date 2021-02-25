import Vue from 'vue'

import 'normalize.css/normalize.css' // A modern alternative to CSS resets

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
// import locale from 'element-ui/lib/locale/lang/en' // lang i18n
import '../static/tinymce4.7.5/langs/zh_CN' //富文本汉化

import '@/styles/index.scss' // global css

import App from './App'
import store from './store'
import router from './router'

import '@/icons' // 引入侧边栏svg图标，此项目可以去掉
import '@/permission' // permission control

/**
 * If you don't want to use mock-server
 * you want to use MockJs for mock api
 * you can execute: mockXHR()
 *
 * Currently MockJs will be used in the production environment,
 * please remove it before going online! ! !
 */
// 打印当前所处的环境
// console.log('当前域名:'+process.env.VUE_APP_BASE_API);
console.log('当前注入环境:'+process.env.NODE_ENV);

// set ElementUI lang to EN
Vue.use(ElementUI)
// Vue.use(ElementUI, { locale })

Vue.config.productionTip = false

Vue.prototype.$store = store;

new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})
