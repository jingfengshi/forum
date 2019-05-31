<template>
   <li class="nav-item dropdown" v-if="notifications.length">

      <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        <span class="glyphicon glyphicon-bell"></span>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

         <a v-for="notification in notifications" class="dropdown-item" href="">
            <a :href="notification.data.link" v-text="notification.data.message" @click="markAsRead(notification)"></a>
         </a>
      </div>

   </li>
</template>

<script>
    export default {
         data(){
             return {
                 notifications:false
             }
         },
        created(){
             axios.get("/profiles/"+window.App.user.name+"/notifications")
                 .then(response=>this.notifications= response.data);
        },
        methods:{
             markAsRead(notification){
               axios.delete("/profiles/"+window.App.user.name+"/notifications/"+notification.id)
             }
        }


    }
</script>

<style scoped>

</style>