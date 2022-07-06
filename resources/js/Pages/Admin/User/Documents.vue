<template>
    <admin-layout>
        <template #header>
            <h4 class="page-heading">Douments</h4>
        </template>
        <template #actions>
            <div class="flex gap-4 items-center">
                <button @click="createForm = true, currentId = currentId " class="qt-btn qt-btn-success">
                upload Douments
            </button>
            </div>
        </template>

        <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <vue-good-table mode="remote" @on-page-change="onPageChange" @on-column-filter="onColumnFilter" @on-per-page-change="onPerPageChange"
                                    :pagination-options="options" :columns="columns"
                                    :totalRows="userdocuments.meta.pagination.total"
                                    :rows="userdocuments.data">
                        <template slot="table-row" slot-scope="props">
                            <!-- Code Column -->
                            <div v-if="props.column.field === 'id'">
                               <div class="py-4" v-html="props.row.id"></div>
                            </div>

                            <!-- Question Column -->
                            <div v-else-if="props.column.field === 'title'">
                                <div class="py-4" v-html="props.row.title"></div>
                            </div>

                            <!-- Question Column -->
                            <div v-else-if="props.column.field === 'type'">
                                <div v-if="props.row.type === 'pdf'">
                                    <p class="pi pi-file-pdf" style="color:#606266 !important; font-size:28px !important;"></p>
                                </div>
                                <div v-if="props.row.type === 'csv' || props.row.type === 'xlsx' || props.row.type === 'xls'">
                                    <p class="pi pi-file-excel" style="color:#606266 !important; font-size:28px !important;"></p>
                                </div>
                            </div>


                            <!-- Actions Column -->
                            <div v-else-if="props.column.field === 'actions'">
                                <Button icon="pi pi-eye" class="p-button-rounded p-button-secondary p-button-text p-mr-2"
                                        @click="viewDocument(props.row.document_path)"/>
                                <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-text"
                                        @click="deleteDoc(props.row.id)"/>
                            </div>

                        </template>

                        <div slot="emptystate">
                            <no-data-table />
                        </div>
                    </vue-good-table>
                    <!-- Sidebar Forms -->
                    <Sidebar position="right" :visible.sync="createForm" v-on:hide="loadItems" class="p-sidebar-md">
                        <DocumentUploadForm :sections="sections" :errors="errors" @close="createForm = false" :currentId="currentId" title="New Document"/>
                    </Sidebar>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
    import AdminLayout from '@/Layouts/AdminLayout'
    import ArcDropdown from '@/Components/Dropdown'
    import ArcDropdownLink from '@/Components/DropdownLink'
    import Toast from 'primevue/toast';
    import Chip from 'primevue/chip';
    import Tag from 'primevue/tag';
    import Button from 'primevue/button';
    import NoDataTable from "@/Components/NoDataTable"
    import Sidebar from 'primevue/sidebar'
    import DocumentUploadForm from "@/Components/Forms/DocumentUploadForm"
    import Column from 'primevue/column'
    import InputText from 'primevue/inputtext';
    export default {
        components: {
            AdminLayout,
            ArcDropdown,
            ArcDropdownLink,
            Toast,
            Chip,
            Tag,
            Button,
            InputText,
            Column,
            DocumentUploadForm,
            Sidebar,
            NoDataTable
        },
        props: {
            questionTypes: Array,
            userdocuments: Object,
            sections: Array,
            skills: Object,
            errors: Object,
        },
        data() {
            return {
                createForm: false,
                userid: null,
                editForm: false,
                currentId: this.$page.props.currentId,
                columns: [
                    {
                        label: 'Id',
                        field: 'id',
                        sortable: true,
                        width: '100px',
                    },
                    {
                        label: 'Title',
                        field: 'title',
                        sortable: true,
                    },
                    {
                        label: 'Document type',
                        field: 'type',
                        sortable: false,
                    },
                    {
                        label: 'Actions',
                        field: 'actions',
                        sortable: false,
                        width: '200px',
                        tdClass: 'text-center',
                    }
                ],
                options: {
                    enabled: true,
                    mode: 'pages',
                    perPage: this.userdocuments.meta.pagination.per_page,
                    setCurrentPage: this.userdocuments.meta.pagination.current_page,
                    perPageDropdown: [10, 20, 50, 100],
                    dropdownAllowAll: false,
                },
                serverParams: {
                    columnFilters: {},
                    sort: {
                        field: '',
                        type: '',
                    },
                    page: 1,
                    perPage: 10
                },
                loading: false,
            }
        },
        metaInfo() {
            return {
                title: this.title
            }
        },

        computed: {
            title() {
                return 'Documents - ' + this.$page.props.general.app_name + ' Admin';
            }
        },
        methods: {
            updateParams(newProps) {
                this.serverParams = Object.assign({}, this.serverParams, newProps);
            },
            onPageChange(params) {
                this.updateParams({page: params.currentPage});
                this.loadItems();
                this.$nextTick(function() {
                    window.renderMathInElement(this.$el);
                });
            },
            onPerPageChange(params) {
                this.updateParams({perPage: params.currentPerPage});
                this.loadItems();
            },
            onSortChange(params) {
                this.updateParams({
                    sort: [{
                        type: params.sortType,
                        field: this.columns[params.columnIndex].field,
                    }],
                });
                this.loadItems();
            },
            onColumnFilter(params) {
                this.updateParams(params);
                this.serverParams.page = 1;
                this.loadItems();
            },
            getQueryParams() {
                let data = {
                    'page': this.serverParams.page,
                    'perPage': this.serverParams.perPage
                }

                for (const [key, value] of Object.entries(this.serverParams.columnFilters)) {
                    if (value) {
                        data[key] = value;
                    }
                }

                return data;
            },
            loadItems() {

                this.$inertia.get(route(route().current(), {id: this.$page.props.currentId}), this.getQueryParams(), {
                    replace: false,
                    preserveState: true,
                    preserveScroll: true,
                });
            },
            uploadDocument() {
                this.$inertia.get(route('documents_upload'));
            },
            viewDocument(path) {
                window.open(path, "_blank");  
            },
            deleteDoc(id) {
                this.$confirm.require({
                    header: 'Confirm Delete',
                    message: 'Do you want to delete this record?',
                    icon: 'pi pi-info-circle',
                    acceptClass: 'p-button-danger',
                    rejectLabel: 'Cancel',
                    acceptLabel: 'Delete',
                    accept: () => {
                        this.$inertia.delete(route('userdocuments.destroy', {id: id}), {}, {
                            onSuccess: () => {
                                this.$toast.add({
                                    severity: 'info',
                                    summary: 'Confirmed',
                                    detail: 'Record deleted',
                                    life: 3000
                                });
                            },
                        });
                    },
                    reject: () => {

                    }
                });

            },
        }
    }
</script>
