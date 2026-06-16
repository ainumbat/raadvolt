<script setup>
import { Link, router } from '@inertiajs/vue3'
import * as LucideIcons from 'lucide-vue-next'

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

        <Link href="/appliances/create" title="Add Appliance" class="bg-green-600 text-white px-4 py-2 rounded">
            <component
                :is="LucideIcons.Plus || LucideIcons.CircleQuestionMark"
                size="18"
            /> 
        </Link>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="overflow-x-auto bg-white rounded-lg border">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                    Icon
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                    Name
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                    Watts
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                    Daily Runtime
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                    Load Type
                </th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                    Actions
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            <tr
                v-for="a in appliances"
                :key="a.id"
                class="hover:bg-gray-50"
            >
                <td class="px-4 py-3">
                    <component
                        :is="LucideIcons[a.icon] || LucideIcons.CircleQuestionMark"
                        size="22"
                        class="text-green-600"
                    />                    
                </td>

                <td class="px-4 py-3 font-medium">{{ a.name }}</td>

                <td class="px-4 py-3">{{ a.watts }} W</td>
                
                <td class="px-4 py-3">{{ a.daily_runtime }} Hours</td>

                <td class="px-4 py-3">
                    <span
                        :class="[
                            'px-2 py-1 rounded-full text-xs font-medium',
                            a.type === 'essential'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-yellow-100 text-yellow-700'
                        ]"
                    >
                        {{ a.type }}
                    </span>
                </td>

                <td class="px-4 py-3">
                    <div class="flex justify-end gap-3">
                        <Link
                            :href="`/appliances/${a.id}/edit`"
                            class="text-blue-600 hover:text-blue-800"
                        >
                            <component
                                :is="LucideIcons.SquarePen || LucideIcons.CircleQuestionMark"
                                size="18"
                                class="text-blue-600"
                            />
                        </Link>

                        <button
                            @click="deleteAppliance(a.id)"
                            class="text-red-600 hover:text-red-800"
                        >
                            <component
                                :is="LucideIcons.Trash2 || LucideIcons.CircleQuestionMark"
                                size="18"
                                class="text-red-600"
                            />
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
    </div>
</div>
</template>