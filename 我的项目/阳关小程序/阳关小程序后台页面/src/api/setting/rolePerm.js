import request from '@/utils/request'

export function authorList(data) {  //角色列表
  return request({
    url: '/admin/System/authorList',
    method: 'post',
    data
  })
}
export function authorAdd(data) {  //角色增加
  return request({
    url: '/admin/System/authorAdd',
    method: 'post',
    data
  })
}
export function authorInfo(data) {  //角色信息
  return request({
    url: '/admin/System/authorInfo',
    method: 'post',
    data
  })
}
export function authorEdit(data) {  //修改角色
  return request({
    url: '/admin/System/authorEdit',
    method: 'post',
    data
  })
}
export function authorDel(data) {  //删除角色
  return request({
    url: '/admin/System/authorDel',
    method: 'post',
    data
  })
}