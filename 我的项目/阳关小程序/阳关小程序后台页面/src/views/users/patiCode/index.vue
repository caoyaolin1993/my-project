<template>
  <div class="app-container">
    <el-row style="margin-bottom:10px">
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
      <el-select
        size="small"
        v-model="labelValue"
        multiple
		collapse-tags
        placeholder="选择患者类别"
        style="margin-left:10px;"
        clearable
      >
        <!-- 标签选择器 -->
        <el-option
          v-for="item in labelOptions"
          :key="item.value"
          :label="item.value"
          :value="item.id"
        ></el-option>
      </el-select>
      <el-input
        size="small"
        style="width:150px"
        placeholder="姓名"
        prefix-icon="el-icon-search"
        v-model.trim="searchName"
        @keyup.enter.native="search_data"
      ></el-input>
      <el-input
        size="small"
        style="width:180px"
        placeholder="手机号码"
        prefix-icon="el-icon-search"
        v-model.trim="searchPhoneNum"
        @keyup.enter.native="search_data"
      ></el-input>
      <el-button type="primary" size="small" @click="search_data">搜索</el-button>
      <el-button type="primary" size="small" @click="createNews">新增</el-button>
      <el-upload
        class="upload-excel"
        action
        :on-change="handleChange"
        :on-exceed="handleExceed"
        :on-remove="handleRemove"
        :file-list="fileListUpload"
        :limit="limitUpload"
        :show-file-list="false"
        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
        :auto-upload="false"
      >
        <el-button v-if="cur_roles[2].exist == '1'" type="primary" size="small">批量导入</el-button>
      </el-upload>
      <el-button type="primary" size="small" @click="createInvite_code">生成邀请码</el-button>
      <el-button v-if="cur_roles[2].exist == '1'" type="primary" size="small" @click="export_excel">导出</el-button>
    </el-row>

    <el-table border :data="tableData" style="width: 100%" max-height="550">
      <el-table-column label="序号" type="index" :index="indexMethod" width="60"></el-table-column>
      <el-table-column prop="number" label="编码"></el-table-column>
      <el-table-column prop="type_name" label="患者分类"></el-table-column>
      <el-table-column prop="name" label="姓名"></el-table-column>
      <el-table-column prop="phone" label="手机号码"></el-table-column>
      <el-table-column prop="code" label="邀请码"></el-table-column>
      <el-table-column label="操作" align="center" width="242px">
        <template slot-scope="scope">
          <el-button size="mini" v-if="cur_roles[1].exist == '1'" @click="handleEdit(scope.$index, scope.row)">修改</el-button>
          <el-button size="mini" type="danger" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
        </template>
      </el-table-column>
      <el-table-column prop="admin" label="操作账号"></el-table-column>
      <el-table-column prop="updatetime" label="修改时间"></el-table-column>
    </el-table>
    <paging ref="paging" :pageIndex.sync="pageIndex" :total="total" @pageChange="pageChange"></paging>

    <!-- 新建数据弹窗Dialog -->
    <el-dialog
      class="dialog-box"
      :title="dialogState === 1 ? '新增数据' : '编辑数据'"
      :visible.sync="dialogFormVisible"
      @opened="openDialog"
      @closed="closeDialog"
      :close-on-click-modal="false"
      center
    >
      <el-form ref="ruleForm" :model="formData" :rules="formDataRules" label-width="80px">
        <el-form-item label="编码" prop="number">
          <el-input v-model="formData.number"></el-input>
        </el-form-item>
        <el-form-item label="患者分类" prop="type">
          <el-select v-model="formData.type" placeholder="标签选择">
            <!-- 标签选择器 -->
            <el-option
              v-for="item in labelOptions2"
              :key="item.value"
              :label="item.value"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="姓名" prop="name">
          <el-input v-model="formData.name"></el-input>
        </el-form-item>
        <el-form-item label="手机号码" prop="phone">
          <el-input v-model="formData.phone"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="dialogFormSubmit('ruleForm',dialogState===1?1:0)">保 存</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  get_pageData,
  pageData_add,
  pageData_del,
  pageData_edit_info,
  pageData_edit,
  create_inviteCode,
  import_excelData,
  export_excelData
} from "@/api/users/patiCode";
import paging from "@/components/paging";
import codePopover from "@/components/codePopover";
import { getCookie } from "@/utils/auth";
import { downloadExcel } from "@/utils";

