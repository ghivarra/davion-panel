<!-- TEMPLATES -->
<template>
    <main id="loginMain" class="position-relative">
        <section id="loginFormSection" class="login-form-section w-100 d-flex align-items-center">
            <form v-on:submit.prevent="login" id="loginForm" method="post" class="w-100">
                <input type="hidden" v-bind:name="token" v-bind:value="hash">
                <div class="h4 text-center fw-bold mb-4">Selamat Datang</div>
                <div class="d-flex justify-content-center mb-5">
                    <img v-bind:src="logo" v-bind:alt="config.name">
                </div>
                <div class="mb-4">
                    <label for="accountInput" class="form-label fw-bold">Username/Email</label>
                    <input v-model="formAccount" name="account" type="text"
                        class="w-100 d-block mb-3 rounded-0 login-form-input" id="accountInput" required>
                </div>
                <div class="mb-4 position-relative">
                    <label for="passwordInput" class="form-label fw-bold">Password</label>
                    <input v-model="formPassword" v-bind:type="passwordInputType" name="password"
                        class="w-100 d-block mb-3 rounded-0 login-form-input" id="passwordInput" required>
                    <button v-on:click="togglePasswordInput" type="button" class="btn btn-link toggle-password-btn">
                        <font-awesome v-if="passwordInputType === 'password'" icon="fa-solid fa-eye"
                            class="icon text-secondary"></font-awesome>
                        <font-awesome v-else icon="fa-solid fa-eye-slash" class="icon text-secondary"></font-awesome>
                    </button>
                </div>
                <div class="pt-3">
                    <button type="submit" class="btn d-block btn-primary w-100 rounded-pill mb-3">LOGIN</button>
                    <p class="text-secondary text-small text-center">{{ config.app_version }}</p>
                </div>
            </form>
        </section>
    </main>
</template>
<!-- END TEMPLATES -->

<!-- SCRIPTS -->
<script>

import { imageUrl, loginUrl, checkAxiosError } from "../libraries/Function"
import { library } from '@fortawesome/fontawesome-svg-core'
import { faEye, faEyeSlash } from '@fortawesome/free-solid-svg-icons'
import axios from 'axios'
import swal from "sweetalert"

// add icon to use
library.add(faEye, faEyeSlash)

export default {
    name: 'login-view',
    props: ['token', 'hash'],
    inject: ['config', 'showLoader', 'hideLoader'],
    data: function() {
        return {
            passwordInputType: 'password',
            formAccount: '',
            formPassword: '',
            background: 'url(' + imageUrl('login-background.jpg') + '&original=true)'
        }
    },
    computed: {
        logo: function() {
            return imageUrl(`logo/${this.config.logo}`, 220)
        }
    },
    methods: {
        togglePasswordInput: function() {
            if (this.passwordInputType === 'password') {
                this.passwordInputType = 'text'
            } else {
                this.passwordInputType = 'password'
            }
        },
        login: function(event) {
            this.showLoader()
            let app = this
            axios.post(loginUrl('authenticate'), new FormData(event.target))  
                .then(function(res) {
                    res = res.data
                    if (res.status !== 'success') {
                        app.hideLoader()
                        document.querySelector(`input[name=${res.data.csrfToken}]`).value = res.data.csrfHash
                        swal({
                            title: 'Otentikasi Gagal',
                            icon: 'warning',
                            text: res.message,
                            closeOnEsc: false,
                            closeOnClickOutside: false,
                            buttons: {
                                confirm: {
                                    className: 'btn btn-primary',
                                    text: 'OK'
                                }
                            }
                        })
                    } else {
                        swal({
                            title: 'Otentikasi Berhasil',
                            icon: 'success',
                            text: 'Anda akan dialihkan ke panel dalam dasbor dalam beberapa detik',
                            closeOnEsc: false,
                            closeOnClickOutside: false,
                            timer: 2500,
                            buttons: {}
                        }).then(() => {
                            window.location.reload()
                        })
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        }
    }
}

</script>
<!-- END SCRIPTS -->

<!-- STYLES -->
<style lang="scss">
#loginMain {
    min-height: 100vh;
    min-height: 100dvh;
    min-width: 100vw;
    min-width: 100dvw;
    background-image: v-bind('background');
    background-position: left;
    background-repeat: no-repeat;
    background-size: cover;

    .icon {
        width: 16px;
    }

    .toggle-password-btn {
        position: absolute;
        bottom: 0px;
        right: 0px;
    }
}

.login-form {

    &-section {
        min-height: 100vh;
        min-height: 100dvh;
        max-width: 380px;
        /* From https://css.glass */
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(8.7px);
        -webkit-backdrop-filter: blur(8.7px);
        padding: 1.5em;

        @media (max-width: 575.98px) {
            max-width: 100%;
            padding: 2.5em;
        }
    }

    &-input {
        border: none;
        padding: 6px 0;
        border-bottom: 2px solid var(--bs-secondary-bg-subtle);
        transition: all ease-in 150ms;
        background-color: none !important;
        background: none !important;

        &:focus,
        &:hover {
            border-radius: 0;
            outline: none;
            border-bottom-color: var(--bs-primary);
        }
    }
}

.swal-overlay {
    .swal-button {
        font-size: 1.15rem;
        padding-left: 1.2rem;
        padding-right: 1.2rem;

        &.btn-primary:not([disabled]):hover {
            background-color: var(--bs-btn-hover-bg);
        }
    }
    
    .swal-footer {
        text-align: center;
    }

    .swal-title {
        margin-bottom: 1.2rem;
    }

    .swal-text {
        text-align: center;
    }
}

</style>
<!-- END STYLES -->