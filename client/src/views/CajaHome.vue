<template>
  <v-container>
        <v-card max-height="700">
          <v-card-title>Vueltas abiertas</v-card-title>
          <v-card-text>
              <v-text-field v-model="buscar" label="Buscar Unidad" placeholder="Numero">
              </v-text-field>
              <v-card height="450" max-height="450" class="overflow-y-auto" v-if="vueltas.length>0">
                <v-list >
                  <v-list-item v-for="(lap, i) in vueltas"  :key="i">
                      <v-list-item-icon>
                                <v-icon>mdi-bus</v-icon>
                            </v-list-item-icon>
                      <v-list-item-content>
                            <v-list-item-title>{{lap.unidad}} Vuelta: {{lap.numero}} Chofer: {{lap.chofer}}</v-list-item-title>
                            <v-list-item-subtitle>
                                Hora Inicio: {{lap.fechaHora}} Ruta: {{lap.ruta}}  Boleteras: {{lap.boleteras.length}}
                            </v-list-item-subtitle>
                      </v-list-item-content>
                      <v-list-item-action>
                          <v-btn color="success" @click="modal=true; id=lap.id"><v-icon>mdi-cash</v-icon></v-btn>
                      </v-list-item-action>
                  </v-list-item>
                </v-list>
              </v-card>
              <v-card  height="450" max-height="450" v-else>
                  <v-card-subtitle>No tienes vueltas abiertas</v-card-subtitle>
              </v-card>
          </v-card-text>
          <v-card-actions>
            <v-btn @click="abrir" >Abrir Vuelta</v-btn>
            <v-btn @click="liquidar">Liquidar Chofer</v-btn>
          </v-card-actions>
        </v-card>
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
    </v-container>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
export default {
    beforeMount(){
        if(this.logeado==null && this.sesion==false){
        this.$router.push('login')
        }
    },
    mounted() {
        this.obtenerVueltas()
    },
    data: () => ({
        buscar: "",
        modal: false,
        password: "",
        id: null,
    }),
    computed: {
        ...mapGetters({
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess',
            openLaps: 'cajeras/getAllOpenLaps',
            openLapsFilter: 'cajeras/getOpenLapsFilter'
        }),
        vueltas(){
            if(this.buscar!=""){
                return this.openLapsFilter(this.buscar)
            }
            return this.openLaps
        }
    },
    methods: {
        ...mapActions({
            obtenerVueltas: 'cajeras/getAllOpenLapsServer',
            cerrarVuelta: 'cajeras/closeLap',
            validar: 'logdata/validarPassword'
        }),
        isEnter(event){
            if(event.key=="Enter"){
                this.validarPassword()
            }
        },
        abrir(){
            this.$router.push('abrirVuelta')
        },
        cerrar(){
            this.$router.push('recibirVuelta')
        },
        liquidar(){
            this.$router.push('liquidarTurno')
        },
        async validarPassword(){
            if(this.password!=""){
                if(await this.validar(this.password)){
                    this.password=""
                    this.modal=false
                    this.$router.push("recibirVuelta/"+this.id)
                }
                else{
                    alert("Contraseña no valida")
                    this.password=""
                }
            }else{
                alert("Escriba la contraseña")
            }
        }
    }
}
</script>

<style>

</style>