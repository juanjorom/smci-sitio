<template>
    <v-container>
        <v-card>
            <v-card-title>Recibir vuelta {{vuelta.numero}} de la {{vuelta.unidad}}</v-card-title>
            <v-card-subtitle>Chofer: {{vuelta.chofer}}</v-card-subtitle>
            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <v-card tile>
                            <v-card-text>
                                <v-row>
                                    <v-col cols="8">
                                    <v-card>
                                        <v-card-title>Boleteras</v-card-title>
                                        <v-card-text>
                                            <v-row>
                                                <v-col cols="8">
                                                    <v-menu max-width="300" offset-y>
                                                        <template v-slot:activator="{on, attrs}">
                                                            <v-text-field v-model="boletera" label="Boletera" placeholder="Ingrese la boletera a cobrar" v-bind="attrs" v-on="on" ></v-text-field>
                                                        </template>
                                                        <v-list>
                                                            <v-list-item v-for="(item, index) in boleterasCobrar" :key="index" @click="boletera=item.codigo" >
                                                                <v-list-item-title >{{item.codigo}}</v-list-item-title>
                                                            </v-list-item>
                                                        </v-list>
                                                    </v-menu>
                                                </v-col>
                                                <v-col cols="4">
                                                    <v-btn @click="prepararBoletera()" :disabled="boletera==''">Cobrar boletos</v-btn>
                                                </v-col>
                                            </v-row>
                                            <v-row>
                                                <v-col cols="12" >
                                                    <v-card class="overflow-y-auto" height="300" max-height="300">
                                                        <v-list>
                                                            <v-list-item v-for="(item, index) in boleterasCobradas" :key="index">
                                                                <v-list-item-icon>
                                                                    <v-icon>mdi-ticket</v-icon>
                                                                </v-list-item-icon>
                                                                <v-list-item-content>
                                                                    <v-list-item-title>{{item.codigo}}</v-list-item-title>
                                                                    <v-list-item-subtitle>${{item.monto}}</v-list-item-subtitle>
                                                                    <v-list-item-subtitle>Boletos del {{item.boletoInicial}} al {{item.boletoFinal}}</v-list-item-subtitle>
                                                                    <v-list-item-subtitle>Total de boletos: {{item.totalBoletos}}</v-list-item-subtitle>
                                                                    <v-list-item-subtitle>Restantes: {{item.sobrantes}}</v-list-item-subtitle>
                                                                </v-list-item-content>
                                                                <v-list-item-action>
                                                                    <v-btn @click="editarBoletera(item)">
                                                                        <v-icon>mdi-pencil</v-icon>
                                                                    </v-btn>
                                                                </v-list-item-action>
                                                            </v-list-item>
                                                        </v-list>
                                                    </v-card>
                                                </v-col>
                                            </v-row>
                                        </v-card-text>
                                    </v-card>  
                                </v-col>
                                <v-col cols="4">
                                    <v-card class="overflow-y-auto" height="500" max-height="500">  
                                        <v-card-title>Gastos</v-card-title>
                                        <v-card-text>
                                            <v-row>
                                                <v-btn @click="gastar=true">Agregar Gasto</v-btn>
                                            </v-row>
                                            <v-row>
                                                <v-col>
                                                    <v-list>
                                                    <v-list-item v-for="(item, index) in gastos" :key="index">
                                                        <v-list-item-content>
                                                            <v-list-item-title>${{item.monto}} de {{item.descripcion}}</v-list-item-title>
                                                        </v-list-item-content>
                                                        <v-list-item-action>
                                                                <v-btn color="error" small fab outlined @click="revGasto(item)">
                                                                    <v-icon>mdi-close</v-icon>
                                                                </v-btn>
                                                            </v-list-item-action>
                                                    </v-list-item>
                                                </v-list>
                                                </v-col>
                                            </v-row>
                                        </v-card-text>        
                                    </v-card>
                                </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
                <v-row> 
                    <v-col cols="4">
                        <v-card>
                            <v-card-title>Venta Bruta</v-card-title>
                            <v-card-text>
                                <p class="display-2">${{ventaBruta}}</p>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="4">
                        <v-card>
                            <v-card-title>Total de gastos</v-card-title>
                            <v-card-text>
                                <p class="display-2">${{gastosTotal}}</p>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="4">
                        <v-card>
                            <v-card-title>Total</v-card-title>
                            <v-card-text>
                                <p class="display-2">${{montoVuelta}}</p>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="6">
                        <v-card  height="200" >
                            <v-card-title>Entregado</v-card-title>
                            <v-card-text>
                                <v-text-field v-model="entregado" label="Cantidad" prefix="$" @keydown="validarTeclaEntregado($event)"></v-text-field>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="6">
                        <v-card class="overflow-y-auto" height="200" max-height="200" >
                            <v-card-title>Comentarios</v-card-title>
                            <v-card-text>
                                <v-textarea auto-grow label="Escriba aqui las observaciones" v-model="comentarios"></v-textarea>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="success" @click="auth=true">Cobrar</v-btn>
                <v-btn to="/caja" color="error" >Cancelar</v-btn>
            </v-card-actions>
        </v-card>
        <boletear v-if="modal" :ver="modal" :boletera="boleteraCobrando" v-on:cerrar="modal=false; boleteraCobrando={}; boletera=''" v-on:hecho="boleteada" v-on:editar="modificar"></boletear>
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
        <v-dialog v-model="alertaNueva" max-width="600" persistent>
            <v-card>
                <v-card-title>Alerta</v-card-title>
                <v-card-text>
                    ¿Que desea hacer?
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="boleterasNuevas()">Abrir vuelta nueva</v-btn>
                    <v-btn @click="irLiquidar()">Liquidar Chofer</v-btn>
                    <v-btn to="cajaHome">Ir a Caja</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="gastar" max-width="300">
            <v-card>
                <v-card-title>Añadir Gasto</v-card-title>
                <v-card-text>
                    <v-text-field label="Descripcion" v-model="descripcion"></v-text-field>
                    <v-text-field label="Monto" v-model="montoGasto" @keydown="validarTecla($event)" prefix="$"></v-text-field>
                    <v-btn @click="addGasto()">Ok</v-btn>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-dialog v-model="alertaBol" max-width="300" persistent>
            <v-card>
                <v-card-title>Alerta Boletos</v-card-title>
                <v-card-text>
                    Hay boleteras que no fueron cobrardas. ¿Desea añadirlos a la siguiente vuelta?
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="abrirNuevaBoletos()">Si</v-btn>
                    <v-btn @click="abrirNueva()">No</v-btn>
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
import boletear from '@/components/cobrarBoletera'
export default {
    beforeMount(){
        if(this.logeado==null && this.sesion==false){
            this.$router.push('login')
        }else{
            this.vuelta= this.vueltaFind(this.$route.params.id)
        }
    },
    data: () => ({
        modal: false,
        boletera: "",
        vuelta: {},
        boleterasCobradas: [],
        boleteraCobrando: {},
        boletos: {},
        alertaBol: false,
        alertaNueva: false,
        mensaje: "",
        auth: false,
        password: "",
        mover: [],
        entregado: "",
        gastos: [],
        gastar: false,
        descripcion: "",
        montoGasto: "",
        comentarios: "",
        loader: false,
        succes: false

    }),
    computed: {
        ...mapGetters({
            vueltaFind: 'cajeras/getLapById',
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess'
        }),
        boleterasCobrar(){
            return this.vuelta.boleteras.filter(el => this.boleterasCobradas.find(item => item.codigo==el.codigo)==undefined) 
        },
        ventaTotal(){
            return this.vuelta.boleteras.filter(el => this.boleterasCobradas.find(item => item.totalBoletos!=el.totalBoletos)!=undefined)
        },
        montoVuelta(){
            return parseFloat(this.ventaBruta-this.gastosTotal).toFixed(2)
        },
        gastosTotal(){
            var bol=0
            this.gastos.forEach(element => {
                bol+= parseFloat(element.monto)
            })
            return parseFloat(bol).toFixed(2)
        },
        ventaBruta(){
            var bol=0;
            this.boleterasCobradas.forEach(element => {
                bol+=parseFloat(element.monto)
            });
            return parseFloat(bol).toFixed(2)
        }
    },
    methods: {
        ...mapActions({
            cerrarVuelta: "cajeras/closeLapId",
            addBoletera: 'cajeras/addBoletera',
            validar: 'logdata/validarPassword',
        }),
        addGasto(){
            this.gastos.push({monto: this.montoGasto, descripcion: this.descripcion.toUpperCase()})
            this.montoGasto="",
            this.descripcion=""
            this.gastar=false
        },
        isEnter(event){
            if(event.key=="Enter"){
                this.autenticar()
            }
        },
        validarTecla(even){
            if(even.key=="Enter"){
                this.addGasto()
            }
            if(isNaN(even.key) && even.key!="." && even.keyCode!=8){
                even.returnValue= false
            }
            else if(even.key=="." && this.entregado.includes(".")){
                even.returnValue=false
            }
        },
        validarTeclaEntregado(even){
            if(isNaN(even.key) && even.key!="." && even.keyCode!=8){
                even.returnValue= false
            }
            else if(even.key=="." && this.entregado.includes(".")){
                even.returnValue=false
            }
        },
        irLiquidar(){
            this.$router.push({name: "pagarReporte", params: {id: this.vuelta.turno}})
        },
        prepararBoletera(){
            this.boleteraCobrando = this.vuelta.boleteras.find(el => el.codigo==this.boletera)
            this.modal = true
        },
        editarBoletera(boletera){
            this.boleteraCobrando = {
                codigo: boletera.codigo,
                totalBoletos: boletera.totalBoletos + boletera.sobrantes,
                boletos: boletera.boletos
            }
            this.modal = true
        },
        modificar(tickets){
            this.modal = false
            this.boleteraCobrando = {}
            this.boletera = ""
            this.boleterasCobradas.splice(this.boleterasCobradas.findIndex(el => tickets.codigo==el.codigo),1,tickets)
        },
        boleteada(tickets){
            this.modal = false
            this.boleteraCobrando = {}
            this.boletera = ""
            this.boleterasCobradas.push(Object.assign(tickets))
        },
        async cobrar(){
            this.loader = true
            if(await this.cerrarVuelta({vuelta: this.vuelta.id, boleteras:this.boleterasCobradas, bruto: this.ventaBruta, gastosTotal: this.gastosTotal, monto: this.montoVuelta, entregado: this.entregado, gastos: this.gastos, comentarios: this.comentarios})){
                this.crearBoletera()
            }else{
                this.mensaje = "Error al cerrar la vuelta"
                this.loader = false
                this.succes = true
            }
        },
        boleterasNuevas(){
            if(this.mover.length>0){
                this.alertaBol=true
            }else{
                this.abrirNueva()
            }
        },
        abrirNueva(){
            this.$router.push({name:'abrirVuelta', params: {unidad: this.vuelta.unidad, chofer: this.vuelta.chofer, ruta: this.vuelta.ruta, boleteras: []}})
        },
        abrirNuevaBoletos(){
            this.$router.push({name:'abrirVuelta', params: {unidad: this.vuelta.unidad, chofer: this.vuelta.chofer, ruta: this.vuelta.ruta, boleteras: this.mover}})
        },
        async crearBoletera(){
            this.boleterasCobrar.forEach(este => {
                this.mover.push(este.codigo)
            })
            if(this.ventaTotal.length>0){
                var nuevas = []
                this.ventaTotal.forEach(el => {
                    var valor = this.boleterasCobradas.find(elem => elem.codigo==el.codigo)
                    if(valor!=undefined){
                        nuevas.push({inicio: (parseInt(valor.boletoFinal,10)+1).toString(10), termina: el.boletoFinal, permisionario: el.permisionario})
                    }
                })
                for( var i=0; i<nuevas.length; i++){
                    var pet = await this.addBoletera(nuevas[i])
                    if(pet.codigo!=""){
                        this.mover.push(pet.codigo)
                        this.mensaje = pet.mensaje
                        
                    }
                }
            }
            if(this.mensaje==""){
                this.mensaje="Datos Guardados"
            }
            this.loader = false
            this.succes = true
            this.alertaNueva= true
        },
        async autenticar(){
            if(this.password!=""){
                if(await this.validar(this.password)){
                    this.password=""
                    this.auth=false
                    this.cobrar()
                }else{
                    alert("Contraseña incorrecta")
                }
            }else{
                alert("Ingrese su contraseña")
            }
        },
        revGasto(item){
            this.gastos.splice(this.gastos.indexOf(item),1)
        }
    },
    components: {
        boletear
    }
}
</script>

<style>

</style>