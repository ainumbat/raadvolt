<script setup>
const props = defineProps({
    title: String,
    value: String,
    subtitle: String,
    icon: Object,
    iconClass: {
        type: String,
        default: 'text-green-600'
    }
})
</script>

<template>
    <div
        class="bg-white rounded-2xl border shadow-sm p-5 hover:shadow-md transition">

        <div class="flex items-start justify-between">

            <div>
                <p class="text-sm text-gray-500">
                    {{ title }}
                </p>

                <h3 class="text-3xl font-bold mt-2">
                    {{ value }}
                </h3>

                <p
                    v-if="subtitle"
                    class="text-sm text-gray-500 mt-2">
                    {{ subtitle }}
                </p>
            </div>

            <component
                :is="icon"
                class="w-10 h-10"
                :class="iconClass"
            />

        </div>
    </div>
</template>

