<template>
  <div class="app-container accManage">
    <el-row style="margin-bottom:10px">
      <el-button type="primary" size="small" @click="addPopshow">新增账号</el-button>
    </el-row>
    <el-table
      v-loading="tab_loading"
      border
      :data="tableData"
      style="width: 800px"
      max-height="550"
    >
      <el-table-column label="序号" type="index" :index="indexMethod" width="60" align="center"></el-table-column>
      <el-table-column label="账号" prop="account" align="center"></el-table-column>
      <el-table-column label="姓名" prop="name" align="center"></el-table-column>
      <el-table-column label="手机号" prop="phone" align="center"></el-table-column>
      <el-table-column label="角色" prop="role" align="center"></el-table-column>
      <el-table-column label="操作" align="center">
        <template slot-scope="scope">
          <el-button size="mini" @click="handleEdit(scope.$index, scope.row)">修改</el-button>
          <el-button size="mini" type="danger" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog :title="isAdd ? '新增账号' : '修改账号信息'" :visible.sync="dialogFormVisible" width="568px" center>
      <el-form :model="formData" :rules="rules" ref="ruleForm" :label-width="label_w">
        <el-form-item label="账号" prop="account">
          <el-input style="width:240px;" v-model.trim="formData.account" auto-complete="off"></el-input>
        </el-form-item>
		<!-- 增加或登录账户为超级管理员时显示 -->
        <el-form-item v-if="isAdd" label="密码" prop="password">
          <el-input style="width:240px;" v-model.trim="formData.password" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item v-else-if="$store.getters.auth == '超级管理员'" label="密码">
          <el-input style="width:240px;" v-model.trim="formData.password" auto-complete="off" placeholder="***"></el-input>
        </el-form-item>
        <el-form-item label="姓名" prop="name">
          <el-input style="width:240px;" v-model.trim="formData.name" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="手机号" prop="phone">
          <el-input style="width:240px;" v-model.trim="formData.phone" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="角色" prop="role_id">
          <el-select style="width:240px;" v-model="formData.role_id" placeholder="角色选择">
            <el-option
              v-for="item in rolesList"
              :key="item.id"
              :label="item.title"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="submit_Account('ruleForm')">提 交</el-button>
        <el-button @click="dialogFormVisible = false">取 消</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { categoryList, roleInfo, categoryAdd, categoryInfo, categoryEdit, categoryDel } from "@/api/setting/accManage";
export default {
  data() {
    return {
      tab_loading: true,
      tableData: [],
      label_w: "140px",
      dialogFormVisible: false,
      isAdd: true,
      currentId: null,
      formData: {
        account: "",
        password: "",
        name: "",
        phone: "",
        role_id: ""
      },

      rolesList: [],
      rules: {
        account: [{ required: true, message: "请输入账号", trigger: "blur" }],
        password: [{ required: true, message: "请输入密码", trigger: "blur" }],
        name: [{ required: true, message: "请输入姓名", trigger: "blur" }],
        phone: [{ required: true, message: "请输入手机号", trigger: "blur" }],
        role_id: [{ required: true, message: "请选择角色", trigger: "blur" }]
      }
    };
  },
  created() {
    this.get_accountData();
    this.role_Info();
  },
  methods: {
    get_accountData() {
      categoryList({
        token: this.$store.getters.token
      }).then(res => {
        if (res.code !== 200) {
          this.$message.error(res.msg);
          return false;
        }
        this.tableData = res.data;
        this.tab_loading = false;
      });
    },
    addPopshow() {
      this.isAdd = true;
	  this.dialogFormVisible = true;
	  this.formData = this.$options.data().formData
    },
    role_Info() {
      roleInfo({
        token: this.$store.getters.token
      }).then(res => {
        this.rolesList = res.data;
      });
    },
    indexMethod(index) {
      //序号
      return index + 1; //从0开始的必须加一
    },
    submit_Account(formName) {
      //提交保存
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (this.isAdd) {
            categoryAdd({
              //增加
              ...this.formData,
              token: this.$store.getters.token
            }).then(res => {
              if (res.code !== 200) {
                this.$message.error(res.msg);
                return false;
              }
              this.$message.success("提交成功！");
              this.get_accountData();
              this.dialogFormVisible = false;
            });
          } else {
			  let editData = {
				id: this.currentId,
				account: this.formData.account,
				name: this.formData.name,
				phone: this.formData.phone,
				role_id: this.formData.role_id,
				token: this.$store.getters.token
			  }
			  
			//   如果登录账户为超级管理员，就可以修改其他所有账户密码
			  if(this.$store.getters.auth == '超级管理员'){
				  if(this.formData.password){
				  	editData.password = this.formData.password;
				  }
			  }
			  categoryEdit(editData).then( res => {
				if (res.code !== 200) {
					this.$message.error(res.msg);
					return false;
				}
				this.$message.success("修改成功！");
				this.get_accountData();
				this.dialogFormVisible = false;
			  })
          }
        }
      });
    },
    handleEdit(index, row) {
      this.isAdd = false;
	  this.dialogFormVisible = true;
	  this.currentId = row.id
	  categoryInfo({
        id: row.id,
		token: this.$store.getters.token
		}).then( res => {
			if (res.code !== 200) {
				this.$message.error(res.msg);
				return false;
			}
			this.formData = res.data;
		})
    },
    handleDelete(index, row) {
      this.$confirm("删除后账号无法恢复，确定要删除吗?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          categoryDel({
            id: row.id,
            token: this.$store.getters.token
          }).then(res => {
            if (res.code !== 200) {
              this.$message.error(res.msg);
              return false;
            }
            this.$message.success("删除成功！");
            this.get_accountData();
          });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "已取消删除"
          });
        });
	}
  }
};
</script>

<style>
</style>