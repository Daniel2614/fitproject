<script>
    import Vue from 'vue'
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePaginationBootstrap from './VuetablePaginationBootstrap'

    export default {
        render(h) {
            return h(
                'div',
                {
                    class: ''
                },
                [
                    h('filter-bar'),
                    this.renderVuetable(h),
                    this.renderPagination(h)
                ]
            )
        },
        data() {
            return {
                queryParams:{
                    sort: 'orderBy',
                    page: 'page',
                    perPage: 'per_page'
                },
                moreParams: {
                    orderDirection: 'desc',
                },
                cssConfig: {
                    tableClass: "table table-striped table-bordered table-hover table--align-middle",
                    loadingClass: "loading",
                    ascendingIcon: "glyphicon glyphicon-chevron-up",
                    descendingIcon: "glyphicon glyphicon-chevron-down",
                    handleIcon: "glyphicon glyphicon-menu-hamburger",
                }
            }
        },
        props:{
            apiToken: { type: String, required: true},
            apiUrl: { type: String, required: true},
            fields: { type: Array, required: true},
            apiToken: { type: String, default() {  return [] }},
            appendParams: {
                type: Object,
                default() {
                    return {}
                }
            },
        },
        components: {
            VuetablePaginationBootstrap,
            Vuetable,
        },
        mounted() {
            this.$eventHub.$on('filter-set', eventData => this.onFilterSet(eventData))
            this.$eventHub.$on('filter-reset', e => this.onFilterReset())
        },
        methods: {
            getSortParam(sortOrder) {
                return sortOrder.map( sort => {
                    this.moreParams.orderDirection = sort.direction
                    return sort.field
                }).join(',');
            },
            onFilterSet(payload) {
                this.moreParams = {'filter': payload},
                    Vue.nextTick( () => this.$refs.vuetable.refresh())
            },
            onFilterReset() {
                this.moreParams = {}
                Vue.nextTick( () => this.$refs.vuetable.refresh())
            },
            onPaginationData (paginationData) {
                this.$refs.pagination.setPaginationData(paginationData)
            },
            onChangePage (page) {
                this.$refs.vuetable.changePage(page)
            },
            renderVuetable(h) {
                return h(
                    'div',
                    {
                        class: 'table-container',
                    },
                    [
                        h('vuetable',
                            {
                                ref: 'vuetable',
                                props: {
                                    apiUrl: this.apiUrl,
                                    fields: this.fields,
                                    queryParams: this.queryParams,
                                    paginationPath: "",
                                    appendParams: this.moreParams,
                                    httpOptions: { headers: {Authorization: 'Bearer ' + this.apiToken }},
                                    css: this.cssConfig,
                                },
                                on:{
                                    'vuetable:pagination-data': this.onPaginationData,
                                },
                                scopedSlots: this.$scopedSlots
                            },
                        )
                    ]
                )
            },
            renderPagination(h) {
                return h(
                    'vuetable-pagination-bootstrap',
                    {
                        ref: 'pagination',
                        on: {
                            'vuetable-pagination:change-page': this.onChangePage
                        }
                    }
                )
            },
        }
    }
</script>
