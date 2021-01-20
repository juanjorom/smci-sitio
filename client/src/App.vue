<template>
  <v-app>
    <v-app-bar
      app
      color="primary"
      dark
      absolute
      clipped-left
      v-if="logeado"
    >
    <v-app-bar-nav-icon v-if="logeado" @click="mostrar=!mostrar"></v-app-bar-nav-icon>
      {{mensaje}}
      <v-spacer></v-spacer>
      <v-menu bottom offset-y >
        <template v-slot:activator="{on, attrs}">
          <v-btn v-bind="attrs" v-on="on" rounded outlined fab>
            <v-icon>mdi-account</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item v-for="(item, index) in opciones" :key="index" v-model="opcion" @click="action(item.value)">
           <v-list-item-icon>
             <v-icon v-text="item.icon"></v-icon>
           </v-list-item-icon>
           <v-list-item-content>
             <v-list-item-title v-text="item.name"></v-list-item-title>
           </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar> 
    <v-navigation-drawer v-if="logeado" v-model="mostrar" app clipped>
      <v-list>
        <v-list-item v-for="(ruta, index) in permisos" :key="index"><v-btn text :to="ruta.ruta">{{ruta.modulo}}</v-btn> </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
export default {
  name: 'App',
  components: {
    
  },
  watch: {
    logeado: function(){
      this.traerPermisos()
    }
  },
  computed: {
    ...mapGetters({
      logeado: 'logdata/getSucess',
      mensaje: 'logdata/getMensaje',
      permisos: 'appdata/getAccesos'
    })
  },
  data: () => ({
    //
    opcion: null,
    mostrar: true,
    opciones: [{name:"Cambiar Contraseña", value: "changePassword", icon: "mdi-key"}, {name:"Cerrar Sesion", value: "closeSesion", icon: "mdi-door"}]
  }),

  methods: {
    ...mapActions({
      cerrar: 'logdata/closeSesion',
      traerPermisos: 'appdata/getAccesosServer'
    }),
    async action(accion){
      if(accion=="closeSesion"){
        await this.cerrar()
        this.$router.push('/login')
      }
      else if(accion == "changePassword"){
        this.$router.push('/password')
      }
    }
  }
};
</script>
