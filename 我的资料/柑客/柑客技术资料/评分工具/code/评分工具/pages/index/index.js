// pages/index/index.js
const app = getApp()

import api from '../../api/api'
import {
  register,
  projectList,
  delProject,
  judgeProjectList
} from '../../api/api_conf' // 链接注意填写正确 


Page({

  /**
   * 页面的初始数据
   */
  data: {
    swicthCode: 1, //导航切换值 1 == 发起  2 == 参与
    userInfo: '',
    hasUserInfo: false,
    creatData: '',
    passData: ''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    
      console.log(app.globalData.userInfo)
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {
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

    // 因异步获取openID 所以轮询获取openid 来调用接口
    var timeOut = setInterval(() => {
      if (app.globalData.openid) {
        this.getCreatList();
        this.passList();
        clearInterval(timeOut);
      }
    }, 1000)

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

  },
























testSubmit:function(e){
    var self= this;
    let openid = 'ohmep5ej2lO7ivCt67vNZwNrFzt0'
    let _access_token = '5_E1pZJQzTC-lC0r-JJz9wVAZv5Zv22CNtmV_7C1T0sqC9TV7mGE4FTmDX2B0PVM4LaGtaTfXwzfJLnD7fDKTg8DOICJNkKBQgn_Ot2zYmBJyY1g1VXoBNdtwUE0QaP8_9tWlbR-Zq7L1OyrrPKCIjAEAOGM';
    let url='https://score.gkgaming.cn/index.php/score/Index/pushTemplateMsg'

    // let _jsonData = {
    //   access_token: _access_token,
    //   touser: openid,
    //   template_id: '_CfGS7SqVyNPg9Op8OXzMp6aOl7X9rCkrRfiMcccms8',
    //   form_id: e.detail.formId,
    //   page: "pages/index/index",
    //   data: {
    //     "keyword1": { "value": "测试数据一", "color": "#173177" },
    //     "keyword2": { "value": "测试数据二", "color": "#173177" },
    //     "keyword3": { "value": "测试数据三", "color": "#173177" },
    //     "keyword4": { "value": "测试数据四", "color": "#173177" },
    //   }
    // }
    console.log('form_id='+e.detail.formId)
    wx.request({
        url: url,
        data: {
          form_id:e.detail.formId
        },
        method: 'GET',
        success: function (res) {
          console.log(res.data)
        },
        fail: function (err) {
          // console.log('request fail ', err);
        },
        complete: function (res) {
          // console.log("request completed!");
        }

 })

},










  // 获取创建所有列表
  getCreatList() {
    console.log(app.globalData.openid)
    api.get(projectList, {
      openid: app.globalData.openid
    }).then(res => {
      this.setData({
        creatData: res.data
      })
    }).catch(err => {
      console.log(err);
    })
  },
  // 获取我参与的列表
  passList() {
    var Arr = []; // 转接去重数据
    api.get(judgeProjectList, {
      openid: app.globalData.openid
    }).then(res => {
      var result = [];
      for (var i = 0; i < res.data.length; i++) {
        var obj = res.data[i];
        var num = obj.id;
        var flag = false;
        for (var j = 0; j < this.data.creatData.length; j++) {
          var aj = this.data.creatData[j];
          var n = aj.id;
          if (n == num) {
            flag = true;
            break;
          }
        }
        if (!flag) {
          result.push(obj);
        }
      }
      this.setData({
        passData: result
      })
    }).catch(err => {
      console.log(err);
    })
  },
  // 我的发起切换
  swicthLeft() {
    this.setData({
      swicthCode: 1
    })
    wx.setNavigationBarTitle({
      title: '我的发起'
    })
  },
  // 我的参与切换
  swicthRight() {
    this.setData({
      swicthCode: 2
    })
    this.passList();
    wx.setNavigationBarTitle({
      title: '我的参与'
    })

  },
  // 获取用户数据
  getUserInfo: function(e) {
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
  // 进入详情页
  creat() {
    if (!app.globalData.userInfo){
      wx.showToast({
        title: '该用户没有授权',
        icon: 'none',
        duration: 3000
      })
      return
    }
    wx.navigateTo({
      url: "../../pages/creation/creation"
    })
    
 
  },
  // 删除
  delProject(e) {
    let _this = this;
    wx.showModal({
      title: '提示',
      content: '是否确定删除该项目',
      success(res) {
        if (res.confirm) {
          api.post(delProject, {
            openid: app.globalData.openid,
            project_id: e.currentTarget.dataset.proid
          }).then((res) => {
            console.log(res);
            _this.getCreatList();
          }).catch((err) => {
            console.log(err);
          })
        } else if (res.cancel) {
          console.log('用户点击取消')
        }
      }
    })
  },
  // 进入详情页
  joinDetail(e) {
    let id = e.currentTarget.dataset.index;
    let _data = this.data.creatData;
    if (_data[id].project_status == 1) {
      wx.navigateTo({
        url: "../../pages/ranking/ranking?proId=" + _data[id].id + "&&proType=" + _data[id].project_status
      })
    } else {
      wx.navigateTo({
        url: "../../pages/detail/detail?proId=" + _data[id].id + "&&proType=" + _data[id].project_status
      })
    }
  },
  joinManage(e) {
    let id = e.currentTarget.dataset.index;
    let _data = this.data.passData
    if (_data[id].project_status == 1) {
      wx.navigateTo({
        url: "../../pages/ranking/ranking?proId=" + _data[id].id + "&&proType=" + _data[id].project_status
      })
    } else {
      wx.navigateTo({
        url: "../../pages/submitPage/submitPage?proId=" + _data[id].id + "&&proType=" + _data[id].project_status + "&&calculate_type=" + _data[id].calculate_type
      })
    }
  }
})