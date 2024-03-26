<template>
    <section class="mb-4">
        Hello ini {{ name }}
    </section>
</template>

<script>

export default {
    name: 'panel-role-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Role'
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