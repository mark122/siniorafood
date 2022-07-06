<template>
    <admin-layout>
        <template #header>
            <h4 class="page-heading">{{ quiz.title }} - {{ schedule !== null ? 'Schedule ' : '' }} Graph Detailed Report</h4>
            <div v-if="schedule !== null" class="text-xs">Schedule ID: {{ schedule.code }}</div>
        </template>
        <div class="py-8">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                <div class="p-3 bg-white shadow rounded-lg w-full overflow-hidden mb-3">
                    <nav class="tabs flex flex-col sm:flex-row">

                        <template v-for="(group, index) in usergroups">
                            <button v-if="group.slug == slug" @click="jumpToTab(group.slug)" class="tab active text-gray-600 py-4 px-6 block hover:text-green-500 focus:outline-none text-green-500 border-b-2 font-medium border-green-500" >
                            {{group.name}}
                            </button>

                            <button v-if="group.slug !== slug" @click="jumpToTab(group.slug)"  class="tab text-gray-600 py-4 px-6 block hover:text-gray-500 focus:outline-none" >
                            {{group.name}}
                            </button>
                        </template>
                       
                    </nav>
                </div>

                 <div v-if="data_show == false" class="p-3 bg-white shadow rounded-lg w-full overflow-hidden">
                    <!-- New chart -->
                    <h2>No data Found</h2>
                </div>

                <div v-if="data_show" class="p-3 bg-white shadow rounded-lg w-full overflow-hidden">
                    <!-- New chart -->
                    <div class="p-3" v-if="datacollection">
                        <bar-chart :height="400" :chart-data="datacollection"></bar-chart>
                    </div>
                </div>

                <div v-if="data_show" class="p-3 bg-white shadow rounded-lg w-full overflow-hidden mt-10">
                    <div class="p-3">
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full">
                                <thead class="text-sm font-semibold uppercase bg-gray-50">
                                    <tr>
                                        <th class="p-4 whitespace-nowrap">
                                            <div class="font-semibold text-left font-bold">Skill</div>
                                        </th>
                                        <th class="p-4 text-right width-auto whitespace-nowrap">
                                            <div class="font-semibold text-center font-bold">Action</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100">
                                    <tr v-for="(skill_data, index) in skills">
                                        <td class="p-1 whitespace-nowrap">
                                            <div class="flex items-center">
                                                {{skill_data.name}}
                                            </div>
                                        </td>
                                        <td class="p-1 text-right width-auto whitespace-nowrap">
                                            <button @click="fetchskillresult(skill_data.id,slug)" class="qt-btn qt-btn-success  qt-btn-sm" type="button">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> 


                <!-- Sidebar Forms -->
                <Sidebar position="right" :visible.sync="editForm" class="p-sidebar-md">
                    <SkillResult :skillId="skill_id" :skillData="skillData" :quizId="quiz.id" :datacollection="datacollection_skill" title="Skill Data" />
                </Sidebar>

            </div>
        </div>
    </admin-layout>
</template>

<script>
    import AdminLayout from '@/Layouts/AdminLayout';
    import NoDataTable from "@/Components/NoDataTable";
    import BarChart from '@/Charts/BarChart.js';
    import Sidebar from 'primevue/sidebar';
    import SkillResult from "@/Components/Forms/SkillResult";
    export default {
        components: {
            AdminLayout,
            NoDataTable,
            BarChart,
            Sidebar,
            SkillResult
        },
        props: {
            quiz: Object,
            quizSessions: Object,
            type: Object,
            schedule: Object,
            skills: Array,
            main_graph: Object,
            slug: String,
            usergroups: Array,
            report_type:String,
        },
        data() {
            return {
                loading: false,
                datacollection: null,
                datacollection_skill: null,
                createForm: false,
                editForm: false,
                currentId: null,
                skill_id:null,
                skillData:null,
                data_show:false,
            }
        },
        methods: {
            export2image: function () {
                let canvas = document.getElementById('bar-chart')
                var win = window.open('', 'Print', 'height=600,width=800');
                win.document.write("<br><img src='" + canvas.toDataURL() + "' />");
                setTimeout(function(){ //giving it 200 milliseconds time to load
                        win.document.close();
                        win.focus()
                        win.print();
                        //win.location.reload()
                }, 200);  

            },
            jumpToTab(slug) {
                if (this.report_type == 'detailed') {
                    this.$inertia.get(route('graph_quizzes.detailed_report_slug', { quiz: this.quiz.id, slug: slug }));
                }else{
                    this.$inertia.get(route('graph_quizzes.overall_report_slug', { quiz: this.quiz.id, slug: slug }));
                }
            },
            fillData () {

                let _this = this;

                if (_this.main_graph.data.length > 0) {
                   this.data_show = true;
                }

                var arrayLabel = _this.main_graph.labels

                var arrayData = _this.main_graph.data;

                var arrayOfObj = arrayLabel.map(function(d, i) {
                  return {
                    label: d,
                    data: arrayData[i] || 0
                  };
                });

                var sortedArrayOfObj = arrayOfObj.sort(function(a, b) {
                  return b.data - a.data;
                });

                var newArrayLabel = [];
                var newArrayData = [];

                sortedArrayOfObj.forEach(function(d){
                  newArrayLabel.push(d.label);
                  newArrayData.push(d.data);
                });

                this.datacollection = {
                    labels: newArrayLabel,
                    datasets: [
                        {
                          label: 'Total Scores',
                          backgroundColor: '#df6368',
                          data:newArrayData
                        }
                    ]
                }
            },
            fetchskillresult(id,slug) {
                let _this = this;
                _this.loading = true;

                if (_this.report_type == 'detailed') {
                    var skill_report = 'graph_skill_detailed_report';
                }else{
                    var skill_report = 'graph_skill_overall_report';
                }

                axios.get(route(skill_report, {quiz: this.quiz.id, slug: slug ,skill: id }))
                .then(function (response) {
                    let data = response.data;

                    var arrayLabel = data.main_graph.labels

                    var arrayData = data.main_graph.data;

                    var arrayOfObj = arrayLabel.map(function(d, i) {
                      return {
                        label: d,
                        data: arrayData[i] || 0
                      };
                    });

                    var sortedArrayOfObj = arrayOfObj.sort(function(a, b) {
                      return b.data - a.data;
                    });

                    var newArrayLabel = [];
                    var newArrayData = [];

                    sortedArrayOfObj.forEach(function(d){
                      newArrayLabel.push(d.label);
                      newArrayData.push(d.data);
                    });

                    _this.datacollection_skill = {
                        labels:  newArrayLabel,
                        datasets: [
                            {
                              label: 'Skill Scores',
                              backgroundColor: '#df6368',
                              data:newArrayData
                            }
                        ]
                    }
                    _this.skillData = data.skill_data;  
                    _this.editForm = true;  
                   
                })
                .catch(function (error) {
                   
                })
                .then(function () {
                   
                });
            },
        },
        metaInfo() {
            return {
                title: this.title
            }
        },
        computed: {
            title() {
                return this.quiz.title + ' Detailed Report - ' + this.$page.props.general.app_name;
            },
        },
        mounted() {
            this.fillData()
        }
    }
</script>
