<template>
  <!-- 去掉app-container 因为嵌套了模板，所有样式会有重叠，这里需要去掉app-container类名 -->
  <div class>
    <el-row style="margin-bottom:10px">
      <el-button
        v-if="cur_roles[1].exist == '1'"
        type="primary"
        size="small"
        @click="export_excel"
      >导出</el-button>
    </el-row>
    <el-table v-loading="tab_loading" :data="tableData" border style="width: 100%">
      <el-table-column prop="name" label="训练名称" width="180"></el-table-column>
      <el-table-column prop="clickCount" label="点击次数" width="180"></el-table-column>
      <el-table-column prop="zCount" label="转发次数"></el-table-column>
      <el-table-column prop="yTime" label="放松总时长"></el-table-column>
      <el-table-column prop="aTime" label="平均时长"></el-table-column>
    </el-table>
  </div>
</template>

<script>
import { relaxSta, excel_relaxSta } from "@/api/dataManage/practData";
import { downloadExcel } from "@/utils";
export default {
  data() {
    return {
      tab_loading: true,
      tableData: []
    };
  },
  created() {
    this.getList();
  },
   computed: {
      cur_roles(){
          return this.$store.getters.roles[1].info[2].info; //进入按钮权限的层级
      }
  },
  methods: {
    getList() {
      relaxSta({
        token: this.$store.getters.token
      }).then(res => {
        console.log(res);
        this.tableData = res.data;
        this.tab_loading = false;
      });
	},
	export_excel(){
		//导出excel
      excel_relaxSta({
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        name: this.searchName,
        phone: this.searchPhoneNum,
        type: this.labelValue,
        page: this.pageIndex,
        limit: this.pageSize
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "放松训练统计数据");
          this.$message({
            type: "warning",
            message: "开始下载！"
          });
        })
        .catch(err => {
          console.log(err);
        });
	}
  }
};
</script>
