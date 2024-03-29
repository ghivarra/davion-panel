<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        <article class="panel-box px-3 py-2 bg-white">
            Hello
        </article>
    </main>
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

<style lang="scss"></style>