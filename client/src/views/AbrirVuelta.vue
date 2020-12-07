<template>
  <v-container>
      <v-card max-width="1000">
          <v-card-title>
              Abrir una vuelta nueva
          </v-card-title>
          <v-card-text>
              <v-menu max-width="150" offset-y  max-height="300">
                  <template v-slot:activator="{on, attrs}">
                      <v-text-field v-model="unidad" label="Unidad" v-bind="attrs" v-on="on" @blur="opacar('unidades', 'unidad')"></v-text-field>
                  </template>
                  <v-list v-model="unidadCodigo" >
                      <v-list-item v-for="(item, index) in unidades" :key="index" @click="unidad=item.nombre; unidadCodigo=item.codigo" >
                          <v-list-item-title>{{item.nombre}}</v-list-item-title>
                      </v-list-item>
                  </v-list>
              </v-menu>
              <v-menu  max-width="300" offset-y max-height="300">
                  <template v-slot:activator="{on, attrs}">
                      <v-text-field v-model="chofer" label="Chofer" v-bind="attrs" v-on="on" @blur="opacar('choferes', 'chofer')"></v-text-field>
                  </template>
                  <v-list v-model="choferCodigo">
                      <v-list-item v-for="(item, index) in choferes" :key="index" @click="chofer=item.nombre; choferCodigo=item.nickname" >
                          <v-list-item-title>{{item.nombre}}</v-list-item-title>
                      </v-list-item>
                  </v-list>
              </v-menu>
              <v-row>
                  <v-col cols="5">
                    <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="date" transition="scale-transition" offset-y min-width="290px" max-height="300">
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field v-model="date" label="Fecha" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
                        </template>
                        <v-date-picker v-model="date" no-title scrollable>
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="menu = false">
                            Cancel
                        </v-btn>
                        <v-btn text color="primary" @click="$refs.menu.save(date)">
                            OK
                        </v-btn>
                        </v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col cols="5">
                      <v-text-field v-model="hora" type="time" label="Salida" placholder="HH:MM"></v-text-field>
                  </v-col>
              </v-row>
              <v-select v-model="ruta" :items="allRutas" label="Ruta" item-text="ruta" item-value="codigo">
              </v-select>
              <v-row>
                  <v-col cols="10">
                  <v-menu max-width="300" offset-y>
                  <template v-slot:activator="{on, attrs}">
                      <v-text-field v-model="boletera" label="Boletera" placeholder="Codigo" v-bind="attrs" v-on="on" @blur="opacar('boleteras', 'boletera')"></v-text-field>
                  </template>
                  <v-list>
                      <v-list-item v-for="(item, index) in boleteras" :key="index" @click="boletera=item.codigo" >
                          <v-list-item-title >{{item.codigo}}</v-list-item-title>
                      </v-list-item>
                  </v-list>
              </v-menu>
              </v-col>
              <v-col cols="2">
                  <v-btn @click="agregarBoletera(boletera); boletera=''">Agregar</v-btn>
              </v-col>
              </v-row>
              <v-card>
                  <v-card-text>
                      <v-list>
                          <v-list-item v-for="(item, index) in boleterasAsignadas" :key="index">
                              <v-list-item-title>{{item}}</v-list-item-title>
                              <v-list-item-action>
                                  <v-btn @click="quitarBoletera(item)">
                                      <v-icon>mdi-cancel</v-icon>
                                  </v-btn>
                              </v-list-item-action>
                          </v-list-item>
                      </v-list>
                  </v-card-text>
              </v-card>
            <v-card-actions>
              <v-btn color="success" :disabled="activar || loader" @click="confirmar=true">Iniciar Vuelta</v-btn>
              <v-btn color="error" to="/caja">Cancelar</v-btn>
            </v-card-actions>
          </v-card-text>
      </v-card>
      <v-dialog v-model="confirmar" max-width="400" @click:outside="codigo=''">
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
    data: () => ({
        date: new Date().toISOString().substr(0, 10),
        hora: "",
        menu: false,
        unidad: "",
        unidadCodigo: "",
        chofer: "",
        choferCodigo: "",
        ruta: "",
        boletera: "",
        boleterasAsignadas: [],
        confirmar: false,
        password: "",
        loader: false,
        mensaje: "",
        succes: false
    }),
    beforeMount(){
        if(this.logeado==null && this.sesion==false){
            this.$router.push('login')
        }
    },
    watch: {
        unidadCodigo: function(){
            this.traerBoleteras(this.unidadCodigo)
        }
    },
    mounted(){
        this.traerUnidades()
        this.traerChoferes()
        this.traerRutas()
        if(this.$route.params.unidad!=undefined){
            this.unidadCodigo = this.allUnidades.find(el => el.nombre==this.$route.params.unidad).codigo
            this.unidad = this.$route.params.unidad
            this.choferCodigo = this.allChoferes.find(el => el.nombre == this.$route.params.chofer).nickname
            this.chofer = this.$route.params.chofer
            if(this.$route.params.ruta!=undefined){
                this.ruta = this.allRutas.find(el => el.ruta==this.$route.params.ruta).codigo
            }
            if(this.$route.params.boleteras.length>0){
                this.boleterasAsignadas= this.$route.params.boleteras
            }
        }
    },
    computed:{
        ...mapGetters({
            allRutas: 'cajeras/getAllRutas',
            allUnidades: 'cajeras/getAllUnidades',
            allChoferes: 'cajeras/getAllChoferes',
            allBoleteras: 'cajeras/getBoleterasAsignar',
            someUnidades: 'cajeras/getUnidadesFilter',
            someChoferes: 'cajeras/getChoferesFilter',
            someBoleteras: 'cajeras/getBoleterasFilter',
            logeado: 'logdata/getKey',
            sesion: 'logdata/getSucess'
        }),
        activar(){
            if(this.unidad!="" && this.chofer!="" && this.ruta!="" && this.hora!="" && this.boleterasAsignadas.length>0){
                return false
            }
            return true
        },
        unidades(){
            if(this.unidad!=""){
                return this.someUnidades(this.unidad)
            }
            return this.allUnidades
        },
        choferes(){
            if(this.chofer!=""){
                return this.someChoferes(this.chofer)
            }
            return this.allChoferes
        },
        boleteras(){
            if(this.boletera!=""){
                var bol = this.someBoleteras(this.boletera.toUpperCase())
                return bol.filter(el => !(this.boleterasAsignadas.includes(el.codigo)))
            }
            return this.allBoleteras.filter(el => !(this.boleterasAsignadas.includes(el.codigo)))
        }
    },
    methods: {
        ...mapActions({
            traerUnidades: 'cajeras/getAllUnidadesServer',
            traerChoferes: 'cajeras/getAllChoferesServer',
            traerRutas: 'cajeras/getAllRutasServer',
            traerBoleteras: 'cajeras/getBoleterasAsignarServer',
            validar: 'logdata/validarPassword',
            iniciarVuelta: 'cajeras/iniciarVuelta'
        }),
        isEnter(event){
            if(event.key=="Enter"){
                this.validarPassword()
            }
        },
        quitarBoletera(item){
            var index = this.boleterasAsignadas.findIndex(el=> el==item)
            this.boleterasAsignadas.splice(index,1)
        },
        agregarBoletera(item){
            if(item!=""){
                if(this.boleterasAsignadas.findIndex(el => el==item)<0){
                    this.boleterasAsignadas.push(item)
                }
            }
        },
        opacar(arreglo, valor){
            if(!this[arreglo].includes(this[valor])){
                this[valor]=""
            }
        },
        async validarPassword(){
            if(this.password!=""){
                if(await this.validar(this.password)){
                    this.password=""
                    this.confirmar=false
                    this.loader=true
                    if(await this.iniciarVuelta({unidad: this.unidadCodigo, fechaHora: this.date+' '+this.hora+':00', chofer: this.choferCodigo, ruta: this.ruta, boleteras: this.boleterasAsignadas})){
                        this.mensaje = "Vuelta Abierta con éxito"
                        this.unidad=""
                        this.unidadCodigo=""
                        this.hora=""
                        this.chofer=""
                        this.choferCodigo=""
                        this.ruta=""
                        this.boleterasAsignadas=[]
                        this.loader= false
                        this.succes = true
                    }
                    else{
                        this.mensaje = "Error al Abrir la vuelta"
                        this.loader = false
                        this.succes = true

                    }
                }
                else{
                    alert("Contraseña no valida")
                }
            }else{
                alert("Escriba la contraseña")
            }
        },
    }
}
</script>

<style>

</style>