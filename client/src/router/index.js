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
import Boleteras from '@/views/Boleteras'
import Caja from '@/views/Caja'
import AbrirVuelta from '@/views/AbrirVuelta'
import RecibirVuelta from '@/views/RecibirVuelta'
import CajaHome from '@/views/CajaHome'
import liquidarTurno from '@/views/LiquidarChofer'
import PagarReporte from '@/views/pagarReporte'

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
        path: "/boleteras",
        name: "boleteras",
        component: Boleteras
    },
    {
        path: "/caja",
        component: Caja,
        children: [
            {
                path: 'cajaHome',
                name: 'cajaHome',
                component: CajaHome
            },
            {
                path: 'abrirVuelta',
                name: 'abrirVuelta',
                params: '',
                component: AbrirVuelta
            },
            {
                path: 'recibirVuelta/:id',
                name: 'recibirVuelta',
                component: RecibirVuelta
            },
            {
                path: 'liquidarTurno',
                name: 'liquidarTurno',
                component: liquidarTurno
            },
            {
                path: 'pagarReporte',
                name: 'pagarReporte',
                component: PagarReporte
            },
            {
                path: '/',
                redirect: 'cajaHome'
            }
        ]
    },
    {
        path: '*',
        redirect: '/dashboard'
    }
]

const router= new Router({
    routes
})

export default router