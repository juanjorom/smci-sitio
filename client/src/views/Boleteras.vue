<template>
    <v-container>
        <v-col>
            <v-card tile>
                <v-card-title>Boleteras Agregadas</v-card-title>
                <v-card-text  v-if="allBoleteras.length>0"   >
                    <v-row>
                        <v-col cols="6">
                            <v-select :items="filtros" v-model="filtro" label="Filtrar por estado"></v-select>
                        </v-col>
                        <v-col cols="6">
                            <v-select :items="permisionarios" v-model="permisionario" label="Filtrar por permisionario"></v-select>
                        </v-col>
                    </v-row>
                    <v-card  max-height="650" class="overflow-y-auto">
                        <v-col>
                            <v-list v-model="selected">
                                <v-list-item v-for="(bol, i) in boleterasMostrar" :key="i">
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            Boletera: {{bol.codigo}}
                                        </v-list-item-title>
                                        <p class="font-weight-black">Inicia: {{bol.boletoInicial}}</p> 
                                        <v-list-item-subtitle>Permisionario: {{bol.permisionario}} Estado: {{bol.estado}}</v-list-item-subtitle>
                                    </v-list-item-content>
                                    <v-list-item-action>
                                            <v-btn fab :disabled="bol.estado!='NO ASIGNADA'" @click="codigo=bol.codigo; confirmar=true">
                                                <v-icon>
                                                    mdi-delete
                                                </v-icon>
                                            </v-btn>
                                        </v-list-item-action>
                                </v-list-item>
                            </v-list>
                        </v-col>
                    </v-card>
                </v-card-text>
                <v-col v-else>
                    <span>Todavía no hay Boleteras Agregadas</span>
                </v-col>
                <v-btn
                absolute
                dark
                fab
                top
                right
                color="pink"
                @click="modal=true">
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </v-card>
        </v-col>
        <v-dialog v-model="modal" max-width="800" persistent>
            <agregar @cerrar="modal=false"></agregar>
        </v-dialog>
        <v-dialog v-model="confirmar" max-width="400" @click:outside="cancel">
            <v-card>
                <v-card-title>Ingrese su contraseña</v-card-title>
                <v-card-text>
                    <v-text-field autofocus v-model="password" label="Contraseña" placeholder="Contraseña" type="password" @keydown="isEnter($event)"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="validarPassword">Ok</v-btn>
                    <v-btn @click="cancel()">Cancelar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
import agregar from '@/components/agregarBoletera'

import {  mapActions, mapGetters, mapMutations } from 'vuex'
export default {
    beforeMount(){
        if(this.logeado==null && this.sesion==false){
            this.$router.push('login')
        }else{
            this.getBoleteras()
        }
    },
    data: () => ({
        confirmar: false,
        codigo: "",
        password: "",
        boletera: {},
        selected: 0,
        permisionario: "",
        modal: false,
        filtros: ["COBRADA", "NO ASIGNADA", "ASIGNADA", "TODAS"],
        filtro: ""
    }),
    computed:{
        ...mapGetters({
            allBoleteras: 'cajeras/getAllBoleteras',
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess'
        }),
        boleterasMostrar(){
            if((this.filtro!="" && this.filtro!="TODAS") && (this.permisionario!= "" && this.permisionario!="TODOS")){
                return this.allBoleteras.filter(el => el.estado==this.filtro && el.permisionario == this.permisionario)
            }else if(this.permisionario!= "" && this.permisionario!="TODOS"){
                return this.allBoleteras.filter(el => el.permisionario==this.permisionario)
            }else if(this.filtro!="" && this.filtro!="TODAS"){
                return this.allBoleteras.filter(el => el.estado==this.filtro)
            }
            return this.allBoleteras
        },
        permisionarios(){
            var filtros = this.allBoleteras.map(el => el.permisionario)
            filtros.push("TODOS")
            return filtros
        }
    },

    methods: {
        ...mapMutations('cajeras',{
            getBole: 'setBoleteras'
        }),
        
        ...mapActions({
            addBoletera: 'cajeras/addBoletera',
            getBoleteras: 'cajeras/getAllBoleterasServer',
            eliminarBoletera: 'cajeras/deleteBoletera',
            validar: 'logdata/validarPassword'
        }),
        async delBoletera(codigo){
            if( await this.eliminarBoletera(codigo)){
                this.getBoleteras()
            }
        },
        isEnter(event){
            if(event.key=="Enter"){
                this.validarPassword()
            }
        },
        cancel(){
            this.confirmar = false
            this.codigo=""
        },
        async validarPassword(){
            if(this.password != ""){
                if(await this.validar(this.password)){
                    this.password = ""
                    this.confirmar = false
                    this.delBoletera(this.codigo)
                }
                else{
                    alert("Contraseña no valida")
                    this.password=""
                }
            }else{
                alert("Escriba la contraseña")
            }
        },
    },
    components: {
        agregar
    }
}
</script>

<style>
</style>