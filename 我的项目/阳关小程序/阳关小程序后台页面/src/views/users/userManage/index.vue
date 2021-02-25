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
        v-if="cur_roles[2].exist == '1'"
        type="primary"
        size="small"
        @click="export_excel"
        >导出</el-button
      >
    </el-row>

    <el-table border :data="tableData" style="width: 100%">
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
        prop="wx_nickname"
        label="昵称"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="wx_phone"
        label="微信手机"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="type_name"
        label="用户分类"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="type_way_name"
        label="分类标记方式"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="code"
        label="邀请码"
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
        prop="phone"
        label="手机号码"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        prop="first_login_time"
        label="首次登录时间"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column
        v-if="cur_roles[1].exist == '1'"
        prop="first_login_time"
        label="操作"
        header-align="center"
        align="center"
      >
        <template slot-scope="scope">
          <el-button
            v-if="scope.row.type_way != 1 || scope.row.type_way != 2"
            type="text"
            size="mini"
            @click="handleEdit(scope.$index, scope.row)"
            >修改</el-button
          >
        </template>
      </el-table-column>
    </el-table>
    <paging
      ref="paging"
      :pageIndex.sync="pageIndex"
      :total="total"
      @pageChange="pageChange"
    ></paging>
    <!-- 新建数据弹窗Dialog -->
    <el-dialog
      class="dialog-box"
      title="修改用户信息"
      :visible.sync="dialogFormVisible"
      @opened="openDialog"
      @closed="closeDialog"
      :close-on-click-modal="false"
      center
    >
      <el-form
        ref="ruleForm"
        :model="formData"
        :rules="formDataRules"
        label-width="80px"
      >
        <el-form-item label="用户分类" prop="type">
          <el-select v-model="formData.type" placeholder="分类选择">
            <!-- 标签选择器 -->
            <el-option
              v-for="item in labelOptions2"
              :key="item.value"
              :label="item.value"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="编码" prop="number">
          <el-input v-model="formData.number"></el-input>
        </el-form-item>
        <el-form-item label="姓名" prop="name">
          <el-input v-model="formData.name"></el-input>
        </el-form-item>
        <el-form-item label="手机号码" prop="phone">
          <el-input v-model="formData.phone"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="dialogFormSubmit('ruleForm')"
          >提 交</el-button
        >
        <el-button type="primary" @click="dialogFormCancel('ruleForm')"
          >取 消</el-button
        >
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  get_pageData,
  export_excelData,
  user_edit_Info,
  user_edit
} from "@/api/users/userManage";
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
    //   校验规则
    var validateNumber = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请填写编码"));
      } else {
        let reg = /^[0-9a-zA-Z]+$/;
        if (!reg.test(value)) {
          //正则匹配英文和数字
          callback(new Error("编码仅支持字母及数字"));
        }
        callback();
      }
    };
    return {
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
        { value: "游客", id: 0 },
        { value: "P-患者", id: 1 },
        { value: "H-高危人群", id: 2 },
        { value: "R-缓解期患者", id: 3 },
        { value: "P2-患者轻度", id: 7 },
        { value: "P3-患者中度", id: 8 },
        { value: "P4-患者重度", id: 9 },
        { value: "P5-自曝患者", id: 12 },
        { value: "N-普通人群", id: 11 }
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
      patient_Id: null, //当前操作的患者id
      formData: {}, //弹窗表单数据
      formDataRules: {
        //验证
        type: [{ required: true, message: "请选择分类", trigger: "blur" }],
        number: [
          { required: true, validator: validateNumber, trigger: "blur" }
        ],
        name: [{ required: true, message: "请填写姓名", trigger: "blur" }],
        phone: [
          { required: true, message: "请正确填写手机号码", trigger: "blur" }
        ]
      }
    };
  },
  watch: {
    labelValue(to, from) {
      this.getPageData();
    }
  },
  computed: {
    cur_roles() {
      return this.$store.getters.roles[0].info[1].info; //进入按钮权限的层级
    }
  },
  methods: {
    getPageData() {
      get_pageData({
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
        this.tableData = res.data.list;
        this.total = res.data.total;
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
      export_excelData({
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
          downloadExcel(res, "用户信息");
          this.$message({
            type: "warning",
            message: "开始下载！"
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    openDialog() {
      //   console.log("打开弹窗");
    },
    closeDialog() {
      //关闭弹窗钩子
      this.formData = {}; //清除表单
    },
    // 修改用户信息
    handleEdit(index, row) {
      user_edit_Info({
        id: row.id,
        token: this.$store.getters.token
      }).then(res => {
        const { number, type, name, phone } = res.data;
        const formData = {
          //弹窗表单数据
          number: number, //号码
          type: type, //类型
          name: name, //名称
          phone: phone //手机
        };
        this.formData = formData;
      });
      this.dialogFormVisible = true;
      //   保存当前操作的id来进行编辑保存时传入此id
      this.patient_Id = row.id;
    },
    dialogFormSubmit(formName) {
      //提交
      this.$refs[formName].validate(valid => {
        console.log(valid);
        if (valid) {
          //   表单编辑状态
          user_edit({
            token: this.$store.getters.token,
            id: this.patient_Id,
            number: this.formData.number,
            name: this.formData.name,
            phone: this.formData.phone,
            type: this.formData.type,
            admin_id: getCookie("admin_id"),
            admin: getCookie("admin_n")
          }).then(res => {
            if (res.code != 200) {
              this.$message.error(res.msg);
              return false;
            }
            this.getPageData(); //获取页面数据
            this.$message({
              message: res.msg,
              type: "success"
            });
          });
          this.dialogFormVisible = false;
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    dialogFormCancel() {
      //取消
      this.dialogFormVisible = false;
    }
  }
};
</script>

<style scoped>
.dialog-box .el-dialog__title {
  font-weight: bold !important;
}
</style>
<style>
.dialog-box .el-dialog__body {
  padding-left: 100px;
  padding-right: 100px;
}
.dialog-box .el-dialog {
  width: 30%;
}
</style>
