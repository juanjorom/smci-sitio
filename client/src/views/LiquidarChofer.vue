<template>
  <v-container>
      <v-card>
        <v-card-title>Liquidar Chofer</v-card-title>
        <v-card-text>
          <v-text-field v-model="buscar" label="Buscar Unidad" placeholder="Numero">
          </v-text-field>
          <v-card height="450" max-height="450" >
                <v-list >
                  <v-list-item v-for="(tur, i) in turnosMostrar"  :key="i">
                      <v-list-item-icon>
                                <v-icon>mdi-bus</v-icon>
                            </v-list-item-icon>
                      <v-list-item-content>
                            <v-list-item-title>{{tur.unidad}} Vueltas: {{tur.vueltas}} Chofer: {{tur.chofer}}</v-list-item-title>
                            <v-list-item-subtitle>
                                Hora Inicio: {{tur.inicio}} Ruta: {{tur.ruta}}
                            </v-list-item-subtitle>
                      </v-list-item-content>
                      <v-list-item-action>
                          <v-btn color="success" @click="modal=true; id=tur.id"><v-icon>mdi-cash</v-icon></v-btn>
                      </v-list-item-action>
                  </v-list-item>
                </v-list>
              </v-card>
        </v-card-text>
        <v-card-actions>
          <v-btn color="error" to="cajaHome">Cancelar</v-btn>
        </v-card-actions>
      </v-card>
      <v-dialog v-model="modal" max-width="400">
            <v-card>
                <v-card-title>Ingrese su contraseña</v-card-title>
                <v-card-text>
                    <v-text-field v-model="password" label="Contraseña" placeholder="Contraseña" type="password" @keydown="isEnter($event)"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="validarPassword">Ok</v-btn>
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
    else{
      this.traerTurnos()
    }
  },
  data: () => ({
    modal: false,
    buscar: "",
    password: "",
    id: null
  }),
  computed: {
    ...mapGetters({
      turnos: "cajeras/getAllTurnos",
      turnosFil: "cajeras/getTurnosFilter",
      logeado: 'logdata/getKey',
      sesion: 'logdata/getSucess'
    }),
    turnosMostrar(){
      if(this.buscar!=""){
        return this.turnosFil(this.buscar)
      }
      return this.turnos
    }
  },
  methods:{
    ...mapActions({
      traerTurnos: "cajeras/getAllTurnosServer",
      validar: "logdata/validarPassword"
    }),
    isEnter(event){
      if(event.key=="Enter"){
        this.validarPassword()
      }
    },
    async validarPassword(){
      if(this.password!=""){
        if(await this.validar(this.password)){
          this.$router.push({name: "pagarReporte", params: {id: this.id}})
        }
      }
    }
  }
}
</script>

<style>

</style>