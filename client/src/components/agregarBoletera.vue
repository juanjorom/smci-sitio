<template>
    <v-card>
        <v-card-title>Agregar Boletera</v-card-title>
        <v-card-text>
            <v-text-field v-model="inicia" label="Inicia" placeholder="Numero" type="number"></v-text-field>
            <v-text-field v-model="termina" label="Termina" placeholder="Numero" type="number"></v-text-field>
            <v-select v-model="permisionarioSelected" label="Permisionario" :items="permisionarios" item-text="nombre" item-value="clave" ></v-select>
        </v-card-text>
        <v-card-actions>
            <v-btn @click="modal=true" color="success" :disabled="boton">Agregar</v-btn>
            <v-btn @click="cancelar()" color="error">Cancelar</v-btn>
        </v-card-actions>
        <v-dialog v-model="modal" max-width="400">
            <v-card>
                <v-card-title>Ingrese su contraseña</v-card-title>
                <v-card-text>
                    <v-text-field v-model="password" label="Contraseña" placeholder="Contraseña" type="password" @keydown="isEnter($event)"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="validarPassword">Ok</v-btn>
                    <v-btn @click="modal=false">Cancelar</v-btn>
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
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    data: () =>({
        modal: false,
        password: "",
        inicia: "",
        termina: "",
        permisionarioSelected: "",
        loader: false,
        mensaje: "",
        succes: false
    }),
    mounted(){
        this.pedirPermisionarios()
    },
    computed: {
        ...mapGetters('cajeras',{
            permisionarios: 'getPermisionarios'
        }),
        boton(){
            if(this.inicia!="" &&  this.termina!="" && this.permisionarioSelected!=""){
                return false
            }
            return true
        }
    },
    methods: {
        ...mapActions({
            pedirPermisionarios: 'cajeras/getAllPermisionarios',
            addBoletera: 'cajeras/addBoletera',
            validar: 'logdata/validarPassword'
        }),
        cancelar(){
            this.password=""
            this.inicia = ""
            this.termina = ""
            this.permisionarioSelected = ""
            this.$emit('cerrar')
        },
        async validarPassword(){
            this.loader= true
            if(this.password!=""){
                if(await this.validar(this.password)){
                    this.password=""
                    this.modal=false
                    this.agregarBoletera()
                }
                else{
                    this.mensaje = "Contraseña no valida"
                    this.loader = false
                    this.succes = true
                    this.password=""
                }
            }else{
                this.mensaje= "Escriba la contraseña"
                this.loader= false
                this.succes= true
            }
        },
        isEnter(event){
            if(event.key=="Enter"){
                this.validarPassword()
            }
        },
        async agregarBoletera(){
            if(this.inicia!="" && this.permisionarioSelected!="" && this.termina!="")
            {
                if(parseInt(this.termina,10) >= parseInt(this.inicia,10)){
                    this.mensaje= await this.addBoletera({inicio: this.inicia, termina: this.termina, permisionario:this.permisionarioSelected})
                    this.mensaje= this.mensaje.mensaje
                    this.termina="",
                    this.inicia="",
                    this.permisionarioSelected=""
                    this.modal=false
                    this.loader= false,
                    this.succes= true
                }else{
                    this.mensaje = "EL numero donde termina debe ser mayor que donde inicia"
                    this.loader= false
                    this.succes= true
                }
            }
            else
            {
                this.mensaje = "Por favor inserta un numero válido"
                this.loader= false
                this.succes = true
            }
            this.modal=false
        }
    }
}
</script>

<style>

</style>