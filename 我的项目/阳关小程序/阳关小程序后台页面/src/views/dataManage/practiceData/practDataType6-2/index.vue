<template>
  <!-- 去掉app-container 因为嵌套了模板，所有样式会有重叠，这里需要去掉app-container类名 -->
  <div class>
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
      <el-button
        v-if="cur_roles[1].exist == '1'"
        type="primary"
        size="small"
        @click="export_excel"
      >导出</el-button>
      <el-button type="primary" size="small" @click="search_data">搜索</el-button>
    </el-row>
	<el-table v-loading="tab_loading" border :data="tableData" style="width: 100%" max-height="550">
      <el-table-column
        label="序号"
        type="index"
        :index="indexMethod"
        width="60"
        header-align="center"
        align="center"
      ></el-table-column>
      <el-table-column prop="open_id" label="用户ID" header-align="center" align="center"></el-table-column>
      <el-table-column prop="number" label="编码" header-align="center" align="center"></el-table-column>
      <el-table-column prop="name" label="姓名" header-align="center" align="center"></el-table-column>
      <el-table-column prop="wx_phone" label="微信手机号" header-align="center" align="center"></el-table-column>
      <el-table-column prop="type_name" label="患者分类" header-align="center" align="center"></el-table-column>
      <el-table-column prop="course" label="课程编号" header-align="center" align="center"></el-table-column>
      <el-table-column prop="stime" label="开始时间" header-align="center" align="center"></el-table-column>
      <el-table-column prop="etime" label="结束时间" header-align="center" align="center"></el-table-column>
      <el-table-column prop="ltime" label="时长" header-align="center" align="center"></el-table-column>
      <el-table-column label="日期" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item.date}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column label="星期" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item.week}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column label="0-1点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-0']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column label="愉悦度0" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-0']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column label="成就感0" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-0']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="1-2点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-1']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度1" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-1']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感1" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-1']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="2-3点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-2']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度2" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-2']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感2" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-2']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="3-4点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-3']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度3" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-3']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感3" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-3']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="4-5点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-4']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度4" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-4']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感4" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-4']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="5-6点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-5']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度5" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-5']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感5" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-5']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="6-7点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-6']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度6" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-6']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感6" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-6']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="7-8点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-7']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度7" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-7']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感7" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-7']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="8-9点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-8']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度8" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-8']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感8" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-8']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="9-10点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-9']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度9" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-9']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感9" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-9']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="10-11点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-10']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度10" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-10']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感10" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-10']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="11-12点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-11']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度11" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-11']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感11" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-11']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="12-13点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-12']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度12" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-12']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感12" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-12']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="13-14点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-13']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度13" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-13']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感13" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-13']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="14-15点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-14']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度14" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-14']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感14" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-14']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="15-16点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-15']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度15" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-15']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感15" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-15']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="16-17点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-16']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度16" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-16']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感16" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-16']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="17-18点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-17']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度17" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-17']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感17" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-17']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="18-19点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-18']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度18" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-18']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感18" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-18']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="19-20点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-19']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度19" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-19']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感19" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-19']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="20-21点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-20']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度20" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-20']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感20" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-20']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="21-22点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-21']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度21" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-21']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感21" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-21']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="22-23点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-22']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度22" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-22']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感22" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-22']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="ltime" label="23-24点" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['activity-23']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="愉悦度23" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['pleasure-23']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="ltime" label="成就感23" header-align="center" align="center">
        <template slot-scope="scope">
          <div v-for="(item,i) in scope.row.info" :key="i">
            {{item['achievement-23']}}
            <hr color="#EBEEF5" v-if="i<scope.row.info.length-1" />
          </div>
        </template>
      </el-table-column>
    </el-table>
    <paging ref="paging" :pageIndex.sync="pageIndex" :total="total" @pageChange="pageChange"></paging>
  </div>
</template>

<script>
// 导入组件和方法
import { s6_activity_record, excel_s6_activity_record } from "@/api/dataManage/practData";
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
	  tab_loading: true,
      tableData: [],
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
      total: 0,
      pageIndex: 1,
	  pageSize: 20,
      startTime: null,
      endTime: null,
    };
  },
  computed: {
    cur_roles() {
      return this.$store.getters.roles[1].info[2].info; //进入按钮权限的层级
    }
  },
  methods: {
    handleDateChange(start, end) {
      this.startTime = start;
      this.endTime = end;
      this.$refs.paging.indexInit();
    },
    userType_change() {
      this.$refs.paging.indexInit();
    },
    getPageData() {
      //定义公共请求参数
      let objData = {
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex
      };
      s6_activity_record(objData).then(res => {
        //信息健康
        this.tableData = res.data.list;
        this.total = res.data.total;
        this.tab_loading = false;
      });
    },
    indexMethod(index) {
      //序号
      return index + 1; //从0开始的必须加一
    },
    search_data() {
      //整个页面搜索共用函数
      this.getPageData();
    },
    export_excel() {
      //导出S2-活动记录表
      excel_s6_activity_record({
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "S2-活动记录表");
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
.el-table .cell,
.el-table th div {
  padding-right: 0%;
  padding-left: 0%;
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