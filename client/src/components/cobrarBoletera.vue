<template>
    <v-card height="700" max-height="700">
        <v-card-title> Boletera {{boleteraLoc.codigo}}</v-card-title>
        <v-card-subtitle>La boletera inicia en el boleto {{boleteraLoc.boletoInicial}}</v-card-subtitle>
        <v-card-subtitle>Por favor escriba el monto en el campo y presione Enter para agregar un nuevo boleto</v-card-subtitle>
        <v-card-text>
            <div class="contenedor">
                <v-container>
                    <v-col cols="12" no-gutters>
                        <v-row v-for="(boleto, index) in boletos.keys" :key="index" no-gutters>
                            <v-col cols="6">
                                <v-text-field ref="boleto" solo readonly :value="boleto"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field ref="monto" outlined v-model="boletos.montos[index]" @keydown="agregar($event, index)" @click="campoClick(index)" prefix="$" :error="alerta" >
                                </v-text-field>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-container>
            </div>
            Total de Boletos: {{totalBoletos}}  Monto Total: ${{montoTotal}}
        </v-card-text>
        <v-card-actions>
            <v-btn color="success" @click="validarMontos()">Aceptar</v-btn>
            <v-btn color="error" @click="cerrar()">Cancelar</v-btn>
        </v-card-actions>
        <v-dialog v-model="alerta" max-width="300">
            <v-card>
                <v-card-title>Error</v-card-title>
                <v-card-text>
                    El dato introducido es incorrecto, por favor verifiquelo   
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="alerta=false">Ok</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="confirmar" max-width="300">
            <v-card>
                <v-card-title>Alerta</v-card-title>
                <v-card-text>
                    Boletos: {{totalBoletos}} Monto Total: ${{montoTotal}}
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="saveTickets()">Confirmar</v-btn>
                    <v-btn @click="confirmar=false">Volver</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="vacios" max-width="300">
            <v-card>
                <v-card-title>Alerta</v-card-title>
                <v-card-text>
                   Hay boletos sin monto, por favor verifique
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="vacios=false">ok</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
export default {
    props:{
        boletera: Object,
    },
    data: function(){
        return{
            boleteraLoc: this.boletera,
            boletos:{keys:[this.boletera.boletoInicial], montos:[""]},
            focusBander: 0,
            alerta: false,
            confirmar: false,
            vacios: false,
            regresar: {boletos:{}}
    }},
    computed: {
        totalBoletos(){
            if(this.boletos.montos[this.boletos.montos.length-1]==""){
                return this.boletos.montos.length-1
            }
            return this.boletos.montos.length
        },
        montoTotal(){
            var total = 0.00
            this.boletos.montos.forEach(element => {
                if(!isNaN(element) && element!=""){
                    total=total+parseFloat(element)
                }

            });
            return total
        },
    },
    methods:{
        agregar(evt,index){
            if(evt.keyCode==13){
                if(this.boletos.montos[index]=="" ||  parseInt(this.boletos.montos[index],10)<9 || this.boletos.montos[index].length>2){
                    this.alerta=true
                }else{
                    if(index==this.boletos.montos.length-1){
                        this.boletos.keys.push(parseInt(this.boletos.keys[index],10)+1)
                        this.boletos.montos.push("")
                        this.focusBander=index+1
                    }else{
                        this.focusBander++
                        this.$refs.monto[this.focusBander].focus()
                    }
                }
            }else if(evt.keyCode==40){
                if(this.focusBander<this.boletos.montos.length-1){
                    if(this.boletos.montos[index]=="" ||  parseInt(this.boletos.montos[index],10)<9 || parseInt(this.boletos.montos[this.focusBander],10)>50){
                        this.alerta=true
                    }else{
                        this.focusBander++
                        this.$refs.monto[this.focusBander].focus()
                    }
                }
            }else if(evt.keyCode==38){
                if(this.focusBander>0){
                    if(this.boletos.montos[index]=="" ||  parseInt(this.boletos.montos[index],10)<9 || parseInt(this.boletos.montos[this.focusBander],10)>50){
                        this.alerta=true
                    }else{
                        this.focusBander--
                        this.$refs.monto[this.focusBander].focus()
                    }
                }
            }else if((evt.keyCode<48 || evt.keyCode>57) && (evt.keyCode<96 || evt.keyCode>105)) {
                if(evt.keyCode!=8 && (evt.keyCode!=39 || evt.keyCode!=37)){
                    evt.returnValue = false
                }
            }
        },
        cerrar(){
            this.focusBander= 0
            this.boletos = {keys:[this.boletera.boletoInicial], montos:[""]},
            this.$emit('cerrar')
        },
        validarMontos(){
            var ok= 0
            for(var i = 0; i<this.boletos.montos.length; i++){
                if(this.boletos.montos[i] == "" && i!=this.boletos.montos.length-1){
                    ok++
                }
            }
            if(ok!=0){
                this.vacios = true
            }else{
                this.confirmar = true
            }
        },
        saveTickets(){
            var ultimo=0
            this.boletos.montos.forEach((el, index)=>{
                if(el!=""){
                    this.regresar.boletos[this.boletos.keys[index]] = el
                    ultimo++
                }
            })
            this.regresar.monto=this.montoTotal
            this.regresar.totalBoletos=this.totalBoletos
            this.regresar.boletoInicial=this.boletos.keys[0]
            this.regresar.boletoFinal=this.boletos.keys[ultimo]
            this.regresar.codigo=this.boleteraLoc.codigo
            this.focusBander= 0
            this.boletos = {keys:[this.boletera.boletoInicial], montos:[""]}
            this.$emit('hecho', this.regresar)
        },
        campoClick(evt){
            if(this.boletos.montos.length>1){
                if(this.focusBander!=this.boletos.montos.length-1){
                    if(this.boletos.montos[this.focusBander]=="" || parseInt(this.boletos.montos[this.focusBander],10)<9 || parseInt(this.boletos.montos[this.focusBander],10)>50){
                        this.alerta = true
                        this.$refs.monto[this.focusBander].focus()
                    }else{
                        this.focusBander = evt
                    }
                }else{
                    if(this.boletos.montos[this.focusBander] == ""){
                        this.focusBander = evt
                    }else if(parseInt(this.boletos.montos[this.focusBander],10)<9 || parseInt(this.boletos.montos[this.focusBander],10)>50){
                        this.alerta=true
                        this.$refs.monto[this.focusBander].focus()
                    }else{
                        this.focusBander = evt
                    }
                }
            }
        }
    },
    updated(){
        this.$refs.monto[this.focusBander].focus()
    },
   
}
</script>

<style>
.contenedor{
    overflow-y: auto;
    height: 450px;
}
</style>