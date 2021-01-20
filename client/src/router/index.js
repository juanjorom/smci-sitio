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
import RolesHome from '@/views/RolesHome'
import Boleteras from '@/views/Boleteras'
import Caja from '@/views/Caja'
import AbrirVuelta from '@/views/AbrirVuelta'
import RecibirVuelta from '@/views/RecibirVuelta'
import CajaHome from '@/views/CajaHome'
import liquidarTurno from '@/views/LiquidarChofer'
import PagarReporte from '@/views/pagarReporte'
import Usuarios from '@/views/Usuarios'
import Unidades from '@/views/Unidades'
import Modulos from '@/views/Modulos'
import Movimientos from '@/views/Movimientos'
import liquidarPermisionario from '@/views/liquidarPermisionario'
import MovimientosHome from '@/views/MovimientosHome'
import ingresarPago from '@/views/ingresarPago'
import Password from '@/views/Password'
import Historial from '@/views/Historial'
import Reportes from '@/views/Reportes'

import reporteVueltas from '@/components/reporteVueltas'
import reporteVentas from '@/components/reporteVentas'

Vue.use(Router)

const routes= [
    {
        path: '/',
        redirect: '/login',
        meta:{
            title: "Admin Bus"
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta:{
            title: "Inicia Sesión - Admin Bus"
        }
        
    },
    {
        path: "/dashboard",
        name: "dashboard",
        component: Dashboard,
        meta:{
            title: "Dashboard - Admin Bus"
        }
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
        component: Roles,
        children: [
            {
                path: 'rolesHome',
                name: 'rolesHome',
                component: RolesHome
            },
            {
                path: '/',
                redirect: 'rolesHome'
            }
        ]
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
        name: "caja",
        component: Caja,
        children: [
            {
                path: 'cajaHome',
                name: 'cajaHome',
                component: CajaHome,
                meta:{
                    title: "Caja - Admin Bus"
                }
            },
            {
                path: 'abrirVuelta',
                name: 'abrirVuelta',
                params: '',
                component: AbrirVuelta,
                meta:{
                    title: "Abrir Vuelta - Admin Bus"
                }
            },
            {
                path: 'recibirVuelta/',
                name: 'recibirVuelta',
                component: RecibirVuelta,
                meta:{
                    title: "Recibir Vuelta - Admin Bus"
                }
            },
            {
                path: 'liquidarTurno',
                name: 'liquidarTurno',
                component: liquidarTurno,
                meta:{
                    title: "Liquidar  - Admin Bus"
                }
            },
            {
                path: 'pagarReporte',
                name: 'pagarReporte',
                component: PagarReporte,
                meta:{
                    title: "Pagar Chofer - Admin Bus"
                }
            },
            {
                path: '/',
                redirect: 'cajaHome'
            }
        ]
    },
    {
        path: '/usuarios',
        name: 'usuarios',
        component: Usuarios
    },
    {
        path: '/unidades',
        name: 'unidades',
        component: Unidades
    },
    {
        path: '/modulos',
        name: 'modulos',
        component: Modulos
    },
    {
        path: '/historial',
        name: 'historial',
        component: Historial
    },
    {
        path: '/movimientos',
        name: 'movimientos',
        component: Movimientos,
        children: 
        [
            {
                path: 'movimientosHome',
                name: 'movimientosHome',
                component: MovimientosHome
            },
            {
                path: 'liquidarPermisionario',
                name: 'liquidarPermisionario',
                component: liquidarPermisionario
            },
            {
                path: 'ingresarPago',
                name: 'ingresarPago',
                component: ingresarPago
            },
            {
                path: '/',
                redirect: 'movimientosHome',
            }
        ]
    },
    {
        path: '/password',
        name: 'password',
        component: Password
    },
    {
        path: '/reporteVueltas',
        name: 'reporteVueltas',
        component: reporteVueltas
    },
    {
        path: '/reporteVentas',
        name: 'reporteVentas',
        component: reporteVentas
    },
    {
        path: '/reportes',
        name: 'reportes',
        component: Reportes,
        children: [
            
        ]
    },
    {
        path: '*',
        redirect: '/dashboard',
    }
]


const router= new Router({
    routes
})

export default router