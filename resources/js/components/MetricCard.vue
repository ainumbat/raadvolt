<script setup>
defineProps({
    title: String,
    value: [String, Number]
})
</script>

<template>
    <div
        class="bg-gray-50 border rounded-xl p-4">

        <div class="text-sm text-gray-500">
            {{ title }}
        </div>

        <div
            class="text-xl font-bold text-gray-900 mt-1">

            {{ value }}

        </div>

    </div>
</template>