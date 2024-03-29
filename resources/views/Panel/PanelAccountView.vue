<template>
    <main role="main" class="mb-4">
        <header class="mb-3">
            <slot name="breadcrumb"></slot>
        </header>
        Hello ini {{ name }}
    </main>
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
        this.$nextTick(function() {
            this.$emit('loaded')
        })
    }
}

</script>

<style lang="scss"></style>