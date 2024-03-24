const env = import.meta.env

export default [

    // Login
    { path: `/${env.VITE_LOGIN_PAGE}`, name: 'login', component: () => import('../views/LoginView.vue') },

    // Header Menu Routes
    { path: `/${env.VITE_PANEL_PAGE}/profile`, name: 'panel.profile', component: () => import('../views/Panel/PanelProfileView.vue') },
    { path: `/${env.VITE_PANEL_PAGE}/account`, name: 'panel.account', component: () => import('../views/Panel/PanelAccountView.vue') },
    { path: `/${env.VITE_PANEL_PAGE}/account/password`, name: 'panel.account.password', component: () => import('../views/Panel/PanelAccountPasswordView.vue') },

    // Admin Menu Routes
    { path: `/${env.VITE_PANEL_PAGE}`, name: 'panel.dashboard', component: () => import('../views/Panel/PanelDashboardView.vue') },

    // Catch All / 404 Page
    { path: '/:pathMatch(.*)*', name: 'pageNotFound', component: () => import('../components/PageNotFoundComponent.vue') },
]