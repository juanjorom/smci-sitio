import axios from 'axios'

const state = {
    vueltas: [],
    reportes: [],
    ventas: []
}

const getters = {
    getVueltas: state =>{
        return state.vueltas
    },
    getReportes: state =>{
        return state.reportes
    },
    getVentas: state => {
        return state.ventas
    }
}

const mutations = {
    setVueltas(state, vueltas){
        state.vueltas = vueltas
    },
    setReportes(state, reportes){
        state.reportes = reportes
    },
    setVentas(state, ventas){
        state.ventas = ventas
    }
}

const actions = {
    async getVueltasServer({rootState, commit},fechas){
        try{
            var peticion = await axios.get(rootState.logdata.host + "/getVueltas?token="+rootState.logdata.key+"&inicio="+fechas.inicio+" 00:00:00"+"&fin="+fechas.fin+" 23:59:59")
            if(peticion.data.mensaje == "ok"){
                commit('setVueltas', peticion.data.data)
            }else{
                commit('setVueltas', [])
            }
        } catch {
            commit('setVueltas', [])
        }
    },
    async getReportesServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host + "/getReportes?token="+rootState.logdata.key)
            if(peticion.data.mensaje == "ok"){
                commit('setReportes', peticion.data.data)
            }else{
                commit('setReportes', [])
            }
        } catch {
            commit('setReportes', [])
        }
    },
    async getVentasServer({rootState, commit}, fechas){
        try{
            var peticion = await axios.get(rootState.logdata.host + fechas.recurso + "?token="+rootState.logdata.key+"&inicio="+fechas.inicio+" 00:00:00"+"&fin="+fechas.fin+" 23:59:59&filtro="+fechas.filtro)
            console.log(peticion);
            if(peticion.data.mensaje == "ok"){
                commit('setVentas', peticion.data.data)
            }else{
                commit('setVentas', [])
            }
        } catch {
            commit('setVentas', [])
        }
    },
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}