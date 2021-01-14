<template>
    <v-container>
        <v-card tile>
            <v-card-title>Usuarios</v-card-title>
            <v-card-text >
                <v-card  max-height="650" class="overflow-y-auto">
                    <v-col>
                        <v-list v-model="selected">
                            <v-list-item v-for="(usuario, i) in usuarios" :key="i">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{usuario.nombre}}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Nickname: {{usuario.nickname}} Rol: {{usuario.rol}}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-action>
                                    <v-btn fab @click="llenarDatos(usuario); accion='Password'" >
                                        <v-icon>
                                            mdi-key
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
                    <v-text-field label="Contraseña" v-model="usuario.password" type="password"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="agregarUsuario(accion)" color="success" >Agregar</v-btn>
                    <v-btn @click="modal=false; limpiar() " color="error" >Cancelar</v-btn>
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
            this.traerUsuarios()
            this.traerRoles()
        }
    },
    mounted(){
        this.traerUsuarios()
        this.traerRoles()
    },
    data: () =>({
        accion: "",
        modal: false,
        loader: false,
        succes: false,
        selected: "",
        mensaje: "",
        usuario: {
            nombre: "",
            nickname: "",
            password: "",
            rol: ""
        }
    }),
    computed: {
        ...mapGetters({
            usuarios: 'admin/getUsuarios',
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess',
            roles: 'admin/getRoles',
            
        })
    },
    methods: {
        ...mapActions({
            traerUsuarios: 'admin/getUsuariosServer',
            traerRoles: 'admin/getRolesServer',
            addUser: 'admin/addUser',
            cambiar: 'admin/changePassword'
        }),
        llenarDatos(usuario){
            this.usuario = usuario
            this.modal = true
        },
        limpiar(){
            this.usuario = {
                nombre: "",
                nickname: "",
                password: "",
                rol: ""
            }
        },
        async agregarUsuario(accion){
            this.loader = true
            this.modal = false
            if(accion == "Agregar"){
                if(this.usuario.nombre!="" && this.usuario.nickname!="" && this.usuario.password!="" && this.usuario.rol){
                    if(await this.addUser(this.usuario)){
                        this.mensaje = "Usuario Añadido con éxito"
                        this.loader = false
                        this.limpiar()        
                        this.succes = true
                    }else{
                        this.mensaje= "Error al añadir al usuario"
                        this.loader = false
                        this.succes = true
                    }
                }else{
                    this.mensaje = "Por favor llene todos los campos"
                    this.loader = false
                    this.succes = true
                }
            }else if(accion == "Password"){
                if(this.usuario.nickname!="" && this.usuario.password!=""){
                    if(await this.cambiar({user: this.usuario.nickname, password: this.usuario.password})){
                        this.mensaje = "Contraseña cambiara"
                        this.modal = false
                        this.loader = false
                        this.limpiar()        
                        this.succes = true
                    }else{
                        this.mensaje= "Error al cambiar al contraseña"
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