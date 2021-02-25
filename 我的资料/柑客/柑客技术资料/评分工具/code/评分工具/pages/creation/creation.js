// pages/creation/creation.js
const app = getApp()
import api from '../../api/api'
import {
  creat,
  apply,
  judgesApproved,
  judgesList
} from '../../api/api_conf' // 链接注意填写正确 


Page({
  /**
   * 页面的初始数据
   */
  data: {
    step: 1,
    progressWidth: 100, // 第一阶段 100  第二阶段 360 第三阶段 600
    selectType: false,
    select: [{
      id: 1,
      val: "平均分"
    }, {
      id: 2,
      val: "总和"
    }],
    btnType: [true, true, true], // 按钮控制
    selectVal: 1, // 计算规则:1求平均分，2求总分。不传默认1
    nameShow: 2, // 是否匿名:1匿名，2不匿名。不传默认 1
    subtract_point: 2, //是否去掉最高最低分:1去掉，2不去。不传默认1
    peopleList: [], // 评审人员列表
    peopleVal: '', // 添加评审输入框
    title: '', // 标题
    count: 100, // 分数
    doneType:false
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {

  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {},

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {},

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {},

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {},

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {},

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {},

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {},
  // 分数变化
  numChange(e) {
    if (Number(e.detail.value) <= 0 || e.detail.value == '') {
      this.setData({
        count: 100
      })
    } else {
      this.setData({
        count: e.detail.value
      })
    }
  },
  // 标题变化
  titleChange(e) {
    if (e.detail.value == '') {
      return
    }
    let arr = this.data.btnType;
    arr[0] = false
    this.setData({
      title: e.detail.value,
      btnType: arr
    })
  },
  // 下一步
  nextStep() {
    let step = this.data.step;
    let nextObj = {
      Step: () => {
        if (step >= 3) {
          return (step = 3);
        } else {
          return step + 1;
        }
      },
      progress: () => {
        switch (step + 1) {
          case 1:
            return 100;
          case 2:
            return 360;
          case 3:
            return 600;
          default:
            return 702;
        }
      }
    };
    this.setData({
      step: nextObj.Step(),
      progressWidth: nextObj.progress()
    });
  },
  // 选项卡按钮
  selectBtn() {
    let type = !this.data.selectType;
    this.setData({
      selectType: type
    });
  },
  // 选项卡选中
  changeScore(e) {
    let selectId = e.target.dataset.id;
    console.log(e);
    this.setData({
      selectVal: selectId
    });
  },
  // 添加人员更改事件
  changeVal(e) {
    let peopleList = this.data.peopleList;
    let inputVal = e.detail.value;
    let arr = this.data.btnType;
    if (inputVal.trim() == '' || inputVal.trim() == undefined) return;
    peopleList.unshift(inputVal)
    console.log(peopleList);
    if (peopleList !== '' || peopleList !== undefined) {
      arr[2] = false;
    }
    this.setData({
      peopleList: peopleList,
      peopleVal: '',
      btnType: arr
    })
  },
  // 删除人员
  delPeople(e) {
    let peopleList = this.data.peopleList;
    let peopleId = e.target.dataset.id
    peopleList.splice(peopleId, 1);
    let arr = this.data.btnType; // 步数按钮状态
    console.log(peopleList);
    if (peopleList == [] || peopleList.length == 0) {
      arr[2] = true;
    } else {
      arr[2] = false;
    }
    this.setData({
      peopleList: peopleList,
      btnType: arr
    })
  },
  // 上一步
  prevStep() {
    let step = this.data.step;
    let nextObj = {
      Prev: () => {
        if (step <= 1) {
          return (step = 1);
        } else {
          return step - 1;
        }
      },
      progress: () => {
        switch (step - 1) {
          case 1:
            return 100;
          case 2:
            return 360;
          case 3:
            return 600;
          default:
            return 702;
        }
      }
    };
    this.setData({
      step: nextObj.Prev(),
      progressWidth: nextObj.progress()
    });
  },
  // 是否匿名
  showName(e) {
    let show = e.detail.value ? '1' : '2';
    this.setData({
      nameShow: show
    })
  },
  // 去掉最高最低分
  subtract(e) {
    let _subtract = e.detail.value ? '1' : '2';
    this.setData({
      subtract_point: _subtract
    })
  },
  // 完成
  send() {
    console.log();
    wx.showLoading({
      title: '创建中',
    })
    this.setData({
      doneType: true
    })
    api.post(creat, {
        creat_openid: app.globalData.openid, //创建人openid
        item: this.data.title, //评分主题
        high_point: this.data.count, // 分值，不传默认100
        calculate_type: this.data.selectVal, // 	计算规则:1求平均分，2求总分。不传默认1
        name_show: this.data.nameShow, // 是否匿名:1匿名，2不匿名。不传默认 1
        subtract_point: this.data.subtract_point, // 是否去掉最高最低分:1去掉，2不去。不传默认1
        players: this.data.peopleList.join(",") // 选手列表，用英文”,”分割	
      })
      .then(res => {
        // 将自己添加为评委
        api.post(apply, {
            openid: app.globalData.openid,
            avatar_url: app.globalData.userInfo.avatarUrl,
            name: app.globalData.userInfo.nickName,
            project_id: res.data
          }).then(res_two => {
            // 获取评委列表
            api.get(judgesList, {
              project_id: res.data
            }).then(res_three => {
              // 自动添加创建者为评委
              api.post(judgesApproved,{
                openid: app.globalData.openid,
                ids: res_three.data.checking["0"].id
              }).then(res_four=>{
                wx.reLaunch({
                  url: "../../pages/index/index",
                })
              }).catch(err_four=>{
                wx.hideLoading();
                this.setData({
                  doneType: false
                })
              })
             
            }).catch(err_three => {
              console.log(err_three)
            })
          })
          .catch(err_two => {
            console.log(err_two)
          })
      }).catch(err => {
        console.log(err);
      })
  }
});