//app.js

import api from './api/api'
import {
  getOpenid,
  getUserInfo
} from './api/api_conf' // 链接注意填写正确 


App({
  onLaunch: function () {
    // 展示本地存储能力
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)
    // 获取code 换 openid
    wx.login({
      success: code => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
        api.post(getOpenid, {
          code: code.code
        }).then(res => {
          // 获取用户信息
          api.get(getUserInfo, {
            openid: res.openid
          }).then(ress => {
            console.log(ress);
          // 获取用户信息 如果后端数据有该用户注册信息，则为true 
            if (ress.data && ress.data.avatar_url){
              this.globalData.registeredType = true;
            }
          }).catch(err => {
            console.log(err);
          })

          this.globalData.openid = res.openid
        }).catch(err => {
          console.log(err)
        })
      }
    })
  },
  globalData: {
    userInfo: null,
    registeredType: false, // 是否注册
    version: '1.0.0',
    openid: null
  }
})