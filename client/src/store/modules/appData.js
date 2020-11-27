const state = {
    datosPrincipal: null,
    
}

const getters = {
    getDatosPrincipal: state => {
        return state.datosPrincipal
    }
}

const mutations = {
    setDatosPrincipal(state, datos){
        state.datosPrincipal = datos
    }
}

const actions = {
    
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}