<template>
    <section id="create-admin-modal">
        <button ref="modalOpenButton" v-on:click="clearForm" class="d-none" data-bs-toggle="modal" data-bs-target="#adminCreateFormModal"></button>
        <div class="modal fade" id="adminCreateFormModal" tabindex="-1" aria-labelledby="adminCreateFormModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form v-on:submit.prevent="submitForm" method="POST" class="modal-content" enctype="multipart/form-data">
                    <div class="modal-header bg-primary text-white">
                        <h1 class="modal-title fs-5" id="adminCreateFormModalLabel">Tambah Admin</h1>
                    </div>
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label for="adminCreateUsername" class="form-label fw-bold">
                                Username
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.username" type="text" class="form-control" id="adminCreateUsername" name="username" autocomplete="off" maxlength="100" required>
                        </div>

                        <div class="mb-3">
                            <label for="adminCreateEmail" class="form-label fw-bold">
                                Email
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.email" type="email" class="form-control" id="adminCreateEmail" name="admin_email" autocomplete="off" maxlength="200" required>
                        </div>

                        <div class="mb-3">
                            <label for="adminCreateFullname" class="form-label fw-bold">
                                Nama Lengkap
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.name" type="text" class="form-control" id="adminCreateFullname" name="admin_fullname" autocomplete="off" maxlength="200" required>
                        </div>

                        <div class="mb-3">
                            <label for="adminCreateRoles" class="form-label fw-bold">
                                Role
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <select v-model="data.adminRoleId" name="admin_role_id" id="adminCreateRoles" class="form-select" required>
                                <option v-for="(role, n) in roles" v-bind:key="n" v-bind:value="role.id">{{ role.name }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="adminCreatePassword" class="form-label fw-bold">
                                Password
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <div class="position-relative">
                                <input v-model="data.password" v-bind:type="passwordInputType" class="form-control" id="adminCreatePassword" name="password" autocomplete="new-password" placeholder="*************" required>
                                <button v-on:click.prevent="passwordToggle('passwordInputType')" type="button" class="btn btn-link text-secondary password-toggle">
                                    <font-awesome v-if="passwordInputType === 'password'" icon="fas fa-eye"></font-awesome>
                                    <font-awesome v-if="passwordInputType === 'text'" icon="fas fa-eye-slash"></font-awesome>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adminCreatePasswordConfirmation" class="form-label fw-bold">
                                Konfirmasi Password
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <div class="position-relative">
                                <input v-model="data.passwordConfirmation" v-bind:type="passwordConfInputType" class="form-control" id="adminCreatePasswordConfirmation" name="confirmation_password" autocomplete="new-password" placeholder="*************" required>
                                <button v-on:click.prevent="passwordToggle('passwordConfInputType')" type="button" class="btn btn-link text-secondary password-toggle">
                                    <font-awesome v-show="passwordConfInputType === 'password'" icon="fas fa-eye"></font-awesome>
                                    <font-awesome v-show="passwordConfInputType === 'text'" icon="fas fa-eye-slash"></font-awesome>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adminCreatePhoto" class="form-label fw-bold">Foto</label>
                            <input v-on:change="showImage" type="file" class="form-control" id="adminCreatePhoto" name="photo" autocomplete="new-password" accept="image/png,image/jpeg,image/gif">
                        </div>

                        <div v-show="showImagePreview" class="mb-3 pt-3">
                            <img ref="imagePreview" src="" class="admin-image-preview" alt="Preview Gambar/Foto">
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
import swal from 'sweetalert'

export default {
    name: 'admin-create-modal',
    inject: ['showLoader', 'hideLoader'],
    props: ['roles'],
    data: function() {
        return {
            data: {
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
            this.data.username = ''
            this.data.password = ''
            this.data.passwordConfirmation = ''
            this.data.name = ''
            this.data.email = ''
            this.data.adminRoleId = '1'
            this.showImagePreview = false
            this.imagePreview = ''
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
            axios.post(panelUrl('administrator/create'), new FormData(event.target))  
                .then(function(res) {
                    res = res.data
                    if (res.status !== 'success') {
                        swal({
                            title: 'Whoopss!!',
                            icon: 'warning',
                            text: res.message,
                            buttons: {
                                confirm: {
                                    className: 'btn btn-primary',
                                    text: 'OK'
                                }
                            }
                        })
                    } else {
                        app.$emit('formSubmitted')
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