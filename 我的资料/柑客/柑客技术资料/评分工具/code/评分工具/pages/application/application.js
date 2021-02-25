// pages/detail/detail.js
const app = getApp()

import api from '../../api/api'
import {
  apply,projectDetail,judgesStatus
} from '../../api/api_conf' // 链接注意填写正确 


Page({

  /**
   * 页面的初始数据
   */
  data: {
    proId: 28, // 项目ID
    detailData: '', // 详细信息
    hasUserInfo: '',
    userInfo:'',
    applyType:false, // 申请按钮禁用状态
    name:'', // 真实姓名姓名
    blackType:false, // 黑层状态
    submitType:false, // 提交按钮禁用状态
    btnType:'', // 状态，-1-未申请，1-待审核，2-已通过，3-已删除
    proType:'',
    calculate_type:""
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    console.log(options);
    this.setData({
      proId:options.proId,
      proType: options.proType,
      calculate_type: options.calculate_type
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    api.get(projectDetail, {
      project_id: this.data.proId
    }).then((res) => {
      this.setData({
        detailData: res.data
      })
    }).catch((err) => {
      console.log(err);
    })
  // 获取用户信息
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        hasUserInfo: true
      })
    } else if (this.data.canIUse) {

      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.setData({
            userInfo: res.userInfo,
            hasUserInfo: true
          })
        }
      })
    }
    // openid异步还没得到。轮询调用
    var timeOut = setInterval(()=>{
      api.post(judgesStatus,{
        project_id:this.data.proId,
        openid:app.globalData.openid
      }).then(res=>{
        clearInterval(timeOut);
        console.log(res);
        // 如果已经通过则直接跳到详情页 带上ID 还有项目状态
        if (res.data.status == 2 ){
          wx.redirectTo({
            url: "../../pages/submitPage/submitPage?proId=" + this.data.proId + "&&calculate_type=" + this.data.calculate_type
          })
        }
        this.setData({
          btnType:res.data.status
        })
      }).catch(err=>{
        console.log(err);
      })
    }, 1000);
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
    
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
    return {
      title: `邀请您加入"${this.data.detailData.item}" 的打分评委`,
      // path: 'xxx?id=' + this.data.id,
      success: function (res) {
        
      }
    };
  },
  apply() {
    this.setData({
      applyType:!this.data.applyType,
      blackType:!this.data.blackType
    })
  },
  // 真实姓名input判断
  nameChange(e){
    if(e.detail.value.trim()!==''&&e.detail.value.trim()!==undefined){
      this.setData({
        submitType:true,
        name:e.detail.value.trim()
      })
    }else{
      this.setData({
        submitType:false,
        name:''
      })
    }
  },
  // 提交审核
  submitFun(){
  if (app.globalData.userInfo) {
      api.post(apply,{
        openid:app.globalData.openid,
        avatar_url:app.globalData.userInfo.avatarUrl,
        name:this.data.name,
        project_id:this.data.detailData.id
      }).then(res=>{
        console.log(res);
          wx.showToast({
            title: '提交成功！',
            duration: 1500
          })
      }).catch(err=>{
        wx.showToast({
          title: '请勿重复提交',
          duration: 1500
        })
      })
    }
    this.setData({
      applyType:true,
      blackType:false
    })
  },
  getUserInfo: function (e) {
    app.globalData.userInfo = e.detail.userInfo
    this.setData({
      userInfo: e.detail.userInfo,
      hasUserInfo: true
    })
    // 注册
    if (!app.globalData.registeredType) {
      api.post(register, {
        openid: app.globalData.openid,
        nick_name: e.detail.userInfo.nickName,
        avatar_url: e.detail.userInfo.avatarUrl,
        province: e.detail.userInfo.province,
        city: e.detail.userInfo.city,
        country: e.detail.userInfo.country,
        sex: e.detail.userInfo.gender
      }).then((result) => {
        console.log(result);
      }).catch((err) => {
        console.log(err);
      });
    }

  },
  // 关闭黑色框按钮 
  closeBlack(){
    this.setData({
      applyType:!this.data.applyType,
      blackType:!this.data.blackType
    })
  }
})