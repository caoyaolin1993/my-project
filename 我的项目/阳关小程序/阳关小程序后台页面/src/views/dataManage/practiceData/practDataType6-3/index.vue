<template>
  <div class="app-container">
    <el-row style="margin-bottom:10px">
      <datePicker @change="handleDateChange"></datePicker>
      <el-input
        size="small"
        style="width:200px"
        placeholder="微信手机"
        prefix-icon="el-icon-search"
        v-model.trim="searchPhoneNum"
        @keyup.enter.native="search_data"
      ></el-input>
      <el-select
        size="small"
        v-model="labelValue"
        multiple
        collapse-tags
        placeholder="选择用户分类"
        clearable
      >
        <!-- 标签选择器 -->
        <el-option
          v-for="item in labelOptions"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        ></el-option>
      </el-select>
      <el-input
        size="small"
        style="width:200px"
        placeholder="编码"
        prefix-icon="el-icon-search"
        v-model.trim="searchCode"
        @keyup.enter.native="search_data"
      ></el-input>
      <!-- 编码对应社康显示 -->
      <codePopover></codePopover>
      <el-input
        size="small"
        style="width:150px;margin-left:10px"
        placeholder="姓名"
        prefix-icon="el-icon-search"
        v-model.trim="searchName"
        @keyup.enter.native="search_data"
      ></el-input>
      <el-button type="primary" size="small" @click="search_data"
        >搜索</el-button
      >
      <el-button
        v-if="cur_roles[1].exist == '1'"
        type="primary"
        size="small"
        @click="export_excel"
        >导出</el-button
      >
    </el-row>

    <el-table
      v-loading="tab_loading"
      border
      :data="tableData"
      style="width: 100%"
      max-height="550"
    >
      <el-table-column
        label="序号"
        type="index"
        :index="indexMethod"
        width="60"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="open_id"
        label="用户ID"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="number"
        label="编码"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="name"
        label="姓名"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="wx_phone"
        label="微信手机号"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="type_name"
        label="患者分类"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="course_number"
        label="课程编号"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="stime"
        label="开始时间"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="etime"
        label="结束时间"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="ltime"
        label="时长"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="situation"
        label="情境"
        header-align="center"
        align="center"
        width="300"
      ></el-table-column>
      <el-table-column
        label="情绪"
        class-name="cust-column"
        header-align="center"
        align="center"
      >
        <template slot-scope="scope">
          <div class="cust-cell" v-for="(item, i) in scope.row.mood" :key="i">
            {{ item.mood }}
          </div>
        </template>
      </el-table-column>
      <el-table-column
        label="情绪评分"
        class-name="cust-column"
        header-align="center"
        align="center"
      >
        <template slot-scope="scope">
          <div class="cust-cell" v-for="(item, i) in scope.row.mood" :key="i">
            {{ item.fraction || "&nbsp;" }}
          </div>
        </template>
      </el-table-column>
      <el-table-column
        label="自动思维"
        class-name="cust-column"
        header-align="center"
        align="center"
        width="300"
      >
        <template slot-scope="scope">
          <div class="cust-cell" v-for="(item, i) in scope.row.think" :key="i">
            {{ item.think }}
          </div>
        </template>
      </el-table-column>
      <el-table-column
        label="思维评分"
        class-name="cust-column"
        header-align="center"
        align="center"
        width="180px"
      >
        <template slot-scope="scope">
          <div class="cust-cell" v-for="(item, i) in scope.row.think" :key="i">
            {{ item.fraction }}
          </div>
        </template>
      </el-table-column>
      <el-table-column
        label="思维误区"
        class-name="cust-column"
        header-align="center"
        align="center"
        width="180px"
      >
        <template slot-scope="scope">
          <div class="cust-cell" v-for="(item, i) in scope.row.think" :key="i">
            {{ item.misunderstanding }}
          </div>
        </template>
      </el-table-column>
      <el-table-column
        prop="idea"
        label="想法"
        header-align="center"
        align="center"
        width="300"
      ></el-table-column>
      <el-table-column
        label="箭头向下"
        class-name="cust-column"
        header-align="center"
        align="center"
        width="300"
      >
        <template slot-scope="scope">
          <div class="cust-cell" v-for="(item, i) in scope.row.idea_arr" :key="i">
            {{ item }}
          </div>
        </template>
      </el-table-column>
    </el-table>
    <paging
      ref="paging"
      :pageIndex.sync="pageIndex"
      :total="total"
      @pageChange="pageChange"
    ></paging>
  </div>
</template>

<script>
import { s6_found_faith, excel_s6_found_faith } from "@/api/dataManage/practData";
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
      tableData: [], //表格数据
      labelOptions: [
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
      ], //患者分类
      labelOptions2: [
        { value: "患者", id: 1 },
        { value: "高危", id: 2 },
        { value: "缓解期", id: 3 }
      ], //修改患者分类
      total: 0,
      pageIndex: 1,
      pageSize: 20,
      startTime: null,
      endTime: null,
      searchCode: "", //编码查询
      labelValue: [], //患者类别
      searchName: "", //姓名查询
      searchPhoneNum: "", //手机号码查询
      dialogFormVisible: false, //新建弹框是否显示
      patient_Id: null //当前操作的患者id
    };
  },
  watch: {
    labelValue(to, from) {
      this.getPageData();
    }
  },
  computed: {
    cur_roles() {
      return this.$store.getters.roles[1].info[2].info; //进入按钮权限的层级
    }
  },
  methods: {
    getPageData() {
      s6_found_faith({
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        name: this.searchName,
        phone: this.searchPhoneNum,
        type: this.labelValue,
        page: this.pageIndex,
        limit: this.pageSize
      }).then(res => {
        console.log(res);
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
      //搜索
      this.$refs.paging.indexInit();
    },
    pageChange(data) {
      //页面条数改动
      this.pageSize = data.pageSize;
      this.getPageData();
    },
    handleDateChange(start, end) {
      this.startTime = start;
      this.endTime = end;
      this.$refs.paging.indexInit();
    },
    export_excel() {
      //导出excel
      excel_s6_found_faith({
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
          downloadExcel(res, "S2-自动思维记录表");
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

<style scoped>
.cust-column .cust-cell {
  border-bottom: 1px solid #ebeef5;
  line-height: 40px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
/deep/ .cust-column {
  padding: 0px !important;
}
/deep/ .cust-column .cell {
  padding: 0px !important;
}
.cust-column .cell .cust-cell:last-of-type {
  border-bottom: 0px !important;
}
</style>
