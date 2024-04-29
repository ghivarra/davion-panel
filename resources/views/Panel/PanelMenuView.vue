<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        <section class="accordion mb-5">
            <draggable v-bind:list="menus" itemKey="id">
                <template v-slot:item="{ element, index }">
                    <div class="accordion-item cursor-grab mb-4">
                        <header class="accordion-header">
                            <div v-bind:data-bs-target="`#group${index}`" v-bind:aria-controls="`group${index}`"
                                type="button" class="accordion-button fw-bold" data-bs-toggle="collapse" aria-expanded="true">
                                {{ element.name }}
                                <span v-if="element.status === 'Aktif'" class="badge ms-2 text-bg-success">Aktif</span>
                                <span v-else class="badge ms-2 text-bg-warning">Nonaktif</span>
                            </div>
                        </header>
                        <div v-bind:id="`group${index}`" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <draggable-menu-component v-bind:child="element.child"
                                    groupName="menus" itemKey="id"></draggable-menu-component>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </section>

    </main>
</template>

<script>

import { checkAxiosError, panelUrl } from '@/libraries/Function'
import draggableMenuComponent from '@/components/DraggableMenuComponent.vue'
import draggable from 'vuedraggable'
import axios from 'axios'

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
            menus: []
        }
    },
    created: function() {
        let app = this

        // get module group list
        axios.get(panelUrl('menu/list'))
            .then(function(res) {
                if (typeof res.data.status !== 'undefined' && res.data.status === 'success') {
                    app.menus = res.data.data
                }
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            })
    },
    mounted: function() {
        this.$nextTick(function() {
            this.$emit('loaded')
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

</style>