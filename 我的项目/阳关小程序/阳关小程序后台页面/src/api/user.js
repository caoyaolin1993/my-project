import request from '@/utils/request'

export function login(data) {  //用户登录
  return request({
    url: '/admin/Login/login',
    method: 'post',
    data
  })
}

export function getInfo(data) { //获取用户信息
  return request({
    url: '/admin/System/levelInfo',
	method: 'post',
	data
  })
}
// export function updatePassword(data) { //修改密码
//   return request({
//     url: '/admin/Index/update_word',
//     method: 'post',
//     data
//   })
// }

export function logout() {  //登出
  return request({
    url: '/admin/login/logout',
    method: 'post'
  })
}
