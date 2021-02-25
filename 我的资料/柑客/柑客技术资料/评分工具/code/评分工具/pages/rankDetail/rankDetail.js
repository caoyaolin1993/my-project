// pages/rankDetail/rankDetail.js

const app = getApp()

import api from '../../api/api'
import {
  rankDetail
} from '../../api/api_conf'


Page({

  /**
   * 页面的初始数据
   */
  data: {
    itemId: '',
    data: '',
    proName: "",
    name_show:'',
    nmImg:'http://score.gkgaming.cn/public/static/img/mm.png',
    calculate_type:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    this.setData({
      itemId: options.itemId,
      proName: options.proName,
      name_show:options.name_show,
      calculate_type: options.calculate_type
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {
    api.get(rankDetail, {
      people_id: this.data.itemId
    }).then(res => {
      console.log(res)
      this.setData({
        data: res.data
      })
    }).catch(err => {
      console.log(err)
    })
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {

  }
})