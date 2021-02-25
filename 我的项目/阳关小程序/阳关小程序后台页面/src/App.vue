<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
#app .tit {
  font-size: 18px;
  font-weight: 800;
  margin-bottom: 40px;
  padding-left: 60px;
}
.el-form .el-input,.el-form .el-textarea {
  max-width: 450px;
}
#app .el-input__tip,#app .el-upload__tip{
	font-size: 12px;
    color: #606266;
	margin: 0px;
    line-height: 12px;
    margin-top: 7px;
}
#app .inline-input,#app .inline-i{
	float: left;
}
#app .inline-i{
	margin: 0 4px;
}
#app .fourbutton-margin{
	margin-top:50px
}

.el-popover{
	 /* 控制全局的el-popover的宽为自适应（内容撑开） */
	width: auto!important; 
}
</style>
