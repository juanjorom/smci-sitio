<template>
    <v-container>
        <v-card tile>
            <v-card-title>Unidades</v-card-title>
            <v-card-text >
                <v-card  max-height="650" class="overflow-y-auto">
                    <v-col>
                        <v-list v-model="selected">
                            <v-list-item v-for="(uni, i) in unidades" :key="i">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{uni.nombre}}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Codigo: {{uni.codigo}} Permisionario: {{uni.permisionarioText}}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-action>
                                    <v-btn fab @click="unidad = uni; accion= 'Modificar'; edit=true" >
                                        <v-icon>
                                            mdi-pencil
                                        </v-icon>
                                    </v-btn>
                                </v-list-item-action>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-card>
            </v-card-text>
            <v-card-actions>
                <v-btn
                absolute
                dark
                fab
                bottom
                right
                color="pink"
                @click="modal=true; accion='Agregar'">
                <v-icon>mdi-plus</v-icon>
            </v-btn>
            </v-card-actions>
        </v-card>
        <v-dialog v-model="modal" max-width="400">
            <v-card>
                <v-card-title>{{accion}}</v-card-title>
                <v-card-text>
                    <v-text-field label="Nombre" v-model="unidad.nombre"></v-text-field>
                    <v-text-field label="Codigo" v-model="unidad.codigo"></v-text-field>
                    <v-select v-model="unidad.permisionario" label="Permisionario" :items="permisionarios" item-text="nombre" item-value="clave" ></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="agregarUnidad(accion)" color="success" >Agregar</v-btn>
                    <v-btn @click="modal=false; limpiar() " color="error" >Cancelar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="edit" max-width="400">
            <v-card>
                <v-card-title>{{accion}}</v-card-title>
                <v-card-text>
                    <v-select v-model="unidad.permisionario" label="Permisionario" :items="permisionarios" item-text="nombre" item-value="clave" ></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="agregarUnidad(accion)" color="success" >Cambiar</v-btn>
                    <v-btn @click="edit=false; limpiar() " color="error" >Cancelar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-overlay v-model="loader" absolute >
            <v-progress-circular indeterminate :size="100" :width="10"></v-progress-circular>
        </v-overlay>
        <v-dialog v-model="succes" max-width="400">
            <v-card>
                <v-card-title>Alerta</v-card-title>
                <v-card-text>{{mensaje}}</v-card-text>
                <v-card-actions>
                    <v-btn @click="succes = false">Ok</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
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
            this.traerUnidades()
            this.traerPermisionarios()
        }
    },
    data: () =>({
        accion: "",
        modal: false,
        loader: false,
        succes: false,
        selected: "",
        mensaje: "",
        unidad: {
            nombre: "",
            codigo: "",
            permisionario: "",
            permisionarioText: ""
        },
        edit: false
    }),
    computed:{
        ...mapGetters({
            unidades: 'cajeras/getAllUnidades',
            permisionarios: 'cajeras/getPermisionarios',
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess',
            roles: 'admin/getRoles',
        })
    },
    methods: {
        ...mapActions({
            traerUnidades: 'cajeras/getAllUnidadesServer',
            traerPermisionarios: 'cajeras/getAllPermisionarios',
            addUnidad: 'admin/addUnidad',
            change: 'admin/changeUnidad'
        }),
        limpiar(){
            this.unidad = {
                nombre: "",
                codigo: "",
                permisionario: ""
            }
        },
        ver(){
            console.log(this.unidad);
        },
        async agregarUnidad(accion){
            this.loader = true
            this.modal = false
            if(accion == "Agregar"){
                if(this.unidad.nombre!="" && this.unidad.codigo!="" && this.unidad.permisionario!="" ){
                    if(await this.addUnidad(this.unidad)){
                        this.mensaje = "Unidad Añadida con éxito"
                        this.loader = false
                        this.limpiar()        
                        this.succes = true
                    }else{
                        this.mensaje= "Error al añadir la unidad"
                        this.loader = false
                        this.succes = true
                    }
                }else{
                    this.mensaje = "Por favor llene todos los campos"
                    this.loader = false
                    this.succes = true
                }
            }else if(accion == "Modificar"){
                if(this.unidad.nombre!="" && this.unidad.codigo!="" && this.unidad.permisionario!=""){
                    if(await this.change(this.unidad)){
                        this.mensaje = "Unidad cambiada con exito"
                        this.loader = false
                        this.edit = false
                        this.limpiar()        
                        this.succes = true
                    }else{
                        this.mensaje= "Error al cambiar la unidad"
                        this.loader = false
                        this.succes = true
                    }
                }else{
                    this.mensaje = "Por favor llene todos los campos"
                    this.loader = false
                    this.succes = true
                }
            }
        }
    }
}
</script>

<style>

</style>