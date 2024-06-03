<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        
        <!-- TABLE -->
        <section ref="roleTableSection">
            <vue-table id="role-table" ref="roleTable" v-bind:defaultLength="25" v-bind:lengthOptions="[10,25,50]"
                v-bind:url="table.url" v-bind:order="table.order" v-bind:columns="table.columns"
                v-bind:processData="processData" v-on:afterCreate="$emit('loaded')">
                <template v-slot:header>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>
                            <input v-model="table.columns[2].query" type="text" class="form-control">
                        </th>
                        <th>
                            <select v-model="table.columns[3].query" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option value="1">Ya</option>
                                <option value="0">Bukan</option>
                            </select>
                        </th>
                        <th>
                            <select v-model="table.columns[4].query" class="form-select">
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

import { panelUrl, checkAxiosError } from '@/libraries/Function'
import VueTable from '@/libraries/Ghivarra/VueTable/VueTable.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

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
                data.row[i].statusDefault = item.status
                data.row[i].status = (item.status === 'Aktif') ? `<span class="bg-success text-white py-2 px-3 rounded-pill fw-bold">${item.status}</span>` : `<span class="bg-warning py-2 px-3 text-white rounded-pill fw-bold">${item.status}</span>`
                data.row[i].superadminDefault = item.is_superadmin
                data.row[i].is_superadmin = (item.is_superadmin === '1') ? `<span class="bg-success text-white py-2 px-3 rounded-pill fw-bold">Ya</span>` : `<span class="bg-warning py-2 px-3 text-white rounded-pill fw-bold">Bukan</span>`
            })

            // return
            return data
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
        delete: function(key) {
            this.showLoader()

            // set status
            let app = this
            let data = app.tableData[key]

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
        let app = this

        // update status
        app.$refs.roleTableSection.addEventListener('click', function(event) {
            if (event.target.closest('.status-button')) {
                event.preventDefault()
                app.updateStatus(event.target.getAttribute('data-key'))
            }
        })

        // delete
        app.$refs.roleTableSection.addEventListener('click', function(event) {
            if (event.target.closest('.delete-button')) {
                event.preventDefault()
                app.delete(event.target.getAttribute('data-key'))
            }
        })
    }
}

</script>

<style lang="scss"></style>