export default {
  components: {
    paging,
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
      pageName: "",
      tableData: [], //表格数据
      labelOptions: [
        { value: "P-患者", id: 1 },
        { value: "H-高危人群", id: 2 },
        { value: "R-缓解期患者", id: 3 },
        { value: "P2-患者轻度", id: 7 },
        { value: "P3-患者中度", id: 8 },
        { value: "P4-患者重度", id: 9 },
        { value: "P5-自曝患者", id: 12 },
        { value: "N-普通人群", id: 11 },
        { value: "全部", id: 10 }
      ], //修改患者分类
      labelOptions2: [
        { value: "P-患者", id: 1 },
        { value: "H-高危人群", id: 2 },
        { value: "R-缓解期患者", id: 3 },
        { value: "P2-患者轻度", id: 7 },
        { value: "P3-患者中度", id: 8 },
        { value: "P4-患者重度", id: 9 },
        { value: "P5-自曝患者", id: 12 },
        { value: "N-普通人群", id: 11 }
      ], //患者分类
      total: 0,
      pageIndex: 1, //跳第几页
      pageSize: 20, //每页条数
      searchCode: "", //编码查询
      labelValue: [], //患者类别
      searchName: "", //姓名查询
      searchPhoneNum: "", //手机号码查询
      fileListUpload: [], //文件列表
      limitUpload: 1, //上传文件个数限制为1
      fileTemp: null, //指向最新的附件
      dialogFormVisible: false, //新建弹框是否显示
      dialogState: 1, //1-表单新建状态，2-表单编辑状态
      patient_Id: null, //当前操作的患者id
      formData: {}, //弹窗表单数据
      formDataRules: {
        //验证
        number: [
          { required: true, validator: validateNumber, trigger: "blur" }
        ],
        type: [{ required: true, message: "请选择分类", trigger: "blur" }],
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
	  cur_roles(){
		  return this.$store.getters.roles[0].info[0].info; //进入按钮权限的层级
	  }
  },
  methods: {
    getPageData() {
      let objData = {
        token: this.$store.getters.token,
        number: this.searchCode,
        type: this.labelValue,
        name: this.searchName,
        phone: this.searchPhoneNum,
        limit: this.pageSize,
        page: this.pageIndex
      };
      get_pageData(objData).then(res => {
        this.tableData = res.data.list;
        this.total = res.data.total;
      });
    },
    search_data() {
      //搜索
      this.$refs.paging.indexInit();
    },
    createInvite_code() {
      //生成邀请码
      create_inviteCode({
        token: this.$store.getters.token,
        admin_id: getCookie("admin_id"),
        admin: getCookie("admin_n")
      }).then(res => {
        if (res.code != 200) {
          this.$message.error(res.msg);
          return false;
        }
        this.getPageData(); //获取页面数据
        this.$message({
          message: "生成邀请码成功！",
          type: "success"
        });
      });
    },
    handleChange(file, fileList) {
      this.fileTemp = file.raw;
      // 导入判断
      if (this.fileTemp) {
        if (
          this.fileTemp.type ==
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
          this.fileTemp.type == "application/vnd.ms-excel"
        ) {
          let fileFormData = new FormData();
          fileFormData.append("admin", getCookie("admin_n"));
          fileFormData.append("admin_id", getCookie("admin_id"));
          fileFormData.append("token", this.$store.getters.token);
          fileFormData.append("file", this.fileTemp);
          console.log("文件", this.fileTemp);
          import_excelData(fileFormData)
            .then(res => {
              console.log("导入的文件数据", res);
              this.$message({
                type: "success",
                message: "导入成功！"
              });
              this.getPageData(); //获取页面数据
            })
            .catch(err => {
              console.log(err);
            });
        } else {
          this.$message({
            type: "warning",
            message: "附件格式错误，请删除后重新上传！"
          });
        }
      } else {
        this.$message({
          type: "warning",
          message: "请上传附件！"
        });
      }
    },
    handleExceed() {
      //超出数量后的动作
    },

    handleRemove(file, fileList) {
      this.fileTemp = null;
    },
    export_excel() {
      //导出excel
      export_excelData({
        token: this.$store.getters.token,
        number: this.searchCode,
        type: this.labelValue,
        name: this.searchName,
        phone: this.searchPhoneNum,
        limit: this.pageSize,
        page: this.pageIndex
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "患者编码");
          this.$message({
            type: "warning",
            message: "开始下载！"
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    indexMethod(index) {
      //序号
      return index + 1; //从0开始的必须加一
    },
    handleEdit(index, row) {
      //数据编辑
      this.dialogState = 0;
      pageData_edit_info({
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
    handleDelete(index, row) {
      //数据删除
      this.$confirm("删除后记录无法恢复，确定要删除吗?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          pageData_del({
            id: row.id,
            token: this.$store.getters.token
          }).then(res => {
            console.log("删除", res);
            this.getPageData(); //获取页面数据
            this.$message({
              message: res.msg,
              type: "success"
            });
          });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "已取消删除"
          });
        });
    },
    createNews() {
      //新建数据
      this.dialogState = 1;
      this.formData = {
        //弹窗表单数据
        number: "", //号码
        name: "", //名称
        phone: "", //手机
        type: "" //类型
      };
      this.dialogFormVisible = true;
    },
    openDialog() {
      console.log("打开弹窗");
    },
    closeDialog() {
      //关闭弹窗钩子
      this.formData = {}; //清除表单
    },
    dialogFormSubmit(formName, dialogState) {
      //新建数据或编辑
      this.$refs[formName].validate(valid => {
        console.log(valid);
        if (valid) {
          if (dialogState === 1) {
            //表单新增状态
            console.log("新建保存", this.$store.getters);
            pageData_add({
              token: this.$store.getters.token,
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
          } else {
            //   表单编辑状态
            pageData_edit({
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
          }
          this.dialogFormVisible = false;
        } else {
          console.log("error submit!!");
          return false;
        }
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
.dialog-box .el-dialog__title {
  font-weight: bold !important;
}
.upload-excel {
  display: inline-block;
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