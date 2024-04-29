<template>
    <draggable v-bind:list="child" v-bind:itemKey="itemKey" v-bind:group="groupName" v-on:move="onMove"
        class="drag-area">
        <template v-slot:item="{ element }">
            <div class="drag-area my-3 bg-white border px-3 pt-2">
                {{ element.title }}
                <draggable v-bind:list="element.child" v-bind:itemKey="itemKey" v-bind:group="groupName" v-on:move="onMove"
                    class="drag-area">
                    <template v-slot:item="{ element }">
                        <div class="my-3 bg-white border px-3 py-2">
                            {{ element.title }}
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
        },
        onMove: {
            type: Function,
            default: function() {
                return true
            }
        },
    },
    components: {
        draggable
    }
}

</script>

<style scoped>

.drag-area {
    min-height: 50px;
}

</style>