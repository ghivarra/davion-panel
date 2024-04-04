<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        <vue-table id="module-table" v-bind:url="table.url" v-bind:order="table.order" v-bind:columns="table.columns">
            <template v-slot:header>
                <tr>
                    <th></th>
                    <th>
                        <input type="text" class="form-control">
                    </th>
                    <th>
                        <input type="text" class="form-control">
                    </th>
                    <th>
                        <input type="text" class="form-control">
                    </th>
                </tr>
            </template>
        </vue-table>
    </main>
</template>

<script>

import { panelUrl } from '@/libraries/Function'
import VueTable from '../../libraries/Ghivarra/VueTable/VueTable.vue'

export default {
    name: 'panel-module-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        'vue-table': VueTable
    },
    data: function() {
        return {
            name: 'Module',
            table: {
                url: panelUrl('module/datatable'),
                order: {
                    column: 'group',
                    dir: 'asc'
                },
                columns: [
                    { query: '', text: 'No.', key: 'no', sortable: false, searchable: false, class: ['col-no'] },
                    { query: '', text: 'Grup', key: 'group', class: ['col-secondary'] },
                    { query: '', text: 'Nama', key: 'name', class: ['col-primary'] },
                    { query: '', text: 'Alias', key: 'alias', class: ['col-secondary'] },
                ]
            }
        }
    },
    mounted: function() {
        this.$nextTick(function() {
            this.$emit('loaded')
        })
    }
}

</script>

<style lang="scss"></style>