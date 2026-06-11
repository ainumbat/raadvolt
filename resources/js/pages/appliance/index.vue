<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
    appliances: Array
})

const deleteAppliance = (id) => {
    if (confirm('Delete this appliance?')) {
        router.delete(`/appliances/${id}`)
    }
}
</script>

<template>
<div class="p-6">
    <div class="flex justify-between mb-4">
        <h1 class="text-xl font-bold">Appliances</h1>

        <Link href="/appliances/create" class="bg-green-600 text-white px-4 py-2 rounded">
            Add Appliance
        </Link>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="a in appliances" :key="a.id" class="p-4 border rounded-lg">
            <h2 class="font-semibold">{{ a.name }}</h2>
            <p>{{ a.watts }} W</p>
            <p class="text-sm text-gray-500">{{ a.type }}</p>

            <div class="flex gap-2 mt-3">
                <Link :href="`/appliances/${a.id}/edit`" class="text-blue-600">Edit</Link>
                <button @click="deleteAppliance(a.id)" class="text-red-600">Delete</button>
            </div>
        </div>
    </div>
</div>
</template>