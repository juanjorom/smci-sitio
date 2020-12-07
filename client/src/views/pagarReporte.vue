<template>
    <v-container>
        <v-card>
            <v-card-title>{{turno.unidad}}, {{turno.chofer}}</v-card-title>
            <v-card-subtitle>Vueltas: {{turno.totalVueltas}}</v-card-subtitle>
            <v-card-text>
                <v-row>
                    <v-col cols="8">
                        <v-card class="overflow-y-auto" height="300" max-height="300">
                            <v-card-title>Desglose de  Vueltas</v-card-title>
                            <v-card-text>
                                <v-data-table ref="tabla" :headers="headers" :items="turno.vueltas"></v-data-table>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col>
                        <v-card class="overflow-y-auto" height="300" max-height="300">
                            <v-card-title>Gastos Administrativos</v-card-title>
                            <v-card-subtitle>Gastos que no se pagan al momento y no afectan el monto de la liquidación</v-card-subtitle>
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
                <v-col class="title">
                    <v-row>
                        <v-col>
                            <v-row>
                                <span class="">Venta Bruta: </span>
                                <span class="blue--text">${{ventaBruta.toFixed(2)}}</span>
                            </v-row>
                            <v-row>
                                <span>Gastos Pagados:</span>
                                <span class="red--text"> ${{gastosTotal.toFixed(2)}}</span>
                            </v-row>
                            <v-row>
                                <span>Total de Vueltas:</span>
                                <span class="green--text"> ${{(ventaBruta-gastosTotal).toFixed(2)}}</span>
                            </v-row>
                        </v-col>
                        <v-col>
                            <v-row>
                                <span>Comision Total:</span>
                                <span class="blue--text"> ${{comisionTotal.toFixed(2)}}</span>
                            </v-row>
                            <v-row>
                                <span>Adeudos:</span>
                                <span class="red--text"> ${{adeudos.toFixed(2)}}</span>
                            </v-row>
                            <v-row>
                                <span>Pago a Chofer: </span>
                                <span class="yellow  green--text"> ${{(comisionTotal-adeudos).toFixed(2)}}</span>
                            </v-row>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <span>Dinero en caja:</span>
                            <span class="yellow black--text"> ${{ventaTotal.toFixed(2)}}</span>
                        </v-col>
                        <v-col>
                            <span>Gastos Administrativos Total:</span>
                            <span class="red--text"> ${{gastosAdmin.toFixed(2)}}</span>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col col="2">
                            <!--<v-btn color="success" @click="prepararPdf()">Imprimir</v-btn>-->
                            <v-btn color="success" @click="modal=true">Cerrar</v-btn>
                            <v-btn color="error" to="cajaHome">Cancelar</v-btn>
                        </v-col>
                    </v-row>
                </v-col>
            </v-card-text>
        </v-card>
        <v-dialog v-model="gastar" max-width="300">
            <v-card>
                <v-card-title>Añadir Gasto</v-card-title>
                <v-card-text>
                    <v-select :items="['DIESEL', 'OTRO']" v-model="sel" label="Seleccione"></v-select>
                    <v-text-field v-if="sel=='OTRO'" label="Especifique" v-model="descripcion"></v-text-field>
                    <v-text-field label="Monto" v-model="montoGasto" @keydown="validarTecla($event)" prefix="$"></v-text-field>
                    <v-btn @click="addGasto()">Ok</v-btn>
                    <v-btn @click="gastar=false; sel=''; descripcion=''; montoGasto='';">Cancelar</v-btn>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-dialog v-model="modal" max-width="400">
            <v-card>
                <v-card-title>Ingrese su contraseña</v-card-title>
                <v-card-text>
                    <v-text-field v-model="password" label="Contraseña" placeholder="Contraseña" type="password" @keydown="isEnter($event)"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="validarPassword()">Ok</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="siguiente" max-width="400">
            <v-card>
                <v-card-title>¿Desea abrir una vuelta nueva?</v-card-title>
                <v-card-actions>
                    <v-btn color="success" :to="{name: 'abrirVuelta', params: {unidad: this.turno.unidad, chofer: this.turno.chofer, ruta: this.turno.ruta}}" >Si</v-btn>
                    <v-btn color="error" to="cajaHome">No</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import jsPDF from 'jspdf'
