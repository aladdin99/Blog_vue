<template>
    <div class="collectionPik">
        <div class="bannerTop">
            <span v-show="changeFlag">我的收藏夹</span>
            <span @click="dialogForm(true)" v-show="changeFlag"><i class="el-icon-plus"></i>新建收藏夹</span>
            <div style="cursor: pointer;" @click="done(true)" v-show="!changeFlag"><i class="el-icon-arrow-left" style="margin-right: .1rem;font-weight: bold;"></i>返回我的收藏夹列表</div>
        </div>
        <ul class="collect" v-show="markFlag" v-if="changeFlag">
            <li v-for="item in favorites" :key="item.id">
                <div class="collectionName">
                    <span @click="done(false,item.title,item.describe,item.power,item.id,item.amount)"><i class="el-icon-lock" v-show="item.power==2" style="margin-right: .5rem;font-weight: bold;"></i>{{item.title}}</span>
                    <div>
                        <span>{{item.amount}}条内容</span>
                        <span>0人关注</span>
                    </div>
                </div>
                <span class="collectionInto" @click="done(false,item.title,item.describe,item.power,item.id,item.amount)"><i class="el-icon-arrow-right"></i></span>
            </li>
        </ul>
        <el-dialog class="newlyBuild" title="新建收藏夹" :visible.sync="dialogFormVisible" :close-on-click-modal="false" :show-close="false" width="35%">
            <ul>
                <li><span>标题</span>
                    <el-input
                            style="width: 80%;"
                            size="small"
                            placeholder="请填写收藏夹名称"
                            v-model="bookMark"
                            clearable>
                    </el-input>
                </li>
                <li><span>描述(选填)</span>
                    <el-input
                            type="textarea"
                            placeholder="请输入描述内容"
                            v-model="describe"
                            maxlength="50"
                            show-word-limit
                            resize="none"
                            rows="3"
                            style="margin-left: 1.7rem;width: 80%;"
                    ></el-input>
                </li>
                <li><span>私密</span>
                    <div>
                        <el-radio v-model="limit" label="1">公开 所有人可见</el-radio>
                        <el-radio v-model="limit" label="2">私密 只有你可以查看这个收藏夹</el-radio>
                    </div>
                </li>
            </ul>
            <div v-show="limit==2" style="margin: 2rem 0 0 20%;font-size: 1.2rem;color: #ca0c16;">转为私密收藏夹，关注者将被取消收藏与查看权限，请谨慎操作</div>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogForm(false)">取 消</el-button>
                <el-button type="primary" @click="dialogForm(false),buildCollection()">确 定</el-button>
            </div>
        </el-dialog>
        <div v-show="!changeFlag" >
            <div class="collect-detail">
                <div><i class="el-icon-lock" v-show="inner.power==2"></i>{{inner.title}}</div>
                <div>收藏夹描述：{{inner.describe}}</div>
                <div>
                    <span>{{inner.amount}}条内容</span>
                    <el-divider direction="vertical"></el-divider>
                    <span>0人关注</span>
                    <el-divider direction="vertical"></el-divider>
                    <span class="opera" @click="dialogForm(true)">编辑</span>
                    <el-divider direction="vertical"></el-divider>
                    <span class="opera" @click="dialogVisible = true">删除</span>
                </div>
            </div>
            <div class="collect-article" v-for="(item,index) in collectArticles" :key="index" v-show="collectArticles.length">
                <div class="collect-article-title">
                    <router-link target="_blank" :to="{path:'/index/user/details',query:{userId:item.authorId,articleId:item.articleId}}"><span>{{item.title}}</span></router-link>
                    <span @click="delCollect(item.pathId,item.bindId,item.articleId,index)">取消收藏</span>
                </div>
