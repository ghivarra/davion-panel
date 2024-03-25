<template>
    <section class="mb-4">
        Hello ini {{ name }}
    </section>
</template>

<script>

export default {
    name: 'panel-profile-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Profile'
        }
    },
    mounted: function() {
        this.hideLoader()
    }
}

</script>

<style lang="scss">


</style>