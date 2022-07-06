<template>
    <div class="overflow-y-auto h-screen px-2">
        <div class="bg-gray-100 py-4 lg:py-4 rounded">
            <div class="container px-6 mx-auto flex flex-col md:flex-row items-start md:items-center justify-between">
                <div>
                    <h4 class="text-base font-semibold leading-tight text-gray-800">
                        {{ skillData.name }}
                    </h4>
                </div>
            </div>
        </div>

        <!-- New chart -->
        <div class="p-3" v-if="datacollection_pass">
            <bar-chart :height="500" :chart-data="datacollection_pass"></bar-chart>
        </div>


    </div>
</template>
<script>
    import InputText from 'primevue/inputtext';
    import Button from 'primevue/button';
    import BarChart from '@/Charts/BarChart.js'
    export default {
        name: 'SkillResult',
        components: {
            InputText,
            Button,
            BarChart,
        },
        props: {
            editFlag: Boolean,
            errors: Object,
            title: '',
            skillData: Object,
            maingraph: Array,
            datacollection: Object,
        },
        data() {
            return {
                editorUrl: window.CKEditorURL,
                datacollection_pass: null,
                formValidated: false,
                loading: false,
            }
        },
        methods: {
            fillData () {
                //console.log(this.datacollection);
                this.datacollection_pass = this.datacollection; 
            },
        },
        mounted() {
            this.fillData()
        }
    }
</script>
