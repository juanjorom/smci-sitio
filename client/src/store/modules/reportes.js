import axios from 'axios'

const state = {
    vueltas: []
}

const getters = {
    getVueltas: state =>{
        return state.vueltas
    }
}

const mutations = {
    setVueltas(state, vueltas){
        state.vueltas = vueltas
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
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}