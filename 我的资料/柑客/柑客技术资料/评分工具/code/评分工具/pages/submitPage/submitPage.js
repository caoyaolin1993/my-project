// pages/submitPage/submitPage.js
const app = getApp()

import api from '../../api/api'
import {
  scoreList,scoreSubmit
} from '../../api/api_conf' // 链接注意填写正确 


Page({

  /**
   * 页面的初始数据
   */
  data: {
    calculate_type:"",
    userInfo:'',
    proId: '', // 项目ID
    detailData: '', // 详细信息
    hasUserInfo: '',
    submitType: false, // 提交按钮禁用状态
    allSelectType:false, // 检查是否所有选项都填写完毕
    btnDisabel:false // 禁止重复提交
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      proId:options.proId,
      calculate_type: options.calculate_type
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    // 列表获取
    api.get(scoreList, {
      project_id: this.data.proId,
      judge_openid: app.globalData.openid
    }).then((res) => {
      if(res.data.is_judged != 1){
        res.data.score_list.map(val => {
          val.judge_point = '';
        })
      }
      this.setData({
        detailData: res.data
      })
    }).catch((err) => {
      console.log(err);
    })


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
          hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.setData({
            hasUserInfo: true
          })
        }
      })
    }
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


  submitFun() {
    if (app.globalData.userInfo) {

    }
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
  // input 失去焦点事件
  checkNum(e) {
    let _data = this.data.detailData;
    let id = e.currentTarget.dataset.id;
    let val = e.detail.value

    if(Number(val) > Number(_data.high_point) ){
      _data.score_list[id].judge_point = _data.high_point
    }else if (Number(val) <= 0){
      console.log(2);
      _data.score_list[id].judge_point = ''
    }
    else{
      console.log(3);
      _data.score_list[id].judge_point = Number(val)
    }
     // 判断有没有空值
    let type = _data.score_list.every(val=>{
     return val.judge_point !== ''
    })
    this.setData({
      detailData: _data,
      allSelectType:type
    })
  },
  // 弃票
  giveUp(e) {
    let _data = this.data.detailData;
    let id = e.currentTarget.dataset.id;
    _data.score_list[id].judge_point == -1?
    _data.score_list[id].judge_point = '':
    _data.score_list[id].judge_point = -1
    // 判断有没有空值
    let type = _data.score_list.every(val=>{
      return val.judge_point !== ''
     })
    this.setData({
      detailData: _data,
      allSelectType:type
    })
  },
  Submit(){
    let _data = this.data.detailData;
    _data.score_list.map(val=>{
      val.judge_score = val.judge_point
    })
    console.log(_data);
    api.post(scoreSubmit,{
      judge_data:_data.score_list,
      project_id:this.data.proId,
      judge_openid:app.globalData.openid,
      judge_data:JSON.stringify(_data.score_list)
    }).then(res=>{

    }).catch(err=>{
      console.log(err);
    })
    this.setData({
      btnDisabel:true
    })
  }
})