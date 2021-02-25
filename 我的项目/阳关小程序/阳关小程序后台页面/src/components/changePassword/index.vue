<template>
    <div>
        <el-dialog width="25%" title="修改密码" :visible.sync="dialogVisible" append-to-body>
            <el-form label-position="right" label-width="80px" :model="form" :rules="rules" ref="form">
                <el-form-item prop="oldPassword" label="旧密码">
                    <el-input type="password" placeholder="请输入旧密码" v-model="form.oldPassword"></el-input>
                </el-form-item>
                <el-form-item prop="newPassword" label="新密码">
                    <el-input type="password" placeholder="请输入新密码" v-model="form.newPassword"></el-input>
                </el-form-item>
                <!-- <el-form-item prop="newPassword" label="确认密码">
                    <el-input type="password" placeholder="请输入确认密码" v-model="form.newPassword"></el-input>
                </el-form-item> -->
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="handleSubmit">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import {updatePassword} from '@/api/user'

export default {
    data() {
        return {
            form: {},
            dialogVisible: false,
            rules: {
                oldPassword: [
                    {
                        required: true,
                        message: "请输入旧密码"
                    }
                ],
                newPassword: [
                    {
                        required: true,
                        message: "请输入新密码"
                    }
                ]
            }
        };
    },
    methods: {
        openDialog() {
            this.dialogVisible = true;
            this.form = {};
        },
        handleSubmit() {
            this.$refs.form.validate(valid => {
                if (valid) {
						updatePassword({
							id: this.$store.getters.id,
							password: this.form.oldPassword,
                      		password_xin: this.form.newPassword
                        })
                        .then(result => {
                            this.dialogVisible = false;
                            this.$message({
                                type: "success",
                                message: "修改成功"
                            });
                        });
                }
            });
        }
    }
};
</script>

<style scoped>

</style>