<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>

        <section class="accordion mb-5">

            <draggable v-bind:list="menus" itemKey="id">
                <template v-slot:item="{ element, index }">
                    <div class="accordion-item cursor-grab mb-4">
                        <header class="accordion-header position-relative">
                            <div v-bind:data-bs-target="`#group${index}`" v-bind:aria-controls="`group${index}`"
                                type="button" class="accordion-button fw-bold" data-bs-toggle="collapse"
                                aria-expanded="true">
                                {{ element.name }}
                                <span v-if="element.status === 'Aktif'" class="badge ms-2 text-bg-success">Aktif</span>
                                <span v-else class="badge ms-2 text-bg-warning">Nonaktif</span>
                            </div>
                        </header>
                        <div v-bind:id="`group${index}`" class="accordion-collapse collapse show">
                            <div class="accordion-body">

                                <div class="d-flex mt-2 mb-4">
                                    <button type="button" class="btn btn-sm btn-primary">
                                        <font-awesome icon="fas fa-pen-to-square" class="me-1"></font-awesome>
                                        Edit
                                    </button>
                                    <button v-bind:class="(element.status === 'Aktif') ? 'btn-warning' : 'btn-success'" type="button"
                                        class="btn btn-sm mx-2 text-white">
                                        <font-awesome icon="fas fa-sliders" class="me-1"></font-awesome>
                                        {{ (element.status === 'Aktif') ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger">
                                        <font-awesome icon="fas fa-trash-can" class="me-1"></font-awesome>
                                        Hapus
                                    </button>
                                </div>

                                <draggable-menu-component v-bind:child="element.child" groupName="menus"
                                    itemKey="id"></draggable-menu-component>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>

            <div>
                <button v-on:click.prevent="save" type="button" class="btn btn-primary">
                    <font-awesome icon="fas fa-save" class="me-1"></font-awesome>
                    Simpan Susunan Menu
                </button>
            </div>

        </section>

        <section id="edit-menu-group">
            <button ref="groupEditFormButton" class="d-none" data-bs-toggle="modal"
                data-bs-target="#groupEditFormModal"></button>
            <div class="modal fade" id="groupEditFormModal" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="groupEditFormModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <form v-on:submit.prevent="update" method="POST" class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h1 class="modal-title fs-5" id="groupEditFormModalLabel">Update Grup Menu</h1>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="groupUpdateName" class="form-label fw-bold">Nama</label>
                                <input v-model="groupUpdateData.name" type="text" class="form-control"
                                    id="groupUpdateName" name="name" maxlength="200" required>
                            </div>
                            <div class="mb-3">
                                <label for="groupUpdateStatus" class="form-label fw-bold">Status</label>
                                <select v-model="groupUpdateData.status" name="status" id="groupUpdateStatus"
                                    class="form-select" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button ref="groupUpdateModalCloseButton" type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    </main>
</template>

<script>

import { checkAxiosError, panelUrl } from '@/libraries/Function'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faSave, faEllipsisVertical } from '@fortawesome/free-solid-svg-icons'
import draggableMenuComponent from '@/components/DraggableMenuComponent.vue'
import draggable from 'vuedraggable'
import axios from 'axios'
import Swal from 'sweetalert2'

library.add(faSave, faEllipsisVertical)

export default {
    name: 'panel-menu-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        draggable,
        'draggable-menu-component': draggableMenuComponent
    },
    data: function() {
        return {
            name: 'Menu',
            menus: [],
            groupUpdateData: {
                name: '',
                status: 'Aktif'
            }
        }
    },
    methods: {
        save: function() {
            let app = this
            let form = new FormData()
            let menuIterator = -1
            let group
            let menu
            let submenu

            app.showLoader()

            for (let i = 0; i < app.menus.length; i++) {
                group = app.menus[i]
                form.append(`group[${i}][id]`, group.id)
                form.append(`group[${i}][sort_order]`, (i + 1))

                if (group.child.length > 0) {
                    for (let n = 0; n < group.child.length; n++) {
                        menuIterator++
                        menu = group.child[n]
                        form.append(`menu[${menuIterator}][admin_menu_group_id]`, group.id)
                        form.append(`menu[${menuIterator}][id]`, menu.id)
                        form.append(`menu[${menuIterator}][sort_order]`, (n + 1))
                        
                        if (menu.child.length > 0) {
                            form.append(`menu[${menuIterator}][type]`, 'Parent')

                            for (let x = 0; x < menu.child.length; x++) {
                                menuIterator++
                                submenu = menu.child[x]
                                form.append(`menu[${menuIterator}][id]`, submenu.id)
                                form.append(`menu[${menuIterator}][sort_order]`, (x + 1))
                                form.append(`menu[${menuIterator}][type]`, 'Child')
                                form.append(`menu[${menuIterator}][admin_menu_parent_id]`, menu.id)
                            }

                        } else {
                            form.append(`menu[${menuIterator}][type]`, 'Primary')
                        }
                    }
                }
            }

            // send
            axios.post(panelUrl('menu/sort'), form)
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        window.location.reload()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        },
        editGroup: function(index) {
            this.groupUpdateData.name = this.menus[index].name
            this.groupUpdateData.status = this.menus[index].status
            this.$refs.groupEditFormButton.click()
        },
        update: function(event) {
            console.log(event.target)
        }
    },
    created: function() {
        let app = this

        // get module group list
        axios.get(panelUrl('menu/list'))
            .then(function(res) {
                if (typeof res.data.status !== 'undefined' && res.data.status === 'success') {
                    app.menus = res.data.data
                    app.$emit('loaded')
                }
            }).catch(function(res) {
                checkAxiosError(res.request.status)
                app.$emit('loaded')
            })
    }
}

</script>

<style lang="scss">

.cursor-grab {
    cursor: grab;

    &:click, &:focus {
        cursor: grabbing
    }
}

.option-menu-button {
    position: absolute;
    right: 0;
    top: 0;

    &.group {
        right: 2.5rem;
        top: .5rem;
    }

    &.parent-primary {
        top: .5rem;
        right: .6rem;
    }

    .dropdown-toggle::after {
        display: none;
    }
}

</style>