<template>

    <header class="d-flex align-items-stretch mb-3">

        <!-- SIDEBAR TOGGLE -->
        <button v-on:click="$emit('sidebarToggleClick')" type="button" class="btn panel-box bg-white sidebar-toggle-button me-3">
            <font-awesome icon="fa fa-bars" class="fs-4 text-primary d-flex align-items-center"></font-awesome>
        </button>

        <!-- NAVBAR -->
        <section class="px-2 bg-white panel-box overflow-visible d-flex justify-content-between align-items-stretch w-100 position-relative">
            
            <!-- Search Result -->
            <nav v-show="showSearchResult" class="page-form-result panel-box p-3">
                <button 
                    v-for="(result, n) in searchPageResult"
                    v-bind:key="n"
                    v-on:click="clickSearchResult(result)"
                    type="button"
                    class="btn btn-link w-100 page-form-result-link text-decoration-none text-start fw-bold"
                    title="Klik untuk membuka halaman">
                    <font-awesome v-bind:icon="result.icon" class="me-2"></font-awesome>
                    {{ result.title }}
                </button>
                <p 
                    v-if="searchPageResult.length < 1"
                    class="m-0 text-secondary">
                    Tidak ditemukan halaman dengan kata kunci "{{ searchPageValue }}"
                </p>
            </nav>

            <!-- Search Form -->
            <div v-show="pageSearch" class="page-form-wrapper w-100">
                <datalist id="panelPages"></datalist>
                <input 
                    v-on:input="attemptSearchPage"
                    v-model="searchPageValue"
                    autocomplete="off"
                    ref="searchform" 
                    name="searchpage" 
                    list="panelPages" 
                    type="text" 
                    class="border-0 py-0 page-form form-control w-100" 
                    placeholder="Ketik Halaman/Menu...">
                <button v-on:click="pageSearch = false; showSearchResult = false" ref="pageSearchCloseButton" type="button" class="btn btn-link d-flex align-items-center">
                    <font-awesome icon="fa fa-xmark" class="text-secondary fs-4"></font-awesome>
                </button>
            </div>

            <!-- Search Button Trigger -->
            <button v-show="!pageSearch" v-on:click="showPageSearch" type="button" class="btn btn-link text-decoration-none w-100 text-start text-secondary align-items-center page-form-button py-0">
                <font-awesome icon="fa fa-magnifying-glass" class="me-2"></font-awesome>
                Cari Halaman
            </button>
            
            <!-- Header Menu -->
            <div ref="headerMenu" class="header-menu dropdown">
                <button type="button" class="header-menu-button btn btn-link text-decoration-none dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="header-menu-image rounded-circle" v-bind:src="profilePicture" v-bind:alt="admin.name">
                </button>
                <ul class="header-menu-dropdown dropdown-menu">
                    <li class="px-2 py-0">
                        <router-link v-bind:to="{ name: 'panel.profile' }" class="d-flex dropdown-item rounded">
                            <img class="header-menu-dropdown-image rounded-circle" v-bind:src="profilePicture" v-bind:alt="admin.name">
                            <div class="ps-2 header-menu-text">
                                <p class="header-menu-name link-primary fw-bold mb-1">{{ admin.name }}</p>
                                <p class="header-menu-role m-0 link-secondary">{{ admin.admin_role_name }}</p>
                            </div>
                        </router-link>
                        </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="px-2 py-0">
                        <router-link v-bind:to="{ name: 'panel.account' }" class="dropdown-item rounded">
                            <font-awesome icon="fa-solid fa-gear" class="me-2 header-menu-icon text-primary"></font-awesome>
                            Pengaturan Akun
                        </router-link>
                    </li>
                    <li class="px-2 py-0">
                        <router-link v-bind:to="{ name: 'panel.account.password' }" class="dropdown-item rounded">
                            <font-awesome icon="fa-solid fa-key" class="me-2 header-menu-icon text-primary"></font-awesome>
                            Ubah Kata Sandi
                        </router-link>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="px-2 py-0">
                        <button v-on:click.prevent="logout" type="button" class="dropdown-item rounded">
                            <font-awesome icon="fa-solid fa-right-from-bracket" class="me-2 header-menu-icon text-danger"></font-awesome>
                            Keluar
                        </button>
                    </li>
                </ul>
            </div>

        </section>


    </header>

