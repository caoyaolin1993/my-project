const app = getApp()

import api from '../../api/api'
import {
  judgesList, judgesApproved,judgesDel
} from '../../api/api_conf' // 链接注意填写正确 


// pages/management/management.js
Page({
  /**
   * 页面的初始数据
   */
  data: {
    proId:'', // 项目ID
    pageType:1, // Tab切换 待审核 == 1 || 已通过 == 2
    // 所有数据
    allData:'',
    editType:false, // 编辑状态
    menuType:true, // 长按后的菜单栏状态
    passArr:[]// 通过需要提交的ID
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      proId:options.proId
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    this.getListData();
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
  // ajax获取数据
  getListData(){
    api.get(judgesList,{
      project_id:this.data.proId
    }).then(res=>{
      res.data.approved = res.data.approved.filter((val,index)=>{
        if (val.judges_status!==3){
          return res
        }
      })
      console.log(res.data)
      this.setData({
        allData:res.data
      })
    }).catch(err=>{
      console.log(err);
    })
    let _Data = this.data.allData;
    if(_Data.checking){
      _Data.checking.map((res)=>{
        res.checkType = false;
      })
    }
    
    
    this.setData({
      allData:_Data
    })
  },
  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  },
  // 切换Tab
  swicthPage(e){
    let pageType = this.data.pageType;
    if (e.currentTarget.dataset.swicthid == 1){
      this.setData({
        pageType:1
      })
    }else{
      this.setData({
        pageType: 2
      })
    }
  },
  // 多选框取值函数
  checkboxChange(e) {
    // console.log(e.detail.value);
    this.setData({
      passArr: e.detail.value
    })
  },
  // 长按进入编辑状态
  checkEdit(e){
    this.setData({
      editType:true  
    })
  },
  // 切换菜单
  swicthMenu(){
    this.setData({
      menuType:!this.data.menuType
    })
  },
  // 退出编辑
  closeEdit(){
    this.setData({
      editType:!this.data.editType
    })
  },
  // 全选
  selectAll(){
    let _Data = this.data.allData;
    _Data.checking.map(res=>{
      res.checkType = true
    })
    this.setData({
      allData:_Data,
      menuType: true
    })
  },
  // 反选
  selectInverse(){
    let _Data = this.data.allData;
    _Data.checking.map(res => {
      res.checkType = !res.checkType
    })
    console.log(_Data);
    this.setData({
      allData: _Data,
      menuType: true
    })
  },
  // 点击row-item元素切换多选框
  swicthCheck(e){
    console.log(e);
    let passArr = this.data.passArr;
    let val = e.currentTarget.dataset.val
    let id = e.currentTarget.dataset.id;
    let _Data = this.data.allData;
    passArr.push(val);
    passArr = Array.from(new Set(passArr));
    this.setData({
      passArr
    })
    // 如果在编辑状态
    if (this.data.editType){
      this.data.allData.checking[id].checkType = !this.data.allData.checking[id].checkType;
      this.setData({
        allData: _Data
      })
    }
  },
  // 删除
  delRow(){
    let _Data = this.data.allData;
    let delArr = [];
    let remainData = _Data.approved.filter(res => !res.checkType); // 删除完剩余的选手
    let delData = _Data.approved.filter(res => res.checkType); // 删除的选手
    _Data.approved = remainData;
    delData.map(val=>{
      delArr.push(val.id)
    });
    api.post(judgesDel,{
      openid:app.globalData.openid,
      id: this.data.passArr.join(",")
    }).then(res=>{
      this.setData({
        editType: !this.data.editType
      })
      this.getListData();
    }).catch(err=>{
      console.log(err)
    })
    this.setData({
      allData: _Data,
      menuType: true
    })
  },
  // 通过
  pass(){
    console.log(this.data.passArr)
    api.post(judgesApproved,{
      openid: app.globalData.openid,
      ids: this.data.passArr.join(",")
    }).then(res=>{
      console.log(res)
      this.setData({
        editType: !this.data.editType
      })
      this.getListData();
    }).catch(err=>{
      console.log(err)
    })
  },
  passOne(e){
    let id = e.currentTarget.dataset.id;
    api.post(judgesApproved,{
      openid: app.globalData.openid,
      ids: id
    }).then(res=>{
     this.getListData();
    }).catch(err=>{
      console.log(err)
    })
  }
})