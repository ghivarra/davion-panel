<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>

        <!-- MODULE CREATE FORM MODAL -->
        <module-create-modal v-bind:module-groups="groupList" v-bind:update-table="updateTable" v-bind:get-module-group="getModuleGroup"></module-create-modal>

        <!-- MODULE UPDATE FORM MODAL -->
        <module-update-modal ref="moduleUpdateModal" v-bind:module-groups="groupList" v-bind:update-table="updateTable" v-bind:get-module-group="getModuleGroup" v-bind:module-update-data="updateData"></module-update-modal>

        <section ref="moduleTableSection">
            <vue-table id="module-table" ref="moduleTable" v-bind:defaultLength="25" v-bind:lengthOptions="[10,25,50]"
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
                            <input v-model="table.columns[3].query" id="tableSearchInput2" type="text" class="form-control">
                        </th>
                        <th>
                            <input v-model="table.columns[4].query" id="tableSearchInput3" type="text" class="form-control">
                        </th>
                        <th>
                            <select v-model="table.columns[5].query" id="tableSearchInput4" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </th>
                    </tr>
                </template>

                <!-- SLOT ROW -->
                <template v-slot:row="{ rowData, columnData, key }">
                    <td v-bind:class="columnData[0].class">{{ rowData.no }}</td>
                    <td v-bind:class="columnData[1].class">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle table-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-list me-1"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button v-on:click.prevent="editData(key)" class="edit-button dropdown-item" type="button" title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square me-1 text-primary"></i>
                                        Edit
                                    </button>
                                </li>
                                <li>
                                    <button v-on:click.prevent="updateStatusRow(key)" v-bind:title="(rowData.status === 'Aktif') ? 'Nonaktifkan Data' : 'Aktifkan Data'" class="status-button dropdown-item" type="button">
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
                    <td v-bind:class="columnData[2].class">{{ rowData.group }}</td>
                    <td v-bind:class="columnData[3].class">{{ rowData.name }}</td>
                    <td v-bind:class="columnData[4].class">{{ rowData.alias }}</td>
                    <td v-bind:class="columnData[5].class">
                        <span v-bind:class="{ 'bg-success': (rowData.status === 'Aktif'), 'bg-warning': (rowData.status === 'Nonaktif') }" class="text-white py-2 px-3 rounded-pill fw-bold">{{ rowData.status }}</span>
                    </td>
                </template>

            </vue-table>
        </section>
    </main>
</template>

<script>

import { panelUrl, checkAxiosError, restructurized } from '@/libraries/Function'
import VueTable from '@/libraries/Ghivarra/VueTable/VueTable.vue'
import ModuleCreateModal from '../Modal/ModuleCreateModal.vue'
import ModuleUpdateModal from '../Modal/ModuleUpdateModal.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'panel-module-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        'vue-table': VueTable,
        'module-create-modal': ModuleCreateModal,
        'module-update-modal': ModuleUpdateModal
    },
    data: function() {
        return {
            name: 'Module',
            table: {
                url: panelUrl('module/datatable'),
                order: {
                    column: 'group',
                    dir: 'asc'
                },
                columns: [
                    { query: '', text: 'No.', key: 'no', sortable: false, searchable: false, class: ['col-no'] },
                    { query: '', text: '', key: 'action', sortable: false, searchable: false, class: ['col-action'] },
                    { query: '', text: 'Grup', key: 'group', class: ['col-secondary'] },
                    { query: '', text: 'Nama', key: 'name', class: ['col-primary'] },
                    { query: '', text: 'Alias', key: 'alias', class: ['col-secondary'] },
                    { query: '', text: 'Status', key: 'status', class: ['col-secondary'] },
                ]
            },
            groupList: [],
            tableData: [],
            updateData: {
                id: '',
                group: '',
                name: '',
                alias: '',
                status: 'Aktif'
            }
        }
    },
    methods: {
        getModuleGroup: function() {
            let app = this

            // get module group list
            axios.get(panelUrl('module/group-list'))
                .then(function(res) {
                    app.groupList = res.data.data
                }).catch(function(res) {
                    checkAxiosError(res.request.status)
                })
        },
        updateTable: function() {
            this.$refs.moduleTable.draw()
        },
        processData: function(data) {
            this.tableData = (data.row.length < 1) ? [] : [... data.row]
            return data
        },
        editData: function(key) {
            let item = restructurized(this.tableData[key])

            this.updateData.id = parseInt(item.id)
            this.updateData.group = item.group
            this.updateData.alias = item.alias
            this.updateData.name = item.name
            this.updateData.status = item.status
            this.$refs.moduleUpdateModal.$refs.modalOpenButton.click()
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
            axios.post(panelUrl('module/update-status'), form)  
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        app.$refs.moduleTable.draw()
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
            axios.post(panelUrl('module/delete'), form)  
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        app.$refs.moduleTable.draw()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        }
    },
    created: function() {
        this.getModuleGroup()
    },
    mounted: function() {
        let app = this

        app.$nextTick(() => {
            app.$emit('loaded')
        })
    }
}

</script>

<style lang="scss">
#module-table {
    min-width: 800px;
    width: 100%;
    .col-no, .col-action {
        width: 50px;
    }
    .col-secondary {
        width: 160px;
    }
}
</style>