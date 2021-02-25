<template>
  <div class="app-container quesPage">
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <el-tab-pane label="信息健康" name="first">
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
            v-model="courseType1"
            @change="courseType1_change"
            multiple
            collapse-tags
            placeholder="课程编号"
            style="width:140px"
          >
            <el-option
              v-for="item in courseNumOptions1"
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
            @click="export_excel1"
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
          <el-table-column label="课程编号" prop="course"></el-table-column>
          <el-table-column label="A01" prop="b_name"></el-table-column>
          <el-table-column label="A02" prop="sex"></el-table-column>
          <el-table-column label="A03" prop="phone"></el-table-column>
          <el-table-column label="A05" prop="birthday"></el-table-column>
          <el-table-column label="A05a" prop="age"></el-table-column>
          <!-- <el-table-column label="A06" prop="age"></el-table-column> -->
          <el-table-column label="A06" prop="nation"></el-table-column>
          <el-table-column label="A07" prop="education"></el-table-column>
          <el-table-column label="A08" prop="job"></el-table-column>
          <el-table-column label="A09" prop="birthplace"></el-table-column>
          <el-table-column label="A10" prop="census"></el-table-column>
          <el-table-column label="A11" prop="live_time"></el-table-column>
          <el-table-column label="A11a" prop="reason"></el-table-column>
          <el-table-column
            label="A12"
            prop="housing_situation"
          ></el-table-column>
          <el-table-column
            label="A13"
            prop="living_situation"
          ></el-table-column>
          <el-table-column label="A14" prop="parent_live"></el-table-column>
          <el-table-column label="A15" prop="contacts"></el-table-column>
          <el-table-column label="A16" prop="marriage"></el-table-column>
          <el-table-column
            label="A16a"
            prop="marriage_status"
          ></el-table-column>
          <el-table-column label="A17" prop="income"></el-table-column>
          <el-table-column
            label="A开始时间"
            prop="b_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="A结束时间"
            prop="b_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="A填写时长"
            prop="ltime"
            width="100"
          ></el-table-column>
          <el-table-column label="B01" prop="height"></el-table-column>
          <el-table-column label="B02" prop="weight"></el-table-column>
          <el-table-column label="B03" prop="smoke"></el-table-column>
          <el-table-column label="B03a" prop="smoke_age"></el-table-column>
          <el-table-column label="B03b" prop="smoke_date"></el-table-column>
          <el-table-column label="B03c" prop="smoke_amount"></el-table-column>
          <el-table-column label="B04" prop="drink"></el-table-column>
          <el-table-column label="B04a" prop="drink_age"></el-table-column>
          <el-table-column label="B04b" prop="drink_day"></el-table-column>
          <el-table-column label="B04c" prop="drink_bad_time"></el-table-column>
          <el-table-column label="B04d" prop="drink_more"></el-table-column>
          <el-table-column
            label="B05"
            prop="confirmed_disease"
          ></el-table-column>
          <el-table-column
            label="B06"
            prop="pregnancy_amount"
          ></el-table-column>
          <el-table-column
            label="B06a"
            prop="childbirth_amount"
          ></el-table-column>
          <el-table-column label="B07" prop="sleep_time"></el-table-column>
          <el-table-column label="B08" prop="sleep_quality"></el-table-column>
          <el-table-column label="B09" prop="sleeping_pills"></el-table-column>
          <el-table-column label="B09a" prop="used_drug"></el-table-column>
          <el-table-column label="B10" prop="want_suicide"></el-table-column>
          <el-table-column label="B11" prop="attempt_suicide"></el-table-column>
          <el-table-column label="B12" prop="suicide_plan"></el-table-column>
          <el-table-column
            label="B13"
            prop="one_attempt_suicide"
          ></el-table-column>
          <el-table-column label="B14" prop="depression"></el-table-column>
          <el-table-column label="B15" prop="exercise_count"></el-table-column>
          <el-table-column
            label="B15a"
            prop="exercise_duration"
          ></el-table-column>
          <el-table-column
            label="B开始时间"
            prop="c_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="B结束时间"
            prop="c_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="B填写时长"
            prop="time"
            width="100"
          ></el-table-column>
          <!-- a.open_id,a.number,a.name,a.wx_phone,a.type_name,b.name as b_name,b.course,b.sex,b.phone,b.idcard,b.birthday,b.age,b.nation,b.education,b.job,b.birthplace,b.census,b.live_time,b.reason,b.housing_situation,b.living_situation,b.parent_live,b.contacts,b.marriage,b.marriage_status,b.income,b.stime,b.etime,b.ltime,c.height,c.weight,c.smoke,c.smoke_age,c.smoke_date,c.smoke_amount,c.drink,c.drink_age,c.drink_day,c.drink_bad_time,c.drink_more,c.confirmed_disease,c.pregnancy_amount,c.childbirth_amount,c.sleep_time,c.sleep_quality,c.sleeping_pills,c.used_drug,c.want_suicide,c.attempt_suicide,c.suicide_plan,c.one_attempt_suicide,c.depression,c.exercise_count,c.exercise_duration,c.stime,c.etime,c.time -->
        </el-table>
        <!--<paging ref="paging" :pageIndex.sync="pageIndex" :total="total" @pageChange="pageChange"></paging> -->
      </el-tab-pane>
      <el-tab-pane label="课前问卷" name="second">
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
            v-model="courseType2"
            @change="courseType2_change"
            multiple
            collapse-tags
            placeholder="课程编号"
            style="width:140px"
          >
            <el-option
              v-for="item in courseNumOptions2"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <el-select
            size="small"
            v-model="depressType"
            @change="depressType_change"
            multiple
            collapse-tags
            placeholder="抑郁分数段"
            style="width:250px"
          >
            <el-option
              v-for="item in depressOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <el-select
            size="small"
            v-model="anxietyType"
            @change="anxietyType_change"
            multiple
            collapse-tags
            placeholder="焦虑分数段"
            style="width:250px"
          >
            <el-option
              v-for="item in anxietyOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <el-button type="primary" size="small" @click="search_data"
            >搜索</el-button
          >
          <el-button type="primary" size="small" @click="export_excel2"
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
          <!-- a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.C01,b.C02,b.C03,b.C04,b.C05,b.C06,b.C07,b.C08,b.C09,b.CP,b.stime as b_stime,b.etime as b_etime,b.ltime as b_ltime,b.state,c.D01,c.D02,c.D03,c.D04,c.D05,c.D06,c.D07,c.stime as c_stime,c.etime as c_etime,c.time as c_ltime -->
          <el-table-column label="用户ID" prop="open_id"></el-table-column>
          <el-table-column label="编码" prop="number"></el-table-column>
          <el-table-column label="姓名" prop="name"></el-table-column>
          <el-table-column label="微信手机" prop="wx_phone"></el-table-column>
          <el-table-column label="用户分类" prop="type_name"></el-table-column>
          <el-table-column label="课程编号" prop="course"></el-table-column>
          <el-table-column label="C01" prop="C01"></el-table-column>
          <el-table-column label="C02" prop="C02"></el-table-column>
          <el-table-column label="C03" prop="C03"></el-table-column>
          <el-table-column label="C04" prop="C04"></el-table-column>
          <el-table-column label="C05" prop="C05"></el-table-column>
          <el-table-column label="C06" prop="C06"></el-table-column>
          <el-table-column label="C07" prop="C07"></el-table-column>
          <el-table-column label="C08" prop="C08"></el-table-column>
          <el-table-column label="C09" prop="C09"></el-table-column>
          <el-table-column label="CP" prop="CP"></el-table-column>
          <el-table-column
            label="C开始时间"
            prop="b_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="C结束时间"
            prop="b_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="C填写时长"
            prop="b_ltime"
            width="100"
          ></el-table-column>
          <el-table-column label="D01" prop="D01"></el-table-column>
          <el-table-column label="D02" prop="D02"></el-table-column>
          <el-table-column label="D03" prop="D03"></el-table-column>
          <el-table-column label="D04" prop="D04"></el-table-column>
          <el-table-column label="D05" prop="D05"></el-table-column>
          <el-table-column label="D06" prop="D06"></el-table-column>
          <el-table-column label="D07" prop="D07"></el-table-column>
          <el-table-column label="DP" prop="DP"></el-table-column
          <el-table-column
            label="D开始时间"
            prop="c_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="D结束时间"
            prop="c_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="D填写时长"
            prop="c_ltime"
            width="100"
          ></el-table-column>
          <!-- E01a,E01b,E01c,E02,E03,E04,E05,EP,e_stime,e_etime,e_ltime -->
          <el-table-column label="E01a" prop="E01a"></el-table-column>
          <el-table-column label="E01b" prop="E01b"></el-table-column>
          <el-table-column label="E01c" prop="E01c"></el-table-column>
          <el-table-column label="E02" prop="E02"></el-table-column>
          <el-table-column label="E03" prop="E03"></el-table-column>
          <el-table-column label="E04" prop="E04"></el-table-column>
          <el-table-column label="E05" prop="E05"></el-table-column>
          <el-table-column label="EP" prop="EP"></el-table-column>
          <el-table-column
            label="E开始时间"
            prop="e_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="E结束时间"
            prop="e_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="E填写时长"
            prop="e_ltime"
            width="100"
          ></el-table-column>
          <!-- F01,F02,F03,F04,F05,FP,f_stime,f_etime,f_ltime -->
          <el-table-column label="F01" prop="F01"></el-table-column>
          <el-table-column label="F02" prop="F02"></el-table-column>
          <el-table-column label="F03" prop="F03"></el-table-column>
          <el-table-column label="F04" prop="F04"></el-table-column>
          <el-table-column label="F05" prop="F05"></el-table-column>
          <el-table-column label="FP" prop="FP"></el-table-column>
          <el-table-column
            label="F开始时间"
            prop="f_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="F结束时间"
            prop="f_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="F填写时长"
            prop="f_ltime"
            width="100"
          ></el-table-column>
          <!-- G01,G02,G03,G04,G05,G06,GP,g_stime,g_etime,g_ltime -->
          <el-table-column label="G01" prop="G01"></el-table-column>
          <el-table-column label="G02" prop="G02"></el-table-column>
          <el-table-column label="G03" prop="G03"></el-table-column>
          <el-table-column label="G04" prop="G04"></el-table-column>
          <el-table-column label="G05" prop="G05"></el-table-column>
          <el-table-column label="G06" prop="G06"></el-table-column>
          <el-table-column label="GP" prop="GP"></el-table-column>
          <el-table-column
            label="G开始时间"
            prop="g_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="G结束时间"
            prop="g_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="G填写时长"
            prop="g_ltime"
            width="100"
          ></el-table-column>
          <!-- H01 ........H28,HP,h_stime,h_etime,h_ltime -->
          <el-table-column label="H01" prop="H01"></el-table-column>
          <el-table-column label="H02" prop="H02"></el-table-column>
          <el-table-column label="H03" prop="H03"></el-table-column>
          <el-table-column label="H04" prop="H04"></el-table-column>
          <el-table-column label="H05" prop="H05"></el-table-column>
          <el-table-column label="H06" prop="H06"></el-table-column>
          <el-table-column label="H07" prop="H07"></el-table-column>
          <el-table-column label="H08" prop="H08"></el-table-column>
          <el-table-column label="H09" prop="H09"></el-table-column>
          <el-table-column label="H10" prop="H10"></el-table-column>
          <el-table-column label="H11" prop="H11"></el-table-column>
          <el-table-column label="H12" prop="H12"></el-table-column>
          <el-table-column label="H13" prop="H13"></el-table-column>
          <el-table-column label="H14" prop="H14"></el-table-column>
          <el-table-column label="H15" prop="H15"></el-table-column>
          <el-table-column label="H16" prop="H16"></el-table-column>
          <el-table-column label="H17" prop="H17"></el-table-column>
          <el-table-column label="H18" prop="H18"></el-table-column>
          <el-table-column label="H19" prop="H19"></el-table-column>
          <el-table-column label="H20" prop="H20"></el-table-column>
          <el-table-column label="H21" prop="H21"></el-table-column>
          <el-table-column label="H22" prop="H22"></el-table-column>
          <el-table-column label="H23" prop="H23"></el-table-column>
          <el-table-column label="H24" prop="H24"></el-table-column>
          <el-table-column label="H25" prop="H25"></el-table-column>
          <el-table-column label="H26" prop="H26"></el-table-column>
          <el-table-column label="H27" prop="H27"></el-table-column>
          <el-table-column label="H28" prop="H28"></el-table-column>
          <el-table-column label="HP" prop="HP"></el-table-column>
          <el-table-column
            label="H开始时间"
            prop="h_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="H结束时间"
            prop="h_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="H填写时长"
            prop="h_ltime"
            width="100"
          ></el-table-column>
          <!-- I01 ........I25,IP,stime,etime,ltime -->
          <el-table-column label="I01" prop="I01"></el-table-column>
          <el-table-column label="I02" prop="I02"></el-table-column>
          <el-table-column label="I03" prop="I03"></el-table-column>
          <el-table-column label="I04" prop="I04"></el-table-column>
          <el-table-column label="I05" prop="I05"></el-table-column>
          <el-table-column label="I06" prop="I06"></el-table-column>
          <el-table-column label="I07" prop="I07"></el-table-column>
          <el-table-column label="I08" prop="I08"></el-table-column>
          <el-table-column label="I09" prop="I09"></el-table-column>
          <el-table-column label="I10" prop="I10"></el-table-column>
          <el-table-column label="I11" prop="I11"></el-table-column>
          <el-table-column label="I12" prop="I12"></el-table-column>
          <el-table-column label="I13" prop="I13"></el-table-column>
          <el-table-column label="I14" prop="I14"></el-table-column>
          <el-table-column label="I15" prop="I15"></el-table-column>
          <el-table-column label="I16" prop="I16"></el-table-column>
          <el-table-column label="I17" prop="I17"></el-table-column>
          <el-table-column label="I18" prop="I18"></el-table-column>
          <el-table-column label="I19" prop="I19"></el-table-column>
          <el-table-column label="I20" prop="I20"></el-table-column>
          <el-table-column label="I21" prop="I21"></el-table-column>
          <el-table-column label="I22" prop="I22"></el-table-column>
          <el-table-column label="I23" prop="I23"></el-table-column>
          <el-table-column label="I24" prop="I24"></el-table-column>
          <el-table-column label="I25" prop="I25"></el-table-column>
          <el-table-column label="IP" prop="IP"></el-table-column>
          <el-table-column label="tough" prop="tough"></el-table-column>
          <el-table-column label="power" prop="power"></el-table-column>
          <el-table-column
            label="optimistic"
            prop="optimistic"
          ></el-table-column>
          <el-table-column
            label="I开始时间"
            prop="i_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="I结束时间"
            prop="i_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="I填写时长"
            prop="i_ltime"
            width="100"
          ></el-table-column>
          <!-- J01 ........J30,IP,stime,etime,ltime -->

          <el-table-column label="J01" prop="J01"></el-table-column>
          <el-table-column label="J02" prop="J02"></el-table-column>
          <el-table-column label="J03" prop="J03"></el-table-column>
          <el-table-column label="J04" prop="J04"></el-table-column>
          <el-table-column label="J05" prop="J05"></el-table-column>
          <el-table-column label="J06" prop="J06"></el-table-column>
          <el-table-column label="J07" prop="J07"></el-table-column>
          <el-table-column label="J08" prop="J08"></el-table-column>
          <el-table-column label="J09" prop="J09"></el-table-column>
          <el-table-column label="J10" prop="J10"></el-table-column>
          <el-table-column label="J11" prop="J11"></el-table-column>
          <el-table-column label="J12" prop="J12"></el-table-column>
          <el-table-column label="J13" prop="J13"></el-table-column>
          <el-table-column label="J14" prop="J14"></el-table-column>
          <el-table-column label="J15" prop="J15"></el-table-column>
          <el-table-column label="J16" prop="J16"></el-table-column>
          <el-table-column label="J17" prop="J17"></el-table-column>
          <el-table-column label="J18" prop="J18"></el-table-column>
          <el-table-column label="J19" prop="J19"></el-table-column>
          <el-table-column label="J20" prop="J20"></el-table-column>
          <el-table-column label="J21" prop="J21"></el-table-column>
          <el-table-column label="J22" prop="J22"></el-table-column>
          <el-table-column label="J23" prop="J23"></el-table-column>
          <el-table-column label="J24" prop="J24"></el-table-column>
          <el-table-column label="J25" prop="J25"></el-table-column>
          <el-table-column label="J26" prop="J26"></el-table-column>
          <el-table-column label="J27" prop="J27"></el-table-column>
          <el-table-column label="J28" prop="J28"></el-table-column>
          <el-table-column label="J29" prop="J29"></el-table-column>
          <el-table-column label="J30" prop="J30"></el-table-column>
          <el-table-column
            label="individual"
            prop="individual"
          ></el-table-column>
          <el-table-column label="negative" prop="negative"></el-table-column>
          <el-table-column
            label="self_confidence"
            prop="self_confidence"
          ></el-table-column>
          <el-table-column label="Helpless" prop="Helpless"></el-table-column>
          <el-table-column label="JP" prop="JP"></el-table-column>
          <el-table-column
            label="J开始时间"
            prop="j_stime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="J结束时间"
            prop="j_etime"
            width="100"
          ></el-table-column>
          <el-table-column
            label="J填写时长"
            prop="j_ltime"
            width="100"
          ></el-table-column>
        </el-table>
        <!-- <paging ref="paging" :pageIndex.sync="pageIndex" :total="total" @pageChange="pageChange"></paging> -->
      </el-tab-pane>
      <el-tab-pane label="心情记录" name="third">
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
            v-model="courseType3"
            @change="courseType3_change"
            multiple
            collapse-tags
            placeholder="课程编号"
            style="width:140px"
          >
            <el-option
              v-for="item in courseNumOptions3"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <el-select
            size="small"
            v-model="depressType"
            @change="depressType_change"
            multiple
            collapse-tags
            placeholder="抑郁分数段"
            style="width:250px"
          >
            <el-option
              v-for="item in depressOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
          <el-select
            size="small"
            v-model="anxietyType"
            @change="anxietyType_change"
            multiple
            collapse-tags
            placeholder="焦虑分数段"
            style="width:250px"
          >
            <el-option
              v-for="item in anxietyOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
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
          style="width: 100%"
          max-height="550"
        >
          <el-table-column
            label="序号"
            type="index"
            :index="indexMethod"
            width="60"
          ></el-table-column>
          <!-- a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.C01,b.C02,b.C03,b.C04,b.C05,b.C06,b.C07,b.C08,b.C09,b.CP,b.stime,b.ltime as b_ltime,c.D01,c.D02,c.D03,c.D04,c.D05,c.D06,c.D07,c.DP,c.etime,c.time as c_ltime -->
          <el-table-column label="用户ID" prop="open_id"></el-table-column>
          <el-table-column label="编码" prop="number"></el-table-column>
          <el-table-column label="姓名" prop="name"></el-table-column>
          <el-table-column label="微信手机" prop="wx_phone"></el-table-column>
          <el-table-column label="用户分类" prop="type_name"></el-table-column>
          <el-table-column label="课程编号" prop="course"></el-table-column>
          <el-table-column
            label="问卷填写开始时间"
            prop="b_stime"
          ></el-table-column>
          <el-table-column
            label="问卷填写结束时间"
            prop="c_etime"
          ></el-table-column>
          <el-table-column label="问卷填写时长" prop="ltime"></el-table-column>
          <el-table-column label="C01" prop="C01"></el-table-column>
          <el-table-column label="C02" prop="C02"></el-table-column>
          <el-table-column label="C03" prop="C03"></el-table-column>
          <el-table-column label="C04" prop="C04"></el-table-column>
          <el-table-column label="C05" prop="C05"></el-table-column>
          <el-table-column label="C06" prop="C06"></el-table-column>
          <el-table-column label="C07" prop="C07"></el-table-column>
          <el-table-column label="C08" prop="C08"></el-table-column>
          <el-table-column label="C09" prop="C09"></el-table-column>
          <el-table-column label="CP" prop="CP"></el-table-column>
          <el-table-column label="D01" prop="D01"></el-table-column>
          <el-table-column label="D02" prop="D02"></el-table-column>
          <el-table-column label="D03" prop="D03"></el-table-column>
          <el-table-column label="D04" prop="D04"></el-table-column>
          <el-table-column label="D05" prop="D05"></el-table-column>
          <el-table-column label="D06" prop="D06"></el-table-column>
          <el-table-column label="D07" prop="D07"></el-table-column>
          <el-table-column label="DP" prop="DP"></el-table-column>
        </el-table>
        <!-- <paging ref="paging" :pageIndex.sync="pageIndex" :total="total" @pageChange="pageChange"></paging> -->
      </el-tab-pane>
      <el-tab-pane label="课后反馈问卷" name="fourth">
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
          <el-button type="primary" size="small" @click="search_data"
            >搜索</el-button
          >
          <el-button type="primary" size="small" @click="export_excel4"
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
          <!-- a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.stime,b.etime,b.ltime,b.Q1,b.Q2,b.Q3,b.Q4,b.Q5,b.Q6,b.question -->
          <el-table-column label="用户ID" prop="open_id"></el-table-column>
          <el-table-column label="编码" prop="number"></el-table-column>
          <el-table-column label="姓名" prop="name"></el-table-column>
          <el-table-column label="微信手机" prop="wx_phone"></el-table-column>
          <el-table-column label="用户分类" prop="type_name"></el-table-column>
          <el-table-column label="课程编号" prop="course"></el-table-column>
          <el-table-column label="问卷分类">
            <template>
              <div>课后反馈问卷</div>
            </template>
          </el-table-column>
          <el-table-column
            label="问卷填写开始时间"
            prop="stime"
          ></el-table-column>
          <el-table-column
            label="问卷填写结束时间"
            prop="etime"
          ></el-table-column>
          <el-table-column label="问卷填写时长" prop="ltime"></el-table-column>
          <el-table-column label="Q1" prop="Q1"></el-table-column>
          <el-table-column label="Q2" prop="Q2"></el-table-column>
          <el-table-column label="Q3" prop="Q3"></el-table-column>
          <el-table-column label="Q4" prop="Q4"></el-table-column>
          <el-table-column label="Q5" prop="Q5"></el-table-column>
          <el-table-column label="Q6" prop="Q6"></el-table-column>
          <el-table-column label="建议" prop="question"></el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>

    <paging
      ref="paging"
      :pageIndex.sync="pageIndex"
      :total="total"
      @pageChange="pageChange"
    ></paging>
  </div>
