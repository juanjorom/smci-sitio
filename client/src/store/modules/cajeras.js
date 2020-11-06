import axios from "axios"

const state = {
    boleteras: [],
    permisionarios: [],
    unidades: [],
    choferes: [],
    rutas: [],
    openLaps: [],
    proxChofer: "",
    proxRuta: "",
    proxUnidad: "",
    proxBoleteras: []
}

const getters = {
    getAllBoleteras: state =>{
        return state.boleteras
    },
    getAllBoleterasList: state => {
        return state.boleteras.filter(el => el.estado=="NO ASIGNADA")
    },
    getBoleteraById: state => id => {
        return state.boleteras.find(el => el.codigo==id)
    },
    getBoleterasByStatus: state => estado => {
        return state.boleteras.filter(el => el.status == estado)
    },
    getBoleterasFilter: state => filtro => {
        return state.boleteras.filter(el =>  (el.codigo.includes(filtro) && el.estado=="NO ASIGNADA")) 
    },
    getPermisionarios: state => {
        return state.permisionarios
    },
    getAllUnidades: state =>{
        return state.unidades
    },
    getAllUnidadesList: state => {
        return state.unidades.map(el => el.nombre)
    },
    getUnidadesFilter: state => filtro => {
        return  state.unidades.filter(el => el.nombre.toUpperCase().includes(filtro.toUpperCase()))
    },
    getAllChoferes: state =>{
        return state.choferes
    },
    getAllChoferesList: state => {
        return state.choferes.map(el => el.nombre)
    },
    getChoferesFilter: state => filtro =>{
        return state.choferes.filter(el => el.nombre.toUpperCase().includes(filtro.toUpperCase()))
    },
    getAllRutas: state => {
        return state.rutas
    },
    getAllOpenLaps: state => {
        return state.openLaps
    },
    getOpenLapsFilter: state => filtro => {
        return state.openLaps.filter(el => el.unidad.includes(filtro.toUpperCase()))
    },
    getLapById: state => id => {
        return state.openLaps.find(el => el.id==id)
    },
    getProxChofer: state =>{
        return state.proxChofer
    },
    getProxUnidad: state => {
        return state.proxUnidad
    },
    getProxRuta: state =>{
        return state.proxRuta
    },
    getProxBoleteras: state =>{
        return state.proxBoleteras
    }
}

const mutations = {
    setBoleteras(state, boleteras){
        state.boleteras = boleteras
    },
    addBoleteraLocal(state, boletera){
        state.boleteras.push(boletera)
    },
    setPermisionarios(state, permisionarios){
        state.permisionarios = permisionarios
    },
    setUnidades(state, unidades){
        state.unidades = unidades
    },
    setChoferes(state, choferes){
        state.choferes = choferes
    },
    setRutas(state, rutas){
        state.rutas = rutas
    },
    setOpenLaps(state, vueltas){
        state.openLaps = vueltas
    },
    setProxChofer(state, chofer){
        state.proxChofer = chofer
    },
    setProxUnidad(state, unidad){
        state.proxChofer = unidad
    },
    setProxRuta(state, ruta){
        state.proxRuta = ruta
    },
    setProxBoleteras(state, boleteras){
        state.proxBoleteras = boleteras
    }
}

const actions = {
    async getAllPermisionarios({rootState, commit}){
        var peticion = await axios.get(rootState.logdata.host + "/getAllPermisionarios?token="+rootState.logdata.key)
        if(peticion.data.mensaje=="ok"){
            commit('setPermisionarios', peticion.data.data)
        }
    },
    async getAllBoleterasServer({rootState, commit})
    {
        var peticion = await axios.get(rootState.logdata.host + "/getAllBoleteras?token="+rootState.logdata.key)
        if(peticion.data.mensaje =="ok")
        {
            commit('setBoleteras', peticion.data.data)
        }else{
            commit('setBoleteras', [])
        }
    },
    async addBoletera({rootState, commit}, boletera){
        var datos = {
            token: rootState.logdata.key,
            boletoInicio: boletera.inicio,
            permisionario: boletera.permisionario,
            boletoFinal: boletera.termina,
            totalBoletos: parseInt(boletera.termina,10) - parseInt(boletera.inicio,10)+1,
            status: "NO ASIGNADA"
        }
        var peticion = await axios.post(rootState.logdata.host + "/addBoletera", datos)
        if(peticion.data.mensaje=="ok"){
            commit('addBoleteraLocal', peticion.data.data)
            alert("Boletera Añadida con éxito")
        }
        else{
            alert("Error al añadir")
        }
    },
    async deleteBoletera({rootState}, boletera){
        try{
            var peticion = await axios.delete(rootState.logdata.host+"/deleteBoletera?token="+rootState.logdata.key+"&codigo="+boletera)
            if(peticion.data.mensaje=="ok"){
                return true
            }
            return false
        }
        catch{
            return false
        }
    },
    async getAllUnidadesServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host+"/getAllUnidades?token="+rootState.logdata.key)
            if(peticion.data.mensaje=="ok"){
                commit('setUnidades', peticion.data.data)
            }
        }
        catch (error) {
            console.log("Error de Conexion", error);
        }
    },
    async getAllChoferesServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host+"/getUsersByRole?token="+rootState.logdata.key+"&rol=CHOFER")
            if(peticion.data.mensaje=="ok"){
                commit('setChoferes', peticion.data.data)
            }
        }
        catch (error) {
            console.log("Error de Conexion", error);
        }
    },

    async getAllRutasServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host+"/getAllRutas?token="+rootState.logdata.key)
            if(peticion.data.mensaje=="ok"){
                commit('setRutas', peticion.data.data)
            }
        }
        catch (error) {
            console.log("Error de Conexion", error);
        }
    },
    async iniciarVuelta({rootState},vuelta){   
        
        vuelta.token = rootState.logdata.key
        try{
            var peticion = await axios.post(rootState.logdata.host+"/openLap", vuelta )
            if(peticion.data.mensaje=="ok"){
                return true
            }
            return false
        }
        catch  {
            return false
        }
    },

    async getAllOpenLapsServer({rootState, commit}){
        try {
            var peticion = await axios.get(rootState.logdata.host+"/getAllOpenLaps?token="+rootState.logdata.key)
            if(peticion.data.mensaje=="ok"){
                commit('setOpenLaps', peticion.data.data)
            }else{
                commit('setOpenLaps',[])
            }
        } catch (error) {
            console.log(error)
            commit('setOpenLaps',[])
        }
    },

    async closeLapId({rootState}, datos){
        datos.token=rootState.logdata.key
        try{
            var peticion =  await axios.put(rootState.logdata.host+"/closeLap", datos)
            if(peticion.data.mensaje=="ok"){
                return true
            }else{
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