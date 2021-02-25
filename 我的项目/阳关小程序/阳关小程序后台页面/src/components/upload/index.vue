<template>
  <div>
    <!-- 音频上传 -->
    <template v-if="audio">
      <el-upload
        ref="upload"
        class="upload-demo"
        :action="upLoadAudioUrl"
        :headers="headerMsg"
        :on-preview="handlePreview"
        :on-change="onChange"
        :on-success="onSuccess"
        :on-remove="onChange"
        :before-remove="beforeRemove"
        multiple
        :limit="limit"
        :before-upload="beforeAudioUpload"
        :on-exceed="handleExceed"
        :file-list="value1"
      >
        <el-button size="small" type="primary" class="el-icon-upload2">上传文件</el-button>
        <div slot="tip" class="el-upload__tip">支持扩展名：mp3,wav,aac,flac,ogg,m4a,m4r</div>
      </el-upload>
    </template>

    <!-- 图片上传 -->
    <template v-else>
      <!-- 多张上传 -->
      <template v-if="multiple">
        <el-upload
          ref="upload"
          class="picture-card-big"
          multiple
          list-type="picture-card"
          :limit="limit"
          :on-exceed="handleExceedLimit"
          :on-preview="handlePictureCardPreview"
          :file-list="value1"
          :before-upload="beforeAvatarUpload"
          :on-change="onChange_pic"
        	:on-success="onSuccess"
          :on-remove="onChange"
          :action="upLoadPicUrl"
          :headers="headerMsg"
        >
          <i class="el-icon-plus"></i>
          <span class="multiple-uploader-text">上传</span>
        </el-upload>
        <el-dialog title="大图查看" :visible.sync="dialogVisible" append-to-body center>
          <img width="100%" :src="dialogImageUrl" alt />
        </el-dialog>
      </template>

      <!-- 单张上传 -->
      <el-upload
        v-else
        ref="upload"
        :class="sizeClass"
        class="avatar-uploader"
        :action="upLoadPicUrl"
        :headers="headerMsg"
        :show-file-list="false"
        :on-success="handleAvatarSuccess"
        :before-upload="beforeAvatarUpload"
      >
        <img v-if="value2" :src="value2" class="avatar" />
        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        <span v-if="!value2" class="uploader-text">上传</span>
		<!-- <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div> -->
      </el-upload>
    </template>
  </div>
</template>

<script>
import { isArray } from 'util';
// console.log('上传域名:'+process.env.VUE_APP_BASE_API);
const baseUrl = process.env.NODE_ENV === 'development' ? '/admin' : process.env.VUE_APP_BASE_API
export default {
  props: {
    //音频上传
    audio: {
      type: Boolean,
      default: false
    },

    // 图片上传
    //是否多张上传
    multiple: {
      type: Boolean,
      default: false
    },
    // 文件信息
    value: {
      default: null
    },
    //限制上传张数
    limit: {
      type: Number,
      default: 0
    },
    // 单张上传小头像
    small: {
      type: Boolean,
      default: false
    },
    // 单张上传banner
    banner: {
      type: Boolean,
      default: false
    },
    // 移动端bg长图
    mbg: {
      type: Boolean,
      default: false
	},
	// 角色详情
	roledet:{
      type: Boolean,
      default: false
	}
  },
  data() {
    return {
      headerMsg: { 'token': this.$store.getters.token }, //必须加$符号
      upLoadAudioUrl: baseUrl+"/admin/Common/upload_music", //上传音频
      upLoadPicUrl: baseUrl+"/admin/Common/upload", //上传图片
    //   isCredentials: true,
      dialogImageUrl: "",
      dialogVisible: false,
      value1: this.value || [], //多文件
	  value2: this.value,
	  fileList: [],
	  picfileList: [],
    };
  },
  watch:{
    value(val){
    //   console.log('是否多张文件',isArray(val));
      if(val === null){ //没传值就清掉文件
        this.$refs.upload.clearFiles() //清除文件
      }
      
      if(isArray(val)){
        this.value1 = val
      }else{
        this.value2 = val
      }
    }
  },
  computed:{
    sizeClass(){// 切换上传图片的尺寸
      if(this.small){
        return 'small-uploader'
      }else if(this.banner){
        return 'banner-uploader'
      }else if(this.mbg){
        return 'mbg-uploader'
      }else if(this.roledet){
		  return 'roledet-uploader'
	  }else{
		  return 'big-uploader'
		}
    }
  },
  methods: {
    onChange(file, fileList) { //文件上传成功，这里才会在回调参数里面加上response响应体
		//改变文件状态  通用
		//   console.log("音频改变状态",file, fileList);
		if(fileList.length != 0){
			if(fileList[fileList.length-1].response){
				const file_list ={
					name: fileList[fileList.length-1].response.data.name,
					url: fileList[fileList.length-1].response.data.path,
					uid: new Date().getTime(),
					status: "success"
				} 
				fileList.pop() //删除最后一项
				fileList.push(file_list)
				this.fileList = fileList
				// console.log('上传成功res',fileList);
				// console.log('上传成功res');
			}else{
				this.fileList = fileList
				// console.log('没有进行上传');
			}
		}
		this.fileList = fileList
		this.$emit("input", this.fileList);
	},
	onChange_pic(file, fileList){
		if(fileList.length != 0){
			if(fileList[fileList.length-1].response){
				const file_list ={
					url: fileList[fileList.length-1].response.data,
					uid: new Date().getTime(),
					status: "success"
				} 
				fileList.pop() //删除最后一项
				fileList.push(file_list)
				this.picfileList = fileList
				// console.log('上传成功res',fileList);
				// console.log('上传成功res');
			}else{
				this.picfileList = fileList
				// console.log('没有进行上传');
			}
		}
		this.picfileList = fileList
		this.$emit("input", this.picfileList);
	},
	onSuccess(res, file, fileList){
      console.log("上传图片", res, file);
      if (res.code != 200) {
        this.$message.error("上传失败");
        return false;
	  }
        this.$message.success("上传成功！");
	},

    // 音频上传
    handlePreview(file) {
      //点击已上传的文件链接时的钩子
      console.log("播放音乐", file);
    },
    beforeAudioUpload(file) {
	  //上传文件之前(判断文件格式)
	//   console.log(file.type);
      const isMP3 = file.type === "audio/mp3";
        if (!isMP3) {
		  this.$message.error("请上传MP3格式音频！");
        }
        return isMP3;
    },
    handleExceed(files, fileList) {
      //文件超出个数限制时的钩子
      this.$message({
        type: "error",
        message: `上传图片个数超出限制，最多上传${this.limit}张图片`
      });
    },
    beforeRemove(file, fileList) {
      //删除文件之前的钩子
      return this.$confirm(`确定移除 ${file.name}？`);
    },

    //   图片上传
    // 多张
    handleExceedLimit() {
      this.$message({
        type: "error",
        message: `上传图片个数超出限制，最多上传${this.limit}张图片`
      });
    },
    handlePictureCardPreview(file) {
      //预览图片
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
    },

    // 单张
    handleAvatarSuccess(res, file) {
    //   console.log("banner图上传成功", res, file);
      if (res.code != 200) {
        this.$message.error("上传图片失败");
        return false;
      }
    	this.$message.success("上传图片成功");
      const fileCDN = res.data;
      this.$emit("input", fileCDN);
    },
    beforeAvatarUpload(file) {
      console.log("文件信息", file);
      const isJPG = file.type === "image/jpeg";
      const isPNG = file.type === "image/png";
	  const isLt10M = file.size / 1024 / 1024 < 10;
	  console.log(isPNG,isLt10M);
        if (!isPNG) {
          this.$message.error("上传图片只能是 PNG 格式!");
		}
        if (!isLt10M) {
          this.$message.error("上传图片大小不能超过 10MB!");
        }
        return isPNG && isLt10M;
    }
  }
};
</script>

