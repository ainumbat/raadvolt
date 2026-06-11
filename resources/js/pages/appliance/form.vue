<script setup>
import { useForm } from '@inertiajs/vue3'
import * as LucideIcons from 'lucide-vue-next'

const props = defineProps({
    appliance: Object
})

const form = useForm({
    name: props.appliance?.name || '',
    watts: props.appliance?.watts || '',
    type: props.appliance?.type || 'essential',
    icon: props.appliance?.icon || ''
})

const submit = () => {
    if (props.appliance) {
        form.put(`/appliances/${props.appliance.id}`)
    } else {
        form.post('/appliances')
    }
}
</script>

<template>
<div class="p-6 max-w-xl">
    <h1 class="text-xl font-bold mb-4">
        {{ appliance ? 'Edit' : 'Add' }} Appliance
    </h1>

    <form @submit.prevent="submit" class="space-y-4">
        <label class="text-sm text-gray-500 block mb-1">Appliance Name</label>
        <input v-model="form.name" placeholder="Name" class="w-full border p-2 rounded" />

        <label class="text-sm text-gray-500 block mb-1">Power Consumption (Watts)</label>
        <input v-model="form.watts" type="number" placeholder="Watts" class="w-full border p-2 rounded" />

        <label class="text-sm text-gray-500 block mb-1">Load Type</label>
        <select v-model="form.type" class="w-full border p-2 rounded">
            <option value="essential">Essential</option>
            <option value="generator">Generator</option>
        </select>

        <label class="text-sm text-gray-500 block mb-1">Icon Name</label>
        <div class="flex gap-2">
            <input v-model="form.icon" placeholder="Icon name" class="w-full border p-2 rounded" />
            <div class="p-2">
                <component :is="LucideIcons[form.icon] || LucideIcons.CircleQuestionMark" size="22" />
            </div>
        </div>

        <button class="bg-green-600 text-white px-4 py-2 rounded">
            Save
        </button>
    </form>
</div>
</template>