<template>
  <v-container>
    <v-row>
      <v-col cols="2" v-for="(dato, index) in datos" :key="index">
        <contador :datos="dato"></contador>
      </v-col>
    </v-row>
    <v-row>
      <!--Vista de Accesos directos-->
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex'
import contador from '@/components/contador'
export default {
  beforeMount(){
    if(this.logeado==null && this.sesion==false){
      this.$router.push('login')
    }
  },
  data: () => ({
    datos: [
      {
        propiedad: "Dinero",
        cantidad: "1200",
        prefijo: "$"
      },
      {
        propiedad: "Gastos totales",
        cantidad: "700",
        prefijo: "$"
      },
      {
        propiedad: "Vueltas Abiertas",
        cantidad: "10",
        prefijo: ""
      },
      {
        propiedad: "Vueltas Cerradas",
        cantidad: "10",
        prefijo: ""
      }
    ]
  }),
  computed: {
    ...mapGetters({
      logeado: 'logdata/getKey',
      sesion: 'logdata/getSucess'
    })
  },
  components: {
    contador
  }

}
</script>

<style>

</style>