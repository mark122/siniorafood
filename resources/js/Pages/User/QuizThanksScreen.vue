<template>
    <app-layout>
        <template #header>
            <h4 class="page-heading">{{ quiz.title }}</h4>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center">
                    <div class="w-full">
                        <div class="w-full rounded shadow-lg p-8 bg-gradient-to-tl from-green-600 to-green-700">
                            <h1 class="text-2xl font-semibold leading-relaxed text-center text-white">Thank You for taking the {{ quiz.title }}<span v-if="type.name == 'Quiz'"> Quiz</span>.</h1>
                            <div v-if="type.name !== 'Personality' && type.name !== 'Quiz'" class="flex flex-col sm:flex-row mt-7 gap-4">
                                <div>
                                    <p class="focus:outline-none text-xs text-gray-300">Answered</p>
                                    <p class="focus:outline-none mt-2 text-base sm:text-lg md:text-xl 2xl:text-2xl text-gray-50">
                                        {{ session.results.answered_questions }} out of {{ session.results.total_questions }}
                                    </p>
                                </div>
                                <div class="sm:ml-11">
                                    <p class="focus:outline-none text-xs text-gray-300">Percentage</p>
                                    <p class="focus:outline-none mt-2 text-base sm:text-lg md:text-xl 2xl:text-2xl text-gray-50">{{ session.results.percentage }}%</p>
                                </div>
                                <div class="sm:ml-11">
                                    <p class="focus:outline-none text-xs text-gray-300">Score</p>
                                    <p class="focus:outline-none mt-2 text-base sm:text-lg md:text-xl 2xl:text-2xl text-gray-50">
                                        {{ session.results.score }}/{{ quiz.total_marks }}
                                    </p>
                                </div>
                                <div class="sm:ml-11">
                                    <p class="focus:outline-none text-xs text-gray-300">Pass/Fail</p>
                                    <p class="focus:outline-none mt-2 text-base sm:text-lg md:text-xl 2xl:text-2xl text-gray-50">{{ session.results.pass_or_fail }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="type.name == 'Personality'" class="personality-content-wrap grid sm:grid-cols-1 md:grid-cols-1 gap-8 py-8">
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="page-heading text-left" style="padding-bottom:0 !important">Here is your assessment:</h3>
                            </div>
                            <div>
                                <a :href="route('download_quiz_report', {quiz: quiz.slug, session: session.code})" target="_blank" class="qt-btn qt-btn-success">
                                Download Score Report
                            </a>
                            </div>
                        </div>

                        <div v-if="max_disc.includes('D') == true" class="w-full bg-white dark:bg-gray-800 rounded flex flex-col items-left justify-left p-5 relative shadow">

                            <h2 class="text-gray-800 dark:text-gray-100 text-lg font-bold mb-3 " style="background: #14967c; padding: 15px 20px; color: #fff;">D – Dominant/Direct</h2>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold">Description</h4>
                            
                            <p class="font-normal text-sm text-gray-600 dark:text-gray-100 mt-1">Someone with A Style is well-respected by the organization as a go-getter who delivers on promises, yet direct with businesslike approach & could be too forceful. They prefer an exciting, action-oriented work environment; however, he/she seems intense and demanding, and can push their ideas through without reaching out to others. They usually don’t seem interested in collaboration and engaging people through teamwork</p>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Strengths</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Getting immediate & quick results</li>
                                <li>Causing action & moving people</li>
                                <li>Accepting challenges</li>
                                <li>Making quick decisions</li>
                                <li>Questioning the status quo</li>
                                <li>Taking charge</li>
                                <li>Solving problems Produces quick results</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Limitations</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Lack of concern for others (insensitive)</li>
                                <li>Impatience that can intimidate others</li>
                                <li>Overlook details</li>
                                <li>They could be overly domineering</li>
                                <li>Can make too risky & hasty decisions</li>
                                <li>Reluctance to delegate</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Power and authority</li>
                                <li>Prestige and challenge</li>
                                <li>Opportunities for individual accomplishments</li>
                                <li>Direct answers</li>
                                <li>Opportunities for advancement</li>
                                <li>Freedom from controls and supervision</li>
                                <li>Many new and varied activities</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Management</li>
                                <li>Team leading</li>
                                <li>Sales</li>
                                <li>Strategy</li>
                            </ul>
                        </div>

                        <div v-if="max_disc.includes('I') == true" class="w-full bg-white dark:bg-gray-800 rounded flex flex-col items-left justify-left p-5 relative shadow">
                            <h2 class="text-gray-800 dark:text-gray-100 text-lg font-bold mb-3" style="background: #d0413e; padding: 15px 20px; color: #fff;">I – Influential/Spirited</h2>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold">Description</h4>
                            
                            <p class="font-normal text-sm text-gray-600 dark:text-gray-100 mt-1">Someone with a B style has priority of enthusiasm with an outgoing nature. He/she seems to know everyone on a first-name basis. They show positive outlook and lively approach with excitement for new ideas. They tend to prioritize action and a fast pace along with spontaneity. Their energetic approach and inclination toward change are very useful, yet they might get so caught up in new ideas that they fail to stick to more routine tasks. They value collaboration and teamwork, enjoying the social aspects of work, but they also crave for attention at times.</p>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Strengths</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Building relationships & working in teams</li>
                                <li>Making a favorable impression</li> 
                                <li>Being articulate & promoter</li>
                                <li>Creating a motivational environment</li>
                                <li>Generating enthusiasm</li>
                                <li>Viewing people and situations with optimism</li>
                                <li>Idea generator</li>
                                <li>Vision-oriented thinker</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Limitations</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Impulsiveness & over commitment</li>
                                <li>Disorganization (Time management issues)</li>
                                <li>Lack of follow through</li>
                                <li>Over eagerness leads to missing on important details</li>
                                <li>Depend too much on intuition & spontaneity</li> 
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Popularity & social recognition</li>
                                <li>Freedom of expression</li>
                                <li>Group activities outside of the job</li>
                                <li>Democratic relationships</li>
                                <li>Freedom from control and detail</li> 
                                <li>Opportunities to verbalize proposals</li>
                                <li>Coaching and counseling</li>
                                <li>Favorable working conditions</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Most Effective Work Areas</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Sales</li>
                                <li>Customer Service</li>
                                <li>Public Relations</li>
                                <li>HR: Training & Development</li>
                                <li>Office Manager</li>
                            </ul>
                        </div>


                        <div v-if="max_disc.includes('S') == true" class="w-full bg-white dark:bg-gray-800 rounded flex flex-col items-left justify-left p-5 relative shadow">
                            <h2 class="text-gray-800 dark:text-gray-100 text-lg font-bold mb-3" style="background: #1c6d9b; padding: 15px 20px; color: #fff;">S – Supportive/Considera</h2>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold">Description</h4>
                            
                            <p class="font-normal text-sm text-gray-600 dark:text-gray-100 mt-1">Someone with a C style seems kind and supportive, and whenever you ask them a question, they are always patient and happy to help. However, while they always seem warm and sincere, they might focus too much energy on supporting others rather than on energizing the team. He/she is well-liked by everyone and can always be counted on to perform his/her job consistently. Sometimes they can be too cautious and tentative. They value relationships and team spirit, yet with a low profile.</p>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Strengths</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Performing in a consistent manner (reliable)</li>
                                <li>Patient</li>
                                <li>Helpful</li>
                                <li>Loyal</li>
                                <li>Good listener</li>
                                <li>Consultative & considerate of others’ views</li>
                                <li>Excellent team player</li>
                                <li>Providing reassurance in difficult times (peacemaker)</li>
                                <li>Creating a stable & harmonious work environment</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Limitations</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Overly accommodating</li> 
                                <li>Avoiding change (likes comfortable zone)</li> 
                                <li>Indecisiveness</li> 
                                <li>Could be too permissive & tolerant (taken advantage of)</li> 
                                <li>Being open to others can lead to missing on important issues</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Maintenance of the status quo unless given reasons for change </li>
                                <li>Predictable routines </li>
                                <li>Credit for work accomplished</li>
                                <li>Sincere appreciation</li>
                                <li>Identification with a group</li>
                                <li>Standard operating procedures</li>
                                <li>Minimal conflict </li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Most Effective Work Areas</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>HR</li>
                                <li>Accounting</li>
                                <li>Office Manager</li>
                                <li>Support Functions in general</li>
                            </ul>
                        </div>


                        <div v-if="max_disc.includes('C') == true" class="w-full bg-white dark:bg-gray-800 rounded flex flex-col items-left justify-left p-5 relative shadow">
                            <h2 class="text-gray-800 dark:text-gray-100 text-lg font-bold mb-3" style="background: #f49e11; padding: 15px 20px; color: #fff;">C – Cautious/Systematic</h2>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold">Description</h4>
                            
                            <p class="font-normal text-sm text-gray-600 dark:text-gray-100 mt-1">Someone with a D style has a systematic approach in dealing with others. They are not highly sociable and maintain a private nature. They require quality and accuracy, checking their work (and others’) two or three times before being satisfied. They want a stable environment where they can ensure reliable outcomes. He/she is careful, skeptical and ask a lot of probing questions. They have tendency to prioritize details and challenge other people’s ideas. Yet they tend to be conscientious and they do follow through on commitments.</p>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Strengths</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Adhering to key directives and standards</li>
                                <li>Concentrating on key details</li>
                                <li>Thinking analytically, weighing pros and cons</li>
                                <li>Using subtle or indirect approaches to conflict</li>
                                <li>Checking for accuracy & objectivity</li>
                                <li>Analyzing performance critically</li>
                                <li>Using a systematic approach to situations or activities</li>
                                <li>Providing structure for activities</li>
                            </ul>
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Limitations</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Overly critical of others</li>
                                <li>Overanalyzing</li>
                                <li>Isolating self (socially)</li>
                                <li>Can be counterproductive with too many details</li>
                                <li>Being too systematic makes them impersonal or lacking creativity</li>
                                <li>Can be too perfectionistic</li>
                            </ul>
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Clearly defined performance expectations</li> 
                                <li>Values on quality and accuracy</li>
                                <li>Reserved, business-like atmosphere</li>
                                <li>Opportunities to demonstrate expertise</li>
                                <li>Control over those factors that affect their performance</li>
                                <li>Opportunity to ask "why" questions</li>
                                <li>Recognition for specific skills and accomplishments</li>
                            </ul>
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Most Effective Work Areas</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Second line of Management</li>
                                <li>Strategic Planning/Thinking</li>
                                <li>Finance</li>
                                <li>R&D</li>
                                <li>Supply Chain</li>
                            </ul>
                        </div>
                    </div>
                
                </div>    
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import QuizCard from "@/Components/Cards/QuizCard";

    export default {
        components: {
            AppLayout,
            QuizCard
        },
        props: {
            quiz: Object,
            type: Object,
            session: Object,
            max_disc: Array
        },
        metaInfo() {
            return {
                title: this.title
            }
        },

        computed: {
            title() {
                return this.quiz.title + ' Thank You - ' + this.$page.props.general.app_name;
            }
        },
    }
</script>
