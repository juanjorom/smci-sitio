const state = {
    boleteras: []
}

const getters = {
    getAllBoleteras: state =>{
        return state.boleteras
    },
    getBoleteraById: (state, id )=> {
        return state.boleteras.find(el => el.id==id)
    },
    getBoleterasByStatus: (state,  estado) => {
        return state.boleteras.filter(el => el.status == estado)
    }
}

const mutations = {
    addBoletera(state, boletera){
        console.log(boletera)
        var boletos = {}
        for(var i=boletera; i<(parseInt(boletera,10)+100); i++)
        {
            boletos[i]=null;
        }
        var datos = {
            id:boletera,
            boletos: boletos,
            status: "EN REVISION"
        }
        console.log(datos)
        state.boleteras.push(datos)
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