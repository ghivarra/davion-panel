<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>

        <section id="create-table" class="mb-4">
            <button v-on:click="adminCreateModalOpen" type="button" class="btn btn-primary">
                <font-awesome icon="fas fa-plus" class="me-2"></font-awesome>
                Tambah Admin
            </button>
        </section>

        <!-- ADMIN CREATE MODAL -->
        <admin-create-modal ref="adminCreateModal" v-bind:roles="roles" v-on:formSubmitted="refreshTable"></admin-create-modal>

        <!-- ADMIN UPDATE MODAL -->
        <admin-update-modal ref="adminUpdateModal" v-bind:admin="adminUpdate" v-bind:roles="roles" v-on:formSubmitted="refreshTable"></admin-update-modal>

        <!-- ADMIN DETAIL MODAL -->
         <admin-detail-modal ref="adminDetailModal" v-bind:admin="adminDetail"></admin-detail-modal>

        <!-- TABLE -->
        <section ref="adminTableSection">
            <vue-table id="admin-table" ref="adminTable" v-bind:defaultLength="25" v-bind:lengthOptions="[10,25,50]"
                v-bind:url="table.url" v-bind:order="table.order" v-bind:columns="table.columns"
                v-bind:processData="processData" v-on:afterCreate="$emit('loaded')">
                <template v-slot:header>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>
                            <input v-model="table.columns[2].query" name="searchInput1" type="text" class="form-control">
                        </th>
                        <th>
                            <input v-model="table.columns[3].query" name="searchInput2" type="text" class="form-control">
                        </th>
                        <th>
                            <input v-model="table.columns[4].query" name="searchInput3" type="text" class="form-control">
                        </th>
                        <th>
                            <select v-model="table.columns[5].query" name="searchInput4" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option v-for="(role, n) in roles" v-bind:key="n" v-bind:value="role.id">
                                    {{ role.name }}
                                </option>
                            </select>
                        </th>
                        <th>
                            <select v-model="table.columns[6].query" name="searchInput6" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </th>
                    </tr>
                </template>
            </vue-table>
        </section>
        <!-- TABLE -->
    </main>
</template>

<script>

