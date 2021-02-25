// pages/detail/detail.js
const app = getApp()

import api from '../../api/api'
import {
  projectDetail,
  projectDone
} from '../../api/api_conf' // 链接注意填写正确 


Page({

  /**
   * 页面的初始数据
   */
  data: {
    proId: '',
    detailData: '',
    popType: false,
    proType: '',
    calculate_type: ""
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    console.log(options);
    this.setData({
      proId: options.proId,
      proType: options.proType,
      calculate_type: options.calculate_type
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {
    api.get(projectDetail, {
      project_id: this.data.proId
    }).then((res) => {
      this.setData({
        detailData: res.data
      })
    }).catch((err) => {
      console.log(err);
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
      title: `邀请您加入"${this.data.detailData.item}" 的打分评委`,
      path: `pages/application/application?proId=${this.data.proId}&&proType=${this.data.proType}&&calculate_type={this.data.calculate_type}`,
      success: function(res) {
        console.log(this.data.proType)
      }
    };
  },
  swicthPop() {
    let Type = !this.data.popType;
    console.log(1);
    this.setData({
      popType: Type
    })
  },
  manage() {
    wx.navigateTo({
      url: "../../pages/management/management?proId=" + this.data.proId
    })
  },
  entryRating() {
    wx.redirectTo({
      url: "../../pages/submitPage/submitPage?proId=" + this.data.proId + "&&calculate_type=" + this.data.calculate_type
    })
  },
  ranking() {
    wx.navigateTo({
      url: "../../pages/ranking/ranking?proId=" + this.data.proId
    })
  }
})