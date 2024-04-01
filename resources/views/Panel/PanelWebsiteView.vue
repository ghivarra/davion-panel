<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        <article class="panel-box bg-white">
            <header class="panel-box-header">
            Informasi
            </header>
            <form ref="websiteMainForm" id="websiteMainForm" class="p-3">
                <div class="mb-3">
                    <label for="formName" class="form-label fw-bold">Nama Website</label>
                    <input v-model="mainForm.name" name="name" id="formName" type="text" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="formTagline" class="form-label fw-bold">Slogan/Tagline</label>
                    <input v-model="mainForm.tagline" name="tagline" id="formTagline" type="text" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="formAppVersion" class="form-label fw-bold">Versi Aplikasi</label>
                    <input v-model="mainForm.app_version" name="app_version" id="formAppVersion" type="text" class="form-control">
                </div>
                <div class="mb-4">
                    <label for="formDescription" class="form-label fw-bold">Deskripsi</label>
                    <textarea v-model="mainForm.description" name="description" id="formDescription" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </article>
    </main>
</template>

<script>

import { panelUrl, checkAxiosError } from '@/libraries/Function'
import axios from 'axios'

export default {
    name: 'panel-website-view',
    inject: ['showLoader', 'hideLoader'],
    data: function() {
        return {
            name: 'Website',
            mainForm: {
                name: '',
                tagline: '',
                description: '',
                app_version: '',
            }
        }
    },
    mounted: function() {

        let app = this

        app.$nextTick(function() {
            app.$emit('loaded')
        })

        // get website data
        axios.get(panelUrl('website/data'))
            .then(function(res) {
                res = res.data
                app.mainForm = res.data
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            })
    }
}

</script>

<style lang="scss"></style>