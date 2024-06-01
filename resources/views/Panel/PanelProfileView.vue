<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        Hello ini {{ name }}
    </main>
</template>

<script>

export default {
    name: 'panel-profile-view',
    inject: ['showLoader', 'hideLoader', 'admin', 'updateAdminData'],
    data: function() {
        return {
            name: 'Profile'
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