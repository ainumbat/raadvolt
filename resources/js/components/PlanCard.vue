<script setup>
import MetricCard from './MetricCard.vue'
import {
    Sun,
    Battery,
    Wallet,
    TrendingUp
} from 'lucide-vue-next'

defineProps({
    plan: Object,
    color: {
        type: String,
        default: 'green'
    }
})
</script>

<template>
    <div
        class="bg-white rounded-3xl shadow-lg border overflow-hidden">

        <div
            class="bg-gradient-to-r from-green-700 to-green-500 p-5 text-white">

            <div class="flex justify-between">

                <div>
                    <h2 class="text-xl font-bold">
                        {{ plan.title }}
                    </h2>

                    <p class="opacity-90 mt-1">
                        {{ plan.description }}
                    </p>
                </div>

                <div class="text-right">

                    <div class="text-sm opacity-80">
                        ROI Time
                    </div>

                    <div class="text-3xl font-bold">
                        {{ plan.roi_time }}
                    </div>

                </div>

            </div>

        </div>

        <div class="p-6">

            <div class="grid md:grid-cols-4 gap-4">

                <MetricCard
                    title="Solar Capacity"
                    :value="`${plan.solar_kw} kW`"
                />

                <MetricCard
                    title="Inverter"
                    :value="`${plan.inverter_kw} kW`"
                />

                <MetricCard
                    title="Monthly Savings"
                    :value="`Rs ${plan.monthly_savings}`"
                />

                <MetricCard
                    title="Estimated Cost"
                    :value="`Rs ${plan.estimated_cost}`"
                />

            </div>

            <div
                v-if="plan.limitations"
                class="mt-6">

                <h4 class="font-semibold mb-2">
                    Limitations
                </h4>

                <ul
                    class="list-disc pl-5 text-sm text-gray-600">

                    <li
                        v-for="item in plan.limitations">
                        {{ item }}
                    </li>

                </ul>

            </div>

        </div>

    </div>
</template>