import { panelUrl, checkAxiosError, restructurized } from '@/libraries/Function'
import VueTable from '@/libraries/Ghivarra/VueTable/VueTable.vue'
import AdminCreateModal from '@/views/Modal/AdminCreateModal.vue'
import AdminUpdateModal from '@/views/Modal/AdminUpdateModal.vue'
import AdminDetailModal from '@/views/Modal/AdminDetailModal.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'panel-admin-view',
    inject: ['admin', 'loggingOut', 'showLoader', 'hideLoader'],
    components: {
        'vue-table': VueTable,
        'admin-create-modal': AdminCreateModal,
        'admin-update-modal': AdminUpdateModal,
        'admin-detail-modal': AdminDetailModal,
    },
    data: function() {
        return {
            table: {
                url: panelUrl('administrator/datatable'),
                order: {
                    column: 'name',
                    dir: 'asc'
                },
                columns: [
                    { query: '', text: 'No.', key: 'no', sortable: false, searchable: false, class: ['col-no'] },
                    { query: '', text: '', key: 'action', sortable: false, searchable: false, class: ['col-action'] },
                    { query: '', text: 'Nama', key: 'name', class: ['col-primary'] },
                    { query: '', text: 'Username', key: 'username', class: ['col-secondary'] },
                    { query: '', text: 'Email', key: 'email', class: ['col-secondary'] },
                    { query: '', text: 'Role', key: 'admin_role_name', class: ['col-email-status'] },
                    { query: '', text: 'Status', key: 'status', class: ['col-secondary'] },
                ]
            },
            tableData: [],
            roles: [],
            adminDetail: {},
            adminUpdate: {}
        }
    },
    methods: {
        processData: function(data) {
            this.tableData = data.row

            if (data.row.length < 1) {
                return data
            }

            data.row.forEach((item, i) => {
                let btnText = (item.status === 'Aktif') ? 'Nonaktifkan' : 'Aktifkan'
                let btnTextColor = (item.status === 'Aktif') ? 'text-warning' : 'text-success'
                data.row[i].action = `<div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle table-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-list me-1"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button data-key="${i}" class="detail-button dropdown-item" type="button" title="Cek Sesi Login">
                                                    <i class="fa-solid fa-circle-info me-1 text-primary"></i>
                                                    Detail
                                                </button>
                                            </li>
                                            <li>
                                                <button data-key="${i}" class="check-session-button dropdown-item" type="button" title="Cek Sesi Login">
                                                    <i class="fa-solid fa-clock-rotate-left me-1 text-primary"></i>
                                                    Sesi Login
                                                </button>
                                            </li>
                                            <li>
                                                <button data-key="${i}" class="edit-button dropdown-item" type="button" title="Edit Data">
                                                    <i class="fa-solid fa-pen-to-square me-1 text-primary"></i>
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button data-key="${i}" class="status-button dropdown-item" type="button" title="${btnText} Data">
                                                    <i class="fa-solid fa-sliders me-1 ${btnTextColor}"></i>
                                                    ${btnText}
                                                </button>
                                            </li>
                                            <li>
                                                <button data-key="${i}" class="delete-button dropdown-item" type="button" title="Hapus Data">
                                                    <i class="fa-solid fa-trash-can me-1 text-danger"></i>
                                                    Hapus
                                                </button>
                                            </li>
                                        </ul>
                                    </div>`

                let statusColor = (item.status === 'Aktif') ? 'success' : 'warning';

                data.row[i].statusDefault = item.status
                data.row[i].status = `<span class="bg-${statusColor} text-white py-2 px-3 rounded-pill fw-bold">${item.status}</span>`

                data.row[i].superadminDefault = item.is_superadmin
                data.row[i].is_superadmin = (item.is_superadmin === '1') ? `<span class="bg-success text-white py-2 px-3 rounded-pill fw-bold">Ya</span>` : `<span class="bg-warning py-2 px-3 text-white rounded-pill fw-bold">Bukan</span>`
            })

            // return
            return data
        },
        refreshTable: function() {
            this.$refs.adminTable.draw()
        },
        adminCreateModalOpen: function() {
            this.$refs.adminCreateModal.$refs.modalOpenButton.click()
        },
        adminUpdateModalOpen: function(key) {
            this.adminUpdate = restructurized(this.tableData[key])
            this.$refs.adminUpdateModal.$refs.modalOpenButton.click()
        },
        adminDetailModalOpen: function(key) {
            this.adminDetail = restructurized(this.tableData[key])
            this.$refs.adminDetailModal.$refs.modalOpenButton.click()
        },
        updateStatus: function(key) {
            this.showLoader()

            // set status
            let app = this
            let data = app.tableData[key]
            let targetStatus = (data.statusDefault === 'Aktif') ? 'Nonaktif' : 'Aktif'
            
            // create form
            let form = new FormData()
            form.append('id', data.id)
            form.append('status', targetStatus)

            // save data
            axios.post(panelUrl('administrator/update-status'), form)  
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        if (data.id === app.admin.id) {
                            app.loggingOut()
                        } else {
                            app.$refs.adminTable.draw()
                        }
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        },
        delete: function(key) {
            this.showLoader()

            // set status
            let app = this
            let data = app.tableData[key]

            // create form
            let form = new FormData()
            form.append('id', data.id)

            // send
            axios.post(panelUrl('administrator/delete'), form)  
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        if (data.id === app.admin.id) {
                            app.loggingOut()
                        } else {
                            app.$refs.adminTable.draw()
                        }
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        }
    },
    mounted: function() {
        let app = this

        // get roles
        axios.get(panelUrl('administrator/get-role'))
            .then(function(res) {
                res = res.data
                if (res.status !== 'success') {
                    Swal.fire('Whoopss!!', res.message, 'warning').then(function() {
                        window.location.reload()
                    })
                } else {
                    app.roles = res.data
                }
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            })
        
        // update data
        app.$refs.adminTableSection.addEventListener('click', (event) => {
            event.preventDefault()
            if (event.target.closest('.edit-button')) {
                app.adminUpdateModalOpen(event.target.getAttribute('data-key'))
            }
        })

        // see detail
        app.$refs.adminTableSection.addEventListener('click', (event) => {
            event.preventDefault()
            if (event.target.closest('.detail-button')) {
                app.adminDetailModalOpen(event.target.getAttribute('data-key'))
            }
        })

        // change status
        app.$refs.adminTableSection.addEventListener('click', (event) => {
            event.preventDefault()
            if (event.target.closest('.status-button')) {
                app.updateStatus(event.target.getAttribute('data-key'))
            }
        })

        // delete
        app.$refs.adminTableSection.addEventListener('click', (event) => {
            event.preventDefault()
            if (event.target.closest('.delete-button')) {
                app.delete(event.target.getAttribute('data-key'))
            }
        })
    }
}

</script>

<style lang="scss">

#admin-table {
    min-width: 900px;

    .col-no, .col-action {
        width: 50px;
    }
    .col-secondary {
        width: 130px;
    }
    .col-email-status, .col-primary {
        width: 200px;
    }
}

</style>