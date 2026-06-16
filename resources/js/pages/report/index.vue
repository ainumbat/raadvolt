<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    reports: Object
})

const selectedReport = ref(null)
const showModal = ref(false)

const viewItems = (report) => {
    selectedReport.value = report
    showModal.value = true
}
</script>

<template>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Name</th>
                        <th class="p-3 text-left">WhatsApp</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Wire Length (meters)</th>
                        <th class="p-3 text-left">Total Load</th>
                        <th class="p-3 text-center">Appliances</th>
                        <th class="p-3 text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="report in reports.data"
                        :key="report.id"
                        class="border-b"
                    >
                        <td class="p-3">
                            {{ report.formatted_date }}
                        </td>

                        <td class="p-3">
                            {{ report.name }}
                        </td>

                        <td class="p-3">
                            {{ report.whatsapp }}
                        </td>

                        <td class="p-3">
                            {{ report.email }}
                        </td>

                        <td class="p-3">
                            {{ report.wire_length/3 }} 
                        </td>

                        <td class="p-3">
                            {{ report.total_load }} W
                        </td>

                        <td class="p-3 text-center">
                            {{ report.items.length }}
                        </td>

                        <td class="p-3 text-center">
                            <button
                                @click="viewItems(report)"
                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-700 cursor-pointer"
                            >
                                View Appliances
                            </button>

                            <Link
                                :href="`/solar/${report.id}/premium_report`"
                                class="inline-flex items-center px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700 cursor-pointer"
                            >
                                View Report
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="showModal"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-xl w-full max-w-4xl p-6">

                <div class="flex justify-between mb-4">
                    <h2 class="text-xl font-bold">
                        Appliances
                    </h2>

                    <button
                        @click="showModal = false"
                        class="text-red-500"
                    >
                        ✕
                    </button>
                </div>

                <div class="mb-4">
                    <h3 class="font-semibold">
                        {{ selectedReport?.name }}
                    </h3>

                    <p>
                        {{ selectedReport?.whatsapp }}
                    </p>

                    <p>
                        {{ selectedReport?.email }}
                    </p>
                </div>

                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 text-left">
                                Appliance ID
                            </th>

                            <th class="p-2 text-center">
                                Watts
                            </th>

                            <th class="p-2 text-center">
                                Qty
                            </th>

                            <th class="p-2 text-center">
                                Type
                            </th>

                            <th class="p-2 text-center">
                                Load
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="item in selectedReport?.items"
                            :key="item.id"
                            class="border-b"
                        >
                            <td class="p-2">
                                {{ item.appliance.name }}
                            </td>

                            <td class="p-2 text-center">
                                {{ item.watts }}
                            </td>

                            <td class="p-2 text-center">
                                {{ item.quantity }}
                            </td>

                            <td class="p-2 text-center">
                                <span
                                    class="px-2 py-1 rounded text-xs"
                                    :class="item.type === 'essential'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-yellow-100 text-yellow-700'"
                                >
                                    {{ item.type }}
                                </span>
                            </td>

                            <td class="p-2 text-center">
                                {{ item.watts * item.quantity }} W
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>