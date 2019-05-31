<template>

</template>

<script>
    import Replies from '../components/Replies';
    import dingyue from '../components/SubscribeButton';

    export default {
        props: ['thread'],

        components: {Replies,dingyue},

        data() {
            return {
                repliesCount:this.thread.replies_count,
                locked:this.thread.locked,
            }
        },
        methods:{
            toggleLock(){
                axios[this.locked ? 'delete':'post']('/locked-threads/'+this.thread.slug)
                this.locked = ! this.locked;
            }
        }
    }
</script>

<style scoped>

</style>