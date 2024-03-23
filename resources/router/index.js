// import PageNotFoundComponent from '../views/Components/PageNotFoundComponent.vue'
// import LoginView from 
// import PanelDashboardView from '../views/Panel/PanelDashboardView.vue'
//import PanelProfileView from '../views/Panel/PanelProfileView.vue'
//import PanelAccountView from '../views/Panel/PanelAccountView.vue'
//import PanelAccountPasswordView from '../views/Panel/PanelAccountPasswordView.vue'

const env = import.meta.env

export default [

    // Login
    { path: `/${env.VITE_LOGIN_PAGE}`, name: 'login', component: () => import('../views/LoginView.vue') },

    // Header Menu Routes
//    { path: `/${env.VITE_PANEL_PAGE}/profile`, name: 'panel.profile', component: PanelProfileView, meta: { componentName: 'Panel/PanelProfileView' } },
//    { path: `/${env.VITE_PANEL_PAGE}/account`, name: 'panel.account', component: PanelAccountView, meta: { componentName: 'Panel/PanelAccountView' } },
//    { path: `/${env.VITE_PANEL_PAGE}/account/password`, name: 'panel.account.password', component: PanelAccountPasswordView, meta: { componentName: 'Panel/PanelAccountPasswordView' } },

    // Admin Menu Routes
    { path: `/${env.VITE_PANEL_PAGE}`, name: 'panel.dashboard', component: () => import('../views/Panel/PanelDashboardView.vue') },

    // Catch All / 404 Page
//    { path: '/:pathMatch(.*)*', name: 'pageNotFound', component: PageNotFoundComponent, meta: { componentName: 'Components/PageNotFoundComponent' } },
]