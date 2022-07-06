<template>
    <div class="w-full relative flex flex-col justify-between items-start rounded shadow bg-white py-6 px-6 hover:shadow-lg">
        <div class="flex items-center justify-center bg-green-100 rounded mb-1">
            <p class="font-mono text-xs leading-loose text-center text-green-700 px-2">{{ quiz.category }}</p>
        </div>
        <h1 class="text-lg mt-2 font-semibold text-gray-800 leading-5 py-2">{{ quiz.title }}</h1>
        <div class="my-4">
            <div class="flex items-center">
                <div>
                    <p class="text-lg font-semibold leading-tight text-green-600">{{ quiz.total_questions }}</p>
                    <p class="font-mono text-sm leading-3 mt-2 text-gray-500">Questions</p>
                </div>
                <div class="ml-11">
                    <p class="text-lg font-semibold leading-tight text-green-600">{{ quiz.total_duration }}</p>
                    <p class="font-mono text-sm leading-3 mt-2 text-gray-500">Minutes</p>
                </div>
            </div>
        </div>
        <div class="w-full flex items-center justify-between mt-4">
            <div class="flex items-center py-2">
            </div>
            <slot name="action"></slot>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'QuizCard',
        props: {
            quiz: Object
        }
    }
</script>
