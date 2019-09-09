<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <link rel="stylesheet" href="{$jsLink}/aliyun-css/imageManage.css">

    <div id="app" style="" v-cloak>
        <div>
            <el-container style="height: 550px; border: 1px solid #eee;">
                <el-aside width="300px" style="background-color: #fff;border-right:1px solid #eee;height: 100%;overflow: hidden;position: relative">
                    <div style="overflow: auto;height: 480px;border-bottom: 1px solid #eee">
                        <template v-for="(item,index) in cate_list">
                            <div  class="btn-block" v-bind:class="{ 'btn-block1': selectedCate == index }" @click="selectCate(index,item)">
                                {{item.cate_name}}
                            </div>
                        </template>
                    </div>
                    <div class="aside-bottom" style="position: absolute;margin-left: 15px;line-height: 45px;">
                        <el-button type="success" size="small" @click="addGroup">新增分组</el-button>
                        <el-button type="primary" size="small" @click="editGroup">编辑分组</el-button>
                        <el-button type="danger" size="small" @click="delGroup">删除分组</el-button>
                    </div>
                </el-aside>

                <el-container style="flex-wrap：wrap;!important;position: relative">
                    <el-header style="margin-top: 10px;">
                        <el-button style="float: left;" @click="getFilesUpload()" type="primary">选择视频</el-button>
                        <el-button style="float: left;" onclick="jiance();" type="primary">开始上传</el-button>
                        <el-button style="float: left;" type="success" @click="moveToGroup" v-show="start_move">移动至其他分组</el-button>
                        <div style="display: none;width:300px;height:30px;background-color:#ccc;float: left; margin-top: 5px; margin-left: 20px;" id="huitiao">
                            <div style="width:0%;height:30px;background-color:#409EFF;" id="jindu"></div>
                        </div>
                    </el-header>

                    <el-container style="padding-left: 20px;display:-webkit-box;!important;">
                        <template v-for="(item,index) in galleryList">
                            <div :key="index"
                                 class="imgListItme">
                                <img :src="item.cover_url"  @click="selectImgEvent(index)" style="width:140px;height: 140px;" />
                                <div style="position: absolute;bottom: 0;background-color: #f0f0f0;width: 142px;text-align: center;overflow: hidden"><span>{{item.filename}}</span>
                                </div>
                                <i class="el-icon-error" @click="delVideoListItme(item)" ></i>
                                <div v-if="type >0 ? isSelect ==  item.video_id: item.is_select " class="is_check" @click="selectImgEvent(index)">
                                    <span style="line-height: 142px;" class="el-icon-check"></span>
                                </div>
                            </div>
                        </template>
                    </el-container>


                    <el-footer>
                        <el-pagination
                            :page-size="pagination.limit"
                            :current-page.sync="pagination.page"
                            :total="pagination.total_items"
                            background
                            layout="prev, pager, next"
                            @current-change="getGalleryList"
                        >
                    </el-pagination>

                    </el-footer>
                    <div class="footer" style="padding-left:20px;background-color: #fff;margin-top: 10px;height: 66px;border-top:#eee;line-height: 66px; ">
                        <el-button type="success" @click="start_move = false" v-show="start_move"  style="width: 120px;">取消移动分组</el-button>
                        <el-button type="success" @click="moveGroup" v-show="!start_move" style="width: 120px;">开始移动分组</el-button>
                        <el-button type="primary" @click="confirm">确定</el-button>
                        <el-button type="default" @click="closePanel">取消</el-button>
                    </div>
                </el-container>
            </el-container>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    pagination: {
                        page: 1,
                        limit: 10,
                        total_pages: 0,
                        total_items: 0
                    },
                    galleryList : [], //视频列表
                    cate_id : "{$_GET['cate_id']}",
                    type : 1, //进入的类型
                    isSelect:'', //选择的视频
                    orgin_type : 1, //选择进入的类型
                    selectedCate:'', //分类的分组
                    cate_list:[],  //分类列表
                    start_move:false  //是否显示分类分组
                },
                watch: {},
                computed: {},
                filters: {},
                methods: {
                    getGroupList: function () {
                        //获取分组列表
                        var that = this;
                        $.ajax({
                            url: "{:U('VideoGroup/getGroupList')}",
                            data: {

                            },
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if(res.state){
                                    var data = res.data;
                                    that.cate_list = data.videoGroupList;
                                }
                                if(!that.cate_id)  that.cate_id = data.videoGroupList[0]['id'];
                                that.getGalleryList();
                            }
                        })
                    },
                    getFilesUpload:function(){
                        //选择视频
                        var that = this;
                        var files = $('#files');
                        files.trigger('click');
                    },
                    addGroup: function () {
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '添加分组',
                            content: "{:U('VideoGroup/addEditGroup')}",
                            area: ['600px', '220px'],
                            end: function (res) {
                                that.getGroupList();
                            }
                        })
                    },editGroup: function () {
                        var that = this;
                        layer.open({
                            type: 2,
                            title: ' 编辑分组',
                            content: "{:U('VideoGroup/addEditGroup')}&cate_id="+that.cate_id,
                            area: ['600px', '220px'],
                            end: function (res) {
                                that.getGroupList();
                            }
                        })
                    },delGroup: function () {
                        var that = this;
                        layer.confirm('确认删除该分类吗？',{btn:['确认','取消']}, function () {
                            $.ajax({
                                url: "{:U('VideoGroup/delGroup')}",
                                data: {
                                    cate_id:that.cate_id
                                },
                                dataType: 'json',
                                type: 'post',
                                success: function (res) {
                                    if(res.status){
                                        that.getGroupList();
                                        layer.closeAll();
                                    }else{
                                        layer.msg(res.msg)
                                    }
                                }
                            })
                        })
                    } , getGalleryList: function () {
                        //获取视频的列表
                        var that = this;
                        var where = {
                            page: that.pagination.page,
                            limit: that.pagination.limit,
                            group_id:that.cate_id
                        };
                        $.ajax({
                            url: "{:U('VideoPanel/getVideoList')}",
                            data: where,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                var data = res.data;
                                that.pagination.page = data.page;
                                that.pagination.limit = data.videoGroupList;
                                that.pagination.total_pages = data.total_pages;
                                that.pagination.total_items = data.total_items;
                                $('#cate_ids').val(that.cate_id);
                                var list = [];
                                data.items.map(function (item) {
                                    item.is_select = false;
                                    list.push(item);
                                });
                                that.galleryList = list
                            }
                        })
                    },moveGroup: function () {
                        //分组移动切换
                        this.start_move = true;
                        this.type = 2;
                    },moveToGroup:function(){
                        //选择需要移动分组
                        var that = this;
                        var arr = [];
                        that.galleryList.forEach(function (item,index) {
                            if(item.is_select){
                                arr.push(item);
                            }
                        });
                        if(arr.length == 0){
                            layer.msg('请选择要移动的视频');
                            return;
                        }
                        layer.open({
                            type: 2,
                            title: '移动至其它分组',
                            content: "{:U('VideoGroup/selectGroup')}",
                            area: ['380px;', '240px']
                        })
                    },
                    moveToOrderGroups: function (event) {
                        //执行分组切换的功能
                        var that = this;
                        cate_id = event.detail.files;
                        var arr = [];
                        that.galleryList.forEach(function (item,index) {
                            if(item.is_select){
                                arr.push(item);
                            }
                        });
                        $.ajax({
                            url: "{:U('VideoGroup/moveVideosToGroup')}",
                            data: {
                                cate_id : cate_id, //分组id
                                arr : arr //列表
                            }, dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                that.getGalleryList();  //视频列表
                                that.type = that.orgin_type;
                                that.start_move = false;
                            }
                        })
                    },
                    selectCate: function (index,item) {
                        //切换分组的点击效果
                        var that = this;
                        that.selectedCate = index;
                        that.cate_id = item.id;
                        that.getGalleryList();
                    },
                    delVideoListItme: function (item) {
                        var that = this;
                        layer.confirm('确认删除吗？',{btn:['确认','取消']}, function () {
                            $.ajax({
                                url: "{:U('VideoPanel/delVideoList')}",
                                data: {
                                    id:item.id
                                },
                                dataType: 'json',
                                type: 'post',
                                success: function (res) {
                                    that.getGalleryList();
                                    that.closePanel();
                                    that.type = that.orgin_type;
                                    that.start_move= false;
                                }
                            })
                        })
                    },
                    selectImgEvent: function (index) {
                        //点击选中的视频
                        this.galleryList[index]['is_select'] = true;
                        this.isSelect = this.galleryList[index].video_id;
                    },
                    confirm: function(){
                        var that= this;
                        files = that.isSelect;
                        if(that.type == 1){
                            event = new CustomEvent('ZTBCMS_ALIYUNVIDEO_VIDEO_FILE', {
                                detail: {
                                    files: files
                                }
                            });
                        }
                        window.parent.dispatchEvent(event);
                        this.closePanel();
                    },
                    closePanel: function(){
                        if(parent.window.layer){
                            parent.window.layer.closeAll();
                        }else{
                            window.close();
                        }
                    }
                },
                mounted: function () {
                    //触发分组的效果
                    window.addEventListener('MOVE_GROUP', this.moveToOrderGroups.bind(this));
                    //获取视频的列表
                    this.getGroupList();
                    //获取进入的类型
                    this.type = parseInt(this.getUrlQuery('type'));
                    //获取进入的类型
                    this.orgin_type = parseInt(this.getUrlQuery('type'));
                }
            })
        })
    </script>
</block>
