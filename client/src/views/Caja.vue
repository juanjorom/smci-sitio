<template>
    <router-view></router-view>
</template>

<script>
import {mapGetters} from 'vuex'
export default {
    beforeMount(){
        if(this.logeado==null && this.sesion==false){
        this.$router.push('login')
        }
    },
    computed: {
    ...mapGetters({
      logeado: 'logdata/getKey',
      sesion: 'logdata/getSucess'
    })
  }
}
</script>

<style>

</style>