<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        Hello this is {{ name }} page. I made it blank so anyone can edit this page easily.
    </main>
</template>

<script>

export default {
    name: 'panel-dashboard-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Dashboard'
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