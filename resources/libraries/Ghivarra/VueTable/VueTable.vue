<template>
    <section v-bind:class="[ `${id}-wrapper`, 'ghivarra-vue-table-wrapper' ]" v-bind:id="`${id}-wrapper`">

        <!-- Vue Table Header -->
        <header class="ghivarra-vue-table-header">

            <!-- Length Options -->
            <div class="input-length-wrapper">
                <select v-model="length" v-bind:id="`${id}-length-selector`" class="input-length">
                    <option v-for="(option, n) in lengthOptions" v-bind:key="n" v-bind:value="option">
                        {{ (option === 0) ? 'Semua' : option }}
                    </option>
                </select>
            </div>

            <!-- Navigator -->
            <div class="top-paginator-wrapper paginator-wrapper">
                <button v-on:click.prevent="previous" v-bind:disabled=prevButtonDisabled type="button"
                    class="prev-button paginator-button">
                    <svg class="chevron chevron-left" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path
                            d="M41.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 256 278.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z">
                        </path>
                    </svg>
                </button>
                <input v-model="pageNow" v-bind:max="pageTotal" v-bind:id="`${id}-paginator-top`" type="number" min="1" class="paginator-input">
                <button v-on:click.prevent="next" v-bind:disabled=nextButtonDisabled type="button"
                    class="next-button paginator-button">
                    <svg class="chevron chevron-right" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path
                            d="M342.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L274.7 256 105.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z">
                        </path>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Vue Table Body -->
        <div class="ghivarra-vue-table-body">

            <div class="ghivarra-vue-table-overflow-wrapper">

                <table v-bind:id="id" class="ghivarra-vue-table">

                    <thead>
                        <slot name="header"></slot>
                        <tr>
                            <th v-for="(item, key) in columns" v-bind:key="key"
                                v-on:click.prevent="sort(item, (item.key === orderColumn) ? orderDir : 'none')"
                                v-bind:class="(typeof item.sortable === 'undefined' || item.sortable === true) ? 'sortable' : '' + item.class.join(' ')"
                                v-bind:data-sort="(item.key === orderColumn) ? orderDir : 'none'">
                                {{ item.text }}
                            </th>
                        </tr>
                    </thead>

                    <tbody v-if="status === 'loading'">
                        <tr>
                            <td v-bind:colspan="columns.length">{{ loadingText }}</td>
                        </tr>
                    </tbody>

                    <tbody v-if="status === 'loaded'">
                        <tr v-if="response.row.length < 1">
                            <td v-bind:colspan="columns.length">{{ emptyText }}</td>
                        </tr>
                        <tr v-else v-for="(item, n) in response.row" v-bind:key="n">
                            <slot name="row" v-bind:row-data="item" v-bind:column-data="columns" v-bind:key="n">
                                <td v-for="(column, b) in columns" v-bind:key="b" v-bind:class="column.class" v-html="item[column.key]"></td>
                            </slot>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th v-for="(item, key) in columns" v-bind:key="key"
                                v-on:click.prevent="sort(item, (item.key === orderColumn) ? orderDir : 'none')"
                                v-bind:class="(typeof item.sortable === 'undefined' || item.sortable === true) ? 'sortable' : '' + item.class.join(' ')"
                                v-bind:data-key="item.key"
                                v-bind:data-sort="(item.key === orderColumn) ? orderDir : 'none'">
                                {{ item.text }}
                            </th>
                        </tr>
                    </tfoot>

                </table>

            </div>

        </div>

        <!-- Vue Table Footer -->
        <footer class="ghivarra-vue-table-footer">

            <!-- Navigator -->
            <div class="bottom-paginator-wrapper paginator-wrapper">
                <button v-on:click.prevent="previous" v-bind:disabled=prevButtonDisabled type="button"
                    class="prev-button paginator-button">
                    <svg class="chevron chevron-left" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path
                            d="M41.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 256 278.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z">
                        </path>
                    </svg>
                </button>
                <input v-model="pageNow" v-bind:max="pageTotal" v-bind:id="`${id}-paginator-bottom`" type="number" min="1" class="paginator-input">
                <button v-on:click.prevent="next" v-bind:disabled=nextButtonDisabled type="button"
                    class="next-button paginator-button">
                    <svg class="chevron chevron-right" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path
                            d="M342.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L274.7 256 105.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Table Info -->
            <div class="info">
                <span v-if="response.recordsFiltered == response.recordsTotal">
                    Menampilkan item {{ pageItems }} dari total {{ pageItemsFiltered }} data
                </span>
                <span v-else>
                    Menampilkan item {{ pageItems }} dari total {{ pageItemsFiltered }} data (disaring dari {{
                    pageItemsTotal }} data)
                </span>
            </div>

        </footer>

    </section>
</template>

<script>

const buildColumn = (form, columns) => {
    columns.forEach((column, i) => {
        let query = (typeof column.query !== 'undefined') ? column.query : ''
        let key = (typeof column.key !== 'undefined') ? column.key : '' 

        if (typeof column.searchable !== 'undefined' && !column.searchable) {
            query = ''
        }

        form.append(`columns[${i}][query]`, query)
        form.append(`columns[${i}][key]`, key)
    })
    return form
}

