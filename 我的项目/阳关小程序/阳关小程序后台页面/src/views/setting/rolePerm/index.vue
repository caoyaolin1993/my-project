<template>
  <div class="app-container rolePerm">
    <el-row style="margin-bottom:10px">
      <el-button type="primary" size="small" @click="addPopshow">新增角色</el-button>
    </el-row>
    <el-table
      v-loading="tab_loading"
      border
      :data="tableData"
      style="width: 800px"
      max-height="550"
    >
      <el-table-column label="序号" type="index" :index="indexMethod" width="60" align="center"></el-table-column>
      <el-table-column label="角色" prop="title" width="140" align="center"></el-table-column>
      <el-table-column label="权限" width="380" align="center">
        <template slot-scope="scope">
          <ul class="authorList">
            <li v-for="item in scope.row.menu_info" :key="item">{{item}}</li>
          </ul>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center">
        <template slot-scope="scope">
          <el-button size="mini" @click="handleEdit(scope.$index, scope.row)">修改</el-button>
          <el-button size="mini" type="danger" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :title="isAdd ? '新增角色' : '修改角色'" :visible.sync="dialogFormVisible" center>
      <el-form :model="form" :rules="rules" ref="ruleForm" label-width="88px">
        <el-form-item label="角色名称" prop="title">
          <el-input style="width:240px;" v-model="form.title" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="权限选择" prop="allcheckedRoles">
          <ul class="roles_table">
            <li
              v-for="(item, index) in form.menu"
              :key="item.id"
              :class="'roles_table_li'+(index+1)+''"
            >
              <h3>{{item.name}}</h3>
              <ul class="roles_l_c">
                <li
                  v-for="(item_c,index_c) in item.info"
                  :key="item_c.id"
                  :class="'roles_l_c_li'+(index_c+1)+''"
                >
                  <div class="roles_c">
                    <el-checkbox
                      v-model="form.menu[index].info[index_c].checkAll"
                      @change="handleCheckAllChange(index,index_c,$event)"
                    >{{ item_c.name }}</el-checkbox>
                  </div>
                  <!-- 有子级才渲染 -->
                  <ul v-if="item_c.info.length > 0" class="roles_l_cc">
                    <el-checkbox-group
                      v-model="form.menu[index].info[index_c].checkedRoles"
                      @change="handleCheckedRolesChange(index,index_c,$event)"
                    >
                      <el-checkbox
                        v-for="item_cc in item_c.info"
                        :key="item_cc.id"
                        :label="item_cc.id.toString()"
                      >{{item_cc.name}}</el-checkbox>
                    </el-checkbox-group>
                  </ul>
                </li>
              </ul>
            </li>
          </ul>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="author_Add('ruleForm')">提 交</el-button>
        <el-button @click="dialogFormVisible = false">取 消</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  authorList,
  authorAdd,
  authorInfo,
  authorEdit,
  authorDel
} from "@/api/setting/rolePerm";

