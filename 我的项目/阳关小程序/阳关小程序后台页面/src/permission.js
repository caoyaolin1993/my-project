import router from './router'
import store from './store'
import { Message } from 'element-ui'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import { getToken, getCookie, setCookie } from '@/utils/auth' // get token from cookie
import getPageTitle from '@/utils/get-page-title'

NProgress.configure({ showSpinner: false }) // NProgress Configuration

// permission judge function
function hasPermission(roles, permissionRoles) {
	if (!permissionRoles) return true
	return roles.some(role => permissionRoles.indexOf(role) >= 0)
}

const whiteList = ['/login'] // no redirect whitelist

router.beforeEach(async (to, from, next) => {
	// start progress bar
	NProgress.start()

	// set page title
	document.title = getPageTitle(to.meta.title)

	// determine whether the user has logged in
	const hasToken = getToken()

	if (hasToken) { // 网页刷新之后的操作  如果有token, 那么直接登录进入主页
		if (to.path === '/login') {
			// if is logged in, redirect to the home page 
			next({ path: '/' })
			NProgress.done()
		} else {
			//   const hasGetUserInfo = store.getters.roles.length === 0
			const hasGetUserInfo = store.getters.roles.length === 0
			console.log('需要重新获取用户信息:', hasGetUserInfo)
			if (hasGetUserInfo) { // 判断当前用户是否已拉取完user_info信息
				try {
					// get user info

					const res = await store.dispatch('user/getUserInfo') // 拉取user_info
					const roles = res.data //  note: roles must be a array! such as: ['editor','develop']
					if(res.code != 200){
						await store.dispatch('user/resetToken')
						return false
					}
					store.dispatch('GenerateRoutes', {
						roles
					}).then(() => { // 根据roles权限生成可访问的路由表
						router.addRoutes(store.getters.addRouters) // 动态添加可访问路由表
						// console.log("store.getters.addRouters", store.getters.addRouters)
						next({
							...to,
							replace: true
						}) // hack方法 确保addRoutes已完成 ,set the replace: true so the navigation will not leave a history record
					})

					// 重新赋值cookie给store 的state.token, 然后发请求的token也跟着变成最新的
					store.commit('user/SET_NAME', getCookie('admin_n'))  //重新存入姓名
					next()
				} catch (error) {
					// remove token and go to login page to re-login
					await store.dispatch('user/resetToken')
					Message.error(error || 'Has Error')
					next(`/login?redirect=${to.path}`)
					NProgress.done()
				}
			} else {
				next()
			}
		}
	} else {
		/* has no token*/

		if (whiteList.indexOf(to.path) !== -1) {
			// in the free login whitelist, go directly
			next()
		} else {
			// other pages that do not have permission to access are redirected to the login page.
			next(`/login?redirect=${to.path}`)
			NProgress.done()
		}
	}
})

router.afterEach(() => {
	// finish progress bar
	NProgress.done()
})
