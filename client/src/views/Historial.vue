<template>
    <v-container>
        <v-card>
            <v-card-title>Historial de Vueltas</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="6">
                        <v-menu ref="menuInicio" v-model="menuInicio" :close-on-content-click="false" :return-value.sync="inicio" transition="scale-transition" offset-y min-width="290px" max-height="400">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field v-model="inicio" label="Inicio" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
                            </template>
                            <v-date-picker v-model="inicio" no-title scrollable>
                                <v-spacer></v-spacer>
                                <v-btn text color="primary" @click="menuInicio = false">
                                    Cancel
                                </v-btn>
                                <v-btn text color="primary" @click="$refs.menuInicio.save(inicio)">
                                    OK
                                </v-btn>
                            </v-date-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="6">
                        <v-menu ref="menuFin" v-model="menuFin" :close-on-content-click="false" :return-value.sync="inicio" transition="scale-transition" offset-y min-width="290px" max-height="400">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field v-model="fin" label="Fin" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
                            </template>
                            <v-date-picker v-model="fin" no-title scrollable>
                                <v-spacer></v-spacer>
                                <v-btn text color="primary" @click="menuFin = false">
                                    Cancel
                                </v-btn>
                                <v-btn text color="primary" @click="$refs.menuFin.save(fin)">
                                    OK
                                </v-btn>
                            </v-date-picker>
                        </v-menu>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <v-data-table :items="vueltas" :headers="headers"></v-data-table>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
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
            this.traerVueltas({inicio: this.inicio, fin: this.fin})
        }
    },
    data: () =>({
        inicio: new  Date().toISOString().substr(0, 10),
        fin: new Date().toISOString().substr(0, 10),
        menuInicio: false,
        menuFin: false,
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
        ]
    }),
    watch: {
        inicio: function(){
            this.traerVueltas({inicio: this.inicio, fin: this.fin})
        },
        fin: function(){
            this.traerVueltas({inicio: this.inicio, fin: this.fin})
        }
    },
    computed: {
        ...mapGetters({
            vueltas: 'reportes/getVueltas',
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess'
        })
    },
    methods: {
        ...mapActions({
            traerVueltas: 'reportes/getVueltasServer'
        })
    }
}
</script>

<style>

</style>