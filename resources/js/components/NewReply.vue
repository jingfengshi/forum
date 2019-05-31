<template>
    <!--@if(auth()->check())-->
    <div v-if="signedIn">
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-group">
                    <textarea name="body" id="body" class="form-control textarea"
                              placeholder="说点什么吧"
                              required
                              v-model="body"
                    ></textarea>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-primary" type="submit"
                        @click="addReply">回复</button>
            </div>
        </div>
    </div>

    <p v-else class="text-center">请<a href="/login">登录</a> 后加入讨论</p>

</template>

<script>
    import 'at.js'
    import 'jquery.caret'
    export default {
       data(){
           return {
               body:'',
           }
       },
        computed:{
            signIn(){
                return window.App.signIn;
            }
        },
        mounted(){
            $("#body").atwho({
                at:'@',
                delay:750,
                callbacks:{
                    remoteFilter:function(query ,callback){
                       $.getJSON('/api/users',{name:query},function(usernames){
                            callback(usernames)
                       })
                    }
                }
            })
        },
        methods:{
           addReply(){
               axios.post(location.pathname +'/replies',{
                   body:this.body
               }).then(response=>{
                   this.body = '';

                   flash('Your reply has been posted');

                   this.$emit('created',response.data)
               }).catch(function(error){
                   console.log(error.response.data);
                   flash(error.response.data,'danger')
               })
           }
        }
    }
</script>

<style scoped>

</style>