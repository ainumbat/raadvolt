<script setup>
import { ref } from 'vue'
import { Download, Sun, Battery, Zap, TriangleAlert, Copy, Check } from 'lucide-vue-next'
import html2pdf from 'html2pdf.js'

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
            JSON.stringify(props.report, null, 2)
        )

        copied.value = true

        setTimeout(() => {
            copied.value = false
        }, 2000)
    } catch (err) {
        console.error(err)
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
            <pre class="bg-slate-900 text-green-400 p-6 rounded-xl overflow-auto text-xs max-h-[80vh]">
                <div class="flex justify-end mb-3">
                    <button
                        @click="copyJson"
                        class="flex items-center gap-2 px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-green-700"
                    >
                        <Check v-if="copied" :size="16" />
                        <Copy v-else :size="16" />
                    </button>
                </div>

                {{ JSON.stringify(props.report, null, 2) }}
            </pre>
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

                        <Download size="18" />

                        Download PDF
                    </button>

                </div>
            </div>

            <!-- CUSTOMER -->
            <div class="p-8">

                <h2 class="text-2xl font-bold mb-4">
                    User Information
                </h2>

                <div class="grid md:grid-cols-4 gap-4">

                    <div>
                        <div class="text-gray-500">
                            Name
                        </div>

                        <div class="font-semibold">
                            {{ report.customer.name }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">
                            WhatsApp
                        </div>

                        <div class="font-semibold">
                            {{ report.customer.whatsapp }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">
                            Email
                        </div>

                        <div class="font-semibold">
                            {{ report.customer.email }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">
                            Report ID
                        </div>

                        <div class="font-semibold">
                            {{ report.customer.report_id }}
                        </div>
                    </div>

                </div>

            </div>

            <!-- Summary Cards -->
            <div class="grid md:grid-cols-3 gap-3 px-8">

                <div
                    class="bg-green-50 border border-green-200 rounded-xl p-6">

                    <h3 class="font-bold text-green-700 mb-2">
                        Essential Load
                    </h3>

                    <div class="text-4xl font-bold">
                        {{ (report.load_summary.essential_load / 1000).toFixed(2) }}
                        KW
                    </div>

                </div>

                <div
                    class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">

                    <h3 class="font-bold text-yellow-700 mb-2">
                        Generator Load
                    </h3>

                    <div class="text-4xl font-bold">
                        {{ (report.load_summary.generator_load / 1000).toFixed(2) }}
                        KW
                    </div>

                </div>

                <div
                    class="bg-orange-50 border border-orange-200 rounded-xl p-6">

                    <h3 class="font-bold text-orange-700 mb-2">
                        Total Load
                    </h3>

                    <div class="text-4xl font-bold">
                        {{ (report.load_summary.total_load / 1000).toFixed(2) }}
                        KW
                    </div>

                </div>

            </div>

            <!-- Appliances Cards -->
            <div class="grid md:grid-cols-2 gap-6 px-8 mt-6">

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
                            v-for="item in report.load_summary.essential_appliances"
                            class="grid grid-cols-12 px-5 py-3 hover:bg-green-50 transition">

                            <div class="col-span-6 font-medium text-gray-800">
                                {{ item.appliance.name }}
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
                            v-for="item in report.load_summary.generator_appliances"
                            class="grid grid-cols-12 px-5 py-3 hover:bg-yellow-50 transition">

                            <div class="col-span-6 font-medium text-gray-800">
                                {{ item.appliance.name }}
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
            <div
                class="mx-8 my-6 border rounded-xl overflow-hidden shadow-sm">

                <div
                    class="bg-green-700 text-white p-4 flex items-center gap-3">

                    <Sun />

                    <h2 class="text-xl font-bold">{{ report.plan_1.title }}</h2>

                </div>

                <div class="p-6">

                    <div class="grid md:grid-cols-4 gap-4">

                        <div>
                            <div class="text-gray-500">
                                Inverter
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_1.inverter_kw }} KW
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Solar Panels
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_1.solar_kw }} KW
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Battery
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_1.battery }} KW
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                ROI Time
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_1.roi_time }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Plan 2 -->
            <div
                class="mx-8 my-6 border rounded-xl overflow-hidden shadow-sm">

                <div
                    class="bg-green-700 text-white p-4 flex items-center gap-3">

                    <Sun />

                    <h2 class="text-xl font-bold">{{ report.plan_2.title }}</h2>

                </div>

                <div class="p-6">

                    <div class="grid md:grid-cols-4 gap-4">

                        <div>
                            <div class="text-gray-500">
                                Inverter
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_2.inverter_kw }} KW
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Solar Panels
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_2.solar_kw }} KW
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Surplus Capacity
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_2.surplus_kw }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Monthly Export Units
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_2.monthly_export_units }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Estimated Income
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_2.estimated_income }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                ROI Time
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_2.roi_time }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Plan 3 -->
            <div
                class="mx-8 my-6 border rounded-xl overflow-hidden shadow-sm">

                <div
                    class="bg-green-700 text-white p-4 flex items-center gap-3">

                    <Sun />

                    <h2 class="text-xl font-bold">{{ report.plan_3.title }}</h2>

                </div>

                <div class="p-6">

                    <div class="grid md:grid-cols-4 gap-4">

                        <div>
                            <div class="text-gray-500">
                                Inverter
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_3.inverter_kw }} KW
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Solar Panels
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_3.solar_kw }} KW
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Battery Specs
                            </div>

                            <div class="font-bold text-lg">
                                ({{ report.plan_3.battery_kwh }}kwh) {{ report.plan_3.battery_voltage }}V, {{ report.plan_3.battery_ah }}amp
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                ROI Time
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_3.roi_time }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Plan 4 -->
            <div
                class="mx-8 my-6 border rounded-xl overflow-hidden shadow-sm">

                <div
                    class="bg-green-700 text-white p-4 flex items-center gap-3">

                    <Sun />

                    <h2 class="text-xl font-bold">{{ report.plan_4.title }}</h2>

                </div>

                <div class="p-6">

                    <div class="grid md:grid-cols-4 gap-4">

                        <div>
                            <div class="text-gray-500">
                                Inverter
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_4.inverter_kw }} KW
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Solar Panels
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_4.solar_kw }} KW
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Battery
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_4.battery_kwh }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                Battery
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_4.night_hours }} hours
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">
                                ROI Time
                            </div>

                            <div class="font-bold text-lg">
                                {{ report.plan_4.roi_time }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Personalized Tips -->
            <div class="mx-8 my-6">

                <h2 class="text-2xl font-bold mb-4">
                    Personalized Recommendations
                </h2>

                <div
                    v-for="tip in report.tips"
                    class="bg-green-50 border-l-4 border-green-500 p-4 mb-3 rounded">

                    {{ tip }}

                </div>

            </div>

            <!-- Warnings -->
            <div
                v-if="report.warnings.length"
                class="mx-8 my-6">

                <h2
                    class="text-2xl font-bold text-red-600 mb-4">

                    Important Warnings
                </h2>

                <div
                    v-for="warning in report.warnings"
                    class="bg-red-50 border-l-4 border-red-500 p-4 mb-3 rounded flex gap-3">

                    <TriangleAlert />

                    <span>
                        {{ warning }}
                    </span>

                </div>

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