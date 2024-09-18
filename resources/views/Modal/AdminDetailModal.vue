<template>
    <section id="create-admin-modal">
        <button ref="modalOpenButton" class="d-none" data-bs-toggle="modal" data-bs-target="#adminDetailModal"></button>
        <div ref="modal" id="adminDetailModal" class="modal fade" tabindex="-1" aria-labelledby="adminDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h1 class="modal-title fs-5" id="adminDetailModalLabel">Informasi Admin</h1>
                    </div>
                    <div v-if="(typeof admin.id !== 'undefined')" class="modal-body">
                        <div class="d-flex align-items-center">
                            <img v-bind:src="profilePicture" v-bind:alt="admin.name" class="admin-info-image rounded-circle me-3 mb-3">
                            <div>
                                <h5 class="fw-bold mb-2">
                                    {{ admin.name }}
                                    <div class="ms-2 badge" v-bind:class="(admin.status === 'Aktif') ? 'bg-success' : 'bg-danger'">
                                        {{ admin.status }}
                                    </div>
                                </h5>
                                <p class="mb-1">
                                    <font-awesome icon="fas fa-user-tie" class="me-2"></font-awesome>
                                    {{ admin.username }} - {{ admin.admin_role_name }}
                                </p>
                                <p class="mb-1">
                                    <font-awesome icon="fas fa-envelope" class="me-2"></font-awesome>
                                    {{ admin.email }}
                                </p>
                                <div class="mb-1">
                                    <div class="badge" v-bind:class="(admin.email_verified_at === 'Terverifikasi') ? 'bg-success' : 'bg-warning'">
                                        Email {{ admin.email_verified_at }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button ref="modalCloseButton" type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>

import { imageUrl } from '@/libraries/Function'


export default {
    props: ['admin'],
    data: function() {
        return {
            
        }
    },
    computed: {
        profilePicture: function() {
            return (typeof this.admin.photo === 'undefined' || this.admin.photo === null || this.admin.photo.length < 1) ? imageUrl('admin/default-user.png', 120) : imageUrl(`admin/${this.admin.photo}`, 120)
        }
    },
    mounted: function() {

    }
}

</script>

<style lang="scss">

.admin-info {

    &-image {
        width: 120px;
        height: 120px;
        object-fit: cover;

        @media (max-width: 500px) {
            width: 80px;
            height: 80px;
        }
    }
}

</style>