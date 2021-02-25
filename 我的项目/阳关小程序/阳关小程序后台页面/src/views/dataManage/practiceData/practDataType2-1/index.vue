<template>
  <!-- 去掉app-container 因为嵌套了模板，所有样式会有重叠，这里需要去掉app-container类名 -->
  <div class="s2-targetList">
    <el-row style="margin-bottom:10px">
      <datePicker @change="handleDateChange"></datePicker>
      <div class="skcode_box" style="display:inline-block">
        <el-input
          size="small"
          style="width:150px"
          placeholder="编码"
          prefix-icon="el-icon-search"
          v-model.trim="searchCode"
          @keyup.enter.native="search_data"
        ></el-input>
        <!-- 编码对应社康显示 -->
        <codePopover></codePopover>
      </div>
      <el-input
        size="small"
        style="width:100px;margin-left:20px;"
        placeholder="姓名"
        prefix-icon="el-icon-search"
        v-model.trim="searchName"
        @keyup.enter.native="search_data"
      ></el-input>
      <el-input
        size="small"
        style="width:150px"
        placeholder="微信手机"
        prefix-icon="el-icon-search"
        v-model.trim="searchPhone"
        @keyup.enter.native="search_data"
      ></el-input>
      <el-select
        size="small"
        v-model="userType"
        @change="userType_change"
        multiple
        collapse-tags
        placeholder="选择用户分类"
        style="width:160px"
      >
        <el-option
          v-for="item in userOptions"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        ></el-option>
      </el-select>
      <el-button
        v-if="cur_roles[1].exist == '1'"
        type="primary"
        size="small"
        @click="export_excel"
      >导出</el-button>
      <el-button type="primary" size="small" @click="search_data">搜索</el-button>
    </el-row>

    <el-table v-loading="tab_loading" border :data="tableData" style="width: 100%" max-height="550">
      <el-table-column label="序号" type="index" :index="indexMethod" width="60"></el-table-column>
      <!-- a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.stime,b.etime,b.ltime,b.Q1,b.Q2,b.Q3,b.Q4,b.Q5,b.Q6,b.question -->
      <el-table-column label="用户ID" prop="open_id" header-align="center" align="center"></el-table-column>
      <el-table-column label="编码" prop="number" header-align="center" align="center"></el-table-column>
      <el-table-column label="姓名" prop="name" header-align="center" align="center"></el-table-column>
      <el-table-column label="微信手机" prop="wx_phone" header-align="center" align="center"></el-table-column>
      <el-table-column label="患者分类" prop="type_name" header-align="center" align="center"></el-table-column>
      <el-table-column label="课程编号" prop="course" header-align="center" align="center"></el-table-column>
      <el-table-column label="问卷填写开始时间" prop="stime" header-align="center" align="center"></el-table-column>
      <el-table-column label="问卷填写结束时间" prop="etime" header-align="center" align="center"></el-table-column>
      <el-table-column label="问卷填写时长" prop="ltime" header-align="center" align="center"></el-table-column>
      <el-table-column label="我的问题清单" class-name="cust-column" header-align="center" align="center" width="400">
        <template slot-scope="scope">
          <div class="cust-cell" v-for="(item,i) in scope.row.problem_arr" :key="i">
            {{item}}
          </div>
        </template>
      </el-table-column>
      <el-table-column label="排序值" class-name="cust-column" header-align="center" align="center">
        <template slot-scope="scope">
          <div class="cust-cell" v-for="(item,i) in scope.row.problem_arr" :key="i">
            {{i+1}}
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="main_target" label="总目标" header-align="center" align="center" width="200"></el-table-column>
      <el-table-column label="具体目标" class-name="cust-column" header-align="center" align="center" width="300">
        <template slot-scope="scope">
          <div class="cust-cell" v-for="(item,i) in scope.row.specific_goals_arr" :key="i">
            {{item}}
          </div>
        </template>
      </el-table-column>
    </el-table>
    <paging ref="paging" :pageIndex.sync="pageIndex" :total="total" @pageChange="pageChange"></paging>
  </div>
</template>

<script>
// 导入组件和方法
import { target_list, excel_target_list } from "@/api/dataManage/practData";
import paging from "@/components/paging";
import datePicker from "@/components/datePicker";
import codePopover from "@/components/codePopover";
import { getCookie } from "@/utils/auth";
import { downloadExcel } from "@/utils";

export default {
  components: {
    paging,
    datePicker,
    codePopover
  },
  data() {
    return {
      tab_loading: true,
      tableData: [],
      searchCode: "",
      searchName: "",
      searchPhone: "",
      userOptions: [
        {
          value: "10",
          label: "全部"
        },
        {
          value: "1",
          label: "P-患者"
        },
        {
          value: "2",
          label: "H-高危人群"
        },
        {
          value: "3",
          label: "R-缓解期患者"
        },
        {
          value: "4",
          label: "高危-分数"
        },
        {
          value: "5",
          label: "患者-B1"
        },
        {
          value: "6",
          label: "缓解期-B2"
        },
        {
          value: "7",
          label: "P2-患者轻度"
        },
        {
          value: "8",
          label: "P3-患者中度"
        },
        {
          value: "9",
          label: "P4-患者重度"
        },
        {
          value: "12",
          label: "P5-自曝患者"
        },
        {
          value: "11",
          label: "N-普通人群"
        },
        {
          value: "0",
          label: "游客"
        }
      ],
      userType: [], //用户分类
      total: 0,
      pageIndex: 1,
	  pageSize: 20,
      startTime: null,
      endTime: null,
    };
  },
  computed: {
    cur_roles() {
      return this.$store.getters.roles[1].info[2].info; //进入按钮权限的层级
    }
  },
  methods: {
    handleDateChange(start, end) {
      this.startTime = start;
      this.endTime = end;
      this.$refs.paging.indexInit();
    },
    userType_change() {
      this.$refs.paging.indexInit();
    },
    getPageData() {
      //定义公共请求参数
      let objData = {
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex
      };
      target_list(objData).then(res => {
        //信息健康
        this.tableData = res.data.list;
        this.total = res.data.total;
        this.tab_loading = false;
      });
    },
    indexMethod(index) {
      //序号
      return index + 1; //从0开始的必须加一
    },
    search_data() {
      //整个页面搜索共用函数
      this.getPageData();
    },
    export_excel() {
      //导出S2-目标清单
      excel_target_list({
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "S2-目标清单");
          this.$message({
            type: "warning",
            message: "开始下载！"
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    pageChange(data) {
      //页面条数改动
      this.pageSize = data.pageSize;
      this.getPageData();
    }
  }
};
</script>

<style scoped>
.cust-column .cust-cell{
	border-bottom: 1px solid #EBEEF5;
	line-height: 40px;
	overflow: hidden;
	text-overflow:ellipsis;
	white-space: nowrap;
}
/deep/ .cust-column{
	padding: 0px !important;
}
/deep/ .cust-column .cell{
	padding: 0px !important;
}
.cust-column .cell .cust-cell:last-of-type{
	border-bottom: 0px !important;
}
</style>

