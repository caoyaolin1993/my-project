// pages/ranking/ranking.js
const app = getApp()

import api from '../../api/api'
import {
  playerRanking
} from '../../api/api_conf'

Page({

  /**
   * 页面的初始数据
   */
  data: {
    proId: '',
    rankingData: '',
    scrollData: ''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    this.setData({
      proId: options.proId
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {
    api.get(playerRanking, {
      project_id: this.data.proId
    }).then(res => {
      this.setData({
        rankingData: res.data,
        scrollData: res.data.ranking_list.splice(3, res.data.ranking_list.length)
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
    return {
      title: `邀请您查看"${this.data.rankingData.item}" 的评分排行`,
      // path: 'xxx?id=' + this.data.id,
      success: function (res) {

      }
    };
  },
  joinDetail(e) {
    let id = e.currentTarget.dataset.id;
    wx.navigateTo({
      url: `../../pages/rankDetail/rankDetail?itemId=${id}&&proName=${this.data.rankingData.item}&&name_show=${this.data.rankingData.name_show}&&calculate_type=${this.data.rankingData.calculate_type}`
    })
  }
})