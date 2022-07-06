<template>
    <admin-layout>
        <template #header>
            <h4 class="page-heading">Graph Detailed Report</h4>
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

                <div v-if="overall_graph_datacollection" class="p-3 mb-8 bg-white shadow rounded-lg w-full overflow-hidden">
                    <h4 class="page-heading">Overall Score Report</h4>
                    <!-- New chart -->
                    <div class="p-3">
                        <bar-chart :height="400" :chart-data="overall_graph_datacollection"></bar-chart>
                    </div>
                </div>

                <!-- <div v-if="data_show == false" class="p-3 bg-white shadow rounded-lg w-full overflow-hidden">
                    <h2>No data Found</h2>
                </div> -->

                <div v-if="results_360_graph_datacollection" class="p-3 mb-8 bg-white shadow rounded-lg w-full overflow-hidden">
                    <h4 class="page-heading">Results 360 Report</h4>
                    <!-- New chart -->
                    <div class="p-3">
                        <bar-chart :height="400" :chart-data="results_360_graph_datacollection"></bar-chart>
                    </div>
                </div>

                <div v-if="results_gpi_graph_datacollection" class="p-3 mb-8 bg-white shadow rounded-lg w-full overflow-hidden">
                    <h4 class="page-heading">Results GPI Report</h4>
                    <!-- New chart -->
                    <div class="p-3">
                        <bar-chart :height="400" :chart-data="results_gpi_graph_datacollection"></bar-chart>
                    </div>
                </div>

                <div v-if="results_assessment_center_datacollection" class="p-3 mb-8 bg-white shadow rounded-lg w-full overflow-hidden">
                    <h4 class="page-heading">Assessment Center Report</h4>
                    <!-- New chart -->
                    <div class="p-3">
                        <bar-chart :height="400" :chart-data="results_assessment_center_datacollection"></bar-chart>
                    </div>
                </div>


                <div v-if="personality_assessment_graph_datacollection" class="p-3 mb-8 bg-white shadow rounded-lg w-full overflow-hidden">
                    <h4 class="page-heading">Personality Assessment Report</h4>
                    <!-- New chart -->
                    <!-- <div class="p-3" >
                        <bar-chart :height="400" :chart-data="personality_assessment_graph_datacollection"></bar-chart>
                    </div> -->

                    <div v-if="disc_result" class="grid sm:grid-cols-1 md:grid-cols-2 gap-4 p-3">
                        <div class="bg-white rounded p-4 border border-gray-200">
                            <h4 style="background: rgb(20, 150, 124); padding: 8px 15px; color: rgb(255, 255, 255);" class="pb-4 mb-4 text-sm font-semibold uppercase text-gray-800">D – Dominant/Direct</h4>
                            <div class="grid grid-cols-1 flex justify-between items-center">
                                <ul>
                                    <li v-for="(disc, index) in disc_result.disc_d" class="mb-2">{{disc.names}}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="bg-white rounded p-4 border border-gray-200">
                            <h4 style="background: rgb(208, 65, 62); padding: 8px 15px; color: rgb(255, 255, 255);" class="pb-4 mb-4 text-sm font-semibold uppercase text-gray-800">I – Influential/Spirited</h4>
                            <div class="grid grid-cols-1 flex justify-between items-center">
                                <ul>
                                    <li v-for="(disc, index) in disc_result.disc_i" class="mb-2">{{disc.names}}</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded p-4 border border-gray-200">
                            <h4 style="background: rgb(28, 109, 155); padding: 8px 15px; color: rgb(255, 255, 255);" class="pb-4 mb-4 text-sm font-semibold uppercase text-gray-800">S – Supportive/Considera</h4>
                            <div class="grid grid-cols-1 flex justify-between items-center">
                                <ul>
                                    <li v-for="(disc, index) in disc_result.disc_s" class="mb-2">{{disc.names}}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="bg-white rounded p-4 border border-gray-200">
                            <h4 style="background: rgb(244, 158, 17); padding: 8px 15px; color: rgb(255, 255, 255);" class="pb-4 mb-4 text-sm font-semibold uppercase text-gray-800">C – Cautious/Systematic</h4>
                            <div class="grid grid-cols-1 flex justify-between items-center">
                                <ul>
                                    <li v-for="(disc, index) in disc_result.disc_c" class="mb-2">{{disc.names}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>


                </div>


                <div v-if="competencies_assessment_datacollection" class="p-3 mb-8 bg-white shadow rounded-lg w-full overflow-hidden">
                    <h4 class="page-heading">Competencies Assessment Report</h4>
                    <!-- New chart -->
                    <div class="p-3">
                        <bar-chart :height="400" :chart-data="competencies_assessment_datacollection"></bar-chart>
                    </div>

                     <h4 class="page-heading">Competencies Average Score</h4>
                    <!-- New chart -->
                    <div class="p-3">
                        <bar-chart :height="400" :chart-data="skills_average_report_datacollection"></bar-chart>
                    </div>

                    <div class="p-3" >
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
                    <SkillResult :skillId="skill_id" :skillData="skillData" :datacollection="datacollection_skill" title="Skill Data" />
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
            skills: [Object,Array],

            results_360_graph: [Object,Array],
            results_gpi_graph: [Object,Array],
            personality_assessment_graph: [Object,Array],
            results_assessment_center_graph: [Object,Array],
            competencies_assessment_graph: [Object,Array],
            overall_graph: [Object,Array],
            disc_result: [Object,Array],
            skills_average_report:[Object,Array],
            slug: String,
            usergroups: Array,
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
                results_360_graph_datacollection: null,
                results_gpi_graph_datacollection: null,
                results_assessment_center_datacollection: null,
                personality_assessment_graph_datacollection: null,
                competencies_assessment_datacollection: null,
                skills_average_report_datacollection: null,
                overall_graph_datacollection: null,
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
                    this.$inertia.get(route('graph-report', {slug: slug }));
                }else{
                    this.$inertia.get(route('graph-report', {slug: slug }));
                }
            },
            fillData (graph) {
                //console.log();
                if (typeof graph == 'object') {
                    let _this = this;
                    if (graph.labels) {
                        var arrayLabel = graph.labels

                        var arrayData = graph.data;

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
                        
                        return {
                            labels: newArrayLabel,
                            datasets: [
                                {
                                  label: 'Total Scores',
                                  backgroundColor: '#df6368',
                                  data:newArrayData
                                }
                            ]
                        }
                    }
                }else{
                    return null;
                }
            },
            fetchskillresult(id,slug) {
                let _this = this;
                _this.loading = true;

                var skill_report = 'graph_skill_overall_report';

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
                return 'Detailed Report - ' + this.$page.props.general.app_name;
            },
        },
        mounted() {
            //console.log(this.results_gpi_graph);
            this.results_360_graph_datacollection = this.fillData(this.results_360_graph);
            this.results_gpi_graph_datacollection = this.fillData(this.results_gpi_graph);
            this.results_assessment_center_datacollection = this.fillData(this.results_assessment_center_graph);
            this.personality_assessment_graph_datacollection = this.fillData(this.personality_assessment_graph);
            this.competencies_assessment_datacollection = this.fillData(this.competencies_assessment_graph);
            this.skills_average_report_datacollection = this.fillData(this.skills_average_report);
            this.overall_graph_datacollection = this.fillData(this.overall_graph);
            //console.log(this.skills);
            //this.datacollection = 
        }
    }
</script>