export default {
  data() {
    return {
      tab_loading: true,
      tableData: [],
      dialogFormVisible: false,
      isAdd: true,
      currentId: null,
      form: {
        title: "",
      	allcheckedRoles: [],
        menu: [
          {
            id: 1,
            name: "用户",
            exist: "0",
            info: [
              {
                id: 2,
                name: "患者编码",
                exist: "0",
                info: [
                  {
                    id: 3,
                    name: "查看",
                    exist: "0"
                  },
                  {
                    id: 4,
                    name: "编辑",
                    exist: "0"
                  },
                  {
                    id: 5,
                    name: "导入导出",
                    exist: "0"
                  }
                ]
              },
              {
                id: 6,
                name: "用户管理",
                exist: "0",
                info: [
                  {
                    id: 7,
                    name: "查看",
                    exist: "0"
                  },
                  {
                    id: 8,
                    name: "修改",
                    exist: "0"
                  },
                  {
                    id: 9,
                    name: "导出",
                    exist: "0"
                  }
                ]
              }
            ]
          },
          {
            id: 10,
            name: "数据",
            exist: "0",
            info: [
              {
                id: 11,
                name: "问卷数据",
                exist: "0",
                info: [
                  {
                    id: 12,
                    name: "查看",
                    exist: "0"
                  },
                  {
                    id: 13,
                    name: "导出",
                    exist: "0"
                  }
                ]
              },
              {
                id: 14,
                name: "课程数据",
                exist: "0",
                info: [
                  {
                    id: 18,
                    name: "查看",
                    exist: "0"
                  },
                  {
                    id: 19,
                    name: "导出",
                    exist: "0"
                  }
                ]
              },
              {
                id: 17,
                name: "练习数据",
                exist: "0",
                info: [
                  {
                    id: 15,
                    name: "查看",
                    exist: "0"
                  },
                  {
                    id: 16,
                    name: "导出",
                    exist: "0"
                  }
                ]
              }
            ]
          },
          {
            id: 20,
            name: "设置",
            exist: "0",
            info: [
              {
                id: 21,
                name: "角色权限",
                exist: "0",
                info: []
              },
              {
                id: 22,
                name: "账号管理",
                exist: "0",
                info: []
              }
            ]
          }
        ]
      },
      rules: {
        title: [{ required: true, message: "请输入角色名称", trigger: "blur" }],
        allcheckedRoles: [
          {type: 'array', required: true, message: "请至少勾选一个权限", trigger: "click" }  //设置为点击触发，是在提交的时候验证
        ]
      }
    };
  },
  created() {
    this.getPagedata();
	this.disposalData(this.form.menu); //洗数据
    console.log("created -> this.form.menu", this.form.menu)
	
  },
  computed: {},
  methods: {
    getPagedata() {
      authorList({
        token: this.$store.getters.token
      }).then(res => {
        console.log("getPagedata -> res", res);
        this.tableData = res.data;
        this.tab_loading = false;
      });
    },
    indexMethod(index) {
      //序号
      return index + 1; //从0开始的必须加一
    },
    handleEdit(index, row) {
      //编辑角色权限
      authorInfo({
        id: row.id,
        token: this.$store.getters.token
      }).then(res => {
        if (res.code !== 200) {
          this.$message.error(res.msg);
          return false;
		}
		// 分开赋值,保留allcheckedRoles 验证字段
        this.form.title = res.data.title;
        this.form.menu = res.data.menu;
        this.disposalData(this.form.menu);
        this.dialogFormVisible = true;
        this.currentId = row.id;
        this.isAdd = false;
      });
    },
    handleDelete(index, row) {
      this.$confirm("删除后角色无法恢复，确定要删除吗?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          authorDel({
            id: row.id,
            token: this.$store.getters.token
          }).then(res => {
            if (res.code !== 200) {
              this.$message.error(res.msg);
              return false;
            }
            this.$message.success("删除成功！");
            this.getPagedata();
          });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "已取消删除"
          });
        });
    },
    addPopshow() {
      //新增角色弹窗
      this.dialogFormVisible = true;
      this.isAdd = true;
      this.form = this.$options.data().form; //初始值是没有添加$set新属性的,所以空白显示
      this.disposalData(this.form.menu); //洗数据
    },

    author_Add(formName) {
      // 新增角色/编辑角色提交
      // 遍历把选中的数组id全部拼起来
      let allcheckedRoles = []; 
      this.form.menu.forEach(item => {
        item.info.forEach(item_c => {
          if (item_c.checkAll) {
            //可以把'设置'的两个孤儿也带进去。
            allcheckedRoles.push(item_c.id.toString());
          }
          allcheckedRoles.push(...item_c.checkedRoles);
        });
      });
	  console.log("author_Add -> allcheckedRoles最后的id数组", allcheckedRoles);
      this.form.allcheckedRoles = allcheckedRoles; //选中的所有id组成的数组

      this.$refs[formName].validate(valid => {
      console.log("author_Add -> valid", valid)
        if (valid) {
          if (this.isAdd) {
            authorAdd({
              token: this.$store.getters.token,
              title: this.form.title,
              menu: this.form.allcheckedRoles
            }).then(res => {
              if (res.code !== 200) {
                this.$message.error(res.msg);
                return false;
              }
              this.$message.success("提交成功！");
              this.getPagedata();
              this.dialogFormVisible = false;
            });
          } else {
            authorEdit({
              id: this.currentId,
              title: this.form.title,
              menu: allcheckedRoles,
              token: this.$store.getters.token
            }).then(res => {
              if (res.code !== 200) {
                this.$message.error(res.msg);
                return false;
              }
              this.$message.success("修改成功！");
              this.getPagedata();
              this.dialogFormVisible = false;
            });
          }
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    handleCheckAllChange(index, index_c, e_val) {  //全选框
        console.log("handleCheckAllChange -> index", index);
        console.log("handleCheckAllChange -> index_c", index_c);
        console.log("handleCheckAllChange -> e_val", e_val);
      this.form.menu[index].info[index_c].checkedRoles = e_val
        ? this.form.menu[index].info[index_c].allroles
        : [];
    },
    handleCheckedRolesChange(index, index_c, e_val) {  //子选框
      //id数组
        console.log("handleCheckedRolesChange -> index", index);
        console.log("handleCheckedRolesChange -> index_c", index_c);
        console.log("handleCheckedRolesChange -> e_val", e_val);
      let checkedCount = e_val.length;
	  this.form.menu[index].info[index_c].checkAll = checkedCount > 0;
	//   通过判断是否包含这个选项id来判断是否选中，做出相应的操作
	  if(index == 0 && index_c == 0){ //第一列患者编码
		// 如果包含id:"4"编辑或者id:"5"导出，就选中id:"3"查看
		if(e_val.indexOf("4")!=-1 || e_val.indexOf("5")!=-1){
            console.log("handleCheckedRolesChange -> this.form.menu[index].info[index_c].checkedRoles", this.form.menu[index].info[index_c].checkedRoles)
			if(e_val.indexOf("3")==-1){
				// 没有id:"3"查看 就添加该选项
				this.form.menu[index].info[index_c].checkedRoles.push("3")
			}
		}
	  }else if(index == 0 && index_c == 1){ //第二列用户管理
		// 如果包含id:"8"编辑或者id:"9"导出，就选中id:"7"查看
		if(e_val.indexOf("8")!=-1 || e_val.indexOf("9")!=-1){
            console.log("handleCheckedRolesChange -> this.form.menu[index].info[index_c].checkedRoles", this.form.menu[index].info[index_c].checkedRoles)
			if(e_val.indexOf("7")==-1){
				// 没有id:"7"查看 就添加该选项
				this.form.menu[index].info[index_c].checkedRoles.push("7")
			}
		}
	  }else if(index == 1 && index_c == 0){ //第三列问卷数据
		// 如果包含id:"13"导出，就选中id:"12"查看
		if(e_val.indexOf("13")!=-1){
            console.log("handleCheckedRolesChange -> this.form.menu[index].info[index_c].checkedRoles", this.form.menu[index].info[index_c].checkedRoles)
			if(e_val.indexOf("12")==-1){
				// 没有id:"12"查看 就添加该选项
				this.form.menu[index].info[index_c].checkedRoles.push("12")
			}
		}
	  }else if(index == 1 && index_c == 1){ //第四列课程数据
		// 如果包含id:"19"导出，就选中id:"18"查看
		if(e_val.indexOf("19")!=-1){
            console.log("handleCheckedRolesChange -> this.form.menu[index].info[index_c].checkedRoles", this.form.menu[index].info[index_c].checkedRoles)
			if(e_val.indexOf("18")==-1){
				// 没有id:"18"查看 就添加该选项
				this.form.menu[index].info[index_c].checkedRoles.push("18")
			}
		}
	  }else if(index == 1 && index_c == 2){ //第五列练习数据
		// 如果包含id:"16"导出，就选中id:"15"查看
		if(e_val.indexOf("16")!=-1){
            console.log("handleCheckedRolesChange -> this.form.menu[index].info[index_c].checkedRoles", this.form.menu[index].info[index_c].checkedRoles)
			if(e_val.indexOf("15")==-1){
				// 没有id:"15"查看 就添加该选项
				this.form.menu[index].info[index_c].checkedRoles.push("15")
			}
		}
	  }
    },
    // 整理数据
    disposalData(roleArr) {
      roleArr.forEach((item, index) => {
        item.info.forEach((item_c, index_c) => {
          let allroles = []; //所有选项id
          let checkedRoles = [];  //选中的选项id
          item_c.info.forEach((item_cc, index_cc) => {
            allroles.push(item_cc.id.toString());  //全选需要这组所有的id
            if (item_cc.exist === "1") {
              checkedRoles.push(item_cc.id.toString());  //这组选中的id
            }
          });

          let ischeckAll = checkedRoles.length > 0;  //只要选中一项，父选项就选中
          if (item_c.info.length === 0) {  //设置模块  没有子选项，如果存在就直接设置为true
            ischeckAll = item_c.exist === "1";
          }
          this.$set(
            this.form.menu[index].info[index_c],
            "checkAll",
            ischeckAll
          ); //是否全选
          this.$set(this.form.menu[index].info[index_c], "allroles", allroles); //一列中所有id的集合（全选的数据）
          this.$set(
            this.form.menu[index].info[index_c],
            "checkedRoles",
            checkedRoles
          ); //默认已选数据
        });
      });
      //   this.form.menu = roleArr;
      console.log("roleArr", this.form.menu);
    }
  }
};
</script>

<style scoped>
.authorList {
  padding: 0 40px;
  margin: 0;
  text-align: left;
}
.authorList li {
  list-style: none;
}
/* 表格样式 */
.roles_table,
.roles_l_c,
.roles_l_cc {
  padding: 0;
  margin: 0;
}
.roles_table {
  border-radius: 4px;
  border: 1px solid #dcdfe6;
  display: inline-block;
  position: relative;
  overflow: hidden;
}

.roles_table li {
  list-style: none;
}
.roles_l_c {
  display: flex;
}
.roles_table h3 {
  padding: 0;
  margin: 0;
  text-align: center;
}
.roles_table > li {
  display: inline-block;
  vertical-align: middle;
  padding: 0;
  margin: 0;
  width: 260px;
  height: 250px;
  border-right: 1px solid #dcdfe6;
}
.roles_table > li:last-of-type {
  border-right: none;
}
.roles_table > li:nth-of-type(2) {
  width: 390px;
}
.roles_l_c {
  height: 84%;
}
.roles_l_c > li {
  width: 50%;
  height: 100%;
  border-top: 1px solid #dcdfe6;
}
.roles_l_c > li:first-of-type {
  border-right: 1px solid #dcdfe6;
}
.roles_table_li2 .roles_l_c .roles_l_c_li2 {
  border-right: 1px solid #dcdfe6;
}
.roles_c {
  border-bottom: 1px solid #dcdfe6;
  text-align: center;
}
.roles_l_cc {
  padding-left: 24px;
  padding-top: 16px;
}
.roles_table_li3 li .roles_c {
  margin-top: 58px;
  border: none;
}
</style>

<style>
/* 弹窗变大 */
.rolePerm .el-dialog {
  width: 1100px;
}
</style>