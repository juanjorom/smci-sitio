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
      Control Bus
      <v-spacer></v-spacer>
      <v-img src="../public/img/startLogin.gif" aspect-ratio="2" max-width="100px" contain></v-img>
    </v-app-bar> 
    <v-navigation-drawer v-if="logeado" v-model="mostrar" app clipped>
      <v-list>
        <v-list-item><v-btn text to="/dashboard">Dashboard</v-btn> </v-list-item>
        <v-list-item><v-btn text to="/roles">Roles</v-btn></v-list-item>
        <v-list-item><v-btn text to="/papeleta">Papeleta</v-btn></v-list-item>
        <v-list-item><v-btn text to="/ubicacion">Ubicacion</v-btn></v-list-item>
        <v-list-item><v-btn text to="/recorrido">Recorrido</v-btn></v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>

export default {
  name: 'App',

  components: {
    
  },

  data: () => ({
    //
    logeado: true,
    mostrar: true
  }),
};
</script>