<!--                <div class="collect-article-describe">简介：1、用处vue的脚手架弄成功后(用命令 vue init webpack my-project)，是JavaScript</div>-->
                <div class="collect-article-by">
                    <span>{{item.author}}</span>
                    <el-divider direction="vertical"></el-divider>
                    <span>{{item.pubdate}}</span>
                </div>
            </div>
        </div>
        <el-dialog
                style="text-align: left;"
                title="删除收藏夹"
                :visible.sync="dialogVisible"
                width="30%"
                :close-on-click-modal="false"
                :show-close="false">
            <el-radio v-model="radio" label="1">一经删除（操作不可恢复！）</el-radio>
            <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">取 消</el-button>
            <el-button type="primary" @click="dialogVisible = false,deleteCollection()">确 定</el-button>
            </span>
        </el-dialog>
        <div class="article_none" v-show="!favorites.length">
            <img :src="none" >
            <div>空空如也</div>
        </div>
    </div>
</template>

<script>
    import none from "assets/none.jpg";//1.引入图片地址
    export default {
        name: "collection",
        data(){
            return{
                none:none,
                dialogFormVisible: false,
                bookMark: '',//收藏夹名称
                describe: '',//描述内容
                limit: 1,
                userId: '',//当前登陆者
                favorites: [],//收藏夹
                markFlag: true,
                changeFlag: true,
                inner: {
                    title: '',
                    describe: '',
                    power: 1,
                    id: '',
                    amount: 0,
                },
                dialogVisible: false,
                radio: '1',
                collectArticles: [],//收藏文章
            }
        },
        methods: {
            delCollect(pathId,bindId,articleId,index){//取消收藏
                let self = this;
                this.$axios.get('http://localhost/graduation_project/blog2/src/php/personal/collection/delCollection',{
                    params: {
                        pathId: pathId,//收藏夹id
                        bindId: bindId,//收藏夹用户id
                        articleId: articleId,//文章id
                        amount: self.inner.amount-1,//收藏夹内文章数减一
                    }
                }).then(function(){
                    self.inner.amount--;
                    self.collectArticles.splice(index, 1);
                });
            },
            dialogForm(flag){
                if(flag){
                    this.previewCollection();
                }
                return this.dialogFormVisible = flag;
            },
            done(flag,title,describe,power,id,amount){
                this.changeFlag = flag;
                this.inner.title = title;
                this.inner.describe = describe;
                this.inner.power = power;
                this.inner.id = id;
                this.inner.amount = amount;

                this.bookMark = title;
                this.describe = describe;
                this.limit = power;
                this.getCollection(id);
            },
            getCollection(pathId){//获取某个收藏夹内的所有文章
                let self = this;
                self.collectArticles = [];
                this.$axios.get('http://localhost/graduation_project/blog2/src/php/personal/collection/getCollection',{
                    params: {
                        pathId: pathId,//收藏夹id
                        bindId: self.userId,//当前用户id
                    }
                }).then(function(res){
                    self.collectArticles = res.data;
                });
            },
            buildCollection(){//新建收藏夹
                let self = this;
                //console.log(self.userId,self.bookMark,self.describe,self.limit);
                this.$axios.post('http://localhost/graduation_project/blog2/src/php/personal/collection/collection',{
                    status: 1,//新建收藏夹
                    bind: self.userId,//传入用户id
                    title: self.bookMark,//收藏夹标题
                    describe: self.describe,//收藏夹的描述
                    power: self.limit,//收藏夹权限
                    id: self.inner.id//收藏夹的id号（新建收藏夹时时没有,更新编辑收藏夹时才有）
                },{
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'} //加上这个
                }).then(function(res){
                    // console.log(JSON.parse(self.form.region));
                    console.log(res);
                    self.bookMark = '';
                    self.describe = '';
                    self.limit = 1;
                    self.previewCollection();
                }).catch(function(res){
                    console.log(res);
                });
            },
            previewCollection(){//预览收藏夹
                let self = this;
                this.$axios.post('http://localhost/graduation_project/blog2/src/php/personal/collection/collection',{
                    status: 2,//查询预览文件夹
                    bind: self.userId,//传入用户id
                },{
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'} //加上这个
                }).then(function(res){
                    self.favorites = res.data;
                    if(self.favorites[0]==0){
                        self.markFlag = false;
                    }
                }).catch(function(res){
                    console.log(res);
                });
            },
            deleteCollection(){//删除收藏夹
                let self = this;
                this.$axios.post('http://localhost/graduation_project/blog2/src/php/personal/collection/collection',{
                    status: 3,//删除文件夹
                    id: self.inner.id,//传入收藏夹id
                    bind: self.userId,//传入用户id
                },{
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'} //加上这个
                }).then(function(res){
                    console.log(res);
                    self.changeFlag = true;
                    self.previewCollection();
                }).catch(function(res){
                    console.log(res);
                });
            }
        },
        mounted() {
            this.userId = localStorage.getItem('email');//当前登陆id
            this.previewCollection();//查询文件夹
        }
    }
