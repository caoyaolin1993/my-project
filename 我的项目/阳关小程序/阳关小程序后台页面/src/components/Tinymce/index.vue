<template>
  <div class="tinymce-container editor-container">
    <textarea class="tinymce-textarea" :id="tinymceId"></textarea>
    <!-- <div class="editor-custom-btn-container">
      <el-button icon='el-icon-upload' size="mini" @click=" dialogVisible=true" type="primary">上传图片</el-button>
    </div>

    <el-dialog @close="key++" width="40%" title="上传图片" :visible.sync="dialogVisible">
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="dialogSubmit">确 定</el-button>
      </div>
    </el-dialog> -->
  </div>
</template>

<script>
import plugins from "./plugins";
import toolbar from "./toolbar";
import { upload_img } from '@/api/common'

export default {
    name: "tinymce",
    props: {
		normal: {
			type: Boolean,
			default: false
		},
        id: {
            type: String
        },
        value: {
            type: String,
            default: ""
        },
        toolbar: {
            type: Array,
            required: false,
            default() {
                return [];
            }
        },
        menubar: {
            default: "file edit insert view format table"
        },
        height: {
            type: Number,
            required: false,
            default: 360
		},
		isOpenDialogAfter: {
			type: Boolean,
			default: false
		},
		accept: {
			default: "image/jpeg, image/png",
			type: String
		},
		maxSize: {
			default: 2097152,
			type: Number
		},
		withCredentials: {
			default: false,
			type: Boolean
		}
    },
    data() {
        return {
            hasChange: false,
            hasInit: false,
            tinymceId: this.id || "vue-tinymce-" + +new Date(),
            // key: 0,
            // fileList: [],
            // dialogVisible: false
        };
    },
    watch: {
        value(val) {
			// console.log('文本val变化',val);
            if (!this.hasChange && this.hasInit) { //加载完成并且没有改变值
                this.$nextTick(() =>
                    window.tinymce.get(this.tinymceId).setContent(val)
                );
			}
			// console.log('不正常使用富文本',!this.normal); //这里的不正常和正常只是用来指两个条件，并非真的不正常
			
			if(!this.normal){ //不正常的去使用这个组件就要各种判断（比如放在弹窗里面切换渲染）
				// 打开弹窗就设置个变量为false，不进入下面判断语句
				if(this.hasChange && this.hasInit && !this.isOpenDialogAfter){ //加载完组件初始化后这两个值都是true,这个情况下去赋值，可以保证此组件加载时不会报方法错误，可以保证弹框切换是渲染正确，
					this.$nextTick(() =>
						window.tinymce.get(this.tinymceId).setContent(val)
					);
				}
			}
        }
	},
	computed:{
		newVal(){
			return this.value;
		}
	},
    mounted() {
        this.initTinymce();
    },
    activated() {
        this.initTinymce();
    },
    deactivated() {
        this.destroyTinymce();
    },
    methods: {
        initTinymce() {
            const _this = this;
            window.tinymce.init({
                selector: `#${this.tinymceId}`,
                height: this.height,
                body_class: "panel-body ",
                object_resizing: false,
                toolbar: this.toolbar.length > 0 ? this.toolbar : toolbar,
                menubar: this.menubar,
                plugins: plugins,
                end_container_on_empty_block: true,
                powerpaste_word_import: "clean",
                code_dialog_height: 450,
                code_dialog_width: 1000,
                advlist_bullet_styles: "square",
                advlist_number_styles: "default",
                imagetools_cors_hosts: ["www.tinymce.com", "codepen.io"],
                default_link_target: "_blank",
				link_title: false,
				paste_data_images: true, // 允许粘贴图像
				  // 图片上传三个参数，图片数据，成功时的回调函数，失败时的回调函数
				images_upload_handler: function (blobInfo, success, failure) {
					if (blobInfo.blob().size > _this.maxSize) {
						failure('文件体积过大，不可超过2M!')
					}
					if (_this.accept.indexOf(blobInfo.blob().type) >= 0) {
						uploadPic()
					} else {
						failure('图片格式错误,只允许上传jpg或png格式的图片！')
					}
					function uploadPic () {
						let formData = new FormData()
						// 服务端接收文件的参数名，文件数据，文件名
						formData.append('upfile', blobInfo.blob(), blobInfo.filename())
						upload_img(formData)
						.then((res) => {
						// 这里返回的是你图片的地址
						console.log('上传成功',res);
						success(res.data)
						})
						.catch(() => {
						failure('上传失败')
						})
					}
				},

                init_instance_callback: editor => {
                    if (_this.value) {
                        editor.setContent(_this.value);
                    }
						console.log('init')
                    _this.hasInit = true;
                    editor.on("NodeChange Change KeyUp SetContent", () => {
                        this.hasChange = true;
						// _this.hasChange = true;
						// console.log('keyup')
                        this.$emit("input", editor.getContent());
                    });
                }
            }).then((res)=>{//初始化完成的回调
				// console.log(_this.newVal);
				// window.tinymce.get(this.tinymceId).setContent(_this.newVal)
			});
        },
        destroyTinymce() {
            if (window.tinymce.get(this.tinymceId)) {
                window.tinymce.get(this.tinymceId).destroy();
            }
        },
        setContent(value) {
            window.tinymce.get(this.tinymceId).setContent(value);
        },
        getContent() {
            window.tinymce.get(this.tinymceId).getContent();
        },
        // dialogSubmit() {
        //     this.fileList.forEach(v => {
        //         window.tinymce
        //             .get(this.tinymceId)
        //             .insertContent(
        //                 `<img class="wscnph" src="${v.response.data}" >`
        //             );
        //     });
        //     this.fileList = [];
        //     this.dialogVisible = false;
        // }
    },
    destroyed() {
		// console.log('摧毁');
		// 关闭弹窗不会销毁，要切换侧边栏或刷新
        this.destroyTinymce(); //失效
    }
};
</script>

<style scoped>
.tinymce-container {
    position: relative;
}
.tinymce-container>>>.mce-fullscreen {
    z-index: 10000;
}
.tinymce-textarea {
    visibility: hidden;
    z-index: -1;
}
.editor-custom-btn-container {
    position: absolute;
    right: 4px;
    top: -3px;
}
</style>
