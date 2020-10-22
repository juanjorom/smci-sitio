import Vue from 'vue'
import Vuex from 'vuex'
import logdata from './modules/logdata'
import appData from './modules/appData'
import cajeras from './modules/cajeras'
Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
      logdata,
      appData,
      cajeras
    }
})