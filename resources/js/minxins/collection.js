export default {
    data(){
        items:[]
    },
    methods:{
        add(item){
            this.items.push(item)
            this.$emit('add')
        },
        remove(index){
            this.items.splice(index,1)
            this.$emit('remove')
            flash('Reply was deleted')
        }
    }
}