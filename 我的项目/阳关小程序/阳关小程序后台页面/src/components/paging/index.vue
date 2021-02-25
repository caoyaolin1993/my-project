<template>
    <div>
        <el-pagination style="display:inline-block;vertical-align:middle" background @size-change="handleSizeChange" :current-page="pageIndex" @current-change="currentChange" :page-sizes="[20, 50, 100]" :page-size="pageSize" layout="total, sizes, prev, pager, next, jumper" :total="total">
		</el-pagination>
		<div style="display:inline-block;vertical-align:middle">
			<el-button plain type="mini" @click="jumpChange">GO</el-button>
		</div>
    </div>
</template>
<script>
export default {
  props: {
    total: {
      type: Number
    },
    pageIndex: {
      type: Number,
      default: 1
    }
  },
  methods: {
    handleSizeChange(val) {
      this.pageSize = val;
      this.indexInit();
    },
    currentChange(val) {
      this.$emit("update:pageIndex", val);
      this.pageChange();
    },
    pageChange() {
      this.$emit("pageChange", {
        pageSize: this.pageSize,
        pageIndex: this.pageIndex
      });
    },
    //回到第一页，并刷新
    indexInit() {
      if (this.pageIndex != 1) {
        this.$emit("update:pageIndex", 1);
      }
      this.pageChange();
	},
	jumpChange() { //自定义事件GO跳转
      this.pageChange();
	}
  },
  data() {
    return {
      pageSize: 20
    };
  },
  created() {
    this.pageChange();
  }
};
</script>

<style scoped>
div {
  margin: 10px 0;
}
</style>
