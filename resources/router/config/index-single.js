const env = import.meta.env

import LoginView from '@/views/LoginView.vue'
import PanelProfileView from '@/views/Panel/PanelProfileView.vue'
import PanelAccountView from '@/views/Panel/PanelAccountView.vue'
import PanelAccountPasswordView from '@/views/Panel/PanelAccountPasswordView.vue'
import PanelDashboardView from '@/views/Panel/PanelDashboardView.vue'
import PanelMenuView from '@/views/Panel/PanelMenuView.vue'
import PanelWebsiteView from '@/views/Panel/PanelWebsiteView.vue'
import PanelModuleView from '@/views/Panel/PanelModuleView.vue'
import PanelAdminView from '@/views/Panel/PanelAdminView.vue'
import PanelAdminSessionView from '@/views/Panel/PanelAdminSessionView.vue'
import PanelRoleView from '@/views/Panel/PanelRoleView.vue'
import PanelRoleCreateView from '@/views/Panel/PanelRoleCreateView.vue'
import PanelRoleEditView from '@/views/Panel/PanelRoleEditView.vue'
import PageNotFoundComponent from '@/components/PageNotFoundComponent.vue'

export default [

    // Login
    { path: `/${env.VITE_LOGIN_PAGE}`, name: 'login', component: LoginView },

    // Header Menu Routes
    { path: `/${env.VITE_PANEL_PAGE}/profile`, name: 'panel.profile', component: PanelProfileView, meta: { 'pageName': 'Profil' } },
    { path: `/${env.VITE_PANEL_PAGE}/account`, name: 'panel.account', component: PanelAccountView, meta: { 'pageName': 'Pengaturan Akun' } },
    { path: `/${env.VITE_PANEL_PAGE}/account/password`, name: 'panel.account.password', component: PanelAccountPasswordView, meta: { 'pageName': 'Password' } },

    // Admin Menu Routes
    { path: `/${env.VITE_PANEL_PAGE}`, name: 'panel.dashboard', component: PanelDashboardView, meta: { 'pageName': 'Dasbor' } },
    { path: `/${env.VITE_PANEL_PAGE}/menu`, name: 'panel.menu', component: PanelMenuView, meta: { 'pageName': 'Tampilan Menu' } },
    { path: `/${env.VITE_PANEL_PAGE}/website`, name: 'panel.website', component: PanelWebsiteView, meta: { 'pageName': 'Pengaturan Website' } },
    { path: `/${env.VITE_PANEL_PAGE}/module`, name: 'panel.module', component: PanelModuleView, meta: { 'pageName': 'Modul & Fitur' } },
    
    // Administrator Page
    { path: `/${env.VITE_PANEL_PAGE}/administrator`, name: 'panel.admin', component: PanelAdminView, meta: { 'pageName': 'Administrator' } },
    { path: `/${env.VITE_PANEL_PAGE}/administrator/session/:adminId`, name: 'panel.admin.sessions.id', component: PanelAdminSessionView, meta: { 'pageName': 'Sesi Login Admin' } },
    
    // Role Page
    { path: `/${env.VITE_PANEL_PAGE}/role`, name: 'panel.role', component: PanelRoleView, meta: { 'pageName': 'Role & Perizinan' } },
    { path: `/${env.VITE_PANEL_PAGE}/role/add-new`, name: 'panel.role.create', component: PanelRoleCreateView, meta: { 'pageName': 'Buat Role Baru' } },
    { path: `/${env.VITE_PANEL_PAGE}/role/edit/:roleId`, name: 'panel.role.edit.id', component: PanelRoleEditView, meta: { 'pageName': 'Edit Role' } },
    
    // Catch All / 404 Page
    { path: '/:pathMatch(.*)*', name: 'pageNotFound', component: PageNotFoundComponent, meta: { 'pageName': 'Halaman Tidak Ditemukan' } },
]