<template>
    <v-container>
        <v-card tile>
            <v-card-title>Modulos</v-card-title>
            <v-card-text >
                <v-card  max-height="650" class="overflow-y-auto">
                    <v-col>
                        <v-list v-model="selected">
                            <v-list-item v-for="(modulo, i) in modulos" :key="i">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{modulo.modulo}}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        {{modulo.ruta}}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-action>
                                        <v-btn fab >
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
                top
                right
                color="pink"
                @click="modal=true">
                <v-icon>mdi-plus</v-icon>
            </v-btn>
            </v-card-actions>
        </v-card>
        <v-dialog v-model="modal" max-width="400">
            <v-card>
                <v-card-title>Agregar Modulo</v-card-title>
                <v-card-text>
                    <v-text-field label="Nombre" v-model="modulo"></v-text-field>
                    <v-text-field label="Ruta" v-model="ruta"></v-text-field>
                    <v-checkbox v-model="acceso" v-for="(rol, index) in roles" :key="index" :value="rol.id" :label="rol.rol"></v-checkbox>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="agregarModulo()">Agregar</v-btn>
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
        }else{
            this.traerRoles()
        }
    },
    data: () =>({
        selected: "",
        modal: false,
        acceso: [],
        ruta: "",
        modulo: "",
        loader: false,
        succes: false,
        mensaje: ""
    }),
    computed:{
        ...mapGetters({
            modulos: 'appdata/getAccesos',
            roles: 'admin/getRoles',
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess'
        })
    },
    methods: {
        ...mapActions({
            addModulo: 'admin/addModulo',
            traerRoles: 'admin/getRolesServer'
        }),
        async agregarModulo(){
            this.loader = true
            console.log(this.acceso);
            if(await this.addModulo({modulo: this.modulo, ruta: this.ruta, roles: this.acceso})){
                this.loader= false
                this.mensaje = "Modulo Añadido con exito"
                this.succes = true
                this.modal= false
                this.modulo= ""
                this.ruta= ""
                this.acceso= []
            }else{
                this.loader= false
                this.mensaje= "Error al añadir el modulo"
                this.succes = true
            }

        }
    }
}
</script>

<style>

</style>