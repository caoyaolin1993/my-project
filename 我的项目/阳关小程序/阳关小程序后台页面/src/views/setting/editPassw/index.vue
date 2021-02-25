<template>
  <div class="app-container editPassw">
    <el-form
      :model="formData"
	   status-icon
      :rules="rules"
      ref="ruleForm"
      :label-width="label_w"
      class="editPassw_ruleForm"
    >
      <el-form-item label="旧密码" prop="password">
        <el-input style="width:240px;" v-model="formData.password" auto-complete="off"></el-input>
      </el-form-item>
      <el-form-item label="新密码" prop="new_password">
        <el-input style="width:240px;" v-model="formData.new_password" auto-complete="off"></el-input>
      </el-form-item>
      <el-form-item label="确认密码" prop="re_new_password">
        <el-input style="width:240px;" v-model="formData.re_new_password" auto-complete="off"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { changePsw } from "@/api/setting/editPassw";
export default {
  data() {
	  var validatePass = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入密码'));
        } else {
          if (this.formData.re_new_password !== '') {
            this.$refs.ruleForm.validateField('re_new_password');
          }
          callback();
        }
      };
      var validatePass2 = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次输入密码'));
        } else if (value !== this.formData.new_password) {
          callback(new Error('两次输入密码不一致!'));
        } else {
          callback();
        }
      };
    return {
      label_w: "100px",
      formData: {
        password: "",
        new_password: "",
        re_new_password: ""
      },
      rules: {
        password: [
          { required: true, message: "请输入旧密码", trigger: "blur" }
        ],
        new_password: [
          { validator: validatePass, trigger: "blur" }
        ],
        re_new_password: [
          { validator: validatePass2, trigger: "blur" }
        ]
      }
    };
  },
  methods: {
    submitForm(formName) {
      //提交保存
      this.$refs[formName].validate(valid => {
        if (valid) {
          changePsw({
            token: this.$store.getters.token,
            ...this.formData
          }).then(res => {
              if (res.code !== 200) {
                this.$message.error(res.msg);
                return false;
              }
              this.$message.success("修改密码成功！");
          });
        }
      });
    }
  }
};
</script>

<style scoped>
.editPassw_ruleForm {
  margin-top: 20px;
}
</style>