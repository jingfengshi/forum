<template>
    <div>
        <div class="level">
            <img :src="avatar" alt="" width="40" height="40" class="mr-1">
            <h1 >
                {{user.name}}
                <small v-text="reputation"></small>
            </h1>

        </div>





        <form v-if="canUpdate"  method="post" enctype="multipart/form-data">
            <image-upload name="avatar" class ="mr-1" @loaded="onLoad"></image-upload>

        </form>



    </div>
</template>

<script>

    import ImageUpload from './ImageUpload.vue';
    export default {
        name: "AvatarForm",
        props:['user'],
        components:{ImageUpload},
        data(){
            return {
                avatar:this.user.avatar_path
            }
        },
        computed:{
            canUpdate(){
                return this.authroize(user=>user.id === this.user.id)
            },
            reputation(){
                return this.user.reputation + 'xp';
            }
        },
        methods:{
            onLoad(avatar){

                this.avatar = avatar.src;
                this.persist(avatar.file)
            },

            persist(avatar){

                let data = new FormData();

                data.append('avatar',avatar)
                axios.post(`/api/users/${this.user.name}/avatar`,data).
                    then(()=>flash('Avatar uploaded!'))
            }
        }
    }
</script>

<style scoped>

</style>