</template>

<script>

import { imageUrl, panelUrl, checkAxiosError } from '../libraries/Function';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faMagnifyingGlass, faXmark, faGear, faKey, faRightFromBracket, faBars, faTableCellsLarge, faUser, faUserTie, faTableColumns, faGlobe, faChevronRight } from '@fortawesome/free-solid-svg-icons'
import Swal from 'sweetalert2'
import axios from 'axios'

library.add(faMagnifyingGlass, faXmark, faGear, faKey, faRightFromBracket, faBars, faTableCellsLarge, faUser, faUserTie, faTableColumns, faGlobe, faChevronRight)

export default {
    name: 'panel-header-component',
    inject: ['admin', 'showLoader', 'hideLoader'],
    data: function() {
        return {
            pageSearch: false,
            searchPageValue: '',
            searchPageResult: [],
            showSearchResult: false,
            delayedSearch: null,
            headerDropdown: {},
        }
    },
    computed: {
        profilePicture: function() {
            return (typeof this.admin.photo === 'undefined' || this.admin.photo === null || this.admin.photo.length < 1) ? imageUrl('admin/default-user.png', 64) : imageUrl(`admin/${this.admin.photo}`, 64)
        }
    },
    methods: {
        showPageSearch: async function() {
            let app = this
            app.pageSearch = true
            await this.$nextTick()
            app.$refs.searchform.focus()
        },
        logout: function() {
            let app = this

            Swal.fire({
                title: 'Peringatan',
                text: 'Apakah anda yakin akan keluar?',
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                showConfirmButton: false,
                denyButtonText: 'Ya, Lanjut Keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isDenied) {
                    Swal.close()
                    app.showLoader()
                    axios.get(panelUrl('public/logout'))
                        .then(function(res) {
                            let data = res.data
                            if(typeof data.status === 'undefined' || data.status !== 'success') {
                                Swal.fire('Whoopss!!', 'Koneksi jaringan atau server sedang bermasalah, silahkan coba lagi', 'warning')
                            } else {
                                window.location.reload()
                            }
                        }).catch(function(res) {
                            checkAxiosError(res.request.status)
                        })
                } else {
                    Swal.close()
                }
            })
        },
        attemptSearchPage: function() {
            let app = this

            clearTimeout(app.delayedSearch)
            app.delayedSearch = setTimeout(() => {
                let data = new FormData()
                data.append('query', app.searchPageValue)

                if (app.searchPageValue.length > 1) {
                    axios.post(panelUrl('public/menu/search'), data)
                        .then(function(res) {
                            app.searchPageResult = res.data.data
                            app.$nextTick(() => {
                                app.showSearchResult = true
                            })                       
                        }).catch(function(res) {
                            checkAxiosError(res.request.status)
                        })
                } else {
                    app.showSearchResult = false
                }
            }, 300)
        },
        clickSearchResult: function(menu) {
            this.showSearchResult = false
            this.searchPageValue = ''
            this.$refs.pageSearchCloseButton.click()
            this.$router.push({
                name: menu.router_name
            })
        }
    }
}

</script>

<style lang="scss">

.page-form {
    outline: none!important;
    border: none!important;
    box-shadow: none !important;

    &-button {
        display: flex;
    }

    &-wrapper {
        display: flex;
        align-items: center;
    }

    &-result {
        width: 100%;
        position: absolute;
        background-color: #ffffff;
        z-index: 100;
        left: 0;
        top: calc(100% + .5rem);

        &-link {
            &:hover {
                background-color: darken(#ffffff, 3%)
            }
        }
    }
}

.header-menu {
    &-image {
        width: 33.75px;
        height: 33.75px;
        object-fit: cover;
        object-position: center;
    }

    &-text {
        max-width: 200px;
    }

    &-name, &-role {
        display: block;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    &-dropdown-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        object-position: center;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        padding: .5em 1em;

        &:active {
            .header-menu-icon, .header-menu-text p {
                color: #ffffff !important;
            }
        }
    }
}

.sidebar-toggle-button {
    height: 47px;
    width: 47px;
    display: none;

    @media (max-width: 991.98px) {
        display: block;
    }
}

</style>