// pages/detail/detail.js
const app = getApp()

import api from '../../api/api'
import {
  projectDetail,
  projectDone,
  judgesList,
  edit,
  noJudgesList,
  doneJudgesList
} from '../../api/api_conf' // 链接注意填写正确 


Page({

  /**
   * 页面的初始数据
   */
  data: {
    proId: '', // 项目ID
    detailData: '',
    proType: '', // 项目状态
    judgesListData: '',
    editType: true, // 编辑框隐藏
    menuType: false, // 导航栏隐藏 
    editMenu: [{
      title: '修改主题'
    }, {
      title: '编辑选手'
    }],
    editActive: 0,
    editTitle: "", // 评分主题
    subtract_point: 2, // 是否去掉最高最低分:1去掉，2不去。不传默认1
    peopleList: [], // 评审选手列表
    peopleVal: '', // 添加评审输入框
    editDisable: false,
    editBtnType: true,
    userInfo: '',
    badgeData: ''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    console.log(options);
    this.setData({
      proId: options.proId,
      proType: options.proType
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {
    console.log(app.globalData.userInfo)
    this.setData({
      userInfo: app.globalData.userInfo
    })
    this.getDetail()
    this.get_noJudgesList()
    // 已提交评分列表 ， 判断是否有评委已评分。禁用项目修改状态
    api.get(doneJudgesList, {
      project_id: this.data.proId
    }).then(res => {
      console.log(res)
      res.data.length == 0 ?
        this.setData({
          editDisable: true
        }) :
        this.setData({
          editDisable: false
        })

    }).catch(err => [
      console.log(err)
    ])
    // 获取待审核评委列表  用处：徽章提醒
    api.get(judgesList, {
      project_id: this.data.proId
    }).then(res => {
      this.setData({
        badgeData: res.data
      })
    }).catch(err => [
      console.log(err)
    ])
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
      path: `pages/application/application?proId=${this.data.proId}&&proType=${this.data.proType}&&calculate_type=${this.data.detailData.calculate_type}`,
      imageUrl: '../../image/share.png',
      success: function(res) {
        console.log(this.data.proType)
      }
    };
  },

  // 初始化获取数据
  // 获取项目详情
  getDetail() {
    let editArr = []
    api.get(projectDetail, {
      project_id: this.data.proId
    }).then((res) => {
      res.data.player_list.map(val => {
        editArr.push(val.name)
      })
      this.setData({
        detailData: res.data,
        editTitle: res.data.item,
        peopleList: editArr,
        subtract_point: res.data.subtract_point
      })
    }).catch((err) => {
      console.log(err);
    })
  },
  // 获取待提交评委列表
  get_noJudgesList() {
    api.get(noJudgesList, {
      project_id: this.data.proId
    }).then((res) => {
      this.setData({
        judgesListData: res.data
      })
    }).catch((err) => {
      console.log(err);
    })
  },
  // 初始化获取数据 End
  // 关闭编辑框
  closePop() {
    this.setData({

      editType: true,
    })
  },

  // 进入评委管理页
  manage() {
    wx.navigateTo({
      url: "../../pages/management/management?proId=" + this.data.proId
    })
  },
  //  进入打分页
  entryRating() {
    wx.redirectTo({
      url: "../../pages/submitPage/submitPage?proId=" + this.data.proId + "&&calculate_type=" + this.data.detailData.calculate_type
    })
  },
  // 结算
  done() {
    // 确认框
    let self = this;
    wx.showModal({
      title: '',
      content: '是否结算项目',
      success: function (config) {
        if (config.confirm) {
          console.log('用户点击确定')
          api.post(projectDone, {
            project_id: self.data.proId,
            openid: app.globalData.openid
          }).then(res => {
            console.log(res);
            wx.showToast({
              title: '结算成功',
              icon: 'success',
              duration: 3000
            })
            wx.reLaunch({
              url: "../../pages/index/index"
            })
          }).catch(err => {
            console.log(err);
            wx.showToast({
              title: '暂无评委打分无法结算',
              icon: 'none',
              duration: 3000
            })
          })
        } else {
          console.log('用户点击取消')
        }

      }
    })
  },
  joinEdit() {
    if (!this.data.editDisable) {
      this.setData({
        editType: true
      })
      console.log(1)
      wx.showToast({
        title: '已有评委提交分数单,无法修改',
        icon: 'none',
        duration: 2000
      })
      return
    } else {
      this.setData({
        editType: false
      })
    }
  },
  ranking() {
    wx.navigateTo({
      url: "../../pages/ranking/ranking?proId=" + this.data.proId
    })
  },
  // 编辑切换
  swicthEdit(e) {
    let index = e.target.dataset.index;
    this.setData({
      editActive: index
    })
  },
  subtract(e) {
    let _subtract = e.detail.value ? '1' : '2';
    this.setData({
      subtract_point: _subtract
    })
  },
  changeVal(e) {
    let peopleList = this.data.peopleList;
    let inputVal = e.detail.value;
    if (inputVal.trim() == '' || inputVal.trim() == undefined) return;
    peopleList.unshift(inputVal)
    console.log(peopleList);
    this.setData({
      peopleList: peopleList,
      peopleVal: ''
    })
    // 如果删到人员为空则禁止按钮
    if (peopleList.length == 0) {
      this.setData({
        editBtnType: false
      })
    } else {
      this.setData({
        editBtnType: true
      })
    }
  },
  // 删除人员
  delPeople(e) {
    let peopleList = this.data.peopleList;
    let peopleId = e.target.dataset.id
    peopleList.splice(peopleId, 1);
    console.log(peopleList);
    this.setData({
      peopleList: peopleList
    })
    // 如果删到人员为空则禁止按钮
    if (peopleList.length == 0) {
      this.setData({
        editBtnType: false
      })
    }
  },
  titleChange(e) {
    if (e.detail.value == '') {
      this.setData({
        editBtnType: false
      })
    } else {
      this.setData({
        editBtnType: true
      })
    }
    // 如果标题为空则禁止按钮
    this.setData({
      editTitle: e.detail.value
    })
  },
  edit() {
    api.post(edit, {
      openid: app.globalData.openid,
      project_id: this.data.proId,
      item: this.data.editTitle,
      subtract_point: this.data.subtract_point,
      players: this.data.peopleList.join(",")
    }).then(res => {
      this.getDetail()
      wx.showToast({
        title: '修改成功',
        icon: 'success',
        duration: 3000
      })
      console.log(res)
      this.closePop()
    }).catch(err => {
      console.log(err)
    })
  },
  // 获取用户数据
  getUserInfo: function(e) {
    app.globalData.userInfo = e.detail.userInfo
    this.setData({
      userInfo: e.detail.userInfo
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
  }
})