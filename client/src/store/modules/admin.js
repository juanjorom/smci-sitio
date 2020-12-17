import axios from 'axios'

const state = {
    usuarios: [],
    roles: [],
    modulos: []
}

const getters =  {
    getUsuarios: state =>{
        return state.usuarios
    },
    getRoles: state =>{
        return state.roles
    },
    getUnidades: state =>{
        return state.unidades
    },
    getModulos: state =>{
        return state.modulos
    }
}

const mutations = {
    setUsuarios(state, usuarios){
        state.usuarios = usuarios
    },
    setRoles(state, roles){
        state.roles = roles
    },
    setModulos(state, modulos){
        state.modulos =modulos
    }
}

const actions = {
    async getUsuariosServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host + "/getAllUsers?token="+rootState.logdata.key)
            if(peticion.data.mensaje == "ok"){
                commit('setUsuarios', peticion.data.data)
            }else{
                commit('setUsuarios', [])
            }
        } catch {
            commit('setUsuarios', [])
        }
    },

    async addModulo({rootState, dispatch}, modulo){
        try{
            modulo.token = rootState.logdata.key
            var peticion = await axios.post(rootState.logdata.host + "/addModulo", modulo)
            if(peticion.data.mensaje == "ok"){
                dispatch('getModulosServer')
                return true
            }
            else{
                return false
            }
        }catch{
            return false
        }
    },
    async getRolesServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host + "/getRoles?token="+rootState.logdata.key)
            if(peticion.data.mensaje == "ok"){
                commit('setRoles', peticion.data.data)
            }else{
                commit('setRoles', [])
            }
        } catch {
            commit('setRoles', [])
        }
    },

    async addUser({rootState, dispatch}, usuario){
        try{
            usuario.token = rootState.logdata.key
            var peticion = await axios.post(rootState.logdata.host + "/addUser", usuario)
            if(peticion.data.mensaje == "ok")
            {
                dispatch('getUsuariosServer')
                return true
            }else{
                return false
            }
        }catch{
            return false
        }
    },
    async addUnidad({rootState}, unidad){
        try{
            unidad.token = rootState.logdata.key
            var peticion = await axios.post(rootState.logdata.host + "/addUnidad", unidad)
            if(peticion.data.mensaje == "ok")
            {
                return true
            }else{
                return false
            }
        }catch{
            return false
        }
    },
    async getModulosServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host + "/getModulos?token="+rootState.logdata.key)
            if(peticion.data.mensaje == "ok"){
                commit('setModulos', peticion.data.data)
            }else{
                commit('setModulos', [])
            }
        } catch {
            commit('setModulos', [])
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