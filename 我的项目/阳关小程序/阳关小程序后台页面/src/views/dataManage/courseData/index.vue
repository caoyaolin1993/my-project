<template>
  <div class="app-container courseData">
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <el-tab-pane label="课程统计" name="first">
        <el-row style="margin-bottom:10px">
          <el-button type="primary" size="small" @click="export_excel1"
            >导出</el-button
          >
        </el-row>
        <el-table
          v-loading="tab_loading"
          border
          :data="tableData"
          style="width: 100%"
          max-height="550"
        >
          <el-table-column
            label="课程编号"
            prop="course"
            align="center"
          ></el-table-column>
          <el-table-column label="总点击次数" align="center">
            <template slot-scope="scope">
              <el-button
                size="mini"
                type="text"
                @click="skipwith_counts(scope.$index, scope.row)"
                >{{ scope.row.counts }}</el-button
              >
            </template>
          </el-table-column>
          <el-table-column
            label="完成课程人数"
            prop="finish_study"
            align="center"
          ></el-table-column>
          <el-table-column label="总回看次数" align="center">
            <template slot-scope="scope">
              <el-button
                size="mini"
                type="text"
                @click="skipwith_reviweNum(scope.$index, scope.row)"
                >{{ scope.row.reviwe_num }}</el-button
              >
            </template>
          </el-table-column>
          <el-table-column label="总转发次数" align="center">
            <template slot-scope="scope">
              <el-button
                size="mini"
                type="text"
                @click="skipwith_shareNum(scope.$index, scope.row)"
                >{{ scope.row.share_num }}</el-button
              >
            </template>
          </el-table-column>
          <el-table-column
            label="总学习时长"
            prop="study_time"
            align="center"
          ></el-table-column>
          <el-table-column
            label="平均每次学习时长"
            prop="average_time"
            align="center"
          ></el-table-column>
        </el-table>
      </el-tab-pane>
      <el-tab-pane label="课程学习分布" name="second">
        <el-row style="margin-bottom:10px">
          <el-button type="primary" size="small" @click="export_excel2"
            >导出</el-button
          >
        </el-row>

        <el-table
          v-loading="tab_loading"
          border
          :data="S1"
          style="width:981px"
          max-height="550"
        >
          <el-table-column
            label="课程编号"
            prop="s"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画1"
            prop="v1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互1"
            prop="p1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画2"
            prop="v2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互2"
            prop="p2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画3"
            prop="v3"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互3(E)"
            prop="p3"
            width="140"
          ></el-table-column>
        </el-table>

        <el-table
          v-loading="tab_loading"
          border
          :data="S2"
          style="width:1121px"
          max-height="550"
        >
          <el-table-column
            label="课程编号"
            prop="s"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画1"
            prop="v1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互1"
            prop="p1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画2"
            prop="v2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互2"
            prop="p2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画3"
            prop="v3"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互3"
            prop="p3"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画4(E)"
            prop="p3"
            width="140"
          ></el-table-column>
        </el-table>

        <el-table
          v-loading="tab_loading"
          border
          :data="S3"
          style="width:1261px"
          max-height="550"
        >
          <el-table-column
            label="课程编号"
            prop="s"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画1"
            prop="v1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互1"
            prop="p1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画2"
            prop="v2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互2"
            prop="p2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画3"
            prop="v3"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互3"
            prop="p3"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互4"
            prop="p4"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画4(E)"
            prop="v4"
            width="140"
          ></el-table-column>
        </el-table>

        <el-table
          v-loading="tab_loading"
          border
          :data="S4"
          style="width:981px"
          max-height="550"
        >
          <el-table-column
            label="课程编号"
            prop="s"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画1"
            prop="v1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互1"
            prop="p1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画2"
            prop="v2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互2"
            prop="p2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互3"
            prop="p3"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画3(E)"
            prop="v3"
            width="140"
          ></el-table-column>
        </el-table>

        <el-table
          v-loading="tab_loading"
          border
          :data="S5"
          style="width:981px"
          max-height="550"
        >
          <el-table-column
            label="课程编号"
            prop="s"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画1"
            prop="v1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互1"
            prop="p1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画2"
            prop="v2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互2"
            prop="p2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互3"
            prop="p3"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画3(E)"
            prop="v3"
            width="140"
          ></el-table-column>
        </el-table>

        <el-table
          v-loading="tab_loading"
          border
          :data="S6"
          style="width:841px"
          max-height="550"
        >
          <el-table-column
            label="课程编号"
            prop="s"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画1"
            prop="v1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互1"
            prop="p1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画2"
            prop="v2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互2"
            prop="p2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画3(E)"
            prop="v3"
            width="140"
          ></el-table-column>
        </el-table>

        <el-table
          v-loading="tab_loading"
          border
          :data="S7"
          style="width:701px"
          max-height="550"
        >
          <el-table-column
            label="课程编号"
            prop="s"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画1"
            prop="v1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互1"
            prop="p1"
            width="140"
          ></el-table-column>
          <el-table-column
            label="动画2"
            prop="v2"
            width="140"
          ></el-table-column>
          <el-table-column
            label="交互2(E)"
            prop="p2"
            width="140"
          ></el-table-column>
        </el-table>
      </el-tab-pane>
      <el-tab-pane label="学习统计" name="third">
        <el-row style="margin-bottom:10px">
          <div class="skcode_box" style="display:inline-block">
            <el-input
              size="small"
              style="width:150px"
              placeholder="编码"
              prefix-icon="el-icon-search"
              v-model.trim="searchCode"
              @keyup.enter.native="search_data"
            ></el-input>
            <!-- 编码对应社康显示 -->
            <codePopover></codePopover>
          </div>
          <el-input
            size="small"
            style="width:100px;margin-left:20px;"
            placeholder="姓名"
            prefix-icon="el-icon-search"
            v-model.trim="searchName"
            @keyup.enter.native="search_data"
          ></el-input>
          <el-input
            size="small"
            style="width:150px"
            placeholder="微信手机"
            prefix-icon="el-icon-search"
            v-model.trim="searchPhone"
            @keyup.enter.native="search_data"
          ></el-input>
          <el-select
            size="small"
            v-model="userType"
            @change="userType_change"
            multiple
            collapse-tags
            placeholder="选择用户分类"
            style="width:160px"
          >
            <el-option
              v-for="item in userOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <!-- <el-select
            size="small"
            v-model="isFinish"
            @change="finish_change"
            multiple
            collapse-tags
            placeholder="是否学完"
            style="width:140px"
          >
            <el-option
              v-for="item in finishOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>-->
          <el-button type="primary" size="small" @click="search_data"
            >搜索</el-button
          >
          <el-button type="primary" size="small" @click="export_excel3"
            >导出</el-button
          >
        </el-row>

        <el-table
          v-loading="tab_loading"
          border
          :data="tableData"
          :span-method="arraySpanMethod"
          style="width: 100%"
          max-height="550"
          cell-class-name="cust-tabstyle"
        >
          <el-table-column label="序号" prop="id" width="60"></el-table-column>
          <el-table-column label="用户ID" prop="open_id"></el-table-column>
          <el-table-column label="编码" prop="number"></el-table-column>
          <el-table-column label="姓名" prop="name"></el-table-column>
          <el-table-column label="微信手机" prop="wx_phone"></el-table-column>
          <el-table-column label="用户分类" prop="type_name"></el-table-column>
          <el-table-column label="课程编号" prop="course"></el-table-column>
          <el-table-column label="总点击次数" prop="counts"></el-table-column>
          <el-table-column label="回看次数" prop="reviwe_num"></el-table-column>
          <el-table-column
            label="总学习时长"
            prop="study_time"
            width="160"
          ></el-table-column>
          <el-table-column
            label="平均每次学习时长"
            prop="average_time"
            width="160"
          ></el-table-column>
          <el-table-column
            label="是否学完"
            prop="study_finish"
          ></el-table-column>
          <el-table-column
            label="完成时间"
            prop="study_finish_time"
          ></el-table-column>
        </el-table>
      </el-tab-pane>
      <el-tab-pane label="学习详情" name="fourth">
        <el-row style="margin-bottom:10px">
          <datePicker @change="handleDateChange"></datePicker>
          <div class="skcode_box" style="display:inline-block">
            <el-input
              size="small"
              style="width:150px"
              placeholder="编码"
              prefix-icon="el-icon-search"
              v-model.trim="searchCode"
              @keyup.enter.native="search_data"
            ></el-input>
            <!-- 编码对应社康显示 -->
            <codePopover></codePopover>
          </div>
          <el-input
            size="small"
            style="width:100px;margin-left:20px;"
            placeholder="姓名"
            prefix-icon="el-icon-search"
            v-model.trim="searchName"
            @keyup.enter.native="search_data"
          ></el-input>
          <el-input
            size="small"
            style="width:150px"
            placeholder="微信手机"
            prefix-icon="el-icon-search"
            v-model.trim="searchPhone"
            @keyup.enter.native="search_data"
          ></el-input>
          <el-select
            size="small"
            v-model="userType"
            @change="userType_change"
            multiple
            collapse-tags
            placeholder="选择用户分类"
            style="width:160px"
          >
            <el-option
              v-for="item in userOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <el-select
            size="small"
            v-model="courseType4"
            @change="courseType4_change"
            multiple
            collapse-tags
            placeholder="课程编号"
            style="width:140px"
          >
            <el-option
              v-for="item in courseNumOptions4"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <el-select
            size="small"
            v-model="isForward"
            @change="forward_change"
            multiple
            collapse-tags
            placeholder="是否转发"
            style="width:140px"
          >
            <el-option
              v-for="item in forwardOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <el-select
            size="small"
            v-model="isLookback"
            @change="lookback_change"
            multiple
            collapse-tags
            placeholder="是否回看"
            style="width:140px"
          >
            <el-option
              v-for="item in lookbackOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <el-button type="primary" size="small" @click="search_data"
            >搜索</el-button
          >
          <el-button
            v-if="cur_roles[1].exist == '1'"
            type="primary"
            size="small"
            @click="export_excel4"
            >导出</el-button
          >
        </el-row>

        <el-table
          v-loading="tab_loading"
          border
          :data="tableData"
          style="width: 100%"
          max-height="550"
        >
          <el-table-column
            label="序号"
            type="index"
            :index="indexMethod"
            width="60"
          ></el-table-column>
          <el-table-column label="用户ID" prop="open_id"></el-table-column>
          <el-table-column label="编码" prop="number"></el-table-column>
          <el-table-column label="姓名" prop="name"></el-table-column>
          <el-table-column label="微信手机" prop="wx_phone"></el-table-column>
          <el-table-column label="用户分类" prop="type_name"></el-table-column>
          <el-table-column label="课程编号" prop="courses"></el-table-column>
          <el-table-column label="学习开始时间" prop="stime"></el-table-column>
          <el-table-column label="学习结束时间" prop="etime"></el-table-column>
          <el-table-column label="学习时长" prop="long_time"></el-table-column>
          <el-table-column
            label="开始节点"
            prop="content_start"
          ></el-table-column>
          <el-table-column
            label="结束节点"
            prop="content_end"
          ></el-table-column>
          <el-table-column label="是否转发" prop="share_name"></el-table-column>
          <el-table-column label="是否回看" prop="new_name"></el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>
        <paging ref="paging" :pageIndex.sync="pageIndex" :total="total" @pageChange="pageChange"></paging>
  </div>
