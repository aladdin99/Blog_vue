<!--个人中心-->
<template>
    <div class="personal">
        <!-- Top navigation bar-->
        <div :class="{'UbannerMenu':bannerScroll}" class="special_bar">
            <navigationBar></navigationBar>
        </div>
        <el-container class="mainbody" style="width: 100%;">
            <!--Left user information bar-->
            <el-tabs
                    v-model="activeCode" :tab-position="tabPosition"
                    style="background-color: rgba(255,255,255,.8);" @tab-click="blogManage">
                <el-tab-pane label="个人资料" name="0"><personalData></personalData></el-tab-pane>
                <el-tab-pane label="我的收藏" name="1"><collection></collection></el-tab-pane>
<!--                <el-tab-pane label="我的勋章" name="2"><medal></medal></el-tab-pane>-->
                <el-tab-pane label="我的关注" name="3"><follow ref="follow"></follow></el-tab-pane>
                <el-tab-pane label="我的粉丝" name="4"><star ref="star"></star></el-tab-pane>
<!--                <el-tab-pane label="我的标签" name="5">我的标签（暂未开发）</el-tab-pane>-->
                <el-tab-pane label="我的博客" name="6" ></el-tab-pane>
<!--                <el-tab-pane label="签到赢福利" name="7"><clockIn></clockIn></el-tab-pane>-->
<!--                <el-tab-pane label="抽奖" name="8">抽奖（暂未开发）</el-tab-pane>-->
            </el-tabs>
<!--            <el-aside width="20rem">-->
<!--                <ul class="leftBar">-->
<!--                    <li style="background-color: #b20b13;color: #fff;">个人资料</li>-->
<!--                    <li>我的收藏</li>-->
<!--                    <li>我的勋章</li>-->
<!--                    <li>我关注的人</li>-->
<!--                    <li>我的粉丝</li>-->
<!--                    <li>我的标签</li>-->
<!--                    <li>我的博客</li>-->
<!--                    <li>我的问答</li>-->
<!--                    <li>签到赢福利</li>-->
<!--                    <li>抽奖</li>-->
<!--                </ul>-->

<!--            </el-aside>-->
<!--            &lt;!&ndash;Middle content preview section&ndash;&gt;-->
<!--            <el-main class="mainstay" style="box-shadow: 1px 2px 3px #e0e0e0;background-color: #fff;">-->

<!--            </el-main>-->
        </el-container>
        <div style="margin-top: 1rem;display: inline-block;"></div>
    </div>
</template>

<script>
    import navigationBar from "components/navigationBar.vue";
    import personalData from "components/personal/dataInfo.vue";
    import collection from "components/personal/collection.vue";
    import follow from "components/personal/follow.vue";
    import star from "components/personal/star.vue";
    // import medal from "components/personal/medal.vue";
    // import clockIn from "components/personal/clockIn.vue";
    export default {
        name: "user",
        components: {
            //medal clockIn
            navigationBar,personalData,collection,follow,star,
        },
        data(){
            return{
                bannerScroll:false,
                leftScroll:false,
                tabPosition: 'left',
                activeCode: '0',
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
            blogManage(targetName){
                if(targetName.index=='4'){
                    this.$router.push('/manage/managing');
                }
                if(targetName.index=='2'){//关注
                    this.$refs.follow.getFollow();
                }
                if(targetName.index=='2'){//粉丝
                    this.$refs.star.init();
                }
            },
            getFollow(){//调用关注页面方法
                alert("调用关注页面方法");
            }
        },
        mounted() {
            this.currentId = localStorage.getItem('email');//当前登陆id
            if(this.currentId==null){
                this.$router.push({path: "/index",redirect: '/index'});//路由重定向
            }

            this.activeCode = this.$route.query.activeCode?this.$route.query.activeCode:'0';
            //监听banner栏坐标
            window.addEventListener('scroll',this.getScrollPosition,false);
        },
        destroyed() {
            //监听banner栏坐标(摧毁)
            window.removeEventListener('scroll',this.getScrollPosition,false)
        }
    }
</script>

<style lang="less">
    .personal{
        height: 100%;
        min-height: 100vh;
        /*background-color: #f5f6f7;*/
        background: url("../../assets/theme.jpg");
        background-size: 100% 100%;
        background-attachment: fixed;
    }
    .special_bar{background-color: rgba(85,84,85, 0.5);color: rgba(85,84,85, 0.5);z-index: 999;}
    .personal{
        .mainbody{padding: 0 12vw;margin-top: 1.5rem;
            .leftBar{list-style: none;text-align: left;cursor: pointer;font-size: 1.4rem;border: 1px solid #E6E6E6;display: inline-block;width: 100%;box-sizing: border-box;
                li{padding: 1rem 1.5rem;box-sizing: border-box; &:hover{background-color: #FDE0EC;color: #b20b13;}}
            }
            .mainstay{padding: 0 3rem;text-align: left;height: auto;
                .bannerTop{font-size: 2rem;color: #3d3d3d;line-height: 9rem;border-bottom: 1px solid #e0e0e0;font-weight: bold;}
            }
        }
    }
</style>