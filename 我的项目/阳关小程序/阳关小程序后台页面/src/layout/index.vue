<template>
  <el-container>
    <el-header style="height: 90px;">
      <el-row type="flex" class="boxContent" justify="space-between" align="middle">
        <el-col :span="12" style="margin-left:20px;">
			 <!-- :xs="12" :sm="7" :md="6" :lg="6" :xl="3" -->
            <h1 id="logo"><a href="#">阳光心情小程序管理后台</a></h1>
        </el-col>
        <!-- <el-col :span="5" :xs="0" :sm="11" :md="10" :lg="8" :xl="6" :pull="2" style="margin-left:20px;">
          <div class="environment">
            <span>当前环境：</span>
            <span class="curr-env">正式环境</span>
            <el-button type="primary" class="toggle-env" @click="toggleEnv">切换环境</el-button>
          </div>
        </el-col> -->
        <el-col :span="12" style="text-align:right;" class="name_admin">
			<!--  :xs="12" :sm="6" :md="8" :lg="12" :xl="12" -->
            <el-dropdown @command="handleCommand" trigger="click">
                <!-- <span class="avatar"><img :src="$store.getters.avatar" alt="管理者头像"></span> -->
                <span class="el-dropdown-link">
                    欢迎您，{{$store.getters.name}}
                    <i class="el-icon-arrow-down el-icon--right"></i>
                </span>
                <el-dropdown-menu slot="dropdown">
                    <!-- <el-dropdown-item command="changePassword">修改密码</el-dropdown-item> -->
                    <el-dropdown-item command="signOut">登出</el-dropdown-item>
                </el-dropdown-menu>
                <!-- <changePassword ref="changePassword"></changePassword> -->
            </el-dropdown>
        </el-col>
      </el-row>
    </el-header>
    <el-container style="margin-top:90px">
      <div :class="classObj" class="app-wrapper">
        <div v-if="device==='mobile'&&sidebar.opened" class="drawer-bg" @click="handleClickOutside" />
        <sidebar class="sidebar-container" />
        <div class="main-container">
          <div :class="{'fixed-header':fixedHeader}">
            <navbar />
          </div>
          <app-main />
        </div>
      </div>
    </el-container>
  </el-container>
</template>

<script>
import { Navbar, Sidebar, AppMain } from './components'
import ResizeMixin from './mixin/ResizeHandler'
// import changePassword from "../components/changePassword";

export default {
  name: 'Layout',
  components: {
    Navbar,
    Sidebar,
	AppMain,
	// changePassword
  },
  mixins: [ResizeMixin],
  computed: {
    sidebar() {
      return this.$store.state.app.sidebar
    },
    device() {
      return this.$store.state.app.device
    },
    fixedHeader() {
      return this.$store.state.settings.fixedHeader
    },
    classObj() {
      return {
        hideSidebar: !this.sidebar.opened,
        openSidebar: this.sidebar.opened,
        withoutAnimation: this.sidebar.withoutAnimation,
        mobile: this.device === 'mobile'
      }
    }
  },
  methods: {
    handleClickOutside() {
      this.$store.dispatch('app/closeSideBar', { withoutAnimation: false })
    },
    handleCommand(command) {
        if (command == "signOut") {
			this.$confirm('您将退出后台，未保存的修改不会生效，确认吗？', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {
				this.$store.dispatch("user/logout").then(() => {
					this.$router.push("/login");
				});
			}).catch(() => {       
				this.$message('取消退出！')
			});
		} 
		// else if (command == "changePassword") {
        //     this.$refs.changePassword.openDialog();
        // }
	},
	// toggleEnv(){
	// 	let str = location.href.split("#")[1];
	// 	console.log('跳转路径',str);
	// 	window.open(`http://localhost:8087`);
	// 	// window.open(`http://localhost:8087/#${str}`);
	// }
  }
}
</script>

<style lang="scss" scoped>
  @import "~@/styles/mixin.scss";
  @import "~@/styles/variables.scss";
  .boxContent{
    height: 100%;
  }
  .el-header {
      background-color: #f2f2f2;
      padding: 10px;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
	  z-index: 10;
  }
  .app-wrapper {
    @include clearfix;
    position: relative;
    height: 100%;
    width: 100%;
    &.mobile.openSidebar{
      position: fixed;
      top: 0;
    }
  }
  .drawer-bg {
    background: #000;
    opacity: 0.3;
    width: 100%;
    top: 0;
    height: 100%;
    position: absolute;
    z-index: 999;
  }

  .fixed-header {
    position: fixed;
    top: 90px;
    right: 0;
    z-index: 9;
    width: calc(100% - #{$sideBarWidth});
    transition: width 0.28s;
  }

  .hideSidebar .fixed-header {
    width: calc(100% - 54px)
  }

  .mobile .fixed-header {
    width: 100%;
  }
  #logo{
    margin: 0;
  }
  #logo a{
    display: block;
  }
  .environment{
    font-size: 20px;
    color: #fff;
  }
  .toggle-env{
    margin-left: 40px;
  }
  .avatar{
    display: inline-block;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    vertical-align: middle;
    margin-right: 10px;
  }
  .avatar img{
    width: 100%;
    height: 100%;
  }
  .name_admin .el-dropdown-link{
    color: #000;
	font-weight: 800;
  }
</style>

<style>
.el-select{
	margin-bottom: 10px;
}
</style>
