import Vue from 'vue'
import Vuex from 'vuex'
import logdata from './modules/logdata'
import appdata from './modules/appData'
import cajeras from './modules/cajeras'
import admin from './modules/admin'
import reportes from './modules/reportes'
Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
      logdata,
      appdata,
      cajeras,
      admin,
      reportes
    }
})