import 'jspdf-autotable'
export default {
    beforeMount(){
        if(this.logeado==null && this.sesion==false){
            this.$router.push('login')
        }
    },
    mounted(){
        this.obtenerTurno(this.$route.params.id)
    },
    data: () =>({
        gastar: false,
        gastos: [],
        modal: false,
        password: "",
        sel: "",
        montoGasto: '',
        descripcion: '',
        siguiente: false,
        headers:[
            {
                text: 'Vuelta',
                value: 'vuelta',
                align: 'center'
            },
            {
                text: "Boletos",
                value: "boletos",
                align: 'center'
            },
            {
                text: 'Venta Bruta',
                value: 'bruto',
                align: 'center'
            },
            {
                text: 'Gastos',
                value: 'gastos',
                align: 'center'
            },
            {
                text: 'Venta Neta',
                value: 'monto',
                align: 'center'
            },
            {
                text: 'Entregado',
                value: 'entregado',
                align: 'center'
            },
            {
                text: 'Adeudo',
                value: 'diferencia',
                align: 'center'
            },
            {
                text: 'Comentarios',
                value: 'comentarios',
                align: 'center'
            }
        ],
        doc: new jsPDF({orientation: 'p', unit:"mm", format:[80, 100]})
    }),
    computed: {
        ...mapGetters({
            turno: 'cajeras/getTurnoPagar',
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess'
        }),
        ventaTotal(){
            var cant= 0
            this.turno.vueltas.forEach(element => {
                cant+= parseFloat(element.monto)
            });
            return cant-this.comisionTotal
        },
        comisionTotal(){
            var cant= 0
            this.turno.vueltas.forEach(element => {
                cant+= parseFloat(element.comision)
            });
            return cant
        },
        boletosTotal(){
            var cant = 0
            this.turno.vueltas.forEach(element => {
                cant+= parseInt(element.boletos)
            });
            return cant
        },
        adeudos(){
            var cant= 0
            this.turno.vueltas.forEach(element => {
                cant+= parseFloat(element.diferencia)
            });
            return cant
        },
        ventaBruta(){
            var cant = 0
            this.turno.vueltas.forEach(element => {
                cant+=parseFloat(element.bruto)
            })
            return cant
        },
        gastosTotal(){
            var cant= 0
            this.turno.vueltas.forEach(element => {
                cant+= parseFloat(element.gastos)
            })
            return cant
        },
        gastosAdmin(){
            var cant = 0
            this.gastos.forEach(element =>{
                cant+= parseFloat(element.monto)
            })
            cant+=this.gastosTotal+this.comisionTotal
            return cant
        }
    },
    methods:{
        ...mapActions({
            obtenerTurno: "cajeras/getTurnoById",
            pagar: "cajeras/pagarTurno",
            validar: "logdata/validarPassword"
        }),
        revGasto(item){
            this.gastos.splice(this.gastos.indexOf(item),1)
        },
         isEnter(event){
            if(event.key=="Enter"){
                this.validarPassword()
            }
        },
        addGasto(){
            if(this.sel!="OTRO"){
                this.gastos.push({monto: this.montoGasto, descripcion: this.sel.toUpperCase()})
            }
            else{
                this.gastos.push({monto: this.montoGasto, descripcion: this.descripcion.toUpperCase()})
            }
            this.montoGasto="",
            this.descripcion=""
            this.gastar=false
        },
        validarTecla(even){
            if(even.key=="Enter"){
                this.addGasto
            }
            if(isNaN(even.key) && even.key!="." && even.keyCode!=8){
                even.returnValue= false
            }
            else if(even.key=="." && this.montoGasto.includes(".")){
                even.returnValue=false
            }
        },
        async validarPassword(){
            if(this.password!=""){
                if(await  this.validar(this.password)){
                    this.prepararPago()
                    this.password=""
                }
                else{
                    alert("Contraseña incorrecta")
                }
            }else{
                alert("Por favor ingrese la contraseña")
            }
        },
        async prepararPago(){
            var gastoLocal= this.gastos
            gastoLocal.push({descripcion: "Gastos de Vuelta", monto:this.gastosTotal},{descripcion: "Comision Chofer", monto: this.comisionTotal})
            if(await this.pagar({id: this.$route.params.id, venta: this.ventaBruta, recaudado: this.ventaTotal, comision: this.comisionTotal, boletos: this.boletosTotal, totalGastos: this.gastosAdmin, gastos: gastoLocal})){
                alert("Se guardo la información")
                this.siguiente= true
            }else{
                alert("Error al guardar")
            }
        },
        prepararPdf(){
            this.doc.setFontSize(10)
            this.doc.text("GRUPO AVANZA",40,10, {align: "center"})
            this.doc.text("Corte de Turno Chofer",40,20, {align: "center"})
            this.doc.text(new Date(this.turno.inicio).toLocaleString() +" - "+ new Date().toLocaleString(),40,30, {align: "center"})
            this.doc.line(1,33,79,33,'F')
            this.doc.text(this.turno.unidad,5,40)
            this.doc.text("Chófer: "+this.turno.chofer,5,50)
            this.doc.line(5,53,73,53,'S')
            var altura= 50
            for(var i=1; i<=this.turno.vueltas.length;i++)
            {
                this.doc.text("Vuelta "+i+":", 5, ((i*10)+50))
                this.doc.text("$"+parseFloat(this.turno.vueltas[i-1].monto).toFixed(2), 73, ((i*10)+50), {align: "right"})
                altura+=(i*10)
            }
            this.doc.line(5,altura+5,73,altura+5,'S')
            this.doc.text("Vueltas Recorridas: ",5,altura+10)
            this.doc.text(this.turno.totalVueltas, 73,altura+10, {align:"right"})
            this.doc.text("Venta total:",5,altura+20)
            this.doc.text("$"+this.ventaTotal.toFixed(2),73,altura+20, {align:"right"})
            this.doc.text("Comision:",5,altura+30)
            this.doc.text("$"+this.comisionTotal.toFixed(2),73,(altura+30), {align:"right"})
            this.doc.text("Gastos:",5,altura+40)
            this.doc.text("$" + parseFloat(this.turno.gastos).toFixed(2),73,(altura+40), {align:"right"})
            this.doc.line(20, altura+60, 60, altura+60)
            this.doc.text("Firma", 40, altura+65, {align: "center"})
            this.doc.autoPrint()
            this.doc.output('dataurlnewwindow')
        }
    }
}
</script>

<style>
</style>