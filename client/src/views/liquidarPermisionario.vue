<template>
    <v-container>
        <v-card>
            <v-card-title>Permisionarios</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <v-row>
                            <v-col cols="4" md="4" sm="12" v-for="(liquidacion, i) in liquidaciones" :key="i">
                                <v-card height="350" >
                                    <v-card-title>{{liquidacion.nombre}}</v-card-title>
                                    <v-card-text >
                                        <p>Venta: ${{liquidacion.venta}}</p>
                                        <p>Gastos: ${{liquidacion.gastos}}</p>
                                        <p>Utilidad: ${{liquidacion.utilidad}}</p>
                                        <p>Recaudado: ${{liquidacion.recaudado}}</p>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-btn :disabled="!liquidacion.activo" color="success" @click="params={id: liquidacion.id, monto: liquidacion.recaudado}; auth= true" >Entregar</v-btn>
                                        <v-btn :disabled="!liquidacion.activo" color="success" :to="{name: 'pagarPermisionario', params: {id: liquidacion.id}}">Ver detalles</v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-dialog v-model="auth" max-width="300" >
            <v-card>
                <v-card-title>Ingresar contraseña</v-card-title>
                <v-card-text>
                    <v-text-field label="Contraseña" v-model="password" type="password" @keydown="isEnter($event)"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="autenticar()">Ok</v-btn>
                    <v-btn @click="auth=false">Cancelar</v-btn>
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
            this.traerLiquidaciones()
        }
    },
    data: () => ({
        auth: false,
        password: "",
        loader: false,
        succes: false,
        mensaje: "",
        params: {}
    }),
    computed: {
        ...mapGetters({
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess',
            liquidaciones: 'cajeras/getLiquidaciones'
        })
    },
    methods: {
        ...mapActions({
            traerLiquidaciones: 'cajeras/getLiquidacionesServer',
            pagarPermisionario: 'cajeras/pagarPermisionario',
            validar: 'logdata/validarPassword'
        }),
        async pagar(){
            if(await this.pagarPermisionario(this.params)){
                this.mensaje = "Se registro el pago"
                this.loader= false
                this.succes = true
            }else{
                this.mensaje = "Error al registrar"
                this.loader= false
                this.succes = true
            }
        },
        async autenticar(){
            this.loader= true
            this.auth=false
            if(this.password!=""){
                if(await this.validar(this.password)){
                    this.password=""
                    this.pagar()
                }else{
                    this.mensaje = "Contraseña incorrecta"
                    this.loader= false
                    this.succes = true
                    this.auth= true
                }
            }else{
                this.mensaje = "Ingrese su contraseña"
                this.loader= false
                this.succes = true
                this.auth= true
            }
        },
        isEnter(event){
            if(event.key=="Enter"){
                this.autenticar()
            }
        },
    }
}
</script>

<style>

</style>