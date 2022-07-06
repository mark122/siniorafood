<template>
    <app-layout>
        <template #header>
            <h4 class="page-heading">{{ __('Dashboard') }}</h4>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center">
                    <div class="w-full sm:w-2/3">
                      <div class="w-full rounded shadow-lg p-5 welcomebox bg-green-700">
                        <h1 class="text-lg font-bold leading-7 pt-6 text-white">Welcome to Siniora Assessments Platform</h1>
                          <p class="pt-4 text-xs leading-5 pr-12 text-white">Through this platform, you can view the assessments that you will conduct for the purpose of determining the development, training and learning needs for Siniora talents.</p>
                            <div class="pt-7 flex items-center justify-end">
                          </div>
                      </div>
                        <div class="flex justify-between items-center mb-4">
                            <div class="relative inline-flex">
                                <h4 class="py-4 px-4 text-xl font-semibold leading-tight text-gray-800">Live Assessments</h4>
                                <span class="flex absolute h-3 w-3 top-4 -right-1">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                              </span>
                            </div>
                            <inertia-link class="px-4" :href="route('live_quizzes')">
                                <span class="text-sm font-semibold text-green-500 underline">View All</span>
                            </inertia-link>
                        </div>
                        <div v-if="quizSchedules.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-4 mb-6">
                            <template v-for="(quiz, index) in quizSchedules">
                                <quiz-schedule-card :quiz="quiz">
                                    <template #action>
                                        <inertia-link :href="route('quiz_schedule_instructions', {quiz: quiz.slug, schedule: quiz.code})">
                                            <span class="qt-btn qt-btn-sm qt-btn-success">Assessment Instructions</span>
                                        </inertia-link>
                                    </template>
                                </quiz-schedule-card>
                            </template>
                        </div>
                        <div class="mb-6" v-else>
                            <empty-student-card :title="'No Scheduled Assessments for You'"></empty-student-card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import SubCategoryCard from "@/Components/Cards/SubCategoryCard";
    import PracticeSessionCard from "@/Components/Cards/PracticeSessionCard";
    import QuizScheduleCard from "@/Components/Cards/QuizScheduleCard";
    import EmptyStudentCard from "@/Components/Cards/EmptyStudentCard";

    export default {
        components: {
            AppLayout,
            SubCategoryCard,
            PracticeSessionCard,
            QuizScheduleCard,
            EmptyStudentCard,
        },
        props: {
            practiceSessions: Array,
            quizSchedules: Array,
        },
        metaInfo() {
            return {
                title: this.title
            }
        },
        computed: {
            title() {
                return 'User Dashboard - ' + this.$page.props.general.app_name;
            }
        },
    }
</script>
