<template>
    <section id="edit-menu-group">
        <button ref="modalOpenButton" v-on:click="setFormData" class="d-none" data-bs-toggle="modal"
            data-bs-target="#groupEditFormModal"></button>
        <div class="modal fade" id="groupEditFormModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="groupEditFormModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form v-on:submit.prevent="submitForm" method="POST" class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h1 class="modal-title fs-5" id="groupEditFormModalLabel">Update Grup Menu</h1>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="groupUpdateName" class="form-label fw-bold">
                                Nama
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.name" type="text" class="form-control" id="groupUpdateName" name="name"
                                maxlength="200" autocomplete="off" required>
                        </div>
                        <div class="mb-3">
                            <label for="groupUpdateStatus" class="form-label fw-bold">
                                Status
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <select v-model="data.status" name="status" id="groupUpdateStatus" class="form-select"
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

import { panelUrl, checkAxiosError } from '@/libraries/Function'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'menu-group-update-modal',
    inject: ['showLoader', 'hideLoader'],
    props: ['updateData'],
    data: function() {
        return {
            data: {
                id: 0,
                name: '',
                status: 'Aktif',
            }
        }
    },
    methods: {
        setFormData: function() {
            this.data.id = this.updateData.id
            this.data.name = this.updateData.name
            this.data.status = this.updateData.status
        },
        submitForm: function() {
            let app = this

            app.$refs.modalCloseButton.click()
            app.showLoader()
            
            let form = new FormData()
            form.append('id', app.data.id)
            form.append('name', app.data.name)
            form.append('status', app.data.status)

            // save data
            axios.post(panelUrl('menu/group/update'), form)  
                .then(function(res) {
                    res = res.data
                    if (res.status !== 'success') {
                        app.hideLoader()
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        window.location.reload()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        }
    }
}

</script>