<template>
  <v-app>
    <v-app-bar
      app
      color="primary"
      dark
      absolute
      clipped-left
    >
    <v-app-bar-nav-icon v-if="logeado" @click="mostrar=!mostrar"></v-app-bar-nav-icon>
      {{mensaje}}
      <v-spacer></v-spacer>
      <v-menu bottom offset-y>
        <template v-slot:activator="{on, attrs}">
          <v-btn v-bind="attrs" v-on="on">User</v-btn>
        </template>
        <v-list>
          <v-list-item v-for="(item, index) in opciones" :key="index" v-model="opcion" >
            <v-btn @click="item.funcion">{{item.name}}</v-btn>
          </v-list-item>
        </v-list>
      </v-menu>
      <v-img src="../public/img/startLogin.gif" aspect-ratio="2" max-width="100px" contain></v-img>
    </v-app-bar> 
    <v-navigation-drawer v-if="logeado" v-model="mostrar" app clipped>
      <v-list>
        <v-list-item><v-btn text to="/dashboard">Dashboard</v-btn> </v-list-item>
        <v-list-item><v-btn text to="/roles">Roles</v-btn></v-list-item>
        <v-list-item><v-btn text to="/papeleta">Papeleta</v-btn></v-list-item>
        <v-list-item><v-btn text to="/ubicacion">Ubicacion</v-btn></v-list-item>
        <v-list-item><v-btn text to="/recorrido">Recorrido</v-btn></v-list-item>
        <v-list-item><v-btn text to="/agregarBoleteras">Boletera</v-btn></v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
import {mapGetters} from 'vuex'
export default {
  name: 'App',

  components: {
    
  },
  computed: {
    ...mapGetters('logdata', {
      logeado: 'getSucess',
      mensaje: 'getMensaje'
    })
  },
  data: () => ({
    //
    opcion: null,
    mostrar: true,
    opciones: [{name:"Cerrar Sesion", value: "closeSesion"}]
  }),
};
</script>
