<template>
  <div class="app-container practicePage">
	  <el-row>
		<el-popover
		ref="practPop"
		placement="bottom-start"
		width="400"
		trigger="click"
		v-model="practPop_isShow"
		>
			<ul class="practTypeList">
				<li v-for="item in practPopData" :key="item.name">
					<span>{{item.name}}</span>
					<div class="itemtype">
						<ul class="practTypeChild">
							<li v-for="itemchild in item.list" :key="itemchild.value">
								<router-link :to="itemchild.path" @click.native="rtLinkClick(itemchild.value)">{{itemchild.value}}</router-link>
							</li>
						</ul>
					</div>
				</li>
			</ul>
		</el-popover>
		<el-select
            size="small"
            v-model="curType"
			 v-popover:practPop
			  style="margin-bottom:16px;"
			  popper-class="drop-downCls"
		></el-select>
		<!-- 下面也能实现功能,但是下拉箭头不会有动画,上面是使用的element组件借用动画效果 -->
	  	<!-- <el-button v-popover:practPop style="margin-bottom:16px;">{{curType}}<i class="el-icon-arrow-down el-icon--right"></i></el-button> -->
	  </el-row>
	  <router-view />
  </div>
</template>

<script>
export default {
	data(){
		return {
			practPopData: [
				{
					name: '首页',
					list:[
						{
							value: '放松训练统计',
							path: '/dataManage/practiceData/1'
						},
						{
							value: '放松训练详情',
							path: '/dataManage/practiceData/2'
						}
					]
				},
				{
					name: '第一节',
					list:[
						{
							value: 'S1-问题清单',
							path: '/dataManage/practiceData/1-1'
						},
						{
							value: 'S1-愉快事件记录表',
							path: '/dataManage/practiceData/1-2'
						}
					]
				},
				{
					name: '第二节',
					list:[
						{
							value: 'S2-目标清单',
							path: '/dataManage/practiceData/2-1'
						},
						{
							value: 'S2-活动记录表',
							path: '/dataManage/practiceData/2-2'
						},
						{
							value: 'S2-自动思维记录表',
							path: '/dataManage/practiceData/2-3'
						}
					]
				},
				{
					name: '第三节',
					list:[
						{
							value: 'S3-一周回顾',
							path: '/dataManage/practiceData/3-1'
						},
						{
							value: 'S3-活动宝箱',
							path: '/dataManage/practiceData/3-2'
						},
						{
							value: 'S3-活动安排',
							path: '/dataManage/practiceData/3-3'
						},
						{
							value: 'S3-识别误区',
							path: '/dataManage/practiceData/3-4'
						},
						{
							value: 'S3-误区比例',
							path: '/dataManage/practiceData/3-5'
						},
						{
							value: 'S3-活动记录表',
							path: '/dataManage/practiceData/3-7'
						},
						{
							value: 'S3-自动思维记录',
							path: '/dataManage/practiceData/3-6'
						}
					]
				},
				{
					name: '第四节',
					list:[
						{
							value: 'S4-任务分解',
							path: '/dataManage/practiceData/4-1'
						},
						{
							value: 'S4-活动安排表',
							path: '/dataManage/practiceData/4-2'
						},
						{
							value: 'S4-活动记录表',
							path: '/dataManage/practiceData/4-3'
						},
						{
							value: 'S4-自动思维记录表',
							path: '/dataManage/practiceData/4-4'
						}
					]
				},
				{
					name: '第五节',
					list:[
						{
							value: 'S5-归因练习',
							path: '/dataManage/practiceData/5-1'
						},
						{
							value: 'S5-问题解决',
							path: '/dataManage/practiceData/5-2'
						} ,
						{
							value: 'S5-活动安排表',
							path: '/dataManage/practiceData/5-3'
						},
						{
							value: 'S5-活动记录表',
							path: '/dataManage/practiceData/5-4'
						}
					]
				},
				{
					name: '第六节',
					list:[
						{
							value: 'S6-活动安排表',
							path: '/dataManage/practiceData/6-1'
						},
						{
							value: 'S6-活动记录表',
							path: '/dataManage/practiceData/6-2'
						},
						{
							value: 'S6-发现内在信念表',
							path: '/dataManage/practiceData/6-3'
						},
						{
							value: 'S6-评估内在信念表',
							path: '/dataManage/practiceData/6-4'
						}
					]
				},
				{
					name: '第七节',
					list:[
						{
							value: 'S7-方法掌握程度',
							path: '/dataManage/practiceData/7-1'
						},
						{
							value: 'S7-我的新目标',
							path: '/dataManage/practiceData/7-2'
						}
					]
				}
			],
			curType: '',
			practPop_isShow: false // 是否显示
		}
	},
	created(){
		this.curType = this.practPopData[0].list[0].value;
	},
	computed: {
		key() {
			return this.$route.path
		}
	},
	methods: {
		rtLinkClick(dataType){
			console.log(dataType);
			this.curType = dataType;
			this.practPop_isShow = false;
		}
	}

}
</script>

<style scoped>
	.practTypeList{
		padding: 0;
		margin: 0;
	}
	.practTypeList li{
		list-style: none;
		line-height: 40px;
	}
	.practTypeList>li>span {
		display: inline-block;
		width: 50px;
		margin-right: 20px;
	}
	.practTypeList .itemtype {
		display: inline-block;
	}
	.practTypeChild{
		padding: 0;
		margin: 0;
		display: flex;
	}
	.practTypeChild li{
		width: 140px;
		color: #409EFF;
	}
</style>
<style>
.el-select-dropdown.drop-downCls{
	/* 隐藏默认下拉框数据为空的样式,因为这里自定义了下拉框，不需要用到默认下拉框的样式*/
	display: none; 
}
</style>