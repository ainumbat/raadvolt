<script setup>
import { useForm } from '@inertiajs/vue3'

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
        <input v-model="form.name" placeholder="Name" class="w-full border p-2 rounded" />

        <input v-model="form.watts" type="number" placeholder="Watts" class="w-full border p-2 rounded" />

        <select v-model="form.type" class="w-full border p-2 rounded">
            <option value="essential">Essential</option>
            <option value="generator">Generator</option>
        </select>

        <input v-model="form.icon" placeholder="Icon name" class="w-full border p-2 rounded" />

        <button class="bg-green-600 text-white px-4 py-2 rounded">
            Save
        </button>
    </form>
</div>
</template>