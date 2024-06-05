<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>

        <section id="create-table" class="mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFormModal">
                <font-awesome icon="fas fa-plus" class="me-2"></font-awesome>
                Tambah Modul
            </button>
            <div class="modal fade" id="createFormModal" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="createFormModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <form v-on:submit.prevent="create" method="POST" class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h1 class="modal-title fs-5" id="createFormModalLabel">Tambah Modul</h1>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="moduleGroup" class="form-label fw-bold">
                                    Grup
                                    <span class="text-danger" title="Wajib Diisi">*</span>
                                </label>
                                <input list="groupList" type="text" class="form-control" id="moduleGroup" name="group" maxlength="200" required>
                                <datalist id="groupList">
                                    <option v-for="(item, n) in groupList" v-bind:key="n" v-bind:value="item">
                                        {{ item }}
                                    </option>
                                </datalist>
                            </div>
                            <div class="mb-3">
                                <label for="moduleAlias" class="form-label fw-bold">
                                    Alias
                                    <span class="text-danger" title="Wajib Diisi">*</span>
                                </label>
                                <input type="text" class="form-control" id="moduleAlias" name="alias" maxlength="100" required>
                            </div>
                            <div class="mb-3">
                                <label for="moduleName" class="form-label fw-bold">
                                    Nama
                                    <span class="text-danger" title="Wajib Diisi">*</span>
                                </label>
                                <input type="text" class="form-control" id="moduleName" name="name" maxlength="200" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label for="moduleStatus" class="form-label fw-bold">
                                    Status
                                    <span class="text-danger" title="Wajib Diisi">*</span>
                                </label>
                                <select name="status" id="moduleStatus" class="form-select" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button ref="createModalCloseBtn" type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section id="edit-table">
            <button ref="editFormButton" class="d-none" data-bs-toggle="modal" data-bs-target="#editFormModal"></button>
            <div class="modal fade" id="editFormModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="editFormModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <form v-on:submit.prevent="update" method="POST" class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h1 class="modal-title fs-5" id="editFormModalLabel">Update Modul</h1>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="moduleGroup" class="form-label fw-bold">
                                    Grup
                                    <span class="text-danger" title="Wajib Diisi">*</span>
                                </label>
                                <input v-model="updateData.group" list="groupList" type="text" class="form-control"
                                    id="moduleGroup" name="group" maxlength="200" required>
                                <datalist id="groupList">
                                    <option v-for="(item, n) in groupList" v-bind:key="n" v-bind:value="item">
                                        {{ item }}
                                    </option>
                                </datalist>
                            </div>
                            <div class="mb-3">
                                <label for="moduleAlias" class="form-label fw-bold">
                                    Alias
                                    <span class="text-danger" title="Wajib Diisi">*</span>
                                </label>
                                <input v-model="updateData.alias" type="text" class="form-control" id="moduleAlias" name="alias" maxlength="100" required>
                            </div>
                            <div class="mb-3">
                                <label for="moduleName" class="form-label fw-bold">
                                    Nama
                                    <span class="text-danger" title="Wajib Diisi">*</span>
                                </label>
                                <input v-model="updateData.name" type="text" class="form-control" id="moduleName" name="name" maxlength="200" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label for="moduleStatus" class="form-label fw-bold">
                                    Status
                                    <span class="text-danger" title="Wajib Diisi">*</span>
                                </label>
                                <select v-model="updateData.status" name="status" id="moduleStatus" class="form-select"
                                    required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button ref="updateModalCloseButton" type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section ref="moduleTableSection">
            <vue-table id="module-table" ref="moduleTable" v-bind:defaultLength="25" v-bind:lengthOptions="[10,25,50]"
                v-bind:url="table.url" v-bind:order="table.order" v-bind:columns="table.columns"
                v-bind:processData="processData" v-on:afterCreate="$emit('loaded')">
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
            </vue-table>
        </section>
    </main>
</template>

<script>

import { panelUrl, checkAxiosError } from '@/libraries/Function'
import VueTable from '@/libraries/Ghivarra/VueTable/VueTable.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'panel-module-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        'vue-table': VueTable
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
                data.row[i].groupDefault = item.group
                data.row[i].statusDefault = item.status
                data.row[i].group = `<p class="m-0 fw-bold">${item.group}</p>`
                data.row[i].status = (item.status === 'Aktif') ? `<span class="bg-success text-white py-2 px-3 rounded-pill fw-bold">${item.status}</span>` : `<span class="bg-warning py-2 px-3 text-white rounded-pill fw-bold">${item.status}</span>`
            })

            return data
        },
        editData: function(key) {
            let app = this
            let item = app.tableData[key]

            // show loader
            app.showLoader()

            // get single data
            axios.get(panelUrl(`module/get?alias=${item.alias}`))
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        app.updateData.id = parseInt(res.data.id)
                        app.updateData.group = res.data.group
                        app.updateData.alias = res.data.alias
                        app.updateData.name = res.data.name
                        app.updateData.status = res.data.status
                        app.$refs.editFormButton.click()
                    }
                }).catch(function(res) {
                    checkAxiosError(res.request.status)
                })
        },
        create: function(e) {
            let form = e.target
            let app = this

            // show loader
            app.showLoader()

            // save data
            axios.post(panelUrl('module/create'), new FormData(form))  
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        form.reset()
                        app.$refs.createModalCloseBtn.click()
                        app.getModuleGroup()
                        app.$refs.moduleTable.draw()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        },
        update: function(e) {
            let form = new FormData(e.target)
            let app = this

            form.append('id', app.updateData.id)

            // show loader
            app.showLoader()

            // save data
            axios.post(panelUrl('module/update'), form)  
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        e.target.reset()
                        app.$refs.updateModalCloseButton.click()
                        app.getModuleGroup()
                        app.$refs.moduleTable.draw()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
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
        delete: function(key) {
            this.showLoader()

            // set status
            let app = this
            let data = app.tableData[key]

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

        app.$refs.moduleTableSection.addEventListener('click', (event) => {
            event.preventDefault()
            if (event.target.closest('.edit-button')) {
                app.editData(event.target.getAttribute('data-key'))
            }
        })

        app.$refs.moduleTableSection.addEventListener('click', (event) => {
            event.preventDefault()
            if (event.target.closest('.status-button')) {
                app.updateStatus(event.target.getAttribute('data-key'))
            }
        })

        app.$refs.moduleTableSection.addEventListener('click', (event) => {
            event.preventDefault()
            if (event.target.closest('.delete-button')) {
                app.delete(event.target.getAttribute('data-key'))
            }
        })
    }
}

</script>

<style lang="scss">
#module-table {
    min-width: 800px;
    width: 100%;
    .col-no {
        width: 90px;
    }
    .col-action {
        width: 110px;
    }
    .col-secondary {
        width: 160px;
    }
}
</style>