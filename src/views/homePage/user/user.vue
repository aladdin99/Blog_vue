<!--进入用户的博文空间/博文列表-->
<template>
    <div class="user">
        <!-- Top navigation bar-->
        <div :class="{'UbannerMenu':bannerScroll}" class="special_bar">
            <navigationBar></navigationBar>
        </div>
        <el-container class="mainbody_user">
            <!--Left user information bar-->
            <el-aside width="30rem" class="leftAside">
                <div>
                    <userLeftBar
                            :class="{'UleftMenu':leftScroll}"
                            :userId="userId"
                            :originalNum="original.length"
                            :lastest="lastest"
                            :collection_clip="collection_clip"></userLeftBar>
                    <span ref="searchBar"></span>
                </div>
            </el-aside>
            <!--Middle content preview section-->
            <el-main class="mainstay">
                <catalogPreview :userId="userId" :alldata="alldata" :original="original" @changeData="changeData"></catalogPreview>
            </el-main>
        </el-container>
    </div>
</template>

<script>
import navigationBar from "components/navigationBar.vue";
import userLeftBar from "components/user/userLeftBar.vue";
import catalogPreview from "components/user/catalogPreview.vue";
export default {
    name: "user",
    components: {
        navigationBar,userLeftBar,catalogPreview
    },
    data(){
        return{
            bannerScroll:false,
            leftScroll:false,
            userId: '',//当前页面的用户ID
            alldata: '',//所有文章
            original: '',//原创文章
            lastest: '',//最新文章
            collection_clip: ""//分类专栏
        }
    },
    methods:{
        getScrollPosition(){//监听banner栏坐标
            let top = document.documentElement.scrollTop || document.body.scrollTop;
            let left = document.documentElement.scrollLeft || document.body.scrollLeft;
            if(top){//获取右边栏底部标签'searchBar'距顶部的距离
                var searchBar = this.$refs.searchBar.getBoundingClientRect().top;
            }
            let currentHeigh = window.innerHeight;//当前内容高度  1235///680
            this.bannerScroll = (top>45)?true:false;
            this.leftScroll = (searchBar<currentHeigh)?true:false;
            return {top,left}
        },
        getArticle(){//获取文章信息
            let self = this;
            self.original = [];
            self.lastest = [];
            this.$axios.get('http://localhost/graduation_project/blog2/src/php/article/article_get',{
                params: {
                    id: self.userId//账户id
                }
            }).then(function(res){
                let article = res.data;
                article = article?article:[];
                article.forEach(item=>{
                    item.sortFlag = self.character(item.time);//插入sortFlag,根据这个排序更新时间
                    item.content = self.getSimpleText(item.content);//文章预览页面.只展示纯文本!
                    if(item.type=='原创'){
                        self.original.push(item);//原创文章
                    }
                });
                self.alldata = article;//全部文章
                let lastest_data = self.alldata.sort(self.compare('sortFlag',false));
                lastest_data.forEach((item,index)=>{
                    if(index<5){
                        self.lastest.push(item);//最新文章
                    }
                });
            });
        },
        character(time_into){//时间格式字符串转成数字
            let time_out = 0;
            time_out = time_into.replace(/-/g,'');
            time_out = time_out.replace(/:/g,'');
            time_out = time_out.replace(/ /g,'');
            time_out = parseInt(time_out);
            return time_out;
        },
        compare(property,flag){//根据数组中的某个元素的值进行排序;property传入的数组 flag：true正序 false倒序
            return function(a,b){
                var value1 = a[property];
                var value2 = b[property];
                return flag?(value1 - value2):(value2 - value1);
            }
        },
        getSimpleText(html){//富文本中提取纯文本(删掉所有的样式和图片,只保留纯文本和标点符号)
            var re1 = new RegExp("<.+?>","g");//匹配html标签的正则表达式，"g"是搜索匹配多个符合的内容
            var msg = html.replace(re1,'');//执行替换成空字符
            var result = msg.replace(/[\r\n]/g,"");//取消文本中的换行
            return result;
        },
        changeData(param){//用于子组件调用父组件的方法
            let flag = parseInt(param);
            switch(flag){
                case 0:
                    // this.alldata.forEach((item,index)=>{
                    //     console.log(item,index);
                    // });
                    this.alldata.sort(this.compare('sortFlag',false));
                    break;
                case 1:
                    // all_data = all_data.reverse();
                    // this.alldata = all_data;
                    this.alldata.sort(this.compare('sortFlag',true));
                    break;
                case 2:
                    alert("访问量待开发中......");
                    break;
                case 3:
                    alert("RSS订阅待开发中......");
                    break;
                default:
                    break;
            }
        },
        get_collection_clip(){
            let self = this;
            this.$axios.get('http://localhost/graduation_project/blog2/src/php/manage/sort_manage_get',{
                params: {
                    bind: self.userId,//账户id
                }
            }).then(function(res){
                self.collection_clip = res.data;
                self.collection_clip = self.collection_clip?self.collection_clip:[];
                self.alldata.forEach(item=>{
                    if(item.category[0]){
                        let ins = item.category[0].id;
                        self.collection_clip.forEach(info=>{
                            if(!info.essay){//如果不存在该字段 那么就添加
                                info.essay = [];
                            }
                            if(ins == info.id) {
                                info.essay.push(item);
                                // console.log(info);
                            }
                        });
                    }
                });
                // console.log(self.collection_clip);
            });
        }
    },
    mounted() {
        //监听banner栏坐标
        window.addEventListener('scroll',this.getScrollPosition,false);
        this.userId = this.$route.query.id;
        this.getArticle();
        this.get_collection_clip();
    },
    destroyed() {
        //监听banner栏坐标(摧毁)
        window.removeEventListener('scroll',this.getScrollPosition,false);
    }
}
</script>

<style lang="less">
    .user{
        height: 100%;
        min-height: 100vh;
        /*background-color: #343536;*/
        background: url("../../../assets/theme.jpg");
        background-size: 100% 100%;
        background-attachment: fixed;
    }
    .special_bar{background-color: rgba(85,84,85, 0.5);color: rgba(85,84,85, 0.5);z-index: 999;}
    .UbannerMenu {position: fixed;top: -5rem;width: 100%;}
    /*.UleftMenu {position: fixed;z-index: 100;width:30rem;bottom: calc(0% + 6rem);}//上滑超出直接将左侧组件置bottom为零(无需计算超出多少高度)*/

    .mainbody_user{padding: 0 12vw 0 0;margin-top: 1.5rem;
        .leftAside{position: relative;margin-right: 0vw; opacity:0;transition: all 2.5s;
            &:hover{opacity:1;transition: all 2.5s;}
        }
        .mainstay{padding: 0 2rem;text-align: left;height: 100%;transition: all 2.5s;}
    }
</style>