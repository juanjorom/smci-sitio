<template>
    <v-container>
        <v-card>
            <v-card-title>Recibir vuelta {{vuelta.numero}} de la {{vuelta.unidad}}</v-card-title>
            <v-card-subtitle>Chofer: {{vuelta.chofer}}</v-card-subtitle>
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
                          </v-list-item-content>
                      </v-list-item>
                  </v-list>
              </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="success" @click="alertaNueva=true">Cobrar</v-btn>
                <v-btn to="/caja" color="error" >Cancelar</v-btn>
            </v-card-actions>
        </v-card>
        <v-dialog v-model="modal" max-width="800" persistent>
            <boletear :boletera="boleteraCobrando" :boletos="this.boletos" v-on:cerrar="modal=false; boleteraCobrando={}; boletera=''" v-on:hecho=" boleteada"></boletear>
        </v-dialog>
        <v-dialog v-model="alertaNueva" max-width="300" persistent>
            <v-card>
                <v-card-title>Vuelta Nueva</v-card-title>
                <v-card-text>
                    Desea abrir una vuelta nueva
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="cobrarNueva()">Si</v-btn>
                    <v-btn @click="cobrar()">No</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="alertaBol" max-width="300" persistent>
            <v-card>
                <v-card-title>Alerta Boletos</v-card-title>
                <v-card-text>
                    {{mensaje}} ¿Desea añadirlos a la siguiente vuelta?
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="abrirNuevaBoletos(); alertaBol=false">Si</v-btn>
                    <v-btn @click="abrirNueva()">No</v-btn>
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
        this.vuelta= this.vueltaFind(this.$route.params.id)
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
        mensaje: ""
    }),
    computed: {
        ...mapGetters({
            vueltaFind: 'cajeras/getLapById'
        }),
        boleterasCobrar(){
            return this.vuelta.boleteras.filter(el => this.boleterasCobradas.find(item => item.codigo==el.codigo)==undefined) 
        },
        ventaTotal(){
            return this.vuelta.boleteras.filter(el => this.boleterasCobradas.find(item => item.totalBoletos!=el.totalBoletos)!=undefined)
        },
        montoVuelta(){
            var bol=0;
            this.boleterasCobradas.forEach(element => {
                bol+=element.monto
            });
            return bol
        }
    },
    methods: {
        ...mapActions({
            cerrarVuelta: "cajeras/closeLapId"
        }),
        prepararBoletera(){
            this.boleteraCobrando = this.vuelta.boleteras.find(el => el.codigo==this.boletera)
            this.modal = true
        },
        boleteada(tickets){
            this.modal = false
            this.boleteraCobrando = {}
            this.boletera = ""
            this.boleterasCobradas.push(tickets)
        },
        cobrar(){
            this.alertaNueva=false
            this.cerrarVuelta({vuelta: this.vuelta.id, boleteras:this.boleterasCobradas, monto: this.montoVuelta})
            this.$router.push({name:'cajaHome'})
        },
        cobrarNueva(){
            if(this.boleterasCobrar.length>0){
                this.mensaje="Hay boleteras que no fueron cobradas"
                this.alertaBol=true
            }else if(this.ventaTotal.length>0){
                this.mensaje="Hay boletos sobrantes de boleteras"
                this.alertaBol=true
            }else{
                this.abrirNueva()
            }
        },
        abrirNuevaBoletos(){
            if(this.cerrarVuelta({vuelta: this.vuelta.id, boleteras:this.boleterasCobradas, monto: this.montoVuelta})){
                alert("Vuelta Cerrada con éxito")
                this.$router.push({name:'abrirVuelta', params: {unidad: this.vuelta.unidad, chofer: this.vuelta.chofer, ruta: this.vuelta.ruta }})
            }else{
                alert("Error al cerrar vuelta")
            }

        },
        abrirNueva(){
            this.alertaNueva=false;
            if(this.cerrarVuelta({vuelta: this.vuelta.id, boleteras:this.boleterasCobradas, monto: this.montoVuelta})){
                alert("Vuelta Cerrada con éxito")
                this.$router.push({name:'abrirVuelta', params: {unidad: this.vuelta.unidad, chofer: this.vuelta.chofer, ruta: this.vuelta.ruta}})
            }else{
                alert("Error al cerrar vuelta")
            }
        }
    },
    components: {
        boletear
    }
}
</script>

<style>

</style>