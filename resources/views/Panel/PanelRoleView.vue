<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        
        <!-- TABLE -->
        <section ref="moduleTableSection">
            <vue-table id="module-table" ref="moduleTable" v-bind:defaultLength="25" v-bind:lengthOptions="[10,25,50]"
                v-bind:url="table.url" v-bind:order="table.order" v-bind:columns="table.columns"
                v-bind:processData="processData" v-on:afterCreate="$emit('loaded')">
                <template v-slot:header>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>
                            <input v-model="table.columns[2].query" type="text" class="form-control">
                        </th>
                        <th>
                            <select v-model="table.columns[3].query" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option value="1">Ya</option>
                                <option value="0">Bukan</option>
                            </select>
                        </th>
                        <th>
                            <select v-model="table.columns[4].query" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </th>
                    </tr>
                </template>
            </vue-table>
        </section>
        <!-- TABLE -->

    </main>
</template>

<script>

import { panelUrl } from '@/libraries/Function'
import VueTable from '@/libraries/Ghivarra/VueTable/VueTable.vue'

export default {
    name: 'panel-role-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        'vue-table': VueTable
    },
    data: function() {
        return {
            table: {
                url: panelUrl('role/datatable'),
                order: {
                    column: 'name',
                    dir: 'asc'
                },
                columns: [
                    { query: '', text: 'No.', key: 'no', sortable: false, searchable: false, class: ['col-no'] },
                    { query: '', text: '', key: 'action', sortable: false, searchable: false, class: ['col-action'] },
                    { query: '', text: 'Nama', key: 'name', class: ['col-primary'] },
                    { query: '', text: 'Superadmin', key: 'is_superadmin', class: ['col-secondary'] },
                    { query: '', text: 'Status', key: 'status', class: ['col-secondary'] },
                ]
            },
        }
    },
    methods: {
        processData: function(data) {
            this.tableData = data.row

            if (data.row.length < 1) {
                return data
            }

            data.row.forEach((item, i) => {
                let btnText = (item.status === 'Aktif') ? 'Nonaktifkan' : 'Aktifkan'
                let btnTextColor = (item.status === 'Aktif') ? 'text-warning' : 'text-success'
                data.row[i].action = `<div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-bars me-1"></i>
                                            Aksi
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button data-key="${i}" class="edit-button dropdown-item" type="button" title="Edit Data">
                                                    <i class="fa-solid fa-pen-to-square me-1 text-primary"></i>
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button data-key="${i}" class="status-button dropdown-item" type="button" title="${btnText} Data">
                                                    <i class="fa-solid fa-sliders me-1 ${btnTextColor}"></i>
                                                    ${btnText}
                                                </button>
                                            </li>
                                            <li>
                                                <button data-key="${i}" class="delete-button dropdown-item" type="button" title="Hapus Data">
                                                    <i class="fa-solid fa-trash-can me-1 text-danger"></i>
                                                    Hapus
                                                </button>
                                            </li>
                                        </ul>
                                    </div>`
                data.row[i].groupDefault = item.group
                data.row[i].statusDefault = item.status
                data.row[i].group = `<p class="m-0 fw-bold">${item.group}</p>`
                data.row[i].status = (item.status === 'Aktif') ? `<span class="bg-success text-white py-2 px-3 rounded-pill fw-bold">${item.status}</span>` : `<span class="bg-warning py-2 px-3 text-white rounded-pill fw-bold">${item.status}</span>`
            })

            return data
        },
    },
    mounted: function() {
        this.$nextTick(function() {
            this.$emit('loaded')
        })
    }
}

</script>

<style lang="scss"></style>