</template>

<script>
// 导入组件和方法
import {
  course_count,
  excel_course_count,
  course_study_dist,
  excel_study_dist,
  study_count,
  excel_study_count,
  course_info,
  excel_course_info
} from "@/api/dataManage/courseData";
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
    return {
      S1: [], //S1学习分布
      S2: [], //S2学习分布
      S3: [], //S3学习分布
      S4: [], //S4学习分布
      S5: [], //S5学习分布
      S6: [], //S6学习分布
      S7: [], //S7学习分布
      tab_loading: true,
      activeName: "first", //默认显示第一个标签
      tableData: [],
      startTime: null,
      endTime: null,
      searchCode: "",
      searchName: "",
      searchPhone: "",
      userOptions: [
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
      ],
      userType: [], //用户分类
      //   信息健康_课程编号
      courseType1: [],
      courseNumOptions1: [
        {
          value: "10",
          label: "全部"
        },
        {
          value: "1",
          label: "S1"
        },
        {
          value: "w1",
          label: "E-1周"
        },
        {
          value: "m1",
          label: "E-1月"
        },
        {
          value: "m3",
          label: "E-3月"
        },
        {
          value: "m6",
          label: "E-6月"
        },
        {
          value: "y1",
          label: "E-1年"
        }
      ],
      //   课前问卷_课程编号
      courseType2: [],
      courseNumOptions2: [
        {
          value: "10",
          label: "全部"
        },
        {
          value: "1",
          label: "S1"
        },
        {
          value: "2",
          label: "S2"
        },
        {
          value: "3",
          label: "S3"
        },
        {
          value: "4",
          label: "S4"
        },
        {
          value: "5",
          label: "S5"
        },
        {
          value: "6",
          label: "S6"
        },
        {
          value: "7",
          label: "S7"
        },
        {
          value: "w1",
          label: "E-1周"
        },
        {
          value: "m1",
          label: "E-1月"
        },
        {
          value: "m3",
          label: "E-3月"
        },
        {
          value: "m6",
          label: "E-6月"
        },
        {
          value: "y1",
          label: "E-1年"
        }
      ],

      //  心情记录_课程编号
      courseType3: [],
      courseNumOptions3: [
        {
          value: "10",
          label: "全部"
        },
        {
          value: "a",
          label: "A"
        },
        {
          value: "1",
          label: "S1"
        },
        {
          value: "2",
          label: "S2"
        },
        {
          value: "3",
          label: "S3"
        },
        {
          value: "4",
          label: "S4"
        },
        {
          value: "5",
          label: "S5"
        },
        {
          value: "6",
          label: "S6"
        },
        {
          value: "7",
          label: "S7"
        },
        {
          value: "w1",
          label: "E-1周"
        },
        {
          value: "m1",
          label: "E-1月"
        },
        {
          value: "m3",
          label: "E-3月"
        },
        {
          value: "m6",
          label: "E-6月"
        },
        {
          value: "y1",
          label: "E-1年"
        }
      ],

      //  课后反馈问卷_课程编号
      courseType4: [],
      courseNumOptions4: [
        {
          value: "10",
          label: "全部"
        },
        {
          value: "1",
          label: "S1"
        },
        {
          value: "2",
          label: "S2"
        },
        {
          value: "3",
          label: "S3"
        },
        {
          value: "4",
          label: "S4"
        },
        {
          value: "5",
          label: "S5"
        },
        {
          value: "6",
          label: "S6"
        },
        {
          value: "7",
          label: "S7"
        }
      ],
      //   isFinish: "",
      //   finishOptions: [
      //     {
      //       value: "1",
      //       label: "是"
      //     },
      //     {
      //       value: "2",
      //       label: "否"
      //     }
      //   ],

      isForward: [],
      forwardOptions: [
        //是否转发
        {
          value: "10",
          label: "全部"
        },
        {
          value: "1",
          label: "是"
        },
        {
          value: "2",
          label: "否"
        }
      ],
      isLookback: [],
      lookbackOptions: [
        //是否回看
        {
          value: "10",
          label: "全部"
        },
        {
          value: "2",
          label: "是"
        },
        {
          value: "1",
          label: "否"
        }
      ],
      total: 0,
      pageIndex: 1,
      pageSize: 20
    };
  },
  computed: {
    cur_roles() {
      return this.$store.getters.roles[1].info[1].info; //进入按钮权限的层级
    }
  },
  methods: {
    handleClick(tab, event) {
      //   console.log(tab, event);
      Object.assign(this.$data, this.$options.data()); //初始化组件的data 为默认值
      this.activeName = tab.name; //watch会监听this.activeName变化时去请求数据，必须在每次重新初始化data之后再操作。
      this.getPageData(); //这里请求的是不同的数据
    },
    handleDateChange(start, end) {
      this.startTime = start;
      this.endTime = end;
      this.$refs.paging.indexInit();
    },
    userType_change() {
      this.$refs.paging.indexInit();
    },
    courseType1_change() {
      this.$refs.paging.indexInit();
    },
    courseType2_change() {
      this.$refs.paging.indexInit();
    },
    courseType3_change() {
      this.$refs.paging.indexInit();
    },
    courseType4_change() {
      this.$refs.paging.indexInit();
    },
    // finish_change() {
    //   //是否学完
    //   this.$refs.paging.indexInit();
    // },
    forward_change() {
      //是否转发
      this.$refs.paging.indexInit();
    },
    lookback_change() {
      //是否回看
      this.$refs.paging.indexInit();
    },
    getPageData() {
      if (this.activeName == "first") {
        course_count({
          token: this.$store.getters.token
        }).then(res => {
          //课程统计
          this.tableData = res.data;
          this.tab_loading = false;
        });
      } else if (this.activeName == "second") {
        // 课程学习分布
        course_study_dist({
          token: this.$store.getters.token
        }).then(res => {
          this.S1 = res.data[0];
          this.S2 = res.data[1];
          this.S3 = res.data[2];
          this.S4 = res.data[3];
          this.S5 = res.data[4];
          this.S6 = res.data[5];
          this.S7 = res.data[6];
          this.tab_loading = false;
        });
      } else if (this.activeName == "third") {
        // 学习统计
        study_count({
          token: this.$store.getters.token,
          number: this.searchCode,
          type: this.userType,
          name: this.searchName,
          phone: this.searchPhone,
          limit: this.pageSize,
          page: this.pageIndex
        }).then(res => {
          let tableData_bef = res.data.list;
          let newtableData = [];
          tableData_bef.forEach((item, index) => {
            for (let i = 0; i < item.other.length; i++) {
              let current = {
                id: index + 1,
                open_id: item.open_id,
                number: item.number,
                name: item.name,
                wx_phone: item.wx_phone,
                type_name: item.type_name,
                course: item.other[i].course,
                counts: item.other[i].counts,
                reviwe_num: item.other[i].reviwe_num,
                study_time: item.other[i].study_time,
                average_time: item.other[i].average_time,
                study_finish: item.other[i].study_finish,
                study_finish_time: item.other[i].study_finish_time
              };
              newtableData.push(current);
            }
          });
          this.tableData = newtableData;
          this.total = res.data.total;
          this.tab_loading = false;
        });
      } else {
        //学习详情
        course_info({
          token: this.$store.getters.token,
          stime: this.startTime,
          etime: this.endTime,
          number: this.searchCode,
          name: this.searchName,
          phone: this.searchPhone,
          type: this.userType,
          course: this.courseType4,
          share: this.isForward,
          new: this.isLookback,
          limit: this.pageSize,
          page: this.pageIndex
        }).then(res => {
          this.tableData = res.data.list;
          this.total = res.data.total;
          this.tab_loading = false;
        });
      }
    },
    indexMethod(index) {
      //序号
      return index + 1; //从0开始的必须加一
    },
    search_data() {
      //整个页面搜索共用函数
      this.getPageData();
    },
    export_excel1() {
      //导出信息健康excel
      excel_course_count({
        token: this.$store.getters.token
        // limit: this.pageSize,
        // page: this.pageIndex,
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "课程统计");
          this.$message({
            type: "warning",
            message: "开始下载！"
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    export_excel2() {
      //导出课前问卷excel
      excel_study_dist({
        token: this.$store.getters.token,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "课程学习分布");
          this.$message({
            type: "warning",
            message: "开始下载！"
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    export_excel3() {
      //导出心情记录excel
      excel_study_count({
        token: this.$store.getters.token,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "学习统计");
          this.$message({
            type: "warning",
            message: "开始下载！"
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    export_excel4() {
      //导出课后反馈问卷excel
      excel_course_info({
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        name: this.searchName,
        phone: this.searchPhone,
        type: this.userType,
        course: this.courseType4,
        share: this.isForward,
        new: this.isLookback,
        limit: this.pageSize,
        page: this.pageIndex
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "学习详情");
          this.$message({
            type: "warning",
            message: "开始下载！"
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    pageChange(data) {
      //页面条数改动
      this.pageSize = data.pageSize;
      this.getPageData();
    },
    // 合并单元格
    arraySpanMethod({ row, column, rowIndex, columnIndex }) {
      if (columnIndex < 6) {
        //当前列号<6的
        if (rowIndex % 8 === 0) {
          //当前行号能够被8整除的就执行下面合并
          return [8, 1]; //合并8行  1列
        } else {
          return [0, 0];
        }
      }
    },
    skipwith_counts(index, row) {
      // console.log(row.course);  //动态传课程编号
      let course = this.courseType4; //赋值初始数据
      switch (row.course) {
        case "S1":
          course = ["1"];
          break;
        case "S2":
          course = ["2"];
          break;
        case "S3":
          course = ["3"];
          break;
        case "S4":
          course = ["4"];
          break;
        case "S5":
          course = ["5"];
          break;
        case "S6":
          course = ["6"];
          break;
        case "S7":
          course = ["7"];
          break;
      }
      this.courseType4 = course;
      this.activeName = "fourth";
      this.getPageData();
    },
    skipwith_reviweNum(index, row) {
      let course = this.courseType4; //赋值初始数据
      switch (row.course) {
        case "S1":
          course = ["1"];
          break;
        case "S2":
          course = ["2"];
          break;
        case "S3":
          course = ["3"];
          break;
        case "S4":
          course = ["4"];
          break;
        case "S5":
          course = ["5"];
          break;
        case "S6":
          course = ["6"];
          break;
        case "S7":
          course = ["7"];
          break;
      }
      this.courseType4 = course;
      this.isLookback = ["2"]; //查询回看
      this.activeName = "fourth";
      this.getPageData();
      // console.log(row.course,'是否回看为是:2');
    },
    skipwith_shareNum(index, row) {
      let course = this.courseType4; //赋值初始数据
      switch (row.course) {
        case "S1":
          course = ["1"];
          break;
        case "S2":
          course = ["2"];
          break;
        case "S3":
          course = ["3"];
          break;
        case "S4":
          course = ["4"];
          break;
        case "S5":
          course = ["5"];
          break;
        case "S6":
          course = ["6"];
          break;
        case "S7":
          course = ["7"];
          break;
      }
      this.courseType4 = course;
      this.isForward = ["1"]; //查询转发
      this.activeName = "fourth";
      this.getPageData();
      // console.log(row.course,'是否转发为是:1');
    }
  }
};
</script>

<style scoped>
#pane-second .el-table {
  margin-top: 40px;
}
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
.el-table__body .cust-tabstyle {
  padding-top: 6px;
  padding-bottom: 6px;
}
</style>