</template>

<script>
// 导入组件和方法
import {
  get_healthData,
  excel_health,
  get_course_befData,
  excel_course_before,
  get_moodData,
  excel_mood,
  get_feedbackData,
  excel_feedback
} from "@/api/dataManage/quesData";
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
      activeName: "first", //默认显示第一个标签
      tabIndex: 0, //上方tab栏下标，从0开始，默认第一个
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
      depressOptions: [
        {
          value: "10",
          label: "全部"
        },
        {
          value: "1",
          label: "无抑郁症状（0-4）"
        },
        {
          value: "2",
          label: "轻度抑郁症状（5-9）"
        },
        {
          value: "3",
          label: "中度抑郁症状（10-14）"
        },
        {
          value: "4",
          label: "中重度抑郁症状（15-19）"
        },
        {
          value: "5",
          label: "重度抑郁症状（20-27）"
        }
      ],
      depressType: [],
      anxietyOptions: [
        {
          value: "10",
          label: "全部"
        },
        {
          value: "1",
          label: "无焦虑症状（0-4）"
        },
        {
          value: "2",
          label: "轻度焦虑症状（5-9）"
        },
        {
          value: "3",
          label: "中度焦虑症状（10-14）"
        },
        {
          value: "4",
          label: "重度焦虑症状（15-21）"
        }
      ],
      anxietyType: [],
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
      total: 0,
      pageIndex: 1,
      pageSize: 20
    };
  },
  computed: {
    cur_roles() {
      return this.$store.getters.roles[1].info[0].info; //进入按钮权限的层级
    }
  },
  methods: {
    handleClick(tab, event) {
      console.log(tab, event);
      console.log("handleClick -> tab", tab.index); //根据下标请求不同数据
      Object.assign(this.$data, this.$options.data()); //初始化组件的data 为默认值
      this.tabIndex = tab.index; //改变下标请求不同数据
      this.getPageData(); //这里请求的是不同的数据（待优化）
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
    depressType_change() {
      this.$refs.paging.indexInit();
    },
    anxietyType_change() {
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
      if (this.tabIndex == 0) {
        //下标为字符串，这里只用等于==就行，不用全等于
        objData.course = this.courseType1;
        get_healthData(objData).then(res => {
          //信息健康
          this.tableData = res.data.list;
          this.total = res.data.total;
          this.tab_loading = false;
        });
      } else if (this.tabIndex == 1) {
        //课前问卷
        objData.course = this.courseType2;
        objData.CP = this.depressType;
        objData.DP = this.anxietyType;
        // 添加三个参数传过去
        get_course_befData(objData).then(res => {
          this.tableData = res.data.list;
          this.total = res.data.total;
          this.tab_loading = false;
        });
      } else if (this.tabIndex == 2) {
        //心情记录
        objData.course = this.courseType3;
        objData.CP = this.depressType;
        objData.DP = this.anxietyType;
        // 添加三个参数传过去
        get_moodData(objData).then(res => {
          this.tableData = res.data.list;
          this.total = res.data.total;
          this.tab_loading = false;
        });
      } else {
        //课后反馈问卷
        objData.course = this.courseType4;
        // 添加三个参数传过去
        get_feedbackData(objData).then(res => {
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
      excel_health({
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex,
        course: this.courseType1
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "信息健康数据");
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
      excel_course_before({
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex,
        course: this.courseType2,
        CP: this.anxietyType,
        DP: this.depressType
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "课前问卷数据");
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
      excel_mood({
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex,
        course: this.courseType3,
        CP: this.anxietyType,
        DP: this.depressType
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "心情记录数据");
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
      excel_feedback({
        token: this.$store.getters.token,
        stime: this.startTime,
        etime: this.endTime,
        number: this.searchCode,
        type: this.userType,
        name: this.searchName,
        phone: this.searchPhone,
        limit: this.pageSize,
        page: this.pageIndex,
        course: this.courseType4
      })
        .then(res => {
          console.log("文件流", res);
          downloadExcel(res, "课后反馈问卷数据");
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
