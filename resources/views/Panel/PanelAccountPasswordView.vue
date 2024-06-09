<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>

        <!-- CHANGE PASSWORD FORM -->
        <section class="panel-box bg-white mb-4">
            <header class="panel-box-header">
                Ubah Password
            </header>
            <form v-on:submit.prevent="submitForm" class="p-3">
                <div class="mb-3 position-relative">
                    <label for="oldPassword" class="form-label fw-bold">Password Lama</label>
                    <input v-model="data.oldPassword" v-bind:type="(show.oldPassword) ? 'text' : 'password'" name="old_password" id="oldPassword" class="form-control" placeholder="*************" required>
                    <button v-on:click="show.oldPassword = !show.oldPassword" type="button" class="btn btn-link show-password-toggle" title="Klik untuk melihat/menutup password">
                        <font-awesome v-if="!show.oldPassword" icon="fas fa-eye"></font-awesome>
                        <font-awesome v-if="show.oldPassword" icon="fas fa-eye-slash"></font-awesome>
                    </button>
                </div>
                <div class="mb-3 position-relative">
                    <label for="newPassword" class="form-label fw-bold">Password Baru</label>
                    <input v-model="data.newPassword" v-bind:type="(show.newPassword) ? 'text' : 'password'" name="new_password" id="newPassword" class="form-control" autocomplete="new-password" placeholder="*************" required>
                    <button v-on:click="show.newPassword = !show.newPassword" type="button" class="btn btn-link show-password-toggle" title="Klik untuk melihat/menutup password">
                        <font-awesome v-if="!show.newPassword" icon="fas fa-eye"></font-awesome>
                        <font-awesome v-if="show.newPassword" icon="fas fa-eye-slash"></font-awesome>
                    </button>
                </div>
                <div class="mb-4 position-relative">
                    <label for="confPassword" class="form-label fw-bold">Konfirmasi Password Baru</label>
                    <input v-model="data.confPassword" v-bind:type="(show.confPassword) ? 'text' : 'password'" name="conf_password" id="confPassword" class="form-control" autocomplete="new-password" placeholder="*************" required>
                    <button v-on:click="show.confPassword = !show.confPassword" type="button" class="btn btn-link show-password-toggle" title="Klik untuk melihat/menutup password">
                        <font-awesome v-if="!show.confPassword" icon="fas fa-eye"></font-awesome>
                        <font-awesome v-if="show.confPassword" icon="fas fa-eye-slash"></font-awesome>
                    </button>
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
import Swal from 'sweetalert2'
import axios from 'axios'

export default {
    name: 'panel-account-password-view',
    inject: ['showLoader', 'hideLoader'],
    components: {

    },
    data: function() {
        return {
            show: {
                oldPassword: false,
                newPassword: false,
                confPassword: false
            },
            data: {
                oldPassword: '',
                newPassword: '',
                confPassword: ''
            }
        }
    },
    methods: {
        submitForm: function(e) {
            let app = this
            let form = e.target

            // check data
            if (app.data.newPassword !== app.data.confPassword) {
                return Swal.fire('Perubahan Dibatalkan', 'Form Password Baru dan form Konfirmasi Password Baru tidak sesuai', 'error')
            }

            app.showLoader()

            // send
            axios.post(panelUrl('account/change-password'), new FormData(form))
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        Swal.fire('Perubahan Berhasil Disimpan', res.message, 'success')
                            .then(function() {
                                app.showLoader()
                                app.$router.push({ name: 'panel.profile' })
                            })
                    }
                })
                .catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        }
    },
    mounted: function() {
        this.$nextTick(function() {
            this.$emit('loaded')
        })
    }
}

</script>

<style lang="scss">

.show-password-toggle {
    position: absolute;
    right: 0;
    top: 2em;
}

</style>