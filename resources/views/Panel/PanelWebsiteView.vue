<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>

        <!-- INFORMASI FORM -->
        <section class="panel-box bg-white mb-4">
            <header class="panel-box-header">
                Informasi
            </header>
            <form v-on:submit.prevent="submitMainForm" id="websiteMainForm" class="p-3">
                <div class="mb-3">
                    <label for="formName" class="form-label fw-bold">Nama Website</label>
                    <input v-model="mainForm.name" name="name" id="formName" type="text" class="form-control" autocomplete="off" required>
                </div>
                <div class="mb-3">
                    <label for="formTagline" class="form-label fw-bold">Slogan/Tagline</label>
                    <input v-model="mainForm.tagline" name="tagline" id="formTagline" type="text" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="formAppVersion" class="form-label fw-bold">Versi Aplikasi</label>
                    <input v-model="mainForm.app_version" name="app_version" id="formAppVersion" type="text" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label for="formDescription" class="form-label fw-bold">Deskripsi</label>
                    <textarea v-model="mainForm.description" name="description" id="formDescription" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary d-flex align-items-center">
                    <font-awesome icon="fa-solid fa-floppy-disk" class="me-2"></font-awesome>
                    Simpan Informasi
                </button>
            </form>
        </section>

        <div class="row row-cols-1 row-cols-sm-2">

            <!-- LOGO FORM -->
            <div class="col">
                <section class="panel-box bg-white mb-4 w-100">
                    <header class="panel-box-header">
                        Logo
                    </header>
                    <form v-on:submit.prevent="submitLogoForm" id="websiteMainForm" class="p-3">
                        <img v-bind:src="logoUrl" v-bind:alt="mainForm.name" class="d-block mb-4">
                        <div class="mb-4">
                            <label for="logoInput" class="form-label fw-bold">Unggah Logo Baru</label>
                            <input name="logo" class="form-control" type="file" id="logoInput" accept="image/png" required>
                        </div>
                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                            <font-awesome icon="fa-solid fa-floppy-disk" class="me-2"></font-awesome>
                            Simpan Logo
                        </button>
                    </form>
                </section>
            </div>

            <!-- ICON FORM -->
            <div class="col">
                <section class="panel-box bg-white mb-4 w-100">
                    <header class="panel-box-header">
                        Icon
                    </header>
                    <form v-on:submit.prevent="submitIconForm" id="websiteMainForm" class="p-3">
                        <img v-bind:src="iconUrl" v-bind:alt="mainForm.name" class="d-block mb-4">
                        <div class="mb-4">
                            <label for="iconInput" class="form-label fw-bold">Unggah Icon Baru</label>
                            <input name="icon" class="form-control" type="file" id="iconInput" accept="image/png" required>
                        </div>
                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                            <font-awesome icon="fa-solid fa-floppy-disk" class="me-2"></font-awesome>
                            Simpan Icon
                        </button>
                    </form>
                </section>
            </div>

        </div>

    </main>
</template>

<script>

import axios from 'axios'
import swal from 'sweetalert'
import { panelUrl, checkAxiosError, imageUrl } from '@/libraries/Function'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faFloppyDisk } from '@fortawesome/free-solid-svg-icons'

library.add(faFloppyDisk)

export default {
    name: 'panel-website-view',
    inject: ['showLoader', 'hideLoader', 'config'],
    data: function() {
        return {
            name: 'Website',
            mainForm: {
                name: '',
                tagline: '',
                description: '',
                app_version: '',
            }
        }
    },
    computed: {
        logoUrl: function() {
            return imageUrl(`logo/${this.config.logo}`, 180)
        },
        iconUrl: function() {
            return imageUrl(`icon/${this.config.icon}`, 64)
        }
    },
    methods: {
        submitMainForm: function(event) {
            let app = this
            app.showLoader()
            axios.post(panelUrl('website/main-form-update'), new FormData(event.target))  
                .then(function(res) {
                    res = res.data
                    if (res.status !== 'success') {
                        app.hideLoader()
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
                        window.location.reload()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        },
        submitLogoForm: function(event) {
            let app = this
            app.showLoader()
            axios.post(panelUrl('website/logo-update'), new FormData(event.target))  
                .then(function(res) {
                    res = res.data
                    if (res.status !== 'success') {
                        app.hideLoader()
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
                        window.location.reload()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        },
        submitIconForm: function(event) {
            let app = this
            app.showLoader()
            axios.post(panelUrl('website/icon-update'), new FormData(event.target))  
                .then(function(res) {
                    res = res.data
                    if (res.status !== 'success') {
                        app.hideLoader()
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
                        window.location.reload()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        }
    },
    mounted: function() {

        let app = this

        // get website data
        axios.get(panelUrl('website/data'))
            .then(function(res) {
                res = res.data
                app.mainForm = res.data
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            }).finally(function() {
                app.$emit('loaded')
            })
    }
}

</script>

<style lang="scss"></style>