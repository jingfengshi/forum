<template>
    <div :id="'reply-'+id" class="card mb-3 " :class="isBest? 'bg-success':''">

        <div class="card-header">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/'+reply.owner.name"
                        v-text="reply.owner.name">
                    </a>
                    said:
                    <span v-text="ago"></span>
                   ...
                </h5>

                <!--@if(Auth::check())-->
                <div v-if="signedIn">
                    <favorite :reply="reply"></favorite>
                </div>
                <!--@endif-->

            </div>
        </div>

        <div class="card-body">
            <div v-if="editing">
                <form  @submit="update">
                    <div class="form-group">
                        <div class="form-control" v-html="body" required>{{body}}</div>
                    </div>
                    <button class="btn btn-sm btn-primary" >Update</button>
                    <button class="btn btn-sm btn-link" @click="editing = false" type="button">Cancel</button>
                </form>

            </div>
            <div v-else v-html="body">
            </div>

        </div>

        <div class="card-footer level" v-if="authorize('owns',reply) || authorize('owns',reply.thread) ">
            <div v-if="authorize('owns',reply)">
                <button class="btn btn-dark btn-sm mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-danger btn-sm mr-1" @click="destroy">delete</button>
            </div>

            <button class="btn btn-primary btn-sm ml-a" @click="markBestReply"  v-if="authorize('owns',reply.thread)" >设为最佳?</button>
        </div>
    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';
    export default {
        props:['reply'],
        components:{
            Favorite
        },
        data(){
            return {
                editing:false,
                id:this.reply.id,
                body:this.reply.body,
                thread:window.thread
            }
        },
        computed:{
            isBest(){
                return this.thread.best_reply_id == this.id;
            },
            ago(){
                return moment(this.reply.created_at).fromNow();
            },


        },


        methods:{
            update(){
                axios.patch('/replies/'+this.reply.id,{
                    body:this.body
                }).then(response=>{
                    this.editing=false

                    flash('updated')
                }).catch(error=>{
                    flash(error.response.data,'danger');
                })


            },
            destroy(){
                axios.delete('/replies/'+this.reply.id)

                this.$emit('deleted',this.reply.id)
                // $(this.$el).fadeOut(300,()=>{
                //     flash('Your reply has been deleted .')
                // });

            },
            markBestReply(){
                axios.post('/replies/'+this.reply.id+'/best')

                this.thread.best_reply_id = this.id;
            }
        }
    }
</script>

<style scoped>

</style>