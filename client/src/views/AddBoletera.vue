<template>
    <v-container>
        <v-row>
            <v-text-field label="Inserte boletera" v-model="boletera" placeholder="Codigo" type="text">
            </v-text-field>
            <v-btn @click="agregarBoletera()" >
                Agregar
            </v-btn>
        </v-row>
        <v-row>
            <v-list v-model="selected">
                <v-list-item v-for="bol in allBoleteras" :key="bol.id">
                    {{bol.id}}
                </v-list-item>
            </v-list>
        </v-row>
    </v-container>
</template>

<script>
import {  mapGetters, mapMutations } from 'vuex'
export default {
    data: () => ({
        boletera: "",
        selected: ""
    }),

    computed:{
        ...mapGetters('cajeras',{
            allBoleteras: 'getAllBoleteras'
        })
    },

    methods: {
        ...mapMutations('cajeras',{
            addBoletera: 'addBoletera'
        }),
        
        agregarBoletera(){
            if(this.boletera!="")
            {
                this.addBoletera(this.boletera)
            }
            else
            {
                alert("Por favor inserta un numero válido")
            }
        }
    }
}
</script>

<style>

</style>