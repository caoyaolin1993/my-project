<template>
  <el-popover placement="bottom" width="180" trigger="click">
    <ul class="shekanUl">
      <div class="left">
        <li class="itemcode" v-for="item in codeListSlice[0]" :key="item.id">
          <i>{{item.id}}</i>&nbsp;
          <span>{{item.name}}</span>
        </li>
      </div>
      <div class="right">
        <li class="itemcode" v-for="item in codeListSlice[1]" :key="item.id">
          <i>{{item.id}}</i>&nbsp;
          <span>{{item.name}}</span>
        </li>
      </div>
    </ul>
    <i class="el-icon-question" slot="reference"></i>
  </el-popover>
</template>

<script>
export default {
  data() {
    return {
      codeList: [
        { id: "01", name: "花果山东" },
        { id: "02", name: "中新街" },
        { id: "03", name: "沙河" },
        { id: "04", name: "峰景" },
        { id: "05", name: "桃源" },
        { id: "06", name: "西丽" },
        { id: "07", name: "松坪山" },
        { id: "08", name: "福光" },
        { id: "09", name: "南山" },
        { id: "10", name: "龙坤" },
        { id: "11", name: "文竹园" },
        { id: "12", name: "海湾" },
        { id: "13", name: "赤湾" },
        { id: "14", name: "深圳湾" },
        { id: "15", name: "湾厦" },
        { id: "16", name: "海昌" },
        { id: "17", name: "桃花园" },
        { id: "18", name: "南水" },
        { id: "19", name: "风华" },
        { id: "20", name: "高新南" },
        { id: "21", name: "后海" },
        { id: "22", name: "慢病院" },
      ]
    };
  },
  computed: {
    codeListSlice() {
      //自动排列，两列分布均匀
      const firstLine = Math.ceil(this.codeList.length / 2); //向上取整
      const firLine_arr = this.codeList.slice(0, firstLine); //第一列为前一半向上取整
      //   console.log("codeListSlice -> firLine_arr", firLine_arr);
      const secLine_arr = this.codeList.slice(firstLine); //第一列为前一半向上取整
      //   console.log("codeListSlice -> secLine_arr", secLine_arr);
      return [firLine_arr, secLine_arr];
    }
  }
};
</script>

<style scoped>
/* App.vue 里面样式控制了全局的el-popover的宽为自适应（内容撑开） */
.shekanUl {
  padding: 0 4px;
  margin: 0;
}
.shekanUl .itemcode {
  list-style: none;
  font-size: 12px;
}
.shekanUl div.left {
  float: left;
  margin-right: 15px;
}
.shekanUl div.right {
  float: right;
}
.shekanUl .itemcode i {
  font-style: normal;
}
.el-icon-question{
	cursor: pointer;
	color: #909399;
}
</style>