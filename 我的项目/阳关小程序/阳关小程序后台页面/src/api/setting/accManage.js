import request from '@/utils/request'

export function categoryList(data) {  //账号列表
  return request({
    url: '/admin/System/categoryList',
    method: 'post',
    data
  })
}
export function roleInfo(data) {  //角色类型
  return request({
    url: '/admin/System/roleInfo',
    method: 'post',
    data
  })
}
export function categoryAdd(data) {  //新增账号
  return request({
    url: '/admin/System/categoryAdd',
    method: 'post',
    data
  })
}
export function categoryInfo(data) {  //要修改的账号信息
  return request({
    url: '/admin/System/categoryInfo',
    method: 'post',
    data
  })
}
export function categoryEdit(data) {  //修改账号
  return request({
    url: '/admin/System/categoryEdit',
    method: 'post',
    data
  })
}
export function categoryDel(data) {  //修改账号
  return request({
    url: '/admin/System/categoryDel',
    method: 'post',
    data
  })
}