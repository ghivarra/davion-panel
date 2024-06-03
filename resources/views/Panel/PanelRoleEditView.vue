<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        Hello ini {{ name }}
    </main>
</template>

<script>

export default {
    name: 'panel-role-edit-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Panel Edit Create'
        }
    },
    created: function() {
        // get role data
        
    },
    mounted: function() {
        console.log(this.$route.params.roleId)
    }
}

</script>

<style lang="scss"></style>