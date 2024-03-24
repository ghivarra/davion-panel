const env = import.meta.env

export default [

    // Login
    { path: `/${env.VITE_LOGIN_PAGE}`, name: 'login', component: () => import('../views/LoginView.vue') },

    // Header Menu Routes
    { path: `/${env.VITE_PANEL_PAGE}/profile`, name: 'panel.profile', component: () => import('../views/Panel/PanelProfileView.vue'), meta: { 'pageName': 'Profil' } },
    { path: `/${env.VITE_PANEL_PAGE}/account`, name: 'panel.account', component: () => import('../views/Panel/PanelAccountView.vue'), meta: { 'pageName': 'Pengaturan Akun' } },
    { path: `/${env.VITE_PANEL_PAGE}/account/password`, name: 'panel.account.password', component: () => import('../views/Panel/PanelAccountPasswordView.vue'), meta: { 'pageName': 'Ubah Password' } },

    // Admin Menu Routes
    { path: `/${env.VITE_PANEL_PAGE}`, name: 'panel.dashboard', component: () => import('../views/Panel/PanelDashboardView.vue'), meta: { 'pageName': 'Dasbor' } },

    // Catch All / 404 Page
    { path: '/:pathMatch(.*)*', name: 'pageNotFound', component: () => import('../components/PageNotFoundComponent.vue'), meta: { 'pageName': 'Halaman Tidak Ditemukan' } },
]