<template>
    <div>Halo {{ name }}</div>
</template>

<script>

export default {
    name: 'my-component',
    props: ['ucing', 'myman'],
    data: function() {
        return {
            name: 'My VueIgniter'
        }
    },
    created: function() {
        console.log(this.ucing)
        console.log(this.myman)
    }
}

</script>

<style></style>