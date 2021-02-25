<template>
  <div class="app-container">
    <el-row style="margin-bottom:10px">
      <!-- <el-input
        style="width:300px"
        placeholder="请输入用户名"
        prefix-icon="el-icon-search"
        v-model.trim="searchInput"
        @keyup.enter.native="searchNews"
      ></el-input>
      <el-button @click="searchNews">搜索</el-button>-->
      <el-button style="float:left" @click="createUser">新建账号</el-button>
    </el-row>

    <el-table border :data="tableData" style="width: 800px">
      <el-table-column label="ID" prop="id" width="60"></el-table-column>
      <el-table-column label="用户名" prop="username"></el-table-column>
      <el-table-column label="用户头像">
        <template slot-scope="scope">
          <img style="width:80px;height:80px;" :src="scope.row.image" alt="用户头像" />
        </template>
      </el-table-column>
      <el-table-column label="角色">
        <span>运营人员</span>
      </el-table-column>
      <!-- <el-table-column prop="conftime" label="创建时间"></el-table-column> -->
      <!-- <el-table-column label="状态" width="80">
        <template slot-scope="scope">
          <span>{{scope.row.states === 0 ? '禁用' : '启用'}}</span>
        </template>
      </el-table-column>-->
      <el-table-column label="操作" align="center" width="242px">
        <template slot-scope="scope">
          <!-- <el-button
            v-if="scope.row.states === 0"
            size="mini"
            @click="handleShelves(scope.$index, scope.row)"
			type="warning"
			plain
          >启用</el-button>
          <el-button v-else size="mini" type="danger" @click="handleUnshelves(scope.$index, scope.row)">禁用</el-button>-->
          <el-button size="mini" type="primary" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
          <el-button size="mini" type="danger" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- <paging ref="paging" :pageIndex.sync="pageIndex" :total="total" @pageChange="pageChange"></paging> -->
    <!-- 新建用户弹窗Dialog -->
    <el-dialog
      class="dialog-box"
      :title="dialogState === 1 ? '新建账号' : '编辑账号'"
      :visible.sync="dialogFormVisible"
      :close-on-click-modal="false"
      center
    >
      <el-form ref="ruleForm" :model="formData" :rules="formDataRules" label-width="140px">
        <el-form-item label="上传头像">
          <upload small v-model="formData.image"></upload>
        </el-form-item>
        <!-- <el-form-item label="角色">
          <el-select v-model="formData.formLabelValue" placeholder="角色选择">
            <el-option
              v-for="item in labelOptions"
              :key="item.value"
              :label="item.value"
              :value="item.value"
            ></el-option>
          </el-select>
        </el-form-item>-->
        <el-form-item label="用户名" prop="username">
          <el-input v-model="formData.username"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input v-model="formData.password" type="password"></el-input>
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
  pageData,
  edit_list,
  delete_user,
  edit_user,
  add_user
} from "@/api/addUsers";
// import paging from "@/components/paging";
import upload from "@/components/upload";

export default {
  components: {
    // paging,
    upload
  },
  data() {
    const validatePassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error("密码不能少于6位"));
      } else {
        callback();
      }
    };
    return {
      tableData: [], //表格数据
      labelOptions: [], //标签选择数据
      //   total: 0,
      //   pageIndex: 1,
      //   pageSize: 0,
      //   labelValue: "", //标签选择
      //   searchInput: "", //搜索内容
      dialogFormVisible: false, //新建弹框是否显示
      dialogState: 1, //1-表单新建状态，2-表单编辑状态
      userId: null, //当前操作的用户id
      formData: {
        //弹窗表单数据
        image: "",
        username: "",
        password: ""
      },
      formDataRules: {
        username: [
          { required: true, trigger: "blur", message: "请输入用户名" }
        ],
        password: [
          { required: true, trigger: "blur", validator: validatePassword }
        ]
      }
    };
  },
  watch: {
    // newsState(to, from) {
    //   //上禁用状态变化时
    //   this.$refs.paging.indexInit();
    // },
    // labelValue(to, from) {
    //   //标签变化时
    //   this.$refs.paging.indexInit();
    // }
  },
  created() {
    // this.getNewsLabel(); //获取用户权限
    this.getPageData();
  },
  methods: {
    // getNewsLabel() {
    //     const labelOptions = ['普通管理员','运营人员']; //用户权限
    //     let labelData = [];
    //     labelOptions.forEach((p, i) => {
    //       //数组转化为带键名的对象
    //       console.log(p, i);
    //       labelData.push({
    //         value: p
    //       });
    //     });
    //     console.log(labelData);
    //     this.labelOptions = labelData;
    // },
    getPageData() {
      pageData().then(res => {
        // // console.log("页面数据", res);
        const tableData = res.data;
        // this.total = res.data.count;
        this.tableData = tableData;
      });
    },
    // handleShelves(index, row) {
    //   //启用
    //   shelves({ id: row.id }).then(res => {
    //     console.log(res);
    // 	this.getPageData()
    // 	this.$message({
    // 		message: '启用成功',
    // 		type: "success"
    // 	});
    //   });
    //   console.log(index, row);
    // },
    // handleUnshelves(index, row) {
    //   //禁用
    //   unShelves({ id: row.id }).then(res => {
    // 	console.log(res);
    // 	this.getPageData()
    // 	this.$message({
    // 		message: '禁用成功',
    // 		type: "success"
    // 	});
    //   });
    //   console.log(index, row);
    // },
    handleEdit(index, row) {
      //编辑
      this.dialogState = 0;
      edit_list({ id: row.id }).then(res => {
        const { image, username, password } = res.data;
        const formData = {
          image: image,
          username: username,
          password: password
        };
        this.formData = formData;
      });
      this.dialogFormVisible = true;
      //   保存当前操作的id来进行编辑保存时传入此id
      this.userId = row.id;
    },
    handleDelete(index, row) {
      //用户删除
      this.$confirm("此操作将永久删除该条用户, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          delete_user({ id: row.id }).then(res => {
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
    createUser() {
      //新建用户
      this.dialogState = 1;
      this.formData = {
        //弹窗表单数据
        image: "",
        username: "",
        password: ""
      };
      this.dialogFormVisible = true;
    },
    dialogFormSubmit(formName, dialogState) {
      //新建用户或编辑
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (dialogState === 1) {
            //表单保存状态
            console.log("新建保存");
            add_user({
              image: this.formData.image,
              username: this.formData.username,
              password: this.formData.password
            }).then(res => {
              console.log(res);
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
            console.log("编辑保存时的id", this.userId);
            //   表单编辑状态
            edit_user({
              id: this.userId,
              image: this.formData.image,
              username: this.formData.username,
              password: this.formData.password
            }).then(res => {
              console.log(res);
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
    }
    // searchNews() {
    //   //搜索
    //   this.$refs.paging.indexInit();
    // },
    // pageChange(data) {
    //   //页面条数改动
    //   this.pageSize = data.pageSize;
    //   this.getPageData();
    // },
  }
};
</script>

<style scoped>
.dialog-box .el-dialog__title {
  font-weight: bold !important;
}
</style>
