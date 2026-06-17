<script setup>
const props = defineProps({
    report: Object
})
</script>

<template>
    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-auto text-sm">
{{ JSON.stringify(props.report, null, 2) }}
    </pre>
</template>