import axios from "axios"
const state = {
    datosPrincipal: null,
    accesos: []
}

const getters = {
    getDatosPrincipal: state => {
        return state.datosPrincipal
    },
    getAccesos: state => {
        return state.accesos
    }
}

const mutations = {
    setDatosPrincipal(state, datos){
        state.datosPrincipal = datos
    },
    setAccesos(state, accesos){
        state.accesos = accesos
    }
}

const actions = {
    async getAccesosServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host + "/getPermisos?token="+rootState.logdata.key)
            if(peticion.data.mensaje == "ok"){
                commit('setAccesos', peticion.data.data)
            }else{
                commit('setAccesos', [])
            }
        } catch {
            commit('setAccesos', [])
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