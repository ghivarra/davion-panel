<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        Hello ini {{ name }}
    </main>
</template>

<script>

export default {
    name: 'panel-role-create-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Panel Role Create'
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