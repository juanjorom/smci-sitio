import axios from 'axios'

const state = {
    key: null,
    user: null,
    host: process.env.VUE_APP_PETICIONES_URL,
    sending: false,
    sucess: false,
    hostList: [],
    usersList:[],
    mensaje: "Control Bus",
    rol: "",
    permisos: []
}

const getters = {
    getKey: state => {
        return  state.key
    },
    getUser: state => {
        return state.user
    },
    getSending: state =>{
        return state.sending
    },
    getSucess: state => {
        return state.sucess
    },
    getUsersList(){
        return []
    },
    getMensaje: state =>{
        return state.mensaje
    },
    getHostList(){
        return []
    },
    getRol: state => {
        return  state.rol
    },
    getPermisos: state => {
        return  state.rol
    },
}

const mutations = {
    setKey(state, key){
        state.key = key
    },
    setUser(state, ojet){
        state.user=ojet.user
    },
    setMensaje(state, mensaje){
        state.mensaje= mensaje
    },
    setSending(state, sending){
        state.sending= sending
    },
    setSucess(state, sucess){
        state.sucess = sucess
    },
    setArbol(state, arbol){
        state.arbol= arbol
    },
    setRol(state, rol){
        state.rol = rol
    },
    setPermisos(state, permisos){
        state.permisos = permisos
    }
}

const actions = {
    async log({commit, dispatch, rootState},form){
        commit('setSending', true)
        var verificado = await dispatch('verify',form)
        if(!verificado){
            alert('Error al logear')
        }else{
            commit('setUser', form.user)
            commit('setMensaje', "Hola "+form.user)
            var datos = await axios.get(rootState.logdata.host + "/getUser"+"?token="+rootState.logdata.key)
            if(datos.data.mensaje=="ok")
            {
                commit('setMensaje', "Hola "+datos.data.data.nombre)
                commit('setRol', datos.data.data.rol)
                commit('setSucess', true)
            }
            
        }
        commit('setSending', false)
        
    },
    async verify({commit, state},form ){
        var exito
        try {
            const response =await axios.post(state.host+"/login", {nickname:form.user,password:form.password})
            if(response.data.mensaje=="ok"){
                commit('setKey', response.data.token)
                exito= true
            }else{
                exito = false
            }
        } catch (error) {
            exito = false
        }
        return exito
    },

    async validarPassword({state}, password){
        try {
            var response = await axios.post(state.host+"/verify", {token: state.key, password: password})
            if(response.data.mensaje=="ok")
            {
                return true
            }
            return false
        }
        catch{
            return false
        }
    },
    async closeSesion({state}){
        try {
            await axios.put(state.host+"/closeSesion",{token: state.key})
        }
        catch (error){
            console.log("Error de Conexion", error);
        }
        state.key = null
        state.user = null
        state.sucess = false
    },
    async changePassword({state}, passwords){
        try{
            passwords.token = state.key
            var response = await axios.put(state.host + "/updatePassword", passwords)
            console.log(response);
            if(response.data.mensaje == "ok"){
                return true
            }
            else{
                return false
            }
        } catch {
            return false
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