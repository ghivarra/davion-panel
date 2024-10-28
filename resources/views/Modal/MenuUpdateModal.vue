<template>
    <section id="update-menu">
        <button ref="modalOpenButton" v-on:click="getMenuData" class="d-none" data-bs-toggle="modal"
            data-bs-target="#menuUpdateFormModal"></button>
        <div class="modal fade" id="menuUpdateFormModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="menuUpdateFormModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form v-on:submit.prevent="submitForm" ref="menuUpdateForm" method="POST" class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h1 class="modal-title fs-5" id="menuUpdateFormModalLabel">Edit Menu/Submenu</h1>
                    </div>
                    <div class="modal-body">
                        <input v-bind:value="data.groupId" id="group-id-update-input" type="hidden" class="d-none" required>
                        <h6 class="mb-3 fw-bold text-primary">Grup: {{ data.groupName }}</h6>
                        <h6 class="mb-3 fw-bold text-primary">Tipe: {{ data.type }}</h6>
                        <div class="mb-3 d-none">
                            <p class="mb-2 fw-bold">Tipe</p>
                            <div class="form-check form-check-inline">
                                <input v-bind:checked="data.type === 'Primary'" v-model="data.type"
                                    class="form-check-input" type="radio" name="type" id="primaryTypeRadio"
                                    value="Primary">
                                <label class="form-check-label" for="primaryTypeRadio">Primary</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input v-bind:checked="data.type === 'Parent'" v-model="data.type"
                                    class="form-check-input" type="radio" name="type" id="parentTypeRadio"
                                    value="Parent">
                                <label class="form-check-label" for="parentTypeRadio">Parent</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input v-bind:checked="data.type === 'Child'" v-model="data.type"
                                    class="form-check-input" type="radio" name="type" id="childTypeRadio" value="Child">
                                <label class="form-check-label" for="parentTypeRadio">Child</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="menuUpdateLabel" class="form-label fw-bold">
                                Label
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.title" type="text" class="form-control" id="menuUpdateLabel"
                                name="title" maxlength="200" required>
                        </div>
                        <div v-if="data.type !== 'Parent'" class="mb-3">
                            <label for="menuUpdateRouterName" class="form-label fw-bold">
                                Nama Routes
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.router_name" type="text" class="form-control" id="menuUpdateRouterName"
                                name="router_name" maxlength="200" required>
                        </div>
                        <div v-if="data.type !== 'Child'" class="mb-3">
                            <label for="menuUpdateIcon" class="form-label fw-bold">
                                Icon
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <input v-model="data.icon" type="text" class="form-control" id="menuUpdateIcon" name="icon"
                                maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="menuUpdateStatus" class="form-label fw-bold">
                                Status
                                <span class="text-danger" title="Wajib Diisi">*</span>
                            </label>
                            <select v-model="data.status" name="status" id="menuUpdateStatus" class="form-select"
                                required>
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
import swal from 'sweetalert'

export default {
    name: 'menu-update-modal',
    inject: ['showLoader', 'hideLoader'],
    props: ['menuId'],
    data: function() {
        return {
            data: {
                type: 'Primary',
                title: '',
                icon: '',
                router_name: '',
                status: 'Aktif',
                groupId: 0,
                groupName: 'Grup',
            }
        }
    },
    methods: {
        getMenuData: function() {
            let app = this
            app.showLoader()

            // get data after id changed
            app.$nextTick(() => {
                axios.get(panelUrl(`menu/get?id=${app.menuId}`))
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
                            app.data.type = res.data.type
                            app.data.title = res.data.title
                            app.data.icon = res.data.icon
                            app.data.router_name = res.data.router_name
                            app.data.status = res.data.status
                            app.data.groupId = res.data.admin_menu_group_id
                            app.data.groupName = res.data.admin_menu_group_name
                        }
                        app.hideLoader()
                    }).catch(function(res) {
                        app.hideLoader()
                        checkAxiosError(res.request.status)
                    })
            })
        },
        submitForm: function() {
            let app = this

            app.$refs.modalCloseButton.click()
            app.showLoader()
            
            let form = new FormData()
            form.append('id', app.menuId)
            form.append('title', app.data.title)
            form.append('status', app.data.status)

            if (app.data.type !== 'Child') {
                form.append('icon', app.data.icon)
            }

            if (app.data.type !== 'Parent') {
                form.append('router_name', app.data.router_name)
            }

            // save data
            axios.post(panelUrl('menu/update'), form)  
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
    }
}

</script>