<template>
    <v-card>
        <v-card-title>Agregar Boletera</v-card-title>
        <v-card-text>
            <v-text-field v-model="inicia" label="Inicia" placeholder="Numero" type="number"></v-text-field>
            <v-text-field v-model="termina" label="Termina" placeholder="Numero" type="number"></v-text-field>
            <v-select v-model="permisionarioSelected" label="Permisionario" :items="permisionarios" item-text="nombre" item-value="clave" ></v-select>
        </v-card-text>
        <v-card-actions>
            <v-btn @click="modal=true" :disabled="boton">Agregar</v-btn>
        </v-card-actions>
        <v-dialog v-model="modal" max-width="400">
            <v-card>
                <v-card-title>Ingrese su contraseña</v-card-title>
                <v-card-text>
                    <v-text-field v-model="password" label="Contraseña" placeholder="Contraseña" type="password"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="validarPassword">Ok</v-btn>
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
        validarPassword(){
            if(this.password!=""){
                if(this.validar(this.password)){
                    this.password=""
                    this.modal=false
                    this.agregarBoletera()
                }
                else{
                    alert("Contraseña no valida")
                    this.password=""
                }
            }else{
                alert("Escriba la contraseña")
            }
        },
        agregarBoletera(){
            if(this.inicia!="" && this.permisionarioSelected!="" && this.termina!="")
            {
                if(parseInt(this.termina,10) >= parseInt(this.inicia,10)){
                    this.addBoletera({inicio: this.inicia, termina: this.termina, permisionario:this.permisionarioSelected})
                }else{
                    alert("EL numero donde termina debe ser mayor que donde inicia")
                }
            }
            else
            {
                alert("Por favor inserta un numero válido")
            }
            this.modal=false
        }
    }
}
</script>

<style>

</style>