<template>
    <section class="mb-4">
        Hello ini {{ name }}
    </section>
</template>

<script>

export default {
    name: 'panel-website-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Website'
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