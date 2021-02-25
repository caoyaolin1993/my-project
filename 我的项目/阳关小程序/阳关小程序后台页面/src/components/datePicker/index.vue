<template>
  <el-date-picker
    v-model="dateRange"
    type="daterange"
    range-separator="至"
    start-placeholder="开始日期"
    end-placeholder="结束日期"
    value-format="yyyy-MM-dd"
    format="yyyy-MM-dd"
    unlink-panels
    :picker-options="pickerOptions"
    align="left"
	class="el-date-picker"
	size="small"
  >
  </el-date-picker>
</template>

<script>
export default {
  data() {
    return {
      dateRange: null,
      pickerOptions: {
        shortcuts: [
          {
            text: "最近一周",
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit("pick", [start, end]);
            }
          },
          {
            text: "最近一个月",
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit("pick", [start, end]);
            }
          },
          {
            text: "最近三个月",
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit("pick", [start, end]);
            }
          }
        ]
      }
    };
  },
  watch: {
    dateRange(to, from) {
      let startTime = this.dateRange ? this.dateRange[0] : null;
      let endTime = this.dateRange ? this.dateRange[1] + " 23:59:59" : null;

      this.$emit("change", startTime, endTime);
    }
  }
};
</script>

<style>
.el-date-picker .el-range-separator{ 
	/* 修复 至 字 被截掉部分*/
	width: 8%;
}
</style>