<template>
    <section id="edit-table">
        <button ref="modalOpenButton" v-on:click.prevent="clearForm" class="d-none" data-bs-toggle="modal" data-bs-target="#editFormModal"></button>
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
                                <option v-for="(item, n) in moduleGroups" v-bind:key="n" v-bind:value="item">
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
                        <button ref="modalCloseButton" type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>

<script>

import { restructurized, checkAxiosError, panelUrl } from '@/libraries/Function'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'module-update-modal',
    inject: ['showLoader', 'hideLoader'],
    props: ['moduleGroups', 'moduleUpdateData', 'getModuleGroup', 'updateTable'],
    data: function() {
        return {
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
        clearForm: function() {
            this.$nextTick(() => {
                const moduleData = restructurized(this.moduleUpdateData)
                
                this.updateData.id = moduleData.id
                this.updateData.group = moduleData.group
                this.updateData.alias = moduleData.alias
                this.updateData.name = moduleData.name
                this.updateData.status = moduleData.status
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
                        app.$refs.modalCloseButton.click()
                        app.getModuleGroup()
                        app.updateTable()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        }
    }
}

</script>