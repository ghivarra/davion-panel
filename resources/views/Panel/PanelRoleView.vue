<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>

        <section id="create-table" class="mb-4">
            <router-link v-bind:to="{ name: 'panel.role.create' }" class="btn btn-primary">
                <font-awesome icon="fas fa-plus" class="me-2"></font-awesome>
                Tambah Role
            </router-link>
        </section>
        
        <!-- TABLE -->
        <section ref="roleTableSection">
            <vue-table id="role-table" ref="roleTable" v-bind:defaultLength="25" v-bind:lengthOptions="[10,25,50]"
                v-bind:url="table.url" v-bind:order="table.order" v-bind:columns="table.columns"
                v-bind:processData="processData" v-on:afterCreate="$emit('loaded')">

                <!-- SLOT HEADER -->
                <template v-slot:header>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>
                            <input v-model="table.columns[2].query" id="tableSearchInput1" type="text" class="form-control">
                        </th>
                        <th>
                            <select v-model="table.columns[3].query" id="tableSearchInput2" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option value="1">Ya</option>
                                <option value="0">Bukan</option>
                            </select>
                        </th>
                        <th>
                            <select v-model="table.columns[4].query" id="tableSearchInput3" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </th>
                    </tr>
                </template>

                <!-- SLOT ROW -->
                <template v-slot:row="{ rowData, columnData, key }">
                    <td v-on:class="columnData[0].class">{{ rowData.no }}</td>
                    <td v-on:class="columnData[1].class">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle table-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-list me-1"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button v-on:click.prevent="$router.push({ path: `${editPage}/${rowData.id}` })" class="edit-button dropdown-item" type="button" title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square me-1 text-primary"></i>
                                        Edit
                                    </button>
                                </li>
                                <li>
                                    <button v-on:click.prevent="updateStatusRow(key)" class="status-button dropdown-item" type="button" title="${btnText} Data">
                                        <i v-bind:class="{ 'text-success': (rowData.status === 'Nonaktif'), 'text-warning': (rowData.status === 'Aktif') }" class="fa-solid fa-sliders me-1"></i>
                                        {{ (rowData.status === 'Aktif') ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </li>
                                <li>
                                    <button v-on:click.prevent="deleteRow(key)" class="delete-button dropdown-item" type="button" title="Hapus Data">
                                        <i class="fa-solid fa-trash-can me-1 text-danger"></i>
                                        Hapus
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td v-on:class="columnData[2].class">{{ rowData.name }}</td>
                    <td v-on:class="columnData[3].class">
                        <span v-bind:class="{ 'bg-success': (rowData.is_superadmin), 'bg-warning': (!rowData.is_superadmin) }" class="text-white py-2 px-3 rounded-pill fw-bold">
                            {{ (rowData.is_superadmin) ? 'Ya' : 'Bukan' }}
                        </span>
                    </td>
                    <td v-on:class="columnData[4].class">
                        <span v-bind:class="{ 'bg-success': (rowData.status === 'Aktif'), 'bg-warning': (rowData.status === 'Nonaktif') }" class="text-white py-2 px-3 rounded-pill fw-bold">
                            {{ rowData.status }}
                        </span>
                    </td>
                </template>
            </vue-table>
        </section>
        <!-- TABLE -->

    </main>
</template>

<script>

import { nextTick } from 'vue'
import { panelUrl, checkAxiosError, restructurized } from '@/libraries/Function'
import VueTable from '@/libraries/Ghivarra/VueTable/VueTable.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const env = import.meta.env

export default {
    name: 'panel-role-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        'vue-table': VueTable
    },
    data: function() {
        return {
            table: {
                url: panelUrl('role/datatable'),
                order: {
                    column: 'name',
                    dir: 'asc'
                },
                columns: [
                    { query: '', text: 'No.', key: 'no', sortable: false, searchable: false, class: ['col-no'] },
                    { query: '', text: '', key: 'action', sortable: false, searchable: false, class: ['col-action'] },
                    { query: '', text: 'Nama', key: 'name', class: ['col-primary'] },
                    { query: '', text: 'Superadmin', key: 'is_superadmin', class: ['col-secondary'] },
                    { query: '', text: 'Status', key: 'status', class: ['col-secondary'] },
                ]
            },
            tableData: [],
            editPage: `/${env.VITE_PANEL_PAGE}/role/edit`,
        }
    },
    methods: {
        processData: function(data) {
            this.tableData = (data.row.length < 1) ? [] : [... data.row]
            return data
        },
        updateStatusRow: function(key) {
            this.showLoader()

            // set status
            let app = this
            let data = restructurized(app.tableData[key])
            let targetStatus = (data.status === 'Aktif') ? 'Nonaktif' : 'Aktif'
            
            // create form
            let form = new FormData()
            form.append('id', data.id)
            form.append('status', targetStatus)

            // save data
            axios.post(panelUrl('role/update-status'), form)  
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        app.$refs.roleTable.draw()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        },
        deleteRow: function(key) {
            this.showLoader()

            // set status
            let app = this
            let data = restructurized(app.tableData[key])

            // create form
            let form = new FormData()
            form.append('id', data.id)

            // send
            axios.post(panelUrl('role/delete'), form)  
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        app.$refs.roleTable.draw()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        }
    },
    mounted: function() {
        nextTick(() => {
            this.$emit('loaded')
        })
    }
}

</script>

<style lang="scss">

#role-table {
    min-width: 700px;

    .col-no, .col-action {
        width: 50px;
    }
    .col-secondary {
        width: 150px;
    }
}

</style>