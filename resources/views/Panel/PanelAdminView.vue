<template>
    <section class="mb-4">
        Hello ini {{ name }}
    </section>
</template>

<script>

export default {
    name: 'panel-admin-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Admin'
        }
    },
    mounted: function() {
        this.$nextTick(function() {
            this.$emit('loaded')
        })
    }
}

</script>

<style lang="scss">


</style>