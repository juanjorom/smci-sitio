import axios from "axios"

const state = {
    boleteras: [],
    permisionarios: [],
    unidades: [],
    choferes: [],
    rutas: [],
    openLaps: [],
    turnos: [],
    turnoPagar: null,
    boleterasAsignar: [],
    movimientos: [],
    liquidaciones: [],
    recaudacion: {fechaHora: null, monto: null}
}

const getters = {
    getAllBoleteras: state =>{
        return state.boleteras
    },
    getAllBoleterasList: state => {
        return state.boleteras.filter(el => el.estado=="NO ASIGNADA")
    },
    getBoleterasAsignar: state => {
        return state.boleterasAsignar
    },
    getBoleteraById: state => id => {
        return state.boleteras.find(el => el.codigo==id)
    },
    getBoleterasByStatus: state => estado => {
        return state.boleteras.filter(el => el.status == estado)
    },
    getBoleterasFilter: state => filtro => {
        return state.boleterasAsignar.filter(el =>  (el.codigo.includes(filtro) && el.estado=="NO ASIGNADA")) 
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
    },
    getAllTurnos: state => {
        return state.turnos
    },
    getTurnosFilter: state => filtro => {
        return state.turnos.filter(el => el.unidad == filtro)
    },
    getTurnoPagar: state => {
        return state.turnoPagar
    },
    getMovimientos: state => {
        return state.movimientos
    },
    getLiquidaciones: state => {
        return state.liquidaciones
    },
    getRecaudado: state => {
        return state.recaudacion
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
    setTurnos(state, turnos){
        state.turnos = turnos
    },
    setTurnoPagar(state, turno){
        state.turnoPagar = turno
    },
    setBoleterasAsignar(state, boleteras){
        state.boleterasAsignar = boleteras
    },
    setMovimientos(state, movimientos){
        state.movimientos = movimientos
    },
    setLiquidaciones(state, liquidaciones){
        state.liquidaciones = liquidaciones
    },
    setRecaudacion(state, recaudado){
        state.recaudacion = recaudado
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
        try {
            var peticion = await axios.post(rootState.logdata.host + "/addBoletera", datos)
            if(peticion.data.mensaje=="ok"){
                commit('addBoleteraLocal', peticion.data.data)
                return {mensaje: "El Codigo de Boletera es: " + peticion.data.data.codigo, codigo: peticion.data.data.codigo}
            }
            else{
                return {mensaje:"Error al añadir", codigo: ""}
            }
        } catch (error) {   
            console.log("Error de conexion", error);
            return {mensaje: "Error de conexión", codigo: ""}
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
                commit('setOpenLaps',  peticion.data.data)
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
            }
            return false
        } catch {
            return false
        }
    },
    async getAllTurnosServer({rootState, commit}){
        try {
            var peticion = await axios.get(rootState.logdata.host+"/getAllTurnos?token="+rootState.logdata.key)
            if(peticion.data.mensaje=="ok"){
                commit("setTurnos", peticion.data.data)
            }else{
                commit("setTurnos", [])
            }
            
        } catch (error) {
            console.log("Error de conexion ", error);
        }
    },
    async getTurnoById({rootState, commit}, id){
        try{
            var peticion = await axios.get(rootState.logdata.host+"/getTurnoById?token="+rootState.logdata.key+"&turno="+id)
            if(peticion.data.mensaje=="ok"){
                commit('setTurnoPagar' ,peticion.data.data)
            }
        }catch (error){
            console.log("Error de conexion", error);
        }
    },
    async pagarTurno({rootState}, turno){
        try{
            turno.token = rootState.logdata.key
            const peticion = await axios.put(rootState.logdata.host+"/pagarTurno", turno)
            if(peticion.data.mensaje=="ok"){
                return true
            }
            return false
        } catch (error) {
            console.log("Error en la conexion ",error)
            return false
        }
    },
    async getBoleterasAsignarServer({rootState, commit}, unidad){
        try{
            var peticion = await axios.get(rootState.logdata.host+"/getBoleterasAsignar?token="+rootState.logdata.key +"&unidad="+unidad)
            if(peticion.data.mensaje=="ok"){
                commit('setBoleterasAsignar', peticion.data.data)
            }else{
                commit('setBoleterasAsignar', [])
            }
        } catch (error){
            console.log("Error en la conexion ", error)
        }
    },
    async getMovimientosServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host+"/getMovimientos?token="+rootState.logdata.key)
            if(peticion.data.mensaje == "ok"){
                commit('setMovimientos', peticion.data.data)
            }else{
                commit('setMovimientos', [])
            }
        } catch (error) {
            commit('setMovimientos', [])
            console.log("Error en la conexion", error);
        }
    },
    async addRetiro({rootState, dispatch}, retiro){
        try{
            retiro.token = rootState.logdata.key
            var peticion = await axios.post(rootState.logdata.host+"/addRetiro", retiro)
            if(peticion.data.mensaje == "ok"){
                dispatch('getMovimientosServer')
                return true
            }else{
                return false
            }
        }catch (error){
            console.log("Error de conexion", error);
            return false
        }
    },
    async getLiquidacionesServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host+"/getLiquidaciones?token="+rootState.logdata.key)
            if(peticion.data.mensaje == "ok"){
                commit('setLiquidaciones', peticion.data.data)
            }else{
                commit('setLiquidaciones', [])
            }
        } catch (error) {
            commit('setLiquidaciones', [])
            console.log("Error en la conexion", error);
        }
    },
    async getRecaudadoServer({rootState, commit}){
        try{
            var peticion = await axios.get(rootState.logdata.host+"/getRecaudacion?token="+rootState.logdata.key)
            if(peticion.data.mensaje == "ok"){
                commit('setRecaudacion', peticion.data.data)
            }else{
                commit('setRecaudacion', {fechaHora: null, monto: null})
            }
        } catch (error) {
            commit('setRecaudacion', {fechaHora: null, monto: null})
            console.log("Error en la conexion", error);
        }
    },
    async pagarPermisionario({rootState, dispatch}, liquidacion){
        try{
            liquidacion.token = rootState.logdata.key
            const peticion = await axios.put(rootState.logdata.host+"/pagarPermisionario", liquidacion)
            if(peticion.data.mensaje=="ok"){
                dispatch('getLiquidacionesServer')
                return true
            }
            return false
        } catch (error) {
            console.log("Error en la conexion ",error)
            return false
        }
    },

    async ingresarPago({rootState, dispatch}, pago){
        try{
            pago.token = rootState.logdata.key
            const peticion = await axios.post(rootState.logdata.host+"/ingresarPago", pago)
            if(peticion.data.mensaje=="ok"){
                dispatch('getMovimientosServer')
                dispatch('getRecaudadoServer')
                return true
            }
            return false
        } catch (error) {
            console.log("Error en la conexion ",error)
            return false
        }
    },
    async realizarRetiro({rootState, dispatch}, retiro){
        try{
            retiro.token = rootState.logdata.key
            const peticion = await axios.post(rootState.logdata.host+"/realizarRetiro", retiro)
            if(peticion.data.mensaje=="ok"){
                dispatch('getMovimientosServer')
                dispatch('getRecaudadoServer')
                return true
            }
            return false
        } catch (error) {
            console.log("Error en la conexion ",error)
            return false
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