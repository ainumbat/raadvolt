<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Download, Sun, Battery, Zap, TriangleAlert, Copy, Check } from 'lucide-vue-next'
import * as LucideIcons from 'lucide-vue-next'
import html2pdf from 'html2pdf.js'
import SummaryCard from '@/components/SummaryCard.vue'

const props = defineProps({
    report: Object
})

const downloadPdf = () => {
    html2pdf(document.getElementById('report'), {
        margin: 10,
        filename: `${props.report.customer.report_id}.pdf`,
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 2 },
        jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'portrait'
        }
    })
}

const viewMode = ref('pretty')

const copied = ref(false)

const copyJson = async () => {
    try {
        await navigator.clipboard.writeText(
            JSON.stringify(props.report.report_data, null, 2)
        )

        copied.value = true

        setTimeout(() => {
            copied.value = false
        }, 2000)
    } catch (err) {
        console.error(err)
    }
}

const form = useForm({
    report_data: JSON.stringify(props.report.report_data, null, 2)
})

const saveJson = () => {
    try {
        JSON.parse(form.report_data) // validate JSON

        form.put(`/reports/${props.report.id}/update_json/`)
    } catch (e) {
        alert('Invalid JSON')
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 py-8">
        <div class="flex gap-0">

            <button
                @click="viewMode = 'pretty'"
                :class="[
                    'px-4 py-2 rounded-lg font-medium',
                    viewMode === 'pretty'
                        ? 'bg-green-400 text-white'
                        : 'bg-gray-200 text-gray-700'
                ]"
            >
                Pretty View
            </button>

            <button
                @click="viewMode = 'raw'"
                :class="[
                    'px-4 py-2 rounded-lg font-medium',
                    viewMode === 'raw'
                        ? 'bg-green-400 text-white'
                        : 'bg-gray-200 text-gray-700'
                ]"
            >
                Raw JSON
            </button>

        </div>

        <!-- Raw JSON view -->
        <div v-if="viewMode === 'raw'">
            <div class="bg-slate-900 p-6 rounded-xl">
                <div class="flex justify-end gap-2 mb-3">
                    <button
                        @click="copyJson"
                        class="px-3 py-2 bg-gray-500 text-white rounded-md"
                    >
                        Copy
                    </button>

                    <button
                        @click="saveJson"
                        :disabled="form.processing"
                        class="px-3 py-2 bg-green-600 text-white rounded-md"
                    >
                        Save
                    </button>
                </div>

                <textarea
                    v-model="form.report_data"
                    class="w-full h-[80vh] bg-slate-900 text-green-400 font-mono text-xs border-0 focus:ring-0"
                />
            </div>
        </div>

        <!-- Pretty view -->
        <div v-if="viewMode === 'pretty'" id="report" class="max-w-6xl mx-auto bg-white shadow-xl rounded-xl overflow-hidden">

            <!-- HEADER -->
            <div
                class="bg-gradient-to-r from-white to-yellow-500 text-white p-8">

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-4">

                        <img
                            src="/images/raadvolt-logo.png"
                            class="h-30"
                        >

                        <div>
                            <h1 class="text-green-600 text-4xl font-bold">
                                RaadVolt AI
                            </h1>

                            <p class="text-green-600 opacity-90">
                                AI Powered Solar Assessment Report
                            </p>
                        </div>

                    </div>

                    <button
                        @click="downloadPdf"
                        class="bg-white text-green-700 px-4 py-2 rounded-lg flex items-center gap-2 cursor-pointer">

                        <component
                                :is="LucideIcons.Download || LucideIcons.Zap"
                                size="18"
                                class="text-blue-600"
                            />

                        Download PDF
                    </button>

                </div>
            </div>

            <!-- Client Information -->
            <div class="px-8 mt-5">

                <div class="bg-white rounded-3xl border border-green-100 shadow-sm overflow-hidden">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-700 to-green-500 px-6 py-4">

                        <div class="flex items-center gap-3">

                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                                <component
                                    :is="LucideIcons.UserRound"
                                    size="24"
                                    class="text-white"
                                />
                            </div>

                            <div>
                                <h2 class="text-xl font-bold text-white">
                                    Customer Information
                                </h2>

                                <p class="text-green-100 text-sm">
                                    Report Owner Details
                                </p>
                            </div>

                        </div>

                    </div>

                    <!-- Body -->
                    <div class="p-6">

                        <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-5">

                            <!-- Name -->
                            <div
                                class="bg-green-50 border border-green-100 rounded-2xl p-4">

                                <div class="flex items-center gap-3 mb-2">

                                    <div
                                        class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">

                                        <component
                                            :is="LucideIcons.User"
                                            size="18"
                                            class="text-green-600"
                                        />

                                    </div>

                                    <span class="text-gray-500 text-sm">
                                        Customer Name
                                    </span>

                                </div>

                                <div class="font-bold text-lg text-gray-900">
                                    {{ report.name }}
                                </div>

                            </div>

                            <!-- WhatsApp -->
                            <div
                                class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4">

                                <div class="flex items-center gap-3 mb-2">

                                    <div
                                        class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">

                                        <component
                                            :is="LucideIcons.MessageCircle"
                                            size="18"
                                            class="text-emerald-600"
                                        />

                                    </div>

                                    <span class="text-gray-500 text-sm">
                                        WhatsApp
                                    </span>

                                </div>

                                <div class="font-bold text-lg text-gray-900">
                                    {{ report.whatsapp }}
                                </div>

                            </div>

                            <!-- Email -->
                            <div
                                class="bg-blue-50 border border-blue-100 rounded-2xl p-4">

                                <div class="flex items-center gap-3 mb-2">

                                    <div
                                        class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">

                                        <component
                                            :is="LucideIcons.Mail"
                                            size="18"
                                            class="text-blue-600"
                                        />

                                    </div>

                                    <span class="text-gray-500 text-sm">
                                        Email Address
                                    </span>

                                </div>

                                <div class="font-bold text-lg text-gray-900 break-all">
                                    {{ report.email }}
                                </div>

                            </div>

                            <!-- Report ID -->
                            <div
                                class="bg-yellow-50 border border-yellow-100 rounded-2xl p-4">

                                <div class="flex items-center gap-3 mb-2">

                                    <div
                                        class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center">

                                        <component
                                            :is="LucideIcons.FileText"
                                            size="18"
                                            class="text-yellow-600"
                                        />

                                    </div>

                                    <span class="text-gray-500 text-sm">
                                        Report ID
                                    </span>

                                </div>

                                <div class="font-bold text-lg text-gray-900">
                                    {{ report.report_data.report_id }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Executive Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 px-8 mt-5 relative z-10">

                <!-- Total Load -->
                <div class="bg-white rounded-2xl border border-green-100 shadow-sm p-5 hover:shadow-lg transition">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Total Load
                            </p>

                            <h3 class="text-3xl font-bold text-gray-900 mt-2">
                                {{ report.report_data.summary.total_load_kw }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ report.report_data.summary.total_load }}
                            </p>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center">
                            <component
                                :is="LucideIcons.Zap"
                                size="28"
                                class="text-green-600"
                            />
                        </div>

                    </div>

                </div>

                <!-- Daily Usage -->
                <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-5 hover:shadow-lg transition">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Daily Usage
                            </p>

                            <h3 class="text-3xl font-bold text-blue-700 mt-2">
                                {{ report.report_data.summary.daily_energy_consumption_kwh }}
                                kWh
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Energy Consumption
                            </p>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                            <component
                                :is="LucideIcons.TrendingUp"
                                size="28"
                                class="text-blue-600"
                            />
                        </div>

                    </div>

                </div>

                <!-- Monthly Usage -->
                <div class="bg-white rounded-2xl border border-purple-100 shadow-sm p-5 hover:shadow-lg transition">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Monthly Usage
                            </p>

                            <h3 class="text-3xl font-bold text-purple-700 mt-2">
                                {{ report.report_data.summary.monthly_energy_consumption_kwh }}
                                kWh
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Monthly Consumption
                            </p>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center">
                            <component
                                :is="LucideIcons.CalendarRange"
                                size="28"
                                class="text-purple-600"
                            />
                        </div>

                    </div>

                </div>

                <!-- Monthly Bill -->
                <div class="bg-white rounded-2xl border border-orange-100 shadow-sm p-5 hover:shadow-lg transition">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Estimated Monthly Bill
                            </p>

                            <h3 class="text-3xl font-bold text-orange-700 mt-2">
                                Rs {{ report.report_data.summary.estimated_monthly_bill }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Current Electricity Cost
                            </p>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-orange-100 flex items-center justify-center">
                            <component
                                :is="LucideIcons.Wallet"
                                size="28"
                                class="text-orange-600"
                            />
                        </div>

                    </div>

                </div>

                <!-- Essential Load -->
                <div class="bg-white rounded-2xl border border-teal-100 shadow-sm p-5 hover:shadow-lg transition">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Essential Load
                            </p>

                            <h3 class="text-3xl font-bold text-teal-700 mt-2">
                                {{ report.report_data.summary.essential_load_kw }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ report.report_data.summary.essential_load }}
                            </p>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-teal-100 flex items-center justify-center">
                            <component
                                :is="LucideIcons.Fan"
                                size="28"
                                class="text-teal-600"
                            />
                        </div>

                    </div>

                </div>

                <!-- Generator Load -->
                <div class="bg-white rounded-2xl border border-yellow-100 shadow-sm p-5 hover:shadow-lg transition">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Generator Load
                            </p>

                            <h3 class="text-3xl font-bold text-yellow-700 mt-2">
                                {{ report.report_data.summary.generator_load_kw }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ report.report_data.summary.generator_load }}
                            </p>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-yellow-100 flex items-center justify-center">
                            <component
                                :is="LucideIcons.Factory"
                                size="28"
                                class="text-yellow-600"
                            />
                        </div>

                    </div>

                </div>

            </div>


            <!-- Appliances Cards -->
            <div class="grid md:grid-cols-2 gap-5 px-8 mt-5">

                <!-- ESSENTIAL LOAD -->
                <div class="bg-white border border-green-200 rounded-2xl shadow-sm overflow-hidden">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-600 to-green-500 text-white px-5 py-4 flex items-center justify-between">
                        <h3 class="font-bold text-lg">
                            Essential Appliances
                        </h3>

                        <span class="text-green-600 text-xs bg-white px-3 py-1 rounded-full">
                            Critical Load
                        </span>
                    </div>

                    <!-- Table Header -->
                    <div class="grid grid-cols-12 text-xs font-semibold text-gray-500 px-5 py-3 border-b bg-gray-50">
                        <div class="col-span-6">Appliance</div>
                        <div class="col-span-2 text-center">W</div>
                        <div class="col-span-2 text-center">Qty</div>
                        <div class="col-span-2 text-right">Total</div>
                    </div>

                    <!-- Items -->
                    <div class="divide-y">
                        <div
                            v-for="item in report.report_data.summary.essential_appliances"
                            class="grid grid-cols-12 px-5 py-3 hover:bg-green-50 transition">

                            <div class="col-span-6 font-medium text-gray-800">
                                {{ item.name }}
                            </div>

                            <div class="col-span-2 text-center text-gray-600">
                                {{ item.watts }}
                            </div>

                            <div class="col-span-2 text-center text-gray-600">
                                {{ item.quantity }}
                            </div>

                            <div class="col-span-2 text-right font-semibold text-green-700">
                                {{ item.watts * item.quantity }} W
                            </div>

                        </div>
                    </div>

                </div>

                <!-- GENERATOR LOAD -->
                <div class="bg-white border border-yellow-200 rounded-2xl shadow-sm overflow-hidden">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-400 text-white px-5 py-4 flex items-center justify-between">
                        <h3 class="font-bold text-lg">
                            Generator Appliances
                        </h3>

                        <span class="text-yellow-600 text-xs bg-white px-3 py-1 rounded-full">
                            Flexible Load
                        </span>
                    </div>

                    <!-- Table Header -->
                    <div class="grid grid-cols-12 text-xs font-semibold text-gray-500 px-5 py-3 border-b bg-gray-50">
                        <div class="col-span-6">Appliance</div>
                        <div class="col-span-2 text-center">W</div>
                        <div class="col-span-2 text-center">Qty</div>
                        <div class="col-span-2 text-right">Total</div>
                    </div>

                    <!-- Items -->
                    <div class="divide-y">
                        <div
                            v-for="item in report.report_data.summary.generator_appliances"
                            class="grid grid-cols-12 px-5 py-3 hover:bg-yellow-50 transition">

                            <div class="col-span-6 font-medium text-gray-800">
                                {{ item.name }}
                            </div>

                            <div class="col-span-2 text-center text-gray-600">
                                {{ item.watts }}
                            </div>

                            <div class="col-span-2 text-center text-gray-600">
                                {{ item.quantity }}
                            </div>

                            <div class="col-span-2 text-right font-semibold text-yellow-700">
                                {{ item.watts * item.quantity }} W
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <!-- Plan 1 -->
            <div class="px-8">
                <div class="bg-white rounded-3xl border border-green-200 shadow-lg overflow-hidden mt-5">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-700 to-green-500 text-white p-6">

                        <div class="flex justify-between items-start">

                            <div>
                                <div class="flex items-center gap-3">

                                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                                        <component
                                            :is="LucideIcons.SunMedium"
                                            size="24"
                                            class="text-white"
                                        />
                                    </div>

                                    <div>
                                        <h2 class="text-2xl font-bold">
                                            {{ report.report_data.plan_1.title }}
                                        </h2>

                                        <p class="text-green-100 mt-1">
                                            {{ report.report_data.plan_1.description }}
                                        </p>
                                    </div>

                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-green-100 text-sm">
                                    Return on Investment
                                </div>

                                <div class="text-3xl font-bold">
                                    {{ report.report_data.plan_1.roi_time }}
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Metrics -->
                    <div class="p-6">

                        <div class="grid md:grid-cols-4 gap-4">

                            <div class="bg-green-50 border border-green-100 rounded-2xl p-4">
                                <div class="flex items-center gap-2 text-green-700 mb-2">
                                    <component :is="LucideIcons.Sun" size="18" />
                                    <span class="text-sm">Solar Capacity</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    {{ report.report_data.plan_1.solar_kw }} kW
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                                <div class="flex items-center gap-2 text-blue-700 mb-2">
                                    <component :is="LucideIcons.Cpu" size="18" />
                                    <span class="text-sm">Inverter</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    {{ report.report_data.plan_1.inverter_kw }} kW
                                </div>
                            </div>

                            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4">
                                <div class="flex items-center gap-2 text-orange-700 mb-2">
                                    <component :is="LucideIcons.Wallet" size="18" />
                                    <span class="text-sm">Estimated Cost</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    Rs {{ report.report_data.plan_1.estimated_cost.toLocaleString() }}
                                </div>
                            </div>

                            <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4">
                                <div class="flex items-center gap-2 text-purple-700 mb-2">
                                    <component :is="LucideIcons.TrendingUp" size="18" />
                                    <span class="text-sm">Monthly Savings</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    Rs {{ report.report_data.plan_1.monthly_savings.toLocaleString() }}
                                </div>
                            </div>

                        </div>

                        <!-- System Details -->
                        <div class="grid md:grid-cols-2 gap-6 mt-6">

                            <!-- Recommended Equipment -->
                            <div class="bg-gray-50 rounded-2xl p-5">

                                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                                    <component :is="LucideIcons.Wrench" size="18" />
                                    Recommended Equipment
                                </h3>

                                <div class="space-y-3">

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Panel Type</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_1.recommended_equipment.panel_type }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Inverter Type</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_1.recommended_equipment.inverter_type }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Panel Count</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_1.recommended_panel_count }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Panel Wattage</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_1.recommended_panel_wattage }}W
                                        </span>
                                    </div>

                                </div>

                            </div>

                            <!-- Features -->
                            <div class="bg-gray-50 rounded-2xl p-5">

                                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                                    <component :is="LucideIcons.BadgeCheck" size="18" />
                                    System Features
                                </h3>

                                <div class="space-y-3">

                                    <div class="flex justify-between">
                                        <span>Battery Backup</span>

                                        <span
                                            class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-700">
                                            Not Available
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span>Net Metering</span>

                                        <span
                                            class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-700">
                                            Not Supported
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span>Future Upgrade</span>

                                        <span class="font-semibold text-green-700">
                                            Yes
                                        </span>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Supported Loads -->
                        <div class="grid md:grid-cols-2 gap-6 mt-6">

                            <div>

                                <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                                    <component :is="LucideIcons.Home" size="18" />
                                    Supported Essential Loads
                                </h3>

                                <div class="flex flex-wrap gap-2">

                                    <span
                                        v-for="item in report.report_data.plan_1.supported_loads"
                                        class="bg-green-100 text-green-700 px-3 py-2 rounded-full text-sm">

                                        {{ item }}

                                    </span>

                                </div>

                            </div>

                            <div>

                                <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                                    <component :is="LucideIcons.Sunrise" size="18" />
                                    Daytime Solar Loads
                                </h3>

                                <div class="flex flex-wrap gap-2">

                                    <span
                                        v-for="item in report.report_data.plan_1.daytime_supported_loads"
                                        class="bg-yellow-100 text-yellow-700 px-3 py-2 rounded-full text-sm">

                                        {{ item }}

                                    </span>

                                </div>

                            </div>

                        </div>

                        <!-- Limitations -->
                        <div class="mt-8 bg-red-50 border border-red-200 rounded-2xl p-5">

                            <h3 class="font-bold text-red-700 mb-4 flex items-center gap-2">
                                <component :is="LucideIcons.TriangleAlert" size="18" />
                                Important Limitations
                            </h3>

                            <ul class="space-y-2">

                                <li
                                    v-for="item in report.report_data.plan_1.limitations"
                                    class="flex items-start gap-2">

                                    <component
                                        :is="LucideIcons.CircleAlert"
                                        size="16"
                                        class="text-red-600 mt-1 flex-shrink-0"
                                    />

                                    <span>{{ item }}</span>

                                </li>

                            </ul>

                        </div>

                        <!-- Upgrade Path -->
                        <div
                            class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5">

                            <div class="flex items-center gap-3">

                                <component
                                    :is="LucideIcons.ArrowUpCircle"
                                    size="22"
                                    class="text-blue-600"
                                />

                                <div>

                                    <div class="font-bold text-blue-800">
                                        Future Upgrade Path
                                    </div>

                                    <div class="text-blue-700">
                                        {{ report.report_data.plan_1.future_upgrade_path }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- Plan 2 -->
            <div class="px-8">
                <div class="bg-white rounded-3xl border border-blue-200 shadow-lg overflow-hidden mt-5">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-700 to-cyan-500 text-white p-6">

                        <div class="flex justify-between items-start">

                            <div>

                                <div class="flex items-center gap-3">

                                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                                        <component
                                            :is="LucideIcons.BadgeDollarSign"
                                            size="24"
                                            class="text-white"
                                        />
                                    </div>

                                    <div>

                                        <h2 class="text-2xl font-bold">
                                            {{ report.report_data.plan_2.title }}
                                        </h2>

                                        <p class="text-blue-100 mt-1">
                                            {{ report.report_data.plan_2.description }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                            <div class="text-right">

                                <div class="text-blue-100 text-sm">
                                    Return on Investment
                                </div>

                                <div class="text-3xl font-bold">
                                    {{ report.report_data.plan_2.roi_time }}
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <!-- Main Metrics -->

                        <div class="grid md:grid-cols-4 gap-4">

                            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-blue-700 mb-2">
                                    <component :is="LucideIcons.Sun" size="18" />
                                    <span class="text-sm">Solar Capacity</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    {{ report.report_data.plan_2.solar_kw }} kW
                                </div>

                            </div>

                            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-indigo-700 mb-2">
                                    <component :is="LucideIcons.Cpu" size="18" />
                                    <span class="text-sm">Inverter</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    {{ report.report_data.plan_2.inverter_kw }} kW
                                </div>

                            </div>

                            <div class="bg-green-50 border border-green-100 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-green-700 mb-2">
                                    <component :is="LucideIcons.Wallet" size="18" />
                                    <span class="text-sm">Monthly Savings</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    Rs {{ report.report_data.plan_2.monthly_savings.toLocaleString() }}
                                </div>

                            </div>

                            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-orange-700 mb-2">
                                    <component :is="LucideIcons.Banknote" size="18" />
                                    <span class="text-sm">Estimated Cost</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    Rs {{ report.report_data.plan_2.estimated_cost.toLocaleString() }}
                                </div>

                            </div>

                        </div>

                        <!-- Net Metering Benefits -->

                        <div class="grid md:grid-cols-3 gap-4 mt-6">

                            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-3">

                                    <component
                                        :is="LucideIcons.ArrowUpRight"
                                        size="18"
                                        class="text-emerald-600"
                                    />

                                    <h3 class="font-semibold">
                                        Surplus Generation
                                    </h3>

                                </div>

                                <div class="text-3xl font-bold text-emerald-700">
                                    {{ report.report_data.plan_2.surplus_kw }} kW
                                </div>

                                <div class="text-sm text-gray-500 mt-2">
                                    Available for export to grid
                                </div>

                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-3">

                                    <component
                                        :is="LucideIcons.Activity"
                                        size="18"
                                        class="text-blue-600"
                                    />

                                    <h3 class="font-semibold">
                                        Export Units
                                    </h3>

                                </div>

                                <div class="text-3xl font-bold text-blue-700">
                                    {{ report.report_data.plan_2.monthly_export_units }}
                                </div>

                                <div class="text-sm text-gray-500 mt-2">
                                    Units exported monthly
                                </div>

                            </div>

                            <div class="bg-green-50 border border-green-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-3">

                                    <component
                                        :is="LucideIcons.Coins"
                                        size="18"
                                        class="text-green-600"
                                    />

                                    <h3 class="font-semibold">
                                        Export Income
                                    </h3>

                                </div>

                                <div class="text-3xl font-bold text-green-700">
                                    Rs {{ report.report_data.plan_2.estimated_income.toLocaleString() }}
                                </div>

                                <div class="text-sm text-gray-500 mt-2">
                                    Estimated monthly earnings
                                </div>

                            </div>

                        </div>

                        <!-- System Information -->

                        <div class="grid md:grid-cols-2 gap-6 mt-6">

                            <div class="bg-gray-50 rounded-2xl p-5">

                                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">

                                    <component
                                        :is="LucideIcons.Settings"
                                        size="18"
                                    />

                                    Recommended Hardware

                                </h3>

                                <div class="space-y-3">

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Panel Count</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_2.recommended_panel_count }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Panel Wattage</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_2.recommended_panel_wattage }}W
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">System Type</span>
                                        <span class="font-semibold">
                                            Ongrid Solar
                                        </span>
                                    </div>

                                </div>

                            </div>

                            <div class="bg-gray-50 rounded-2xl p-5">

                                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">

                                    <component
                                        :is="LucideIcons.BadgeCheck"
                                        size="18"
                                    />

                                    Features

                                </h3>

                                <div class="space-y-3">

                                    <div class="flex justify-between">

                                        <span>Net Metering</span>

                                        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700">
                                            Supported
                                        </span>

                                    </div>

                                    <div class="flex justify-between">

                                        <span>Battery Backup</span>

                                        <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-700">
                                            Not Available
                                        </span>

                                    </div>

                                    <div class="flex justify-between">

                                        <span>Grid Dependency</span>

                                        <span class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700">
                                            Required
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Limitations -->

                        <div class="mt-8 bg-red-50 border border-red-200 rounded-2xl p-5">

                            <h3 class="font-bold text-red-700 mb-4 flex items-center gap-2">

                                <component
                                    :is="LucideIcons.TriangleAlert"
                                    size="18"
                                />

                                Important Limitations

                            </h3>

                            <ul class="space-y-2">

                                <li
                                    v-for="item in report.report_data.plan_2.limitations"
                                    class="flex items-start gap-2">

                                    <component
                                        :is="LucideIcons.CircleAlert"
                                        size="16"
                                        class="text-red-600 mt-1 flex-shrink-0"
                                    />

                                    <span>
                                        {{ item }}
                                    </span>

                                </li>

                            </ul>

                        </div>

                        <!-- Upgrade Path -->

                        <div
                            class="mt-6 bg-gradient-to-r from-cyan-50 to-blue-50 border border-blue-200 rounded-2xl p-5">

                            <div class="flex items-center gap-3">

                                <component
                                    :is="LucideIcons.ArrowUpCircle"
                                    size="22"
                                    class="text-blue-600"
                                />

                                <div>

                                    <div class="font-bold text-blue-800">
                                        Future Upgrade Path
                                    </div>

                                    <div class="text-blue-700">
                                        {{ report.report_data.plan_2.future_upgrade_path }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>


            <!-- Plan 3 -->
            <div class="px-8">
                <div class="bg-white rounded-3xl border border-purple-200 shadow-lg overflow-hidden mt-8">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-purple-700 to-indigo-600 text-white p-6">

                        <div class="flex justify-between items-start">

                            <div>

                                <div class="flex items-center gap-3">

                                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                                        <component
                                            :is="LucideIcons.BatteryCharging"
                                            size="24"
                                            class="text-white"
                                        />
                                    </div>

                                    <div>

                                        <h2 class="text-2xl font-bold">
                                            {{ report.report_data.plan_3.title }}
                                        </h2>

                                        <p class="text-purple-100 mt-1">
                                            {{ report.report_data.plan_3.description }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                            <div class="text-right">

                                <div class="text-purple-100 text-sm">
                                    Return on Investment
                                </div>

                                <div class="text-3xl font-bold">
                                    {{ report.report_data.plan_3.roi_time }}
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <!-- Main Metrics -->

                        <div class="grid md:grid-cols-4 gap-4">

                            <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-yellow-700 mb-2">
                                    <component :is="LucideIcons.Sun" size="18" />
                                    <span class="text-sm">Solar Capacity</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    {{ report.report_data.plan_3.solar_kw }} kW
                                </div>

                            </div>

                            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-blue-700 mb-2">
                                    <component :is="LucideIcons.Cpu" size="18" />
                                    <span class="text-sm">Hybrid Inverter</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    {{ report.report_data.plan_3.inverter_kw }} kW
                                </div>

                            </div>

                            <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-purple-700 mb-2">
                                    <component :is="LucideIcons.Wallet" size="18" />
                                    <span class="text-sm">Estimated Cost</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    Rs {{ report.report_data.plan_3.estimated_cost.toLocaleString() }}
                                </div>

                            </div>

                            <div class="bg-green-50 border border-green-100 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-green-700 mb-2">
                                    <component :is="LucideIcons.TrendingUp" size="18" />
                                    <span class="text-sm">Monthly Savings</span>
                                </div>

                                <div class="text-2xl font-bold">
                                    Rs {{ report.report_data.plan_3.monthly_savings.toLocaleString() }}
                                </div>

                            </div>

                        </div>

                        <!-- Battery Information -->

                        <div class="grid md:grid-cols-4 gap-4 mt-6">

                            <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-2">
                                    <component
                                        :is="LucideIcons.Battery"
                                        size="18"
                                        class="text-purple-600"
                                    />
                                    <span class="text-sm">
                                        Battery Capacity
                                    </span>
                                </div>

                                <div class="text-3xl font-bold text-purple-700">
                                    {{ report.report_data.plan_3.battery_kwh }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    kWh Storage
                                </div>

                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-2">
                                    <component
                                        :is="LucideIcons.Gauge"
                                        size="18"
                                        class="text-blue-600"
                                    />
                                    <span class="text-sm">
                                        Battery Voltage
                                    </span>
                                </div>

                                <div class="text-3xl font-bold text-blue-700">
                                    {{ report.report_data.plan_3.battery_voltage }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    Volts
                                </div>

                            </div>

                            <div class="bg-green-50 border border-green-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-2">
                                    <component
                                        :is="LucideIcons.Activity"
                                        size="18"
                                        class="text-green-600"
                                    />
                                    <span class="text-sm">
                                        Battery AH
                                    </span>
                                </div>

                                <div class="text-3xl font-bold text-green-700">
                                    {{ report.report_data.plan_3.battery_ah }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    Amp Hours
                                </div>

                            </div>

                            <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-2">
                                    <component
                                        :is="LucideIcons.Clock3"
                                        size="18"
                                        class="text-orange-600"
                                    />
                                    <span class="text-sm">
                                        Backup Time
                                    </span>
                                </div>

                                <div class="text-3xl font-bold text-orange-700">
                                    {{ report.report_data.plan_3.backup_hours }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    Hours
                                </div>

                            </div>

                        </div>

                        <!-- Battery Technology -->

                        <div class="grid md:grid-cols-2 gap-6 mt-6">

                            <div class="bg-gray-50 rounded-2xl p-5">

                                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">

                                    <component
                                        :is="LucideIcons.BatteryCharging"
                                        size="18"
                                    />

                                    Battery Technology

                                </h3>

                                <div class="space-y-3">

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Battery Type</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_3.battery_type }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Life Cycles</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_3.battery_cycles.toLocaleString() }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Backup Load</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_3.supported_backup_load }}
                                        </span>
                                    </div>

                                </div>

                            </div>

                            <div class="bg-gray-50 rounded-2xl p-5">

                                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">

                                    <component
                                        :is="LucideIcons.Settings"
                                        size="18"
                                    />

                                    Recommended Hardware

                                </h3>

                                <div class="space-y-3">

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Panel Count</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_3.recommended_panel_count }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Panel Wattage</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_3.recommended_panel_wattage }}W
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">System Type</span>
                                        <span class="font-semibold">
                                            Hybrid Solar
                                        </span>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Advantages -->

                        <div class="mt-6 bg-green-50 border border-green-200 rounded-2xl p-5">

                            <h3 class="font-bold text-green-700 mb-4 flex items-center gap-2">

                                <component
                                    :is="LucideIcons.BadgeCheck"
                                    size="18"
                                />

                                Key Benefits

                            </h3>

                            <div class="grid md:grid-cols-2 gap-3">

                                <div class="flex items-center gap-2">
                                    <component :is="LucideIcons.CheckCircle2" size="16" class="text-green-600"/>
                                    <span>Battery Backup Available</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <component :is="LucideIcons.CheckCircle2" size="16" class="text-green-600"/>
                                    <span>Reduced Grid Dependency</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <component :is="LucideIcons.CheckCircle2" size="16" class="text-green-600"/>
                                    <span>Load Shedding Protection</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <component :is="LucideIcons.CheckCircle2" size="16" class="text-green-600"/>
                                    <span>Future Expandable</span>
                                </div>

                            </div>

                        </div>

                        <!-- Limitations -->

                        <div class="mt-6 bg-red-50 border border-red-200 rounded-2xl p-5">

                            <h3 class="font-bold text-red-700 mb-4 flex items-center gap-2">

                                <component
                                    :is="LucideIcons.TriangleAlert"
                                    size="18"
                                />

                                Important Limitations

                            </h3>

                            <ul class="space-y-2">

                                <li
                                    v-for="item in report.report_data.plan_3.limitations"
                                    class="flex items-start gap-2">

                                    <component
                                        :is="LucideIcons.CircleAlert"
                                        size="16"
                                        class="text-red-600 mt-1 flex-shrink-0"
                                    />

                                    <span>{{ item }}</span>

                                </li>

                            </ul>

                        </div>

                        <!-- Upgrade Path -->

                        <div class="mt-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-purple-200 rounded-2xl p-5">

                            <div class="flex items-center gap-3">

                                <component
                                    :is="LucideIcons.ArrowUpCircle"
                                    size="22"
                                    class="text-purple-600"
                                />

                                <div>

                                    <div class="font-bold text-purple-800">
                                        Future Upgrade Path
                                    </div>

                                    <div class="text-purple-700">
                                        {{ report.report_data.plan_3.future_upgrade_path }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- Plan 4 -->
            <div class="px-8">
                <div class="bg-white rounded-3xl border border-amber-200 shadow-xl overflow-hidden mt-8">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-amber-600 via-orange-500 to-yellow-500 text-white p-6">

                        <div class="flex justify-between items-start">

                            <div>

                                <div class="flex items-center gap-3">

                                    <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center">

                                        <component
                                            :is="LucideIcons.Crown"
                                            size="28"
                                            class="text-white"
                                        />

                                    </div>

                                    <div>

                                        <h2 class="text-2xl font-bold">
                                            {{ report.report_data.plan_4.title }}
                                        </h2>

                                        <p class="text-yellow-100 mt-1">
                                            {{ report.report_data.plan_4.description }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                            <div class="text-right">

                                <div class="text-yellow-100 text-sm">
                                    Return on Investment
                                </div>

                                <div class="text-3xl font-bold">
                                    {{ report.report_data.plan_4.roi_time }}
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <!-- Main Metrics -->

                        <div class="grid md:grid-cols-4 gap-4">

                            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-yellow-700 mb-2">
                                    <component :is="LucideIcons.Sun" size="18" />
                                    <span class="text-sm">Solar Capacity</span>
                                </div>

                                <div class="text-3xl font-bold">
                                    {{ report.report_data.plan_4.solar_kw }} kW
                                </div>

                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-blue-700 mb-2">
                                    <component :is="LucideIcons.Cpu" size="18" />
                                    <span class="text-sm">Offgrid Inverter</span>
                                </div>

                                <div class="text-3xl font-bold">
                                    {{ report.report_data.plan_4.inverter_kw }} kW
                                </div>

                            </div>

                            <div class="bg-green-50 border border-green-200 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-green-700 mb-2">
                                    <component :is="LucideIcons.TrendingUp" size="18" />
                                    <span class="text-sm">Monthly Savings</span>
                                </div>

                                <div class="text-3xl font-bold">
                                    Rs {{ report.report_data.plan_4.monthly_savings.toLocaleString() }}
                                </div>

                            </div>

                            <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4">

                                <div class="flex items-center gap-2 text-purple-700 mb-2">
                                    <component :is="LucideIcons.Banknote" size="18" />
                                    <span class="text-sm">Estimated Cost</span>
                                </div>

                                <div class="text-3xl font-bold">
                                    Rs {{ report.report_data.plan_4.estimated_cost.toLocaleString() }}
                                </div>

                            </div>

                        </div>

                        <!-- Independence Banner -->

                        <div class="mt-6 bg-gradient-to-r from-green-600 to-emerald-500 rounded-3xl p-6 text-white">

                            <div class="flex items-center gap-4">

                                <component
                                    :is="LucideIcons.ShieldCheck"
                                    size="40"
                                />

                                <div>

                                    <div class="text-sm opacity-90">
                                        Energy Independence
                                    </div>

                                    <div class="text-4xl font-bold">
                                        {{ report.report_data.plan_4.energy_independence }}
                                    </div>

                                    <div class="opacity-90 mt-1">
                                        Operate almost entirely without utility grid
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Battery Section -->

                        <div class="grid md:grid-cols-4 gap-4 mt-6">

                            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-2">

                                    <component
                                        :is="LucideIcons.BatteryCharging"
                                        size="18"
                                        class="text-amber-600"
                                    />

                                    <span class="text-sm">
                                        Battery Storage
                                    </span>

                                </div>

                                <div class="text-3xl font-bold text-amber-700">
                                    {{ report.report_data.plan_4.battery_kwh }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    kWh
                                </div>

                            </div>

                            <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-2">

                                    <component
                                        :is="LucideIcons.MoonStar"
                                        size="18"
                                        class="text-purple-600"
                                    />

                                    <span class="text-sm">
                                        Night Runtime
                                    </span>

                                </div>

                                <div class="text-3xl font-bold text-purple-700">
                                    {{ report.report_data.plan_4.night_hours }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    Hours
                                </div>

                            </div>

                            <div class="bg-green-50 border border-green-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-2">

                                    <component
                                        :is="LucideIcons.RotateCw"
                                        size="18"
                                        class="text-green-600"
                                    />

                                    <span class="text-sm">
                                        Battery Cycles
                                    </span>

                                </div>

                                <div class="text-3xl font-bold text-green-700">
                                    {{ report.report_data.plan_4.battery_cycles.toLocaleString() }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    Cycles
                                </div>

                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">

                                <div class="flex items-center gap-2 mb-2">

                                    <component
                                        :is="LucideIcons.Expand"
                                        size="18"
                                        class="text-blue-600"
                                    />

                                    <span class="text-sm">
                                        Expansion Ready
                                    </span>

                                </div>

                                <div class="text-3xl font-bold text-blue-700">
                                    +{{ report.report_data.plan_4.future_expansion_capacity_kw }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    Additional kW
                                </div>

                            </div>

                        </div>

                        <!-- System Details -->

                        <div class="grid md:grid-cols-2 gap-6 mt-6">

                            <div class="bg-gray-50 rounded-2xl p-5">

                                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">

                                    <component
                                        :is="LucideIcons.Battery"
                                        size="18"
                                    />

                                    Battery Technology

                                </h3>

                                <div class="space-y-3">

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Battery Type</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_4.battery_type }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Expected Life</span>
                                        <span class="font-semibold">
                                            15-18 Years
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Cycle Life</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_4.battery_cycles.toLocaleString() }}
                                        </span>
                                    </div>

                                </div>

                            </div>

                            <div class="bg-gray-50 rounded-2xl p-5">

                                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">

                                    <component
                                        :is="LucideIcons.Settings"
                                        size="18"
                                    />

                                    Recommended Hardware

                                </h3>

                                <div class="space-y-3">

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Panel Count</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_4.recommended_panel_count }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Panel Wattage</span>
                                        <span class="font-semibold">
                                            {{ report.report_data.plan_4.recommended_panel_wattage }}W
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500">System Type</span>
                                        <span class="font-semibold">
                                            Fully Offgrid
                                        </span>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Advantages -->

                        <div class="mt-6 bg-green-50 border border-green-200 rounded-2xl p-5">

                            <h3 class="font-bold text-green-700 mb-4 flex items-center gap-2">

                                <component
                                    :is="LucideIcons.Award"
                                    size="18"
                                />

                                Premium Benefits

                            </h3>

                            <div class="grid md:grid-cols-2 gap-3">

                                <div class="flex items-center gap-2">
                                    <component :is="LucideIcons.CheckCircle2" size="16" class="text-green-600"/>
                                    <span>No Grid Dependency</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <component :is="LucideIcons.CheckCircle2" size="16" class="text-green-600"/>
                                    <span>No Generator Required</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <component :is="LucideIcons.CheckCircle2" size="16" class="text-green-600"/>
                                    <span>Full Day & Night Operation</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <component :is="LucideIcons.CheckCircle2" size="16" class="text-green-600"/>
                                    <span>Maximum Energy Independence</span>
                                </div>

                            </div>

                        </div>

                        <!-- Limitations -->

                        <div class="mt-6 bg-red-50 border border-red-200 rounded-2xl p-5">

                            <h3 class="font-bold text-red-700 mb-4 flex items-center gap-2">

                                <component
                                    :is="LucideIcons.TriangleAlert"
                                    size="18"
                                />

                                Important Limitation

                            </h3>

                            <ul class="space-y-2">

                                <li
                                    v-for="item in report.report_data.plan_4.limitations"
                                    class="flex items-start gap-2">

                                    <component
                                        :is="LucideIcons.CircleAlert"
                                        size="16"
                                        class="text-red-600 mt-1 flex-shrink-0"
                                    />

                                    <span>{{ item }}</span>

                                </li>

                            </ul>

                        </div>

                    </div>

                </div>
            </div>

            <!-- Solar Analysis -->
            <div class="px-8">
                <div class="bg-white rounded-3xl border border-yellow-200 shadow-lg overflow-hidden mt-8">

                    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-5">

                        <div class="flex items-center gap-3">

                            <component
                                :is="LucideIcons.Sun"
                                size="28"
                            />

                            <div>

                                <h2 class="text-2xl font-bold">
                                    Solar Performance Analysis
                                </h2>

                                <p class="text-yellow-100">
                                    Recommended solar technology and performance
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <div class="grid md:grid-cols-5 gap-4">

                            <div class="bg-yellow-50 rounded-2xl p-4 border">
                                <div class="text-sm text-gray-500">
                                    Solar Hours
                                </div>
                                <div class="text-3xl font-bold text-yellow-700">
                                    {{ report.report_data.solar_analysis.average_solar_hours }}
                                </div>
                                <div class="text-sm">
                                    hrs/day
                                </div>
                            </div>

                            <div class="bg-green-50 rounded-2xl p-4 border">
                                <div class="text-sm text-gray-500">
                                    Panel Life
                                </div>
                                <div class="text-xl font-bold text-green-700">
                                    {{ report.report_data.solar_analysis.expected_panel_life }}
                                </div>
                            </div>

                            <div class="bg-blue-50 rounded-2xl p-4 border">
                                <div class="text-sm text-gray-500">
                                    Panel Size
                                </div>
                                <div class="text-xl font-bold text-blue-700">
                                    {{ report.report_data.solar_analysis.recommended_panel_size }}
                                </div>
                            </div>

                            <div class="bg-purple-50 rounded-2xl p-4 border">
                                <div class="text-sm text-gray-500">
                                    Technology
                                </div>
                                <div class="font-bold text-purple-700">
                                    {{ report.report_data.solar_analysis.recommended_panel_type }}
                                </div>
                            </div>

                            <div class="bg-orange-50 rounded-2xl p-4 border">
                                <div class="text-sm text-gray-500">
                                    Degradation
                                </div>
                                <div class="font-bold text-orange-700">
                                    {{ report.report_data.solar_analysis.performance_degradation }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            
            <!-- Battery Analysis -->
            <div class="px-8">
                <div class="bg-white rounded-3xl border border-purple-200 shadow-lg overflow-hidden mt-8">

                    <div class="bg-gradient-to-r from-purple-700 to-indigo-600 text-white p-5">

                        <div class="flex items-center gap-3">

                            <component
                                :is="LucideIcons.BatteryCharging"
                                size="28"
                            />

                            <div>

                                <h2 class="text-2xl font-bold">
                                    Battery Analysis
                                </h2>

                                <p class="text-purple-100">
                                    Backup and storage recommendations
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <div class="grid md:grid-cols-4 gap-4">

                            <div class="bg-purple-50 rounded-2xl border p-5">
                                <div class="text-sm text-gray-500">
                                    Battery Type
                                </div>

                                <div class="font-bold text-purple-700 mt-2">
                                    {{ report.report_data.battery_analysis.recommended_technology }}
                                </div>
                            </div>

                            <div class="bg-blue-50 rounded-2xl border p-5">
                                <div class="text-sm text-gray-500">
                                    Expected Life
                                </div>

                                <div class="text-3xl font-bold text-blue-700">
                                    {{ report.report_data.battery_analysis.expected_life_years }}
                                </div>

                                <div class="text-sm">
                                    Years
                                </div>
                            </div>

                            <div class="bg-green-50 rounded-2xl border p-5">
                                <div class="text-sm text-gray-500">
                                    Replacement Cycle
                                </div>

                                <div class="font-bold text-green-700">
                                    {{ report.report_data.battery_analysis.replacement_cycle }}
                                </div>
                            </div>

                            <div class="bg-yellow-50 rounded-2xl border p-5">
                                <div class="text-sm text-gray-500">
                                    Backup Energy
                                </div>

                                <div class="text-3xl font-bold text-yellow-700">
                                    {{ report.report_data.battery_analysis.daily_backup_energy_kwh }}
                                </div>

                                <div class="text-sm">
                                    kWh
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- Financial Analysis -->
            <div class="px-8">
                <div class="bg-white rounded-3xl border border-green-200 shadow-lg overflow-hidden mt-8">

                    <div class="bg-gradient-to-r from-green-700 to-emerald-500 text-white p-5">

                        <div class="flex items-center gap-3">

                            <component
                                :is="LucideIcons.Banknote"
                                size="28"
                            />

                            <div>

                                <h2 class="text-2xl font-bold">
                                    Financial Analysis
                                </h2>

                                <p class="text-green-100">
                                    Electricity usage and cost analysis
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <div class="grid md:grid-cols-4 gap-4">

                            <div class="bg-green-50 rounded-2xl border p-5">
                                <div class="text-sm text-gray-500">
                                    Monthly Bill
                                </div>

                                <div class="text-3xl font-bold text-green-700">
                                    Rs {{ report.report_data.financial_analysis.monthly_bill_estimate.toLocaleString() }}
                                </div>
                            </div>

                            <div class="bg-blue-50 rounded-2xl border p-5">
                                <div class="text-sm text-gray-500">
                                    Annual Bill
                                </div>

                                <div class="text-3xl font-bold text-blue-700">
                                    Rs {{ report.report_data.financial_analysis.annual_bill_estimate.toLocaleString() }}
                                </div>
                            </div>

                            <div class="bg-yellow-50 rounded-2xl border p-5">
                                <div class="text-sm text-gray-500">
                                    Monthly Units
                                </div>

                                <div class="text-3xl font-bold text-yellow-700">
                                    {{ report.report_data.financial_analysis.monthly_units_consumed }}
                                </div>
                            </div>

                            <div class="bg-purple-50 rounded-2xl border p-5">
                                <div class="text-sm text-gray-500">
                                    Electricity Rate
                                </div>

                                <div class="text-3xl font-bold text-purple-700">
                                    Rs {{ report.report_data.financial_analysis.electricity_rate }}
                                </div>

                                <div class="text-sm">
                                    per unit
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- Personalized Insights -->
            <div class="px-8">
                <div class="bg-white rounded-3xl border border-blue-200 shadow-lg overflow-hidden mt-8">

                    <div class="bg-gradient-to-r from-blue-700 to-cyan-500 text-white p-5">

                        <div class="flex items-center gap-3">

                            <component
                                :is="LucideIcons.Lightbulb"
                                size="28"
                            />

                            <h2 class="text-2xl font-bold">
                                AI Personalized Insights
                            </h2>

                        </div>

                    </div>

                    <div class="p-6">

                        <div
                            v-for="(item,index) in report.report_data.personalized_insights"
                            :key="index"
                            class="mb-4 bg-blue-50 border border-blue-200 rounded-2xl p-5">

                            <div class="flex gap-3">

                                <component
                                    :is="LucideIcons.Sparkles"
                                    size="18"
                                    class="text-blue-600 mt-1"
                                />

                                <span>
                                    {{ item }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- Site Checklist -->
            <div class="px-8">
                <div class="bg-white rounded-3xl border border-emerald-200 shadow-lg overflow-hidden mt-8">

                    <div class="bg-gradient-to-r from-emerald-600 to-green-500 text-white p-5">

                        <div class="flex items-center gap-3">

                            <component
                                :is="LucideIcons.ClipboardCheck"
                                size="28"
                            />

                            <h2 class="text-2xl font-bold">
                                Site Readiness Checklist
                            </h2>

                        </div>

                    </div>

                    <div class="p-6">

                        <div
                            v-for="(item,index) in report.report_data.site_checklist"
                            :key="index"
                            class="flex items-center gap-3 py-3 border-b">

                            <component
                                :is="LucideIcons.CheckCircle2"
                                size="20"
                                class="text-green-600"
                            />

                            <span>{{ item }}</span>

                        </div>

                    </div>

                </div>
            </div>

            <!-- Tips and Warnings -->
            <div class="px-8">
                <div class="grid md:grid-cols-2 gap-6 mt-8">

                    <!-- Tips -->
                    <div class="bg-green-50 border border-green-200 rounded-3xl p-6">

                        <div class="flex items-center gap-3 mb-4">

                            <component
                                :is="LucideIcons.Lightbulb"
                                size="24"
                                class="text-green-600"
                            />

                            <h2 class="text-xl font-bold text-green-700">
                                Optimization Tips
                            </h2>

                        </div>

                        <div
                            v-for="tip in report.report_data.tips"
                            class="flex gap-3 mb-3">

                            <component
                                :is="LucideIcons.CheckCircle2"
                                size="18"
                                class="text-green-600 mt-1"
                            />

                            <span>{{ tip }}</span>

                        </div>

                    </div>

                    <!-- Warnings -->
                    <div class="bg-red-50 border border-red-200 rounded-3xl p-6">

                        <div class="flex items-center gap-3 mb-4">

                            <component
                                :is="LucideIcons.TriangleAlert"
                                size="24"
                                class="text-red-600"
                            />

                            <h2 class="text-xl font-bold text-red-700">
                                Important Warnings
                            </h2>

                        </div>

                        <div
                            v-for="warning in report.report_data.warnings"
                            class="flex gap-3 mb-3">

                            <component
                                :is="LucideIcons.CircleAlert"
                                size="18"
                                class="text-red-600 mt-1"
                            />

                            <span>{{ warning }}</span>

                        </div>

                    </div>

                </div>
            </div>

            <div class="px-8">

            </div>
            

            <!-- Footer -->
            <div
                class="bg-gray-900 text-white p-8 mt-10">

                <div class="text-center">

                    <div class="text-2xl font-bold">

                        RaadVolt AI

                    </div>

                    <div class="opacity-80">

                        Smart Solar Planning Powered by Artificial Intelligence

                    </div>

                    <div class="mt-4 text-sm opacity-60">

                        This report is generated automatically using appliance
                        data provided by the customer. Actual system sizing
                        may vary depending on site conditions.

                    </div>

                </div>

            </div>
        </div>    
    </div>
</template>