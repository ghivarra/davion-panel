const env = import.meta.env

export default [

    // Login
    { path: `/${env.VITE_LOGIN_PAGE}`, name: 'login', component: () => import('../views/LoginView.vue') },

    // Header Menu Routes
    { path: `/${env.VITE_PANEL_PAGE}/profile`, name: 'panel.profile', component: () => import('../views/Panel/PanelProfileView.vue'), meta: { 'pageName': 'Profil' } },
    { path: `/${env.VITE_PANEL_PAGE}/account`, name: 'panel.account', component: () => import('../views/Panel/PanelAccountView.vue'), meta: { 'pageName': 'Pengaturan Akun' } },
    { path: `/${env.VITE_PANEL_PAGE}/account/password`, name: 'panel.account.password', component: () => import('../views/Panel/PanelAccountPasswordView.vue'), meta: { 'pageName': 'Password' } },

    // Admin Menu Routes
    { path: `/${env.VITE_PANEL_PAGE}`, name: 'panel.dashboard', component: () => import('../views/Panel/PanelDashboardView.vue'), meta: { 'pageName': 'Dasbor' } },
    { path: `/${env.VITE_PANEL_PAGE}/administrator`, name: 'panel.admin', component: () => import('../views/Panel/PanelAdminView.vue'), meta: { 'pageName': 'Administrator' } },
    { path: `/${env.VITE_PANEL_PAGE}/role`, name: 'panel.role', component: () => import('../views/Panel/PanelRoleView.vue'), meta: { 'pageName': 'Role & Perizinan' } },
    { path: `/${env.VITE_PANEL_PAGE}/module`, name: 'panel.module', component: () => import('../views/Panel/PanelModuleView.vue'), meta: { 'pageName': 'Modul & Fitur' } },
    { path: `/${env.VITE_PANEL_PAGE}/menu`, name: 'panel.menu', component: () => import('../views/Panel/PanelMenuView.vue'), meta: { 'pageName': 'Tampilan Menu' } },
    { path: `/${env.VITE_PANEL_PAGE}/website`, name: 'panel.website', component: () => import('../views/Panel/PanelWebsiteView.vue'), meta: { 'pageName': 'Pengaturan Website' } },
    
    // Catch All / 404 Page
    { path: '/:pathMatch(.*)*', name: 'pageNotFound', component: () => import('../components/PageNotFoundComponent.vue'), meta: { 'pageName': 'Halaman Tidak Ditemukan' } },
]