<template>
    <draggable ref="draggableMenu" v-bind:list="child" v-bind:itemKey="itemKey" v-bind:group="groupName"
        v-bind:options="options" class="drag-area parent-drag-area pb-4" v-bind:move="checkMove">
        <template v-slot:item="{ element }">
            <div class="my-3 border px-3 pb-2 pt-3 fw-bold parent-menu-area position-relative">
                <font-awesome v-if="element.icon !== null" v-bind:icon="element.icon" class="me-1"></font-awesome>
                <font-awesome v-else icon="far fa-circle" class="me-1"></font-awesome>
                {{ element.title }}
                <span v-if="element.status === 'Aktif'" class="badge ms-2 text-bg-success">Aktif</span>
                <span v-else class="badge ms-2 text-bg-warning">Nonaktif</span>

                <button v-on:click.prevent="edit" type="button" class="btn btn-link edit-menu-button" title="Edit Menu">
                    <font-awesome icon="fas fa-gear"></font-awesome>
                </button>

                <draggable v-bind:list="element.child" v-bind:itemKey="itemKey" v-bind:group="groupName"
                    v-bind:move="checkMove" class="drag-area child-drag-area pb-4">
                    <template v-slot:item="{ element }">
                        <div class="my-3 bg-white border px-3 py-2 child-menu-area">
                            <font-awesome icon="far fa-circle" class="me-1"></font-awesome>
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
        edit: function() {
            this.$emit('editButtonTrigger')
        }
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
        height: 48px !important;
    }
}

</style>