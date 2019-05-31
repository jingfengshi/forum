<template>
    <ul class="pagination" v-if="shouldPaginate">
        <li class="page-item" v-show="prevUrl">
            <a class="page-link" href="#" aria-label="Previous" @click.prevent="page--">
                <span aria-hidden="true">上一頁</span>
                <span class="sr-only">上一頁</span>
            </a>
        </li>

        <li class="page-item" v-show="nextUrl">
            <a class="page-link" href="#" aria-label="Next" @click.prevent="page++">
                <span aria-hidden="true">下一頁;</span>
                <span class="sr-only">下一頁</span>
            </a>
        </li>
    </ul>
</template>

<script>
    export default {
        props:['dataSet'],
        data(){
            return {
                page:1,
                prevUrl:false,
                nextUrl:false
            }
        },
        watch:{
            dataSet(){
                this.page = this.dataSet.current_page;
                this.prevUrl = this.dataSet.prev_page_url;
                this.nextUrl = this.dataSet.next_page_url;
            },
            page(){
                this.broadcast().updateUrl();
            }
        },
        computed:{
            shouldPaginate(){
                return !!this.prevUrl || !!this.nextUrl
            }
        },
        methods:{
            broadcast(){
                this.$emit('changed',this.page);
                return this;
            },
            updateUrl(){
                history.pushState(null,null,'?page='+this.page);
            }
        }
    }
</script>

<style scoped>

</style>