export default {
    name: 'vue-table',
    props: {
        id: {
            type: String,
            default: 'vue-table'
        },
        language: {
            type: String,
            default: 'id'
        },
        url: {
            type: String
        },
        headers: {
            type: Object,
            default: function(rawProps) {
                let defaultProp = {
                    'X-Requested-With': 'XMLHttpRequest'
                }

                return (typeof rawProps.headers !== 'undefined') ? rawProps.headers : defaultProp
            }
        },
        defaultLength: {
            type: Number,
            default: 10
        },
        lengthOptions: {
            type: Array,
            default: function(rawProps) {
                return (typeof rawProps.lengthOptions !== 'undefined') ? rawProps.lengthOptions : [ 0, 10, 25, 50 ]
            }
        },
        order: {
            type: Object,
            default: function(rawProps) {
                let defaultOrder = {
                    column: 'id',
                    dir: 'asc'
                }

                return (typeof rawProps.order !== 'undefined') ? rawProps.order : defaultOrder
            }
        },
        loadingText: {
            type: String,
            default: 'Sedang memuat data...'
        },
        emptyText: {
            type: String,
            default: 'Belum ada data untuk ditampilkan'
        },
        columns: {
            type: Array,
            default: function(rawProps) {
                return (typeof rawProps.columns !== 'undefined') ? rawProps.columns : []
            }
        },
        processData: {
            type: Function,
            default: function(data) {
                return data
            }
        }
    },
    data: function() {
        return {
            response: {
                draw: 0,
                length: 0,
                recordsTotal: 0,
                recordsFiltered: 0,
                row: []
            },
            drawCount: 0,
            length: 10,
            idle: true,
            pageNow: 1,
            status: 'loading',
            searchStatus: false,
            searchTimeout: 500,
            orderColumn: '',
            orderDir: 'asc'
        }
    },
    computed: {
        pageTotal: function() {
            return (this.length === 0) ? 1 : Math.ceil(this.response.recordsFiltered / this.length)
        },
        prevButtonDisabled: function() {
            return (this.pageNow <= 1) ? true : false
        },
        nextButtonDisabled: function() {
            return (this.pageNow === this.pageTotal) ? true : false
        },
        pageItems: function() {

            // if empty data
            if (this.response.recordsFiltered < 1)
            {
                return '0 - 0'
            }

            let length = (this.length === 0) ? this.response.recordsFiltered : this.length
            let pageNow = this.pageNow - 1

            // detect start items
            let start = (pageNow * length) + 1
            start = new Intl.NumberFormat(this.language).format(start)

            // detect end items
            let end = (pageNow * length) + this.response.length
            end = new Intl.NumberFormat(this.language).format(end)

            return `${start} - ${end}`
        },
        pageItemsFiltered: function() {
            return new Intl.NumberFormat(this.language).format(this.response.recordsFiltered)
        },
        pageItemsTotal: function() {
            return new Intl.NumberFormat(this.language).format(this.response.recordsTotal)
        }
    },
    watch: {
        pageNow: function() {
            this.draw()
        },
        length: function() {
            if (this.pageNow === 1) {
                this.draw()
            } else {
                this.pageNow = 1
            }
        },
        columns: {
            handler: function() {
                let app = this
                if (app.searchStatus) {
                    clearTimeout(app.searchStatus)
                }

                app.searchStatus = setTimeout(() => {
                    this.pageNow = 1
                    app.draw()
                }, app.searchTimeout)
            },
            deep: true
        }
    },
    methods: {
        handleError: function(statusCode, statusText) {
            if (statusCode === 401) {
                alert('Anda harus login untuk mengakses data')
            } else if (statusCode === 403) {
                alert('Anda tidak memiliki izin untuk mengakses data')
            } else if (statusCode === 404) {
                alert('URL data tidak ditemukan')
            } else if (statusCode >= 500) {
                alert('Server sedang sibuk silahkan coba lagi di lain waktu')
            } else {
                alert(statusText)
            }
        },
        previous: function() {
            if (!this.prevButtonDisabled && this.idle) {
                this.pageNow--
            }
        },
        next: function() {
            if (!this.nextButtonDisabled && this.idle) {
                this.pageNow++
            }
        },
        sort: function(item, sort) {
            if (typeof item.sortable !== 'undefined' && !item.sortable) {
                return false
            }

            if (this.idle) {
                this.orderColumn = item.key
                this.orderDir = (sort === 'asc') ? 'desc' : 'asc'
                this.draw()
            }
        },
        init: function() {
            let app = this
            app.length = app.defaultLength
            app.orderColumn = app.order.column
            app.orderDir = app.order.dir

            // return new promise functions
            return app.draw();
        },
        draw: function() {
            if (!this.idle) {
                return
            }

            let app = this
            app.idle = false
            app.status = 'loading'
            let data = new FormData

            // set data
            app.drawCount++
            let all = (app.length === 0) ? true : false
            let offset = (app.pageNow - 1) * app.length

            // append data
            data.append('draw', app.drawCount)
            data.append('all', all)
            data.append('limit', app.length)
            data.append('offset', offset)
            data.append('order[column]', app.orderColumn)
            data.append('order[dir]', app.orderDir)

            // build column and append
            data = buildColumn(data, app.columns)

            // return new promise
            return fetch(app.url, {
                method: 'POST',
                headers: app.headers,
                body: data
            }).then((res) => {
                if (!res.ok) {
                    app.idle = true
                    return app.handleError(res.status, res.statusText)
                }

                res.json().then((res) => {
                    if (res.status !== 'success') {
                        return alert('Gagal menarik data')
                    }
                    app.response = app.processData(res.data)
                    app.status = 'loaded'
                    app.idle = true
                })
            })
        }
    },
    created: function() {
        let app = this
        app.$emit('beforeCreate')
        app.init().finally(() => {
            app.$emit('afterCreate')
        })
    }
}

</script>

<style lang="scss">
@import './style.scss';
</style>