<style>
.upload-demo {
  max-width: 454px;
}
/* 居中 */
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}

/* 多张图片上传 */
.picture-card-big .el-upload {
  width: 140px;
  height: 264px;
  position: relative;
}

.multiple-uploader-text {
  position: absolute;
  left: 56px;
  bottom: 84px;
  line-height: 14px;
}
.el-upload-list--picture-card .el-upload-list__item {
  width: 140px;
  height: 264px;
}

/* 单张图片上传 */
/* 大图 */
.big-uploader {
  width: 236px;
  height: 145px;
  position: relative;
}
.big-uploader .avatar {
  width: 236px;
  height: 145px;
  display: block;
  position: relative;
}
.big-uploader div {
  width: 236px;
  height: 145px;
}

.big-uploader .avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 236px;
  height: 145px;
  line-height: 145px;
  text-align: center;
}
.big-uploader .uploader-text {
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: 106px;
  bottom: 15px;
}

/* 小图 */
.small-uploader {
  width: 100px;
  height: 100px;
  position: relative;
}
.small-uploader .avatar {
  width: 100px;
  height: 100px;
  display: block;
  position: relative;
}
.small-uploader div {
  width: 100px;
  height: 100px;
}

.small-uploader .avatar-uploader-icon {
  font-size: 18px;
  color: #8c939d;
  width: 100px;
  height: 100px;
  line-height: 100px !important;
  text-align: center;
}
.small-uploader .uploader-text {
  font-size: 11px;
  text-align: center;
  position: absolute;
  left: 39px;
  bottom: 0;
}
/* 移动端背景长图 */
.mbg-uploader {
  width: 132px;
  height: 236px;
  position: relative;
}
.mbg-uploader .avatar {
  width: 132px;
  height: 236px;
  display: block;
  position: relative;
}
.mbg-uploader div {
  width: 132px;
  height: 236px;
}

.mbg-uploader .avatar-uploader-icon {
  font-size: 18px;
  color: #8c939d;
  width: 132px;
  height: 236px;
  line-height: 100px !important;
  text-align: center;
}
.mbg-uploader .uploader-text {
  font-size: 11px;
  text-align: center;
  position: absolute;
  left: 54px;
  bottom: 0;
}

/* banner图上传 */
.banner-uploader {
  width: 127px;
  height: 72px;
  position: relative;
  margin: 0 auto;
}
.banner-uploader .avatar {
  width: 127px;
  height: 72px;
  display: block;
  position: relative;
}
.banner-uploader div {
  width: 127px;
  height: 72px;
}

.banner-uploader .avatar-uploader-icon {
  font-size: 16px;
  color: #8c939d;
  width: 127px;
  height: 72px;
  line-height: 72px !important;
  text-align: center;
}
.banner-uploader .uploader-text {
  font-size: 11px;
  text-align: center;
  position: absolute;
  left: 52px;
  bottom: 0;
}

/* role detail图上传 */
.roledet-uploader {
  width: 305px;
  height: 543px;
  position: relative;
}
.roledet-uploader .avatar {
  width: 305px;
  height: 543px;
  display: block;
  position: relative;
}
.roledet-uploader div {
  width: 305px;
  height: 543px;
}

.roledet-uploader .avatar-uploader-icon {
  font-size: 30px;
  color: #8c939d;
  width: 305px;
  height: 543px;
  line-height: 543px !important;
  text-align: center;
}
.roledet-uploader .uploader-text {
  font-size: 16px;
  text-align: center;
  position: absolute;
  left: 137px;
  bottom: 0;
}
</style>