<template>
    <draggable ref="draggableMenu" v-bind:list="child" v-bind:itemKey="itemKey" v-bind:group="groupName"
        v-bind:options="options" class="drag-area parent-drag-area" v-bind:move="checkMove">
        <template v-slot:item="{ element }">
            <div class="my-3 border px-3 py-2 fw-bold parent-menu-area">
                {{ element.title }}
                <span v-if="element.status === 'Aktif'" class="badge ms-2 text-bg-success">Aktif</span>
                <span v-else class="badge ms-2 text-bg-warning">Nonaktif</span>

                <draggable v-bind:list="element.child" v-bind:itemKey="itemKey" v-bind:group="groupName" v-bind:move="checkMove"
                    class="drag-area child-drag-area">
                    <template v-slot:item="{ element }">
                        <div class="my-3 bg-white border px-3 py-2 child-menu-area">
                            {{ element.title }}
                            <span v-if="element.status === 'Aktif'" class="badge ms-2 text-bg-success">Aktif</span>
                            <span v-else class="badge ms-2 text-bg-warning">Nonaktif</span>
                        </div>
                    </template>
                </draggable>
            </div>
        </template>
    </draggable>
</template>

<script>

import draggable from 'vuedraggable'

export default {
    name: 'draggable-menu-component',
    props: {
        child: {
            required: true,
            type: Array
        },
        itemKey: {
            required: true
        },
        groupName: {
            required: true,
            type: String
        }
    },
    components: {
        draggable
    },
    data: function() {
        return {
            options: {
                swapThreshold: .5
            }
        }
    },
    methods: {
        checkMove: function(event) {
            let child = event.dragged.getElementsByClassName('child-menu-area')
            return (child.length > 0 && event.to.classList.contains('child-drag-area')) ? false : true
        },
    }

}

</script>

<style scoped>

.drag-area {
    min-height: 50px;
}

.parent-menu-area {
    background-color: #ecfdf5;

    &.sortable-ghost {
        height: 39.5px !important;
    }
}

</style>