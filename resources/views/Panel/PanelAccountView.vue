<template>
    <section class="mb-4">
        Hello ini {{ name }}
    </section>
</template>

<script>

export default {
    name: 'panel-account-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Account'
        }
    },
    mounted: function() {
        this.hideLoader()
    }
}

</script>

<style lang="scss">


</style>