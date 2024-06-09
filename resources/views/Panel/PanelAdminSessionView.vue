<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        
        <!-- PROFILE -->
        <section class="panel-profile w-100 overflow-hidden rounded-2 panel-box mb-4">
            <div class="panel-profile-background pb-2">
                <h2 class="h3 fw-bold text-white">{{ admin.name }}</h2>
            </div>
            <div class="panel-profile-box py-3">
                <img class="panel-profile-box-image rounded-circle" v-bind:src="profilePicture" v-bind:alt="admin.name">
                <div class="d-md-flex pe-3">
                    <p class="mb-1 text-secondary me-3">
                        <font-awesome icon="fas fa-user-tie" class="text-primary me-1"></font-awesome>
                        {{ admin.admin_role_name }}
                    </p>
                    <p class="m-0 text-secondary">
                        <font-awesome icon="fas fa-envelope" class="text-primary me-1"></font-awesome>
                        {{ admin.email }}
                    </p>
                </div>
            </div>
        </section>

        <!-- SESSIONS -->
        <section>
            <header class="panel-box-header rounded-2 panel-box mb-4">
                Sesi Login
            </header>
            <div v-if="sessions.length < 1" class="panel-box bg-white p-3">
                Akun ini tidak memiliki sesi login aktif
            </div>
            <div v-else class="row row-cols-1 row-cols-md-2 g-3">
                <div v-for="(session, n) in sessions" v-bind:key="n" class="col">
                    <div class="p-4 panel-box bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div>
                                <img v-if="session.useragent.mobile" v-bind:src="mobileIcon" alt="Mobile Icon">
                                <img v-else v-bind:src="nonMobileIcon" alt="Non-Mobile Icon">
                            </div>
                            <div class="ps-3">
                                <h5 class="fw-bold mb-1">
                                    {{ session.useragent.os }}
                                </h5>
                                <p class="mb-0">{{ session.useragent.platform }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="fw-bold mb-0">Browser:</p>
                            <p>{{ session.useragent.browser }}</p>
                            <p class="fw-bold mb-0">IP Address:</p>
                            <p>{{ session.ip_address }}</p>
                            <p class="fw-bold mb-0">Terakhir Aktif:</p>
                            <p>{{ session.last_update }}</p>
                            <button v-on:click.prevent="deleteSession(session)" type="button" class="btn btn-link link-danger text-decoration-none ps-0" title="Logout Sesi Ini">
                                <font-awesome icon="fas fa-trash-alt" class="me-1"></font-awesome>
                                Hapus Sesi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
</template>

<script>

import { panelUrl, checkAxiosError, imageUrl } from '@/libraries/Function'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'panel-admin-session-view',
    inject: ['showLoader', 'hideLoader', 'updateAdminData'],
    data: function() {
        return {
            admin: {},
            mobileIcon: imageUrl('tablet-smartphone.png', 60),
            nonMobileIcon: imageUrl('computer.png', 60),
            sessions: []
        }
    },
    computed: {
        profilePicture: function() {
            return (typeof this.admin.photo === 'undefined' || this.admin.photo === null || this.admin.photo.length < 1) ? imageUrl('admin/default-user.png', 120) : imageUrl(`admin/${this.admin.photo}`, 120)
        },
    },
    methods: {
        deleteSession: function(session) {
            let app = this
            Swal.fire({
                html: `Apakah anda yakin akan menghapus sesi login di <b>${session.useragent.platform}</b> dari akun <b>${app.admin.name}</b>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batalkan',
            }).then((result) => {
                if (result.isConfirmed) {
                    app.showLoader()

                    let form = new FormData()
                    form.append('session_id', session.id)
                    form.append('admin_id', app.admin.id)

                    // send
                    axios.post(panelUrl('administrator/delete-session'), form)
                        .then(function(res) {
                            res = res.data
                            if (res.status !== 'success') {
                                Swal.fire('Whoopss!!', res.message, 'warning')
                            } else {
                                app.updateAdminData()
                                window.location.reload()
                            }
                        })
                        .catch(function(res) {
                            app.hideLoader()
                            checkAxiosError(res.request.status)
                        })
                }
            });
        }
    },
    mounted: function() {
        let app = this

        // get admin data
        axios.get(panelUrl(`administrator/get?id=${app.$route.params.adminId}`))
            .then(function(res) {
                res = res.data
                app.admin = res.data
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            })

        // get session data
        axios.get(panelUrl(`administrator/get-account-session?id=${app.$route.params.adminId}`))
            .then(function(res) {
                res = res.data
                for (let i = 0; i < res.data.length; i++) {
                    const item = res.data[i]
                    const date = new Date(item.last_update)
                    const dateOptions = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        timeZone: 'Asia/Jakarta',
                        timeZoneName: 'short',
                        hour: '2-digit',
                        minute: '2-digit',
                        hourCycle: 'h24'
                    }

                    res.data[i].useragent = JSON.parse(item.useragent)
                    res.data[i].last_update = date.toLocaleString('id-ID', dateOptions)
                }
                app.sessions = res.data
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            }).finally(function() {
                app.$emit('loaded')
            })
    }
}

</script>

<style lang="scss">
.panel-profile {
    &-background {
        height: 80px;
        padding-left: 180px;
        display: flex;
        align-items: end;
        background-color: #13241B;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400' viewBox='0 0 800 800'%3E%3Cg fill='none' stroke='%231F3A2C' stroke-width='1'%3E%3Cpath d='M769 229L1037 260.9M927 880L731 737 520 660 309 538 40 599 295 764 126.5 879.5 40 599-197 493 102 382-31 229 126.5 79.5-69-63'/%3E%3Cpath d='M-31 229L237 261 390 382 603 493 308.5 537.5 101.5 381.5M370 905L295 764'/%3E%3Cpath d='M520 660L578 842 731 737 840 599 603 493 520 660 295 764 309 538 390 382 539 269 769 229 577.5 41.5 370 105 295 -36 126.5 79.5 237 261 102 382 40 599 -69 737 127 880'/%3E%3Cpath d='M520-140L578.5 42.5 731-63M603 493L539 269 237 261 370 105M902 382L539 269M390 382L102 382'/%3E%3Cpath d='M-222 42L126.5 79.5 370 105 539 269 577.5 41.5 927 80 769 229 902 382 603 493 731 737M295-36L577.5 41.5M578 842L295 764M40-201L127 80M102 382L-261 269'/%3E%3C/g%3E%3Cg fill='%23294D3A'%3E%3Ccircle cx='769' cy='229' r='5'/%3E%3Ccircle cx='539' cy='269' r='5'/%3E%3Ccircle cx='603' cy='493' r='5'/%3E%3Ccircle cx='731' cy='737' r='5'/%3E%3Ccircle cx='520' cy='660' r='5'/%3E%3Ccircle cx='309' cy='538' r='5'/%3E%3Ccircle cx='295' cy='764' r='5'/%3E%3Ccircle cx='40' cy='599' r='5'/%3E%3Ccircle cx='102' cy='382' r='5'/%3E%3Ccircle cx='127' cy='80' r='5'/%3E%3Ccircle cx='370' cy='105' r='5'/%3E%3Ccircle cx='578' cy='42' r='5'/%3E%3Ccircle cx='237' cy='261' r='5'/%3E%3Ccircle cx='390' cy='382' r='5'/%3E%3C/g%3E%3C/svg%3E");
    }

    &-box {
        position: relative;
        min-height: 80px;
        background-color: #ffffff;
        padding-left: 180px;

        &-image {
            position: absolute;
            z-index: 20;
            top: -50px;
            left: 35px;
            height: 120px;
            width: 120px;
            object-fit: cover;
            object-position: center;
        }
    }
}

@media screen and (max-width: 575.98px) {
    .panel-profile {
        &-background {
            padding-left: 140px;
        }
        &-box {
            padding-left: 140px;
            &-image {
                left: 20px;
                height: 100px;
                width: 100px;
            }
        }
    }
}

@media screen and (max-width: 475.98px) {
    .panel-profile {
        &-background {
            padding-left: 120px;
            height: 100px;
            align-items: center;
        }
        &-box {
            padding-left: 1.5rem;
            &-image {
                left: 20px;
                height: 80px;
                width: 80px;
                top: -90px;
            }
        }
    }
}
</style>