<template>
    <section id="update-admin-modal">
        <button ref="modalOpenButton" v-on:click="clearForm" class="d-none" data-bs-toggle="modal" data-bs-target="#adminUpdateFormModal"></button>
        <div class="modal fade" id="adminUpdateFormModal" tabindex="-1" aria-labelledby="adminUpdateFormModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form v-on:submit.prevent="submitForm" method="POST" class="modal-content" enctype="multipart/form-data">
                    <div class="modal-header bg-primary text-white">
                        <h1 class="modal-title fs-5" id="adminUpdateFormModalLabel">Tambah Admin</h1>
                    </div>
                    <div class="modal-body">

                        <input name="id" type="hidden" v-model="data.id">
                        
                        <div class="mb-3">
                            <label for="adminUpdateUsername" class="form-label fw-bold">
                                Username
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.username" type="text" class="form-control" id="adminUpdateUsername" name="username" autocomplete="off" maxlength="100" required>
                        </div>

                        <div class="mb-3">
                            <label for="adminUpdateEmail" class="form-label fw-bold">
                                Email
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.email" type="email" class="form-control" id="adminUpdateEmail" name="admin_email" autocomplete="off" maxlength="200" required>
                        </div>

                        <div class="mb-3">
                            <label for="adminUpdateFullname" class="form-label fw-bold">
                                Nama Lengkap
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.name" type="text" class="form-control" id="adminUpdateFullname" name="admin_fullname" autocomplete="off" maxlength="200" required>
                        </div>

                        <div class="mb-3">
                            <label for="adminUpdateRoles" class="form-label fw-bold">
                                Role
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <select v-model="data.adminRoleId" name="admin_role_id" id="adminUpdateRoles" class="form-select" required>
                                <option v-for="(role, n) in roles" v-bind:key="n" v-bind:value="role.id">{{ role.name }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="adminUpdatePassword" class="form-label fw-bold">
                                Password
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <div class="position-relative">
                                <input v-model="data.password" v-bind:type="passwordInputType" class="form-control" id="adminUpdatePassword" name="password" autocomplete="new-password" placeholder="*************">
                                <button v-on:click.prevent="passwordToggle('passwordInputType')" type="button" class="btn btn-link text-secondary password-toggle">
                                    <font-awesome v-if="passwordInputType === 'password'" icon="fas fa-eye"></font-awesome>
                                    <font-awesome v-if="passwordInputType === 'text'" icon="fas fa-eye-slash"></font-awesome>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adminUpdatePasswordConfirmation" class="form-label fw-bold">
                                Konfirmasi Password
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <div class="position-relative">
                                <input v-model="data.passwordConfirmation" v-bind:type="passwordConfInputType" class="form-control" id="adminUpdatePasswordConfirmation" name="confirmation_password" autocomplete="new-password" placeholder="*************">
                                <button v-on:click.prevent="passwordToggle('passwordConfInputType')" type="button" class="btn btn-link text-secondary password-toggle">
                                    <font-awesome v-show="passwordConfInputType === 'password'" icon="fas fa-eye"></font-awesome>
                                    <font-awesome v-show="passwordConfInputType === 'text'" icon="fas fa-eye-slash"></font-awesome>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adminUpdatePhoto" class="form-label fw-bold">Foto</label>
                            <div v-show="showImagePreview" class="mb-3 pt-1">
                                <img ref="imagePreview" src="" class="admin-image-preview" alt="Preview Gambar/Foto">
                            </div>
                            <input v-on:change="showImage" type="file" class="form-control" id="adminUpdatePhoto" name="photo" autocomplete="new-password" accept="image/png,image/jpeg,image/gif">
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

import { panelUrl, checkAxiosError, restructurized } from '@/libraries/Function'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'admin-update-modal',
    inject: ['showLoader', 'hideLoader'],
    props: ['roles', 'admin'],
    data: function() {
        return {
            data: {
                id: '',
                username: '',
                password: '',
                passwordConfirmation: '',
                name: '',
                email: '',
                adminRoleId: '1'
            },
            showImagePreview: false,
            passwordInputType: 'password',
            passwordConfInputType: 'password'
        }
    },
    methods: {
        clearForm: function() {
            let app = this
            this.$nextTick(function() {
                let admin = restructurized(app.admin)
                app.data.id = admin.id
                app.data.username = admin.username
                app.data.password = ''
                app.data.passwordConfirmation = ''
                app.data.name = admin.name
                app.data.email = admin.email
                app.data.adminRoleId = admin.admin_role_id
                app.showImagePreview = false
                app.imagePreview = ''
            })
        },
        showImage: function(event) {
            let app = this
            const [file] = event.target.files
            if (file) {
                app.$refs.imagePreview.setAttribute('src', URL.createObjectURL(file))
                app.showImagePreview = true
            } else {
                app.showImagePreview = false
                app.$refs.imagePreview.setAttribute('src', '')
            }
        },
        passwordToggle: function(key) {
            let type = this[key]
            this[key] = (type === 'text') ? 'password' : 'text'
        },
        submitForm: function(event) {
            let app = this
            
            // submit form
            app.$refs.modalCloseButton.click()
            app.showLoader()

            // save data
            axios.post(panelUrl('administrator/update'), new FormData(event.target))  
                .then(function(res) {
                    res = res.data
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        app.$emit('formSubmitted', app.data.id)
                    }
                }).catch(function(res) {
                    checkAxiosError(res.request.status)
                }).finally(function() {
                    app.hideLoader()
                })
        }
    }
}

</script>

<style lang="scss">

.admin-image-preview {
    max-width: 200px;
}

.password-toggle {
    position: absolute;
    top: 0;
    right: 0;
}

</style>