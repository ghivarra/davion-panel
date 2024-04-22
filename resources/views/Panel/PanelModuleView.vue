<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        <vue-table id="module-table" v-bind:url="table.url" v-bind:order="table.order" v-bind:columns="table.columns">
            <template v-slot:header>
                <tr>
                    <th></th>
                    <th>
                        <input 
                            ref="tableSearch1" 
                            v-bind:value="table.columns[1].query" 
                            v-on:keyup="searchColumn($refs.tableSearch1, 1)" 
                            type="text" 
                            class="form-control">
                    </th>
                    <th>
                        <input 
                            ref="tableSearch2" 
                            v-bind:value="table.columns[2].query" 
                            v-on:keyup="searchColumn($refs.tableSearch2, 2)" 
                            type="text" 
                            class="form-control">
                    </th>
                    <th>
                        <input 
                            ref="tableSearch3" 
                            v-bind:value="table.columns[3].query" 
                            v-on:keyup="searchColumn($refs.tableSearch3, 3)" 
                            type="text" 
                            class="form-control">
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
            searchTableStatus: false,
            searchTableTimeout: 300,
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
    methods: {
        searchColumn: function(target, key) {
            let app = this

            if (app.searchTableStatus) {
                clearTimeout(app.searchTableStatus)
            }

            app.searchTableStatus = setTimeout(() => {
                app.table.columns[key].query = target.value
            }, app.searchTableTimeout);
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