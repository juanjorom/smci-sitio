<template>
    <v-container>
        <v-card  max-height="650" class="overflow-y-auto">
            <v-card-title>Ingresar Pago</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="6">
                        <v-select :items="conceptos" v-model="concepto" label="Concepto"></v-select>
                    </v-col>
                    <v-col cols="6">
                        <v-text-field v-model="monto" type="number" prefix="$" label="Monto"></v-text-field>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="6">
                        <v-menu max-width="150" offset-y  max-height="300">
                            <template v-slot:activator="{on, attrs}">
                                <v-text-field v-model="unidad" label="Unidad" v-bind="attrs" v-on="on" @focus="temporal=''" @keyup="temporal=unidad" @blur="opacar('unidades', 'unidad')"></v-text-field>
                            </template>
                            <v-list v-model="unidadCodigo" >
                                <v-list-item v-for="(item, index) in unidades" :key="index" @click="unidad=item.nombre; unidadCodigo=item.codigo" >
                                    <v-list-item-title>{{item.nombre}}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-col>
                    <v-col cols="6">
                        <v-btn>Pagar</v-btn>
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
        if(this.logeado == null && this.sesion == false){
            this.$router.push('login')
        }else{
            this.traerUnidades()
        }
    },
    data: () => ({
        monto: "",
        concepto: "",
        conceptos: ["Pago Administrativo", "Pago Monitoreo"],
        unidad: "",
        temporal: "",
        unidadCodigo: ""
    }),
    computed: {
        ...mapGetters({
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess',
            allUnidades: 'cajeras/getAllUnidades',
            someUnidades: 'cajeras/getUnidadesFilter',
        }),
        unidades(){
            if(this.temporal!=""){
                return this.someUnidades(this.temporal)
            }
            return this.allUnidades
        },
    },
    methods:{
        ...mapActions({
            traerUnidades: 'cajeras/getAllUnidadesServer', 
        }),
        opacar(arreglo, valor){
            if(!this[arreglo].includes(this[valor])){
                this[valor]=""
            }
        },
    }
}
</script>

<style>

</style>