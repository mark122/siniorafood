<template>
    <admin-layout>
        <template #header>
            <h4 class="page-heading">{{ quiz.title }} Score Report</h4>
        </template>
        <template #actions>
            <a :href="route('quiz_session_report', {quiz: quiz.slug, session: session.id})" target="_blank" class="qt-btn qt-btn-success">
                Download Score Report
            </a>
        </template>
        
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid sm:grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded flex flex-col items-center justify-center p-5 relative shadow">
                        <div class="w-full flex sm:justify-center items-center">
                            <table class="w-full table-auto">
                                <tbody>
                                <tr>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-medium text-sm">Test Taker</td>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 text-sm">{{ session.name }}</td>
                                </tr>
                                <tr>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-medium text-sm">Email</td>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 text-sm">{{ session.email }}</td>
                                </tr>
                                <tr>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-medium text-sm">Session</td>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-mono text-sm uppercase">{{ session.id }}</td>
                                </tr>
                                <tr class="bg-emerald-200">
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-medium text-sm">Completion</td>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 text-sm">{{ session.completed }}</td>
                                </tr>
                                <tr>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-medium text-sm">IP</td>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 text-sm">{{ session.results.ip_address }}</td>
                                </tr>
                                <tr>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-medium text-sm">Device</td>
                                    <td class="border border-emerald-500 px-4 py-2 text-emerald-600 text-sm">{{ session.device }}, {{ session.os }}, {{ session.browser }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>




                    <div v-if="type.name !=='Personality'" class="grid sm:grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded flex items-center justify-between px-6 py-4 relative shadow">
                            <div class="absolute w-2 h-4 bg-green-700 left-0"></div>
                            <h3 class="focus:outline-none py-6 leading-4 text-gray-800 dark:text-gray-100 font-normal text-base">{{ session.status }}</h3>
                            <div class="flex flex-col items-end">
                                <h2 class="focus:outline-none text-green-700 dark:text-gray-100 text-xl leading-normal font-bold">{{ session.results.percentage }}%</h2>
                                <p tabindex="0" class="focus:outline-none ml-2 mb-1 text-sm text-gray-600 dark:text-gray-400">Min. {{ session.results.cutoff }}%</p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded flex items-center justify-between px-6 py-4 relative shadow">
                            <div class="absolute w-2 h-4 bg-green-700 left-0"></div>
                            <h3 class="focus:outline-none py-6 leading-4 text-gray-800 dark:text-gray-100 font-normal text-base">Score</h3>
                            <div class="flex flex-col items-end">
                                <h2 class="focus:outline-none text-green-700 dark:text-gray-100 text-xl leading-normal font-bold">{{ session.results.score }}</h2>
                                <p tabindex="0" class="focus:outline-none ml-2 mb-1 text-sm text-gray-600 dark:text-gray-400">Out of {{ session.results.total_marks }}</p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded flex items-center justify-between px-6 py-4 relative shadow">
                            <div class="absolute w-2 h-4 bg-green-700 left-0"></div>
                            <h3 class="focus:outline-none py-6 leading-4 text-gray-800 dark:text-gray-100 font-normal text-base">Accuracy</h3>
                            <div class="flex flex-col items-end">
                                <h2 class="focus:outline-none text-green-700 dark:text-gray-100 text-xl leading-normal font-bold">{{ session.results.accuracy }}%</h2>
                                <p tabindex="0" class="focus:outline-none ml-2 mb-1 text-sm text-gray-600 dark:text-gray-400"></p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded flex items-center justify-between px-6 py-4 relative shadow">
                            <div class="absolute w-2 h-4 bg-green-700 left-0"></div>
                            <h3 class="focus:outline-none py-6 leading-4 text-gray-800 dark:text-gray-100 font-normal text-base">Speed</h3>
                            <div class="flex flex-col items-end">
                                <h2 class="focus:outline-none text-green-700 dark:text-gray-100 text-xl leading-normal font-bold">{{ session.results.speed }}</h2>
                                <p tabindex="0" class="focus:outline-none ml-2 mb-1 text-sm text-gray-600 dark:text-gray-400">Que/Hour</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="type.name ==='Personality'" class="grid sm:grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded flex items-center justify-between px-6 py-4 relative shadow">
                            <div class="absolute w-2 h-4 bg-green-700 left-0"></div>
                            <h3 class="focus:outline-none py-6 leading-4 text-gray-800 dark:text-gray-100 font-normal text-base font-bold">D</h3>
                            <div class="flex flex-col items-end">
                                <h2 class="focus:outline-none text-green-700 dark:text-gray-100 text-xl leading-normal font-bold">{{ getQuestionScoringDISC(questions,'D') }}</h2>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded flex items-center justify-between px-6 py-4 relative shadow">
                            <div class="absolute w-2 h-4 bg-green-700 left-0"></div>
                            <h3 class="focus:outline-none py-6 leading-4 text-gray-800 dark:text-gray-100 font-normal text-base font-bold">I</h3>
                            <div class="flex flex-col items-end">
                                <h2 class="focus:outline-none text-green-700 dark:text-gray-100 text-xl leading-normal font-bold">{{ getQuestionScoringDISC(questions,'I') }}</h2>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded flex items-center justify-between px-6 py-4 relative shadow">
                            <div class="absolute w-2 h-4 bg-green-700 left-0"></div>
                            <h3 class="focus:outline-none py-6 leading-4 text-gray-800 dark:text-gray-100 font-normal text-base font-bold">S</h3>
                            <div class="flex flex-col items-end">
                                <h2 class="focus:outline-none text-green-700 dark:text-gray-100 text-xl leading-normal font-bold">{{ getQuestionScoringDISC(questions,'S') }}</h2>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded flex items-center justify-between px-6 py-4 relative shadow">
                            <div class="absolute w-2 h-4 bg-green-700 left-0"></div>
                            <h3 class="focus:outline-none py-6 leading-4 text-gray-800 dark:text-gray-100 font-normal text-base font-bold">C</h3>
                            <div class="flex flex-col items-end">
                                <h2 class="focus:outline-none text-green-700 dark:text-gray-100 text-xl leading-normal font-bold">{{ getQuestionScoringDISC(questions,'C') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="type.name == 'Personality'" class="personality-content-wrap grid sm:grid-cols-1 md:grid-cols-1 gap-8 py-8">
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
                
                <div v-if="type.name ==='Personality'" class="py-4">
                </div>
                <div v-if="type.name !=='Personality'" class="grid sm:grid-cols-1 md:grid-cols-3 gap-8 py-8">
                    <div class="bg-white dark:bg-gray-800 rounded flex flex-col items-center justify-center p-5 relative shadow">
                        <h2 class="focus:outline-none text-gray-800 text-base leading-normal font-semibold mb-6">
                            Total {{ session.results.total_questions }} Questions
                        </h2>
                        <div class="flex gap-4 sm:justify-center items-center">
                            <div class="w-28">
                                <doughnut-chart id="status" :key="'status'" :chart-data="statusChartData" :element-text="totalAnswered"/>
                            </div>
                            <div class="flex flex-col gap-2 justify-center">
                                <div class="flex gap-2 items-center text-xs">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ session.results.correct_answered_questions }} Correct
                                </div>
                                <div class="flex gap-2 items-center text-xs">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ session.results.wrong_answered_questions }} Wrong
                                </div>
                                <div class="flex gap-2 items-center text-xs">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ session.results.unanswered_questions }} Unanswered
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded flex flex-col items-center justify-center p-5 relative shadow">
                        <h2 class="focus:outline-none text-gray-800 text-base leading-normal font-semibold mb-6">
                            Total {{ session.results.total_duration }} Minutes
                        </h2>
                        <div class="flex gap-4 sm:justify-center items-center">
                            <div class="w-28">
                                <doughnut-chart id="time_spent" :key="'time_spent'" :chart-data="timeSpentChartData" :element-text="totalTimeSpent"/>
                            </div>
                            <div class="flex flex-col gap-2 justify-center">
                                <div class="flex gap-2 items-center text-xs">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ session.results.time_taken_for_correct_answered.detailed_readable }} on Correct
                                </div>
                                <div class="flex gap-2 items-center text-xs">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ session.results.time_taken_for_wrong_answered.detailed_readable }} on Wrong
                                </div>
                                <div class="flex gap-2 items-center text-xs">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ session.results.time_taken_for_other.detailed_readable }} on Other
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded flex flex-col items-center justify-center p-5 relative shadow">
                        <h2 class="focus:outline-none text-gray-800 text-base leading-normal font-semibold mb-6">
                            Total Scored Marks
                        </h2>
                        <div class="w-full flex gap-4 sm:justify-center items-center">
                            <table class="w-full table-auto">
                                <tbody>
                                    <tr>
                                        <td class="border border-emerald-500 px-4 py-2 text-emerald-600 text-sm">Marks Earned</td>
                                        <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-medium text-sm text-right">{{ session.results.marks_earned }}</td>
                                    </tr>
                                    <tr class="bg-emerald-200">
                                        <td class="border border-emerald-500 px-4 py-2 text-emerald-600 text-sm">Negative Marks</td>
                                        <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-medium text-sm text-right">-{{ session.results.marks_deducted }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-emerald-500 px-4 py-2 text-emerald-600 text-sm">Total Marks</td>
                                        <td class="border border-emerald-500 px-4 py-2 text-emerald-600 font-medium text-sm text-right">{{ session.results.score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div v-if="type.name ==='Quiz'" class="mb-6">
                    <h2 class="text-lg font-semibold leading-tight text-gray-800 mb-6">Skill Results</h2>
                    <div class="w-full card">
                        <div class="w-full card-body lg:flex flex-wrap">
                            <!-- Table -->
                            <div class="w-full bg-white rounded-sm grid sm:grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-3">
                                    <div class="overflow-x-auto">
                                        <table class="table-auto w-full">
                                            <thead class="text-sm font-semibold uppercase bg-gray-50">
                                                <tr>
                                                    <th class="p-4 whitespace-nowrap">
                                                        <div class="font-semibold text-left font-bold">Skill</div>
                                                    </th>
                                                    <th class="p-4 whitespace-nowrap">
                                                        <div class="font-semibold text-center font-bold">Score</div>
                                                    </th>
                                                    <th class="p-4 width-auto whitespace-nowrap">
                                                        <div class="font-semibold text-center font-bold">Total marks</div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-sm divide-y divide-gray-100">
                                                <tr v-for="(skill_data, index) in skillwiseresult">
                                                    <td class="p-3 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            {{skill_data.skill}}
                                                        </div>
                                                    </td>
                                                    <td class="p-3 whitespace-nowrap">
                                                        <div class="text-center"> {{skill_data.correctans}}/{{skill_data.totalques}}</div>
                                                    </td>
                                                    <td class="p-3 width-auto whitespace-nowrap">
                                                        <div class="text-center font-medium ">{{skill_data.marksobtained}}/{{skill_data.totalmarks}}</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- New chart -->
                                <div class="p-3" v-if="datacollection">
                                    <bar-chart :chart-data="datacollection"></bar-chart>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-semibold leading-tight text-gray-800 mb-6">User Answers</h2>
                    <div class="w-full card">
                        <div class="w-full card-body lg:flex flex-wrap">
                            <div class="py-4 lg:w-1/3 w-full md:pr-6">
                                <ul v-if="loading">
                                    <li>
                                        <NavigationQuestionCardShimmer></NavigationQuestionCardShimmer>
                                    </li>
                                    <li>
                                        <NavigationQuestionCardShimmer></NavigationQuestionCardShimmer>
                                    </li>
                                    <li>
                                        <NavigationQuestionCardShimmer></NavigationQuestionCardShimmer>
                                    </li>
                                    <li>
                                        <NavigationQuestionCardShimmer></NavigationQuestionCardShimmer>
                                    </li>
                                </ul>
                                <ul v-else class="my-6 flex flex-wrap justify-center items-center gap-2">
                                    <li v-for="(question, index) in questions" :key="question.code" @click="jumpToQuestion(index)">
                                        <practice-question-chip :sno="index+1"
                                                                :is_correct="question.is_correct" :status="question.status"
                                                                :active="current_question === index"></practice-question-chip>
                                    </li>
                                </ul>
                            </div>
                            <div class="py-4 lg:w-2/3 w-full md:pl-6 sm:border-l border-gray-200">
                                <div class="w-full sm:w-2/3" v-if="loading">
                                    <div class="card card-body">
                                        <PracticeQuestionShimmer class="w-full"></PracticeQuestionShimmer>
                                    </div>
                                </div>
                                <div v-else>
                                    <div v-for="(question, index) in questions" :key="question.id">
                                        <div :id="question.code" v-show="current_question === index">
                                            <div class="flex justify-between items-center mb-6">
                                                <div class="font-mono px-2 py-1 inline-block bg-qt-option text-white rounded text-sm mb-2">
                                                    Time Spent: {{ question.time_taken.detailed_readable }}
                                                </div>
                                                <div v-if="question.status === 'answered'" :class="question.is_correct ? 'bg-green-400' : 'bg-red-400'"
                                                     class="font-mono px-2 py-1 inline-block text-white rounded text-sm mb-2">
                                                    <span v-if="question.is_correct">+{{ question.marks_earned }} Marks Earned</span>
                                                    <span v-if="!question.is_correct">-{{ question.marks_deducted }} Marks Deducted</span>
                                                </div>
                                                <div v-else class="font-mono px-2 py-1 inline-block bg-gray-100 text-gray-600 rounded text-sm mb-2">
                                                    Not Answered
                                                </div>
                                            </div>
                                            <practice-question-card :key="question.card" :question="question"
                                                                    :sno="index+1"
                                                                    :total-questions="quiz.total_questions"></practice-question-card>
                                            <div class="mt-4"
                                                 v-if="question.questionType === 'MSA' || question.questionType === 'TOF'">
                                                <MSASolution :key="question.code" :parent-qid="question.code"
                                                             :is-correct="question.is_correct"
                                                             :status="question.status"
                                                             :parent-options="question.options"
                                                             :parent-answer="question.user_answer"
                                                             :correct-answer="question.ca">
                                                </MSASolution>
                                            </div>
                                            <div class="mt-4"
                                                 v-if="question.questionType === 'MMA'">
                                                <MMASolution :key="question.code" :parent-qid="question.code"
                                                             :is-correct="question.is_correct"
                                                             :status="question.status"
                                                             :parent-options="question.options"
                                                             :parent-answer="question.user_answer"
                                                             :correct-answer="question.ca">
                                                </MMASolution>
                                            </div>
                                            <div class="mt-4" v-if="question.questionType === 'MTF'">
                                                <MTFSolution :key="question.code" :parentQid="question.code"
                                                             :is-correct="question.is_correct"
                                                             :status="question.status"
                                                             :parent-options="question.options"
                                                             :parent-answer="question.user_answer"
                                                             :correct-answer="question.ca">
                                                </MTFSolution>
                                            </div>
                                            <div class="mt-4" v-if="question.questionType === 'ORD'">
                                                <ORDSolution :key="question.code" :parentQid="question.code"
                                                             :is-correct="question.is_correct"
                                                             :status="question.status"
                                                             :parent-options="question.options"
                                                             :parent-answer="question.user_answer"
                                                             :correct-answer="question.ca">
                                                </ORDSolution>
                                            </div>
                                            <div class="mt-4" v-if="question.questionType === 'FIB'">
                                                <FIBSolution :key="question.code" :parentQid="question.code"
                                                             :is-correct="question.is_correct"
                                                             :status="question.status"
                                                             :parent-options="question.options"
                                                             :parent-answer="question.user_answer"
                                                             :correct-answer="question.ca">
                                                </FIBSolution>
                                            </div>
                                            <div class="mt-4" v-if="question.questionType === 'SAQ'">
                                                <SAQSolution :key="question.code" :parentQid="question.code"
                                                             :is-correct="question.is_correct"
                                                             :status="question.status"
                                                             :parent-options="question.options"
                                                             :parent-answer="question.user_answer"
                                                             :correct-answer="question.ca">
                                                </SAQSolution>
                                            </div>
                                            <div class="mt-4">
                                                <practice-solution-card :solution="question.solution"></practice-solution-card>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </admin-layout>
</template>

<script>
    import AdminLayout from '@/Layouts/AdminLayout'
    import DoughnutChart from "@/Charts/DoughnutChart";
    import SkillChart from "@/Charts/SkillChart";
    import RewardsBadge from "@/Components/RewardsBadge";
    import MSASolution from "@/Components/Questions/Solutions/MSASolution";
    import PracticeQuestionCard from "@/Components/Cards/PracticeQuestionCard";
    import PracticeQuestionChip from "@/Components/Cards/PracticeQuestionChip";
    import NavigationQuestionCardShimmer from "@/Components/Shimmers/NavigationQuestionCardShimmer";
    import PracticeQuestionShimmer from "@/Components/Shimmers/PracticeQuestionShimmer";
    import PracticeSolutionCard from "@/Components/Cards/PracticeSolutionCard";
    import MMASolution from "@/Components/Questions/Solutions/MMASolution";
    import MTFSolution from "@/Components/Questions/Solutions/MTFSolution";
    import ORDSolution from "@/Components/Questions/Solutions/ORDSolution";
    import FIBSolution from "@/Components/Questions/Solutions/FIBSolution";
    import SAQSolution from "@/Components/Questions/Solutions/SAQSolution";
    import BarChart from '@/Charts/BarChart.js';
    export default {
        components: {
            SAQSolution,
            FIBSolution,
            ORDSolution,
            MTFSolution,
            MSASolution,
            MMASolution,
            AdminLayout,
            DoughnutChart,
            RewardsBadge,
            PracticeQuestionCard,
            PracticeQuestionChip,
            NavigationQuestionCardShimmer,
            PracticeQuestionShimmer,
            PracticeSolutionCard,
            BarChart
        },
        props: {
            quiz: Object,
            type: Object,
            session: Object,
            skillwiseresult: Object,
            skillwisegraph_result: Object,
        },
        data() {
            return {
                loading: false,
                questions: [],
                current_question: 0,
                scores: [],
                max_disc: [],
                statusChartData: {
                    labels: ['Correct', 'Wrong', 'Unanswered'],
                    datasets: [{
                        label: 'Status',
                        data: [
                            this.session.results.correct_answered_questions,
                            this.session.results.wrong_answered_questions,
                            this.session.results.unanswered_questions
                        ],
                        backgroundColor: [
                            'rgb(52, 211, 153)',
                            'rgb(248, 113, 113)',
                            'rgb(156, 163, 175)'
                        ],
                        hoverOffset: 4
                    }]
                },
                timeSpentChartData: {
                    labels: ['Correct', 'Wrong', 'Other'],
                    datasets: [{
                        label: 'Time Spent',
                        data: [
                            this.session.results.time_taken_for_correct_answered.seconds,
                            this.session.results.time_taken_for_wrong_answered.seconds,
                            this.session.results.time_taken_for_other.seconds],
                        backgroundColor: [
                            'rgb(52, 211, 153)',
                            'rgb(248, 113, 113)',
                            'rgb(156, 163, 175)'
                        ],
                        hoverOffset: 4
                    }]
                },
                datacollection: null
            }
        },
        metaInfo() {
            return {
                title: this.title
            }
        },
        methods: {
            fillData () {
                let _this = this;
                this.datacollection = {
                    labels: _this.skillwisegraph_result.labels,
                    datasets: [
                        {
                          label: 'Skill Scores',
                          backgroundColor: '#df6368',
                          data:_this.skillwisegraph_result.data
                        }
                    ]
                }
            },
            jumpToQuestion(questionID) {
                this.current_question = questionID;
            },
            fetchQuestions() {
                let _this = this;
                _this.loading = true;
                axios.get(route('fetch_quiz_session_solutions', {
                    quiz: this.quiz.slug,
                    session: this.session.id
                }))
                .then(function (response) {
                    let data = response.data;
                    _this.questions = data.questions;
                    _this.get_max_score_disc();
                     
                })
                .catch(function (error) {
                    _this.loading = false;
                })
                .then(function () {
                    _this.loading = false;
                });

            },
            get_max_score_disc() {
                let _this = this;
                const maxScoreDisc = ["D","I","S","C"];
                maxScoreDisc.map((disc,index) => {
                    _this.scores[disc] = _this.getQuestionScoringDISC(_this.questions,disc);
                });

                var max_array = [];
                for (const [key, value] of Object.entries(_this.scores)) {
                    max_array.push(value)
                }
                const max = max_array.reduce((a, b) => { return Math.max(a, b) });

                var get_max = 0;
                var get_max_disc = [];
                var remove_dubble = [];
                for (const [key, value] of Object.entries(_this.scores)) {
                    if((value == max || value == this.nextBiggest(max_array)) && !remove_dubble.includes(value) && value > 0){
                        get_max_disc.push(key);
                        remove_dubble.push(value); 
                    }
                }
                
                this.quiz.get_max_disc = get_max_disc;
                this.max_disc = get_max_disc; 
            },
            nextBiggest(arr) {
              let max = -Infinity, result = -Infinity;

              for (const value of arr) {
                const nr = Number(value)

                if (nr > max) {
                  [result, max] = [max, nr] // save previous max
                } else if (nr < max && nr > result) {
                  result = nr; // new second biggest
                }
              }

              return result;
            },
            getQuestionScoringDISC(questions,indexNumber) {
                const discTable = [
                        {"D":"B","I":"D","S":"A","C":"C"},
                        {"D":"A","I":"C","S":"D","C":"B"},
                        {"D":"C","I":"B","S":"A","C":"D"},
                        {"D":"A","I":"D","S":"C","C":"B"},
                        {"D":"D","I":"B","S":"C","C":"A"},
                        {"D":"B","I":"A","S":"D","C":"C"},
                        {"D":"C","I":"D","S":"B","C":"A"},
                        {"D":"B","I":"A","S":"D","C":"C"},
                        {"D":"D","I":"A","S":"C","C":"B"},
                        {"D":"C","I":"B","S":"D","C":"A"},
                        {"D":"A","I":"D","S":"C","C":"B"},
                        {"D":"D","I":"C","S":"A","C":"B"},
                        {"D":"B","I":"A","S":"D","C":"C"},
                        {"D":"C","I":"D","S":"B","C":"A"},
                        {"D":"D","I":"A","S":"C","C":"B"},
                        {"D":"A","I":"B","S":"C","C":"D"},
                        {"D":"B","I":"C","S":"D","C":"A"},
                        {"D":"C","I":"A","S":"B","C":"D"},
                        {"D":"D","I":"B","S":"C","C":"A"},
                        {"D":"A","I":"D","S":"C","C":"B"},
                        {"D":"A","I":"B","S":"C","C":"D"},
                        {"D":"D","I":"C","S":"B","C":"A"},
                        {"D":"D","I":"B","S":"A","C":"C"},
                        {"D":"D","I":"C","S":"A","C":"B"}
                    ]

              // /console.log(discTable);      
              let score = 0;

              const discAlphabet = ["A","B","C","D"];

                questions.map((question,index) => {

                    if(discTable[index][indexNumber] === discAlphabet[question.user_answer - 1]) {
                        score ++
                    }

                });

                return score;
            }
        },
        computed: {
            title() {
                return this.quiz.title + ' Score Report - ' + this.$page.props.general.app_name;
            },
            totalAnswered() {
                return this.session.results.answered_questions+'/'+this.session.results.total_questions+' Answered';
            },
            totalTimeSpent() {
                return this.session.results.total_time_taken.detailed_readable +' Spent';
            }
        },
        mounted() {
            this.fetchQuestions();
            this.fillData()
        }
    }
</script>
