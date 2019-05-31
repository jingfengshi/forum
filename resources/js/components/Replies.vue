<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :reply="reply" @deleted="remove(index)"></reply>
        </div>
        <paginator :dataSet="dataSet" @changed="fetch"></paginator>

        <p v-if="$parent.locked">
            This thread has been locked. No more replies are allowed.
        </p>
        <new-reply @created="add" v-else ></new-reply>
    </div>
</template>

<script>
    import Reply from './Reply';
    import NewReply from './NewReply';
    import collection from '../minxins/collection'
    export default {
        name: "Replies.vue",
        mixins:[collection],
        components:{Reply,NewReply},
        data(){
            return {
                dataSet:false,
                items:[],
            }
        },
        created(){
            this.fetch()
        },
        methods:{
            fetch(page){
                axios.get(this.url(page))
                    .then(this.refresh)
            },
            refresh(response){
                this.dataSet =response.data;
                this.items =response.data.data;

                window.scrollTo(0,0)

            },
            url(page){
                if(!page){
                   let query= location.search.match(/page=(\d+)/);
                   page = query ? query[1]:1;
                }
                return `${location.pathname}/replies?page=${page}`;
            },

        }
    }
</script>

<style scoped>

</style>