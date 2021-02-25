import request from '@/utils/request'
// import store from '../../store'
// let token_obj = { token: store.getters.token } // 全部带入token参数,退出重新登录之后cookie中的token还是之前的，因为这儿不能保证token是最新的，登录请求是异步的，只能获取store中的token
// 以上娶不到最新token，方法无效
export function get_pageData(data) { //页面数据
	return request({
		url: '/admin/user/index',
		method: 'post',
		data
	})
}
export function export_excelData(data) { //页面数据
	return request({
		url: '/admin/user/excel',
		method: 'post',
		data,
		responseType: 'blob' //控制返回的类型为blob类型
	})
}
export function user_edit_Info(data) { //页面数据
	return request({
		url: '/admin/user/info',
		method: 'post',
		data,
	})
}
export function user_edit(data) { //页面数据
	return request({
		url: '/admin/user/edit',
		method: 'post',
		data,
	})
}