</script>

<style lang="less">
    .collectionPik{display: inline-block;width: 65vw;padding-right: 5rem;padding-left:2em;margin-bottom: 4rem;min-height: 68rem;
        .bannerTop{
            font-size: 1.6rem;color: #3d3d3d;line-height: 5rem;border-bottom: 1px solid #e0e0e0;font-weight: bold;
            display: flex;justify-content: space-between;padding-right: 2rem;
            span:nth-child(1){border-bottom: .2rem solid #ca0c16;cursor: pointer;}
            span:nth-child(2){color: #6b6b6b;font-weight: 400;cursor: pointer; i{padding-right: .5rem;}}
        }
        .collect{border-bottom: 1px solid #e0e0e0;list-style: none;padding-right: 2rem;
            li{display: flex;height: 6rem;justify-content: space-between;align-items: center;font-size: 1.4rem;color: #3d3d3d;font-weight: bold;cursor: pointer;border-bottom: 1px solid #e0e0e0;
                .collectionName{display: flex;height: 100%;align-items: center;
                    &>span{display: inline-block;min-width: 15rem;text-align: left;}
                    &>div{flex: 1;display: flex;margin-left: 5rem;flex-direction: column;align-items: center;border-left: 1px solid #e0e0e0;padding-left: 1rem;
                        span{flex:1;color: #6b6b6b;font-weight: 400;letter-spacing:2px;}
                    }
                }
                .collectionInto{i{font-size: 2rem;font-weight: bold;}}
            }
        }
        .newlyBuild{text-align: left;color: #6b6b6b;font-weight: 400;
            ul{list-style: none;height: 20rem;padding: 0;margin: 0;
                li{margin-bottom: 1.5rem;color: #6b6b6b;font-weight: 400;letter-spacing:2px;display: flex;justify-content: space-between;
                    &:last-child{margin-top: 2.5rem;
                        div{display: flex;flex-direction: column;width: 80%;padding: 1rem 0;}
                    }
                }
                .el-radio{margin-bottom: .5rem;}
            }
        }
        .collect-detail{display: flex;flex-direction: column;align-items: flex-start;margin-top: 1.5rem;border-bottom: 1px solid #e0e0e0;;
            &>div{line-height: 3.5rem;
                &:nth-child(1){font-weight: bold;
                    i{margin-right: .5rem;font-weight: bold;}
                }
                &:nth-child(2){color: #6B6B6B;font-weight: 400;letter-spacing:1px;}
                &:nth-child(3){color: #999;
                    .opera{color: #6B6B6B;cursor: pointer;}
                }
            }
        }
        .collect-article{text-align: left;padding: 2rem 2.5rem;background-color: #fafafa;margin-top: 1rem;
            &>div{padding-bottom: 1rem;}
            .collect-article-title{
                display: flex;justify-content: space-between;width: 100%;overflow: hidden;
                text-overflow:ellipsis;white-space: nowrap;align-items: center;cursor: pointer;
                span:nth-child(1){flex: 9;text-align: left;font-size: 1.6rem;color: #4d4d4d;font-weight: bold;}
                span:nth-child(2){flex: 1;text-align: right;color: #6b6b6b;font-size: 1.4rem;}
            }
            .collect-article-describe{color: #999;font-size: 1.4rem;}
            .collect-article-by{
                &>span:nth-child(1){color: #6b6b6b;}
                &>span:last-child(1){color: #999;}
            }
            &:hover{background-color: #eff2f5;}
        }
    }
    .article_none{margin-top: 10rem;
        img{width: 35rem;height: 25rem;}
        div{font-size: 1.6rem;color: #6B6B6B;font-weight: bold;letter-spacing: .2rem;margin: 1.5rem 0;}
    }
</style>