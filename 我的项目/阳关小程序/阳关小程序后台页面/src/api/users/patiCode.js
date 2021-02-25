import request from '@/utils/request'
// import store from '../../store'
// let token_obj = {token: store.getters.token} // 全部带入token参数,退出重新登录之后cookie中的token还是之前的，因为这儿不能保证token是最新的，登录请求是异步的，只能获取store中的token
// 以上娶不到最新token，方法无效
export function get_pageData(data) {//
	return request({
	  url: '/admin/Account/index',
	  method: 'post',
	  data
	})
}
  
export function pageData_add(data) {//
	return request({
	  url: '/admin/Account/add',
	  method: 'post',
	  data
	})
}
  
export function pageData_del(data) {//
	return request({
	  url: '/admin/Account/del',
	  method: 'post',
	  data
	})
}
  
export function pageData_edit_info(data) {//
	return request({
	  url: '/admin/Account/info',
	  method: 'post',
	  data
	})
}
  
export function pageData_edit(data) {//
	return request({
	  url: '/admin/Account/edit',
	  method: 'post',
	  data
	})
}

export function create_inviteCode(data) {//
	return request({
	  url: '/admin/Account/inviteCode',
	  method: 'post',
	  data
	})
}
export function import_excelData(data) {//
	return request({
	  url: '/admin/Account/importExecl',
	  method: 'post',
	  data
	})
}
export function export_excelData(data) {//
	return request({
	  url: '/admin/Account/excel',
	  method: 'post',
	  data,
	  responseType: 'blob' //控制返回的类型为blob类型
	})
}
