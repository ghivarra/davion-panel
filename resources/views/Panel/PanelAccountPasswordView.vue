<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        Hello ini {{ name }}
    </main>
</template>

<script>

export default {
    name: 'panel-account-password-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Account Password'
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