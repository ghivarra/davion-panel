<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>

        <!-- ACCOUNT FORM -->
        <section class="panel-box bg-white mb-4">
            <header class="panel-box-header">
                Data Akun
            </header>
            <form v-on:submit.prevent="submitForm" class="p-3" enctype="multipart/form-data">
                <div class="mb-3 position-relative">
                    <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                    <input v-model="data.name" autocomplete="off" name="name" id="name" type="text" class="form-control" required>
                </div>
                <div class="mb-3 position-relative">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input v-model="data.email" autocomplete="off" name="email" id="email" type="email" class="form-control" required>
                </div>
                <div class="position-relative">
                    <label for="photo" class="form-label fw-bold">Unggah Foto Baru</label>
                    <input v-on:change="showImage" name="photo" id="photo" type="file" class="form-control" accept="image/jpeg,image/png,image/gif">
                </div>
                <div class="my-4">
                    <img v-show="showImagePreview" ref="imagePreview" src="" class="image-preview" alt="image-preview">
                </div>
                <button type="submit" class="btn btn-primary">
                    <font-awesome icon="fas fa-save" class="me-1"></font-awesome>
                    Simpan Perubahan
                </button>
            </form>
        </section>
    </main>
</template>

<script>

import { panelUrl, checkAxiosError } from '@/libraries/Function'
import swal from 'sweetalert'
import axios from 'axios'

export default {
    name: 'panel-account-view',
    inject: ['showLoader', 'hideLoader', 'admin', 'updateAdminData'],
    data: function() {
        return {
            showImagePreview: false,
            data: {
                name: '',
                email: '',
            },
        }
    },
    watch: {
        admin: function(newData) {
            this.data.name = newData.name
            this.data.email = newData.email
        }
    },
    methods: {
        showImage: function(e) {
            let app = this
            const [file] = e.target.files
            if (file) {
                app.$refs.imagePreview.setAttribute('src', URL.createObjectURL(file))
                app.showImagePreview = true
            } else {
                app.showImagePreview = false
                app.$refs.imagePreview.setAttribute('src', '')
            }
        },
        submitForm: function(e) {
            let app = this
            let form = e.target

            // show loader
            app.showLoader()

            // send
            axios.post(panelUrl('account/update'), new FormData(form))
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
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
                        app.updateAdminData()
                        app.$router.push({ name: 'panel.profile' })
                    }
                })
                .catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        }
    },
    mounted: function() {
        this.data.name = this.admin.name
        this.data.email = this.admin.email
        this.$nextTick(function() {
            this.$emit('loaded')
        })
    }
}

</script>

<style lang="scss">
.image-preview {
    max-width: 300px;
    height: auto;
}
</style>