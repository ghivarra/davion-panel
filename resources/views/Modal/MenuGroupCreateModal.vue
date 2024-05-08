<template>
    <section id="create-menu-group">
        <button ref="modalOpenButton" v-on:click="clearForm" class="d-none" data-bs-toggle="modal"
            data-bs-target="#groupCreateFormModal"></button>
        <div class="modal fade" id="groupCreateFormModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="groupCreateFormModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form v-on:submit.prevent="submitForm" method="POST" class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h1 class="modal-title fs-5" id="groupCreateFormModalLabel">Buat Grup Menu</h1>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="groupCreateName" class="form-label fw-bold">Nama</label>
                            <input v-model="data.name" type="text" class="form-control" id="groupCreateName"
                                name="name" maxlength="200" required>
                        </div>
                        <div class="mb-3">
                            <label for="groupCreateStatus" class="form-label fw-bold">Status</label>
                            <select v-model="data.status" name="status" id="groupCreateStatus"
                                class="form-select" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button ref="modalCloseButton" type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
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
    name: 'menu-group-create-modal',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            data: {
                name: '',
                status: 'Aktif',
            }
        }
    },
    methods: {
        clearForm: function() {
            this.data.name = ''
            this.data.status = 'Aktif'
        },
        submitForm: function() {
            let app = this

            app.$refs.modalCloseButton.click()
            app.showLoader()
            
            let form = new FormData()
            form.append('name', app.data.name)
            form.append('status', app.data.status)

            // save data
            axios.post(panelUrl('menu/group/create'), form)  
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