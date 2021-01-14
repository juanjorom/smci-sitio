<template>
    <v-container>
        <v-col justify="center" cols="4">
            <v-card>
                <v-card-title>Cambiar Contraseña</v-card-title>
                <v-card-text>
                    <v-text-field v-model="passwords.actual" type="password" label="Actual"></v-text-field>
                    <v-text-field v-model="passwords.nueva" type="password" label="Nueva"></v-text-field>
                    <v-text-field v-model="passwords.confirmar" type="password" label="Confirmar"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="change()" color="success">Ok</v-btn>
                    <v-btn @click="cancel()" color="error">Cancelar</v-btn>
                </v-card-actions>
            </v-card>
        </v-col>
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
    },
    data: () => ({
        passwords: {
            actual: "",
            nueva: "",
            confirmar: ""
        },
        loader: false,
        succes: false,
        mensaje: ""
    }),
    computed: {
        ...mapGetters({
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess'
        })
    },
    methods:{
        ...mapActions({
            update: 'logdata/changePassword'
        }),
        async change(){
            this.loader= true
            if(this.passwords.actual!="" && this.passwords.nueva!="" && this.passwords.confirmar!=""){
                if(this.passwords.nueva==this.passwords.confirmar){
                    if(await this.update(this.passwords)){
                        this.mensaje= "Contraseña actualizada con exito"
                        this.passwords = {
                            actual: "",
                            nueva: "",
                            confirmar: ""
                        }
                        this.loader = false
                        this.succes = true
                    }else{
                        this.mensaje= "Error al actualizar la contraseña"
                        this.loader = false
                        this.succes = true
                    }
                }else{
                    this.mensaje= "Las contraseñas no coinciden"
                    this.loader = false
                    this.succes = true
                }
            }else{
                this.mensaje= "Por favor llene todos los campos"
                this.loader = false
                this.succes = true
            }
        },
        cancel(){
            this.passwords = {
                actual: "",
                nueva: "",
                confirmar: ""
            }
            this.$router.push('/dashboard')
        }
    }
}
</script>

<style>

</style>