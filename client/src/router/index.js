import Vue from 'vue'
import Router from 'vue-router'
//import Home from '@/views/Home'
import Login from '@/views/Login'
import Dashboard from '@/views/Dashboard'
import Papeleta from '@/views/Papeleta'
import Recorrido from '@/views/Recorrido'
import Ubicacion from '@/views/Ubicacion'
import Configuracion from '@/views/Configuracion'
import Roles from '@/views/Roles'
import Boleteras from '@/views/AddBoletera'

Vue.use(Router)

const routes= [
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: "/dashboard",
        name: "dashboard",
        component: Dashboard
    },
    {
        path: "/papeleta",
        name: "papeleta",
        component: Papeleta
    },
    {
        path: "/recorrido",
        name: "recorrido",
        component: Recorrido
    },
    {
        path: "/ubicacion",
        name: "ubicacion",
        component: Ubicacion
    },
    {
        path: "/roles",
        name: "roles",
        component: Roles
    },
    {
        path: "/configuracion",
        name: "configuracion",
        component: Configuracion
    },
    {
        path: "/agregarBoleteras",
        name: "agregarBoleteras",
        component: Boleteras
    }
]

const router= new Router({
    routes
})

export default router