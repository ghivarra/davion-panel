<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>

        <!-- TOP ACTION BUTTON -->
        <section class="mb-4">
            <button v-on:click.prevent="createGroupModalOpen" type="button" class="btn btn-primary me-3">
                <font-awesome icon="fas fa-plus" class="me-1"></font-awesome>
                Tambah Grup
            </button>
        </section>

        <!-- ACCORDION SORTIR MENU -->
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
                                    <button v-on:click.prevent="editGroupModalOpen(index)"
                                        v-if="(parseInt(element.id) !== 1) " type="button"
                                        class="btn btn-sm btn-primary">
                                        <font-awesome icon="fas fa-pen-to-square" class="me-1"></font-awesome>
                                        Edit
                                    </button>
                                    <button v-on:click.prevent="updateGroupStatus(index)"
                                        v-if="(parseInt(element.id) !== 1) "
                                        v-bind:class="(element.status === 'Aktif') ? 'btn-warning' : 'btn-success'"
                                        type="button" class="btn btn-sm mx-2 text-white">
                                        <font-awesome icon="fas fa-sliders" class="me-1"></font-awesome>
                                        {{ (element.status === 'Aktif') ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <button v-on:click.prevent="deleteGroup(index)" v-if="(parseInt(element.id) !== 1) "
                                        type="button" class="btn btn-sm btn-danger me-2">
                                        <font-awesome icon="fas fa-trash-can" class="me-1"></font-awesome>
                                        Hapus
                                    </button>
                                    <button v-on:click.prevent="createMenuModalOpen(index)" type="button"
                                        class="btn btn-sm btn-dark">
                                        <font-awesome icon="fas fa-plus" class="me-1"></font-awesome>
                                        Tambah Menu
                                    </button>
                                </div>

                                <draggable-menu-component v-bind:child="element.child"
                                    v-bind:setUpdateMenuId="setUpdateMenuId" groupName="menus"
                                    itemKey="id"></draggable-menu-component>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>

            <div>
                <button v-on:click.prevent="saveList" type="button" class="btn btn-primary">
                    <font-awesome icon="fas fa-save" class="me-1"></font-awesome>
                    Simpan Susunan Menu
                </button>
            </div>

        </section>

        <!-- CREATE GROUP MODAL -->
        <group-menu-create-modal ref="groupCreateModal"></group-menu-create-modal>

        <!-- UPDATE GROUP MODAL -->
        <group-menu-update-modal ref="groupUpdateModal" v-bind:updateData="groupUpdateData"></group-menu-update-modal>

        <!-- CREATE MENU MODAL -->
        <menu-create-modal ref="menuCreateModal" v-bind:groupId="menuCreateGroupId"
            v-bind:groupName="menuCreateGroupName"></menu-create-modal>

        <!-- UPDATE MENU MODAL -->
        <menu-update-modal ref="menuUpdateModal" v-bind:menuId="menuUpdateId"></menu-update-modal>

    </main>
</template>

<script>

import GroupMenuCreateModal from '../Modal/GroupMenuCreateModal.vue'
import GroupMenuUpdateModal from '../Modal/GroupMenuUpdateModal.vue'
import MenuCreateModal from '../Modal/MenuCreateModal.vue'
import MenuUpdateModal from '../Modal/MenuUpdateModal.vue'
import { checkAxiosError, panelUrl } from '@/libraries/Function'
import draggableMenuComponent from '@/components/DraggableMenuComponent.vue'
import draggable from 'vuedraggable'
import axios from 'axios'
import swal from 'sweetalert'

export default {
    name: 'panel-menu-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        draggable,
        'draggable-menu-component': draggableMenuComponent,
        'group-menu-update-modal': GroupMenuUpdateModal,
        'group-menu-create-modal': GroupMenuCreateModal,
        'menu-create-modal': MenuCreateModal,
        'menu-update-modal': MenuUpdateModal
    },
    data: function() {
        return {
            name: 'Menu',
            menus: [],
            groupUpdateData: {
                id: 0,
                name: '',
                status: 'Aktif'
            },
            menuCreateGroupId: 0,
            menuCreateGroupName: 'Grup',
            menuUpdateId: 0
        }
    },
    methods: {
        setUpdateMenuId: function(id) {
            this.menuUpdateId = id
            this.$refs.menuUpdateModal.$refs.modalOpenButton.click()
        },
        saveList: function() {
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
                                form.append(`menu[${menuIterator}][admin_menu_group_id]`, group.id)
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
        createMenuModalOpen: function(index) {
            this.menuCreateGroupId = this.menus[index].id
            this.menuCreateGroupName = this.menus[index].name
            this.$refs.menuCreateModal.$refs.modalOpenButton.click()
        },
        createGroupModalOpen: function() {
            this.$refs.groupCreateModal.$refs.modalOpenButton.click()
        },
        editGroupModalOpen: function(index) {
            this.groupUpdateData.id = this.menus[index].id
            this.groupUpdateData.name = this.menus[index].name
            this.groupUpdateData.status = this.menus[index].status
            this.$refs.groupUpdateModal.$refs.modalOpenButton.click()
        },
        updateGroupStatus: function(index) {
            let app = this
            let status = (app.menus[index].status === 'Aktif') ? 'Nonaktif' : 'Aktif';

            app.showLoader()
            
            let form = new FormData()
            form.append('id', app.menus[index].id)
            form.append('status', status)

            // save data
            axios.post(panelUrl('menu/group/update-status'), form)  
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
        deleteGroup: function(index) {
            let app = this

            app.showLoader()
            
            let form = new FormData()
            form.append('id', app.menus[index].id)

            // save data
            axios.post(panelUrl('menu/group/delete'), form)  
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