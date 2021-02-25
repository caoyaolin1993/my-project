const host = 'https://score.gkgaming.cn/score/'

const request = (url, options) => {
  return new Promise((resolve, reject) => {
    wx.request({
      url: `${host}${url}`,
      method: options.method,
      data: options.method === 'GET' ? options.data : JSON.stringify(options.data),
      header: {
        'Content-Type': 'application/json; charset=UTF-8',
        'x-token': 'x-token'  // 看自己是否需要
      },
      success(request) {
        if (request.data.code === 100) {
          resolve(request.data)
        } else {
          reject(request.data)
        }
      },
      fail(error) {
        reject(error.data)
      }
    })
  })
}

const get = (url, options = {}) => {
  return request(url, { method: 'GET', data: options })
}

const post = (url, options) => {
  return request(url, { method: 'POST', data: options })
}

module.exports = {
  get,
  post
}