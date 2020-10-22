import axios from 'axios'

const state = {
    key: null,
    user: null,
    host: "http://smci.com.mx/api/recursos",
    sending: false,
    sucess: false,
    arbol: null,
    password: null,
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
    getArbol: state =>{
        return state.arbol
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
        //await idb.addUser({user:ojet})
        //jdb.writeUser(ojet)
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
    async genArbol({dispatch}, variables){
        var result = []
        var hijos = variables.grup.filter(item => item.groupfatherid==variables.padre)
        for (var key in hijos) {
            var ob={
                name: hijos[key].groupname,
                id: hijos[key].groupid,
                hijos: await dispatch('genArbol',{padre: hijos[key].groupid, grup: variables.grup, cars: variables.cars}),
                carros: variables.cars.filter(car => car.groupid==hijos[key].groupid)
            }
            result.push(ob)
        }
        return result
    },

    async obtenerArbol({dispatch}, variables){
        var arbolito = {}
        Object.defineProperties(arbolito,{
            id:{
                value: variables.grup[0].groupid,
                writable: true
            },
            name: {
                value: variables.grup[0].groupname,
                writable: true
            },
            hijos: {
                value: await dispatch('genArbol',{padre: variables.grup[0].groupid, grup: variables.grup, cars: variables.cars}),
                writable: true
            },
            carros: {
                value: variables.cars.filter(item => item.groupid==variables.grup[0].groupid),
                writable: true
            },
        })
        return arbolito
    },

    async pedirDatos({state},arbol){
        try {
            const response = await axios.get(state.host+arbol+'?key='+state.key)
            return response
        } catch (error) {
            alert(error)
        }
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

}


export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}