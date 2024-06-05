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
                                <option value="1">Terverifikasi</option>
                                <option value="0">Belum Verifikasi</option>
                            </select>
                        </th>
                        <th>
                            <select v-model="table.columns[6].query" name="searchInput5" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option value="1">Ya</option>
                                <option value="0">Bukan</option>
                            </select>
                        </th>
                        <th>
                            <select v-model="table.columns[7].query" name="searchInput6" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                                <option value="Dibekukan">Dibekukan</option>
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

import { panelUrl, checkAxiosError } from '@/libraries/Function'
import VueTable from '@/libraries/Ghivarra/VueTable/VueTable.vue'
import AdminCreateModal from '@/views/Modal/AdminCreateModal.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'panel-admin-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        'vue-table': VueTable,
        'admin-create-modal': AdminCreateModal
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
                    { query: '', text: 'Status Email', key: 'email_verified_at', class: ['col-email-status'] },
                    { query: '', text: 'Superadmin', key: 'is_superadmin', class: ['col-secondary'] },
                    { query: '', text: 'Status Akun', key: 'status', class: ['col-secondary'] },
                ]
            },
            tableData: [],
            roles: []
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
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-bars me-1"></i>
                                            Aksi
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button data-key="${i}" class="check-button dropdown-item" type="button" title="Cek Sesi Login">
                                                    <i class="fa-solid fa-clock-rotate-left me-1 text-dark"></i>
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

                let statusColor;

                switch (item.status) {
                    case 'Nonaktif':
                        statusColor = 'warning'
                        break;

                    case 'Dibekukan':
                        statusColor = 'error'
                        break;
                
                    default:
                        statusColor = 'success'
                        break;
                }

                data.row[i].statusDefault = item.status
                data.row[i].status = `<span class="bg-${statusColor} text-white py-2 px-3 rounded-pill fw-bold">${item.status}</span>`

                data.row[i].superadminDefault = item.is_superadmin
                data.row[i].is_superadmin = (item.is_superadmin === '1') ? `<span class="bg-success text-white py-2 px-3 rounded-pill fw-bold">Ya</span>` : `<span class="bg-warning py-2 px-3 text-white rounded-pill fw-bold">Bukan</span>`

                data.row[i].emailStatusDefault = item.email_verified_at
                data.row[i].email_verified_at = (item.email_verified_at === 'Belum Verifikasi') ? `<span class="bg-warning text-white py-2 px-3 rounded-pill fw-bold">Belum Verifikasi</span>` : `<span class="bg-success py-2 px-3 text-white rounded-pill fw-bold">Terverifikasi</span>`
            })

            // return
            return data
        },
        refreshTable: function() {
            this.$refs.adminTable.draw()
        },
        adminCreateModalOpen: function() {
            this.$refs.adminCreateModal.$refs.modalOpenButton.click()
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
    }
}

</script>

<style lang="scss">

#admin-table {
    min-width: 1200px;

    .col-no {
        width: 90px;
    }
    .col-action {
        width: 110px;
    }
    .col-secondary {
        width: 130px;
    }
    .col-email-status, .col-primary {
        width: 200px;
    }
}

</style>