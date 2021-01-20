<template>
    <v-container>
        <v-card>
            <v-card-title>Vueltas</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="6">
                        <v-menu ref="menuInicio" v-model="menuInicio" :close-on-content-click="false"  transition="scale-transition" offset-y min-width="290px" max-height="400">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field v-model="inicio" label="Inicio" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
                            </template>
                            <v-date-picker v-model="inicio" no-title scrollable @input="menuInicio = false">
                            </v-date-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="6">
                        <v-menu ref="menuFin" v-model="menuFin" :close-on-content-click="false"  transition="scale-transition" offset-y min-width="290px" max-height="400">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field v-model="fin" label="Fin" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
                            </template>
                            <v-date-picker v-model="fin" no-title scrollable @input="menuFin = false">
                            </v-date-picker>
                        </v-menu>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="6">
                        <v-select label="Filtro" v-model="filtro" :items="filtros"></v-select>
                    </v-col>
                    <v-col cols="6">
                        <v-text-field v-model="filtrar" v-show="filtro!='' && filtro!='Sin filtro'"></v-text-field>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <v-data-table :items="registros" :headers="headers" height="350" ></v-data-table>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn to="/reportes">Regresar</v-btn>
            </v-card-actions>
        </v-card>
        <v-overlay v-model="loader" absolute>
            <v-progress-circular indeterminate :size="100" :width="10"></v-progress-circular>
        </v-overlay>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    beforeMount(){
        if(this.logeado==null && this.sesion==false){
            this.$router.push('login')
        }
        else{
            this.traerVueltas()
        }
    },
    data: () =>({
        inicio: new Date().toISOString().substr(0,10),
        fin: new Date().toISOString().substr(0,10),
        menuInicio: false,
        menuFin: false,
        loader: false,
        filtrar: "",
        filtro: "",
        headers: [
            {
                text: "Inicio",
                value: "inicio"
            },
            {
                text: "Fin",
                value: "fin"
            },
            {
                text: "Vuelta",
                value: "numero"
            },
            {
                text: "Unidad",
                value: "unidad"
            },
            {
                text: "Boletos",
                value: "boletos"
            },
            {
                text: "Ruta",
                value: "ruta"
            },
            {
                text: "Chofer",
                value: "chofer"
            },
            {
                text: "Bruto",
                value: "bruto"
            },
            {
                text: "Gastos",
                value: "gastos"
            },
            {
                text: "Monto",
                value: "monto"
            },
            {
                text: "Comision",
                value: "comision"
            },
            {
                text: "Comentarios",
                value: "comentarios"
            }
        ],
        filtros:["Sin filtro", "Chofer", "Ruta", "Unidad"]
    }),
    watch: {
        inicio: function(){
            this.traerVueltas()
        },
        fin: function(){
            this.traerVueltas()
        }
    },
    computed: {
        ...mapGetters({
            vueltas: 'reportes/getVueltas',
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess'
        }),
        registros(){
            if(this.filtro!="" && this.filtro!="Sin filtro"  && this.filtrar!=""){
                switch(this.filtro){
                    case "Chofer":
                        return this.vueltas.filter(el => el.chofer.includes(this.filtrar.toUpperCase()))
                    case "Ruta":
                        return this.vueltas.filter(el => el.ruta.includes(this.filtrar.toUpperCase()))
                    case "Unidad":
                        return this.vueltas.filter(el => el.unidad.includes(this.filtrar.toUpperCase()))
                }
            }    
            return this.vueltas
        }
    },
    methods: {
        ...mapActions({
            getVueltas: 'reportes/getVueltasServer'
        }),
        async traerVueltas(){
            this.loader = true
            await this.getVueltas({inicio: this.inicio, fin: this.fin})
            this.loader = false
        }
    }
}
</script>

<style>

</style>