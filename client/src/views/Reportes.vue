<template>
    <v-container>
        <v-card>
            <v-card-title>Reportes</v-card-title>
            <v-card-text>
                <v-row>
                    <v-btn x-large v-for="(rep, i) in reportes" :key="i" :to="{name: 'reporteVentas', params: {nombre: rep.reporte, data: rep.config, recurso: rep.ruta}}">
                        <v-icon>{{rep.icon}}</v-icon>
                        {{rep.reporte}}
                    </v-btn>
                </v-row>
                <v-row>
                    <router-view></router-view>
                </v-row>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    beforeMount(){
        if(this.logeado == null && this.sesion == false){
            this.$router.push('login')
        }
        else{
            this.traerReportes()
        }
    },
    computed: {
        ...mapGetters({
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess',
            reportes: 'reportes/getReportes'
        })
    },
    methods:{
        ...mapActions({
            traerReportes: 'reportes/getReportesServer'
        })
    }
}
</script>

<style>

</style>