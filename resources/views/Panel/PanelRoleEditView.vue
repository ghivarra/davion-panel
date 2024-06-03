<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        Hello ini {{ name }}
    </main>
</template>

<script>

import { panelUrl, checkAxiosError } from '@/libraries/Function'
import Swal from 'sweetalert2'
import axios from 'axios'

export default {
    name: 'panel-role-edit-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Panel Edit Create'
        }
    },
    created: function() {
        let app = this

        // get role data
        axios.get(panelUrl(`role/get?id=${app.$route.params.roleId}`))
            .then(function(res) {
                res = res.data
                console.log(res)
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            }).finally(function() {
                app.hideLoader()
            })
    }
}

</script>

<style lang="scss"></style>