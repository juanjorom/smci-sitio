<template>
    <v-container>
        <v-card>
            <v-card-title>Movimientos</v-card-title>
            <v-card-text >
                <v-row>
                    Dinero actual: {{recaudado.monto}}, Ultimo movimiento: {{recaudado.fechaHora}}
                </v-row>
                <v-row>
                    <v-col cols="4">
                        <v-card>
                            <v-card-title>Entradas</v-card-title>
                            <v-card-text>
                                <v-data-table :items="movimientos.entradas" :headers="headers" height="250"></v-data-table>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="4">
                        <v-card>
                            <v-card-title>Salidas</v-card-title>
                            <v-card-text>
                                <v-data-table :items="movimientos.salidas" :headers="headers" height="250"></v-data-table>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="4">
                        <v-card>
                            <v-card-title>Ajustes</v-card-title>
                            <v-card-text>
                                <v-data-table :items="movimientos.ajustes" :headers="headers" height="250"></v-data-table>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn
                dark
                bottom
                right
                color="success"
                :to="{name:'liquidarPermisionario'}">
                    <v-icon>mdi-cash</v-icon>
                    Entrega a Permisionario
                </v-btn>
                <v-btn
                dark
                bottom
                right
                color="success"
                @click="modal=true">
                    <v-icon>mdi-cash</v-icon>
                    Ingresar Pago
                </v-btn>
                <v-btn
                dark
                bottom
                right
                color="success"
                @click="retiro=true">
                    <v-icon>mdi-cash</v-icon>
                    Retiro de Efectivo
                </v-btn>
            </v-card-actions>
        </v-card>
        <v-dialog max-width="400"  v-model="modal" persistent>
            <v-card class="overflow-y-auto">
                <v-card-title>Ingresar Pago</v-card-title>
                <v-card-text>
                    <v-select :items="conceptos" v-model="pago.concepto" label="Concepto" ></v-select>
                    <v-text-field v-model="pago.especificacion" v-if="pago.concepto=='Otro'" label="Especifique"></v-text-field>
                    <v-text-field v-model="pago.monto" type="number" prefix="$" label="Monto" ></v-text-field>
                    <v-menu max-width="150" offset-y  max-height="300">
                        <template v-slot:activator="{on, attrs}">
                            <v-text-field v-model="unidad" label="Unidad" v-bind="attrs" v-on="on" @focus="temporal=''" @keyup="temporal=unidad" @blur="opacar('unidades', 'unidad')"></v-text-field>
                        </template>
                        <v-list v-model="pago.unidadCodigo" >
                            <v-list-item v-for="(item, index) in unidades" :key="index" @click="unidad=item.nombre; pago.unidadCodigo=item.codigo" >
                                <v-list-item-title>{{item.nombre}}</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-btn @click="auth=true" :disabled="activar" color="success" >Pagar</v-btn>
                    <v-btn @click="modal=false; pago= {concepto: '', monto: '', especificacion: '', unidadCodigo: '' }; unidad= '' " color="error">Cancelar</v-btn>
                </v-card-text>
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
        <v-dialog v-model="retiro" max-width="300" persistent>
            <v-card>
                <v-card-title>Retirar Dinero</v-card-title>
                <v-card-text>
                    <v-text-field label="Monto" v-model="retirar.monto" type="number" ></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="verify = true">Ok</v-btn>
                    <v-btn @click="retiro=false; retirar.monto='';" >Cancelar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="verify" max-width="300" persistent >
            <v-card>
                <v-card-title>Se Requiere Administrador</v-card-title>
                <v-card-text>
                    <v-text-field label="Usuario" v-model="retirar.user"></v-text-field>
                    <v-text-field label="Contraseña" v-model="retirar.password" type="password" ></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="hacerRetiro()">Ok</v-btn>
                    <v-btn @click="verify=false">Cancelar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
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
        if(this.logeado == null && this.sesion == false){
            this.$router.push('login')
        }else{
            this.traerMovimientos()
            this.traerRecaudado()
            this.traerUnidades()
        }
    },
    data: () =>({
        modal: false,
        verify: false,
        accion: "",
        selected: "",
        password: "",
        succes: false,
        mensaje: "",
        pago: {
            concepto: "",
            monto: "",
            unidadCodigo: "",
            especificacion: ""
        },
        headers: [
            {
                text: "Fecha y hora",
                value: "fechaHora"
            },
            {
                text: "Monto",
                value: "monto"
            },
            {
                text: "Descripcion",
                value: "descripcion"
            }
        ],
        monto: "",
        conceptos: ["Pago Administrativo", "Pago Monitoreo", "Otro"],
        unidad: "",
        temporal: "",
        unidadCodigo: "",
        loader: false,
        auth: false,
        retiro: false,
        retirar: {monto: "", user: "", password: ""}
    }),
    computed: {
        ...mapGetters({
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess',
            movimientos: 'cajeras/getMovimientos',
            recaudado: 'cajeras/getRecaudado',
            allUnidades: 'cajeras/getAllUnidades',
            someUnidades: 'cajeras/getUnidadesFilter',
        }),
        unidades(){
            if(this.temporal!=""){
                return this.someUnidades(this.temporal)
            }
            return this.allUnidades
        },
        activar(){
            if(this.pago.monto!="" && this.pago.concepto!="" && this.pago.unidadCodigo!=""){
                if(this.pago.concepto=="Otro" && this.pago.especificacion==""){
                    return true
                }
                return false
            }
            return true
        }
    },
    methods: {
        ...mapActions({
            traerMovimientos: 'cajeras/getMovimientosServer',
            validar: 'logdata/validarPassword',
            traerRecaudado: 'cajeras/getRecaudadoServer',
            traerUnidades: 'cajeras/getAllUnidadesServer', 
            pagar: 'cajeras/ingresarPago',
            realizarRetiro: 'cajeras/realizarRetiro'
        }),
        opacar(arreglo, valor){
            if(!this[arreglo].includes(this[valor])){
                this[valor]=""
            }
        },
        async autenticar(){
            this.loader = true
            this.auth = false
            this.modal = false
            if(this.password!=""){
                if(await this.validar(this.password)){
                    this.password=""
                    this.auth=false
                    this.hacerPago()
                }else{
                    this.mensaje = "Contraseña incorrecta"
                    this.succes = true
                    this.loader = false
                    this.auth = true
                    this.modal = true
                }
            }else{
                this.mensaje = "Ingrese Contraseña"
                this.succes = true
                this.loader = false
                this.auth = true
                this.modal = true
            }
        },
        async hacerPago(){
            if(this.pago.concepto=="Otro"){
                this.pago.concepto = this.pago.especificacion
            }
            if(await this.pagar(this.pago)){
                this.mensaje = "Pago realizado con éxito"
                this.succes = true
                this.loader = false
                this.pago = {
                    concepto: "",
                    monto: "",
                    unidadCodigo: "",
                    especificacion: ""
                }
                this.unidad = ""
            }else{
                this.mensaje = "Error al guardar el pago"
                this.succes = true
                this.loader = false
                this.modal = true
            }
        },
        isEnter(event){
            if(event.key=="Enter"){
                this.autenticar()
            }
        },
        async hacerRetiro(){
            this.loader = true
            this.verify = false
            this.retiro = false
            if(this.retirar.monto!="" && this.retirar.admin!="" && this.retirar.password!=""){
                if(await this.realizarRetiro(this.retirar)){
                    this.mensaje = "Retiro Realizado"
                    this.succes = true
                    this.loader = false
                    this.retirar = {monto: "", user: "", password: ""}
                }else{
                    this.mensaje= "Error al realizar el retiro"
                    this.retirar.user = ""
                    this.retirar.password = ""
                    this.succes = true
                    this.loader = false
                    this.retiro = true
                }
            }else{
                this.mensaje = "Llene todos los campos"
                this.succes = true
                this.loader= false
                this.retiro = true
            }
        }
    }
}
</script>

<style>

</style>