import request from '@/utils/request'

export function changePsw(data) {  //账号列表
  return request({
    url: '/admin/System/changePsw',
    method: 'post',
    data
  })
}