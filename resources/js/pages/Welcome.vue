<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
import { register } from '@/routes';

import { ref, computed, onMounted } from 'vue'
import {
    Fan,
    Refrigerator,
    WashingMachine,
    Laptop,
    Monitor,
    Router,
    Camera,
    Wind,
    Flame,
    Cpu,
    Plus,
    Trash2,
    Sun,
    BatteryCharging,
    Zap,
    Lightbulb,
    Factory,
    AirVent,
    Heater,
    SmartphoneCharging,
    UtilityPole,
    X,
    Grid3x3,
    PlugZap,
    Sheet,
} from 'lucide-vue-next'

const props = defineProps({
    months: Array,
})
  
const applianceCatalog = [
    { id: 1, name: 'Ceiling Fan', watts: 40, type: 'essential', icon: Fan },
    { id: 2, name: 'LED Light', watts: 12, type: 'essential', icon: Lightbulb },
    { id: 3, name: 'Bracket Fan', watts: 60, type: 'essential', icon: Wind },
    { id: 4, name: 'Stand Fan', watts: 80, type: 'generator', icon: Wind },
    { id: 5, name: 'Air Cooler', watts: 200, type: 'generator', icon: Wind },
    { id: 6, name: 'Fridge', watts: 200, type: 'essential', icon: Refrigerator },
    { id: 7, name: 'Deep Freezer', watts: 300, type: 'generator', icon: Refrigerator },
    { id: 8, name: 'Iron', watts: 1000, type: 'generator', icon: Flame },
    { id: 9, name: 'Motor', watts: 800, type: 'generator', icon: Cpu },
    { id: 10, name: 'Washing Machine', watts: 500, type: 'generator', icon: WashingMachine },
    { id: 11, name: 'Microwave', watts: 1200, type: 'generator', icon: Flame },
    { id: 12, name: 'Air Fryer', watts: 1800, type: 'generator', icon: Flame },
    { id: 13, name: 'Desktop Computer', watts: 120, type: 'generator', icon: Monitor },
    { id: 14, name: 'Laptop', watts: 65, type: 'generator', icon: Laptop },
    { id: 15, name: 'Security Camera', watts: 25, type: 'essential', icon: Camera },
    { id: 16, name: 'WiFi Router', watts: 10, type: 'essential', icon: Router },
    { id: 17, name: 'Air Conditioner', watts: 2000, type: 'generator', icon: AirVent },
    { id: 18, name: 'Electric Geyser', watts: 2500, type: 'generator', icon: Heater },
    { id: 19, name: 'UPS', watts: 800, type: 'generator', icon: SmartphoneCharging },
    { id: 20, name: 'Electric Stove', watts: 1800, type: 'generator', icon: Flame },
    { id: 21, name: 'Tube Light', watts: 35, type: 'essential', icon: Lightbulb },
    { id: 22, name: 'Custom Appliance', watts: 500, type: 'generator', icon: Plus },
]

const selectedAppliance = ref('')
const rows = ref([])

const addAppliance = (appliance) => {
    rows.value.push({
        uid: Date.now(),
        appliance_id: appliance.id,
        name: appliance.name,
        watts: appliance.watts,
        icon: appliance.icon,
        type: appliance.type || 'essential',
        quantity: appliance.quantity || 1,
    })
}

const getAppliance = (id) => {
    return applianceCatalog.find(a => a.id === id)
}

const removeRow = (index) => {
    rows.value.splice(index, 1)
}


const essentialLoad = computed(() => {
    return rows.value.reduce((sum, item) => {
        if (item.type !== 'essential') return sum
        return sum + item.watts * item.quantity
    }, 0)
})

const generatorLoad = computed(() => {
    return rows.value.reduce((sum, item) => {
        if (item.type !== 'generator') return sum
        return sum + item.watts * item.quantity
    }, 0)
})

const totalLoad = computed(() => {
    return essentialLoad.value + generatorLoad.value
})

const selectedMonth = ref('')
const unitsRows = ref([])

const addMonth = (month) => {
    unitsRows.value.push({
        id: month.id,
        name: month.name,
        units: 0,
    })
}

const averageUnits = computed(() => {
    if (!unitsRows.value.length) return 0

    const total = unitsRows.value.reduce(
        (sum, row) => sum + Number(row.units || 0),
        0
    )

    return Math.round(total / unitsRows.value.length)
})

const toggleType = (row) => {
    row.type = row.type === 'essential' ? 'generator' : 'essential'
}

const selectedPanelWatt = ref(635)

const requiredSolarCapacity = computed(() => {
    return Math.ceil(totalLoad.value * 1.3)
})

const backup_hours = ref(3)
const requiredBatteryCapacity = computed(() => {
    return Math.ceil((essentialLoad.value * 1.2) * backup_hours.value)
})

const totalPanels = computed(() => {
    return Math.ceil(
        requiredSolarCapacity.value / selectedPanelWatt.value
    )
})

const installedCapacity = computed(() => {
    return totalPanels.value * selectedPanelWatt.value
})

const spareCapacity = computed(() => {
    return Math.ceil((installedCapacity.value - requiredSolarCapacity.value) * 0.7)
})

const showApplianceModal = ref(false)

onMounted(() => {
    [
        { id: 1, quantity: 5 }, // 5 Fans
        { id: 2, quantity: 8 }, // 8 LED Lights
        { id: 6, quantity: 1 }, // 1 Fridge
        { id: 8, quantity: 1 }, // 1 Iron
    ].forEach(item => {
        const appliance = applianceCatalog.find(a => a.id === item.id)

        if (appliance) {
            addAppliance({
                ...appliance,
                quantity: item.quantity,
            })
        }
    })
})
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] text-[#1b1b18] lg:justify-center p-2 lg:p-8 dark:bg-[#0a0a0a]"
    >
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-1750 lg:grow starting:opacity-0"
        >
            <main class="w-full max-w-7xl overflow-hidden rounded-lg" >
                <div class="w-full max-w-7xl mx-auto px-0 sm:px-6">
                    <div class="p-0">

                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-1">
                            <div class="flex items-center gap-3">
                                <img
                                    src="/images/raadvolt-logo.png"
                                    alt="RaadVolt"
                                    class="h-26 sm:h-26 w-auto"
                                />
                            </div>

                            <!-- <h1 class="flex items-center gap-2 text-xl sm:text-2xl font-bold text-green-700">
                                <Sun class="text-yellow-500" />
                                Solar Calculator
                            </h1> -->
                        </div>

                        <!-- Summary -->
                        <div
                            v-if="rows.length"
                            class="mt-6 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 mb-4"
                        >
                            <div class="p-0">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-100 text-green-700 p-2 rounded-xl">
                                        <Zap size="32" />
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-800">Light Load</h3>
                                        <p class="text-sm text-gray-500">{{ essentialLoad }} W</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-0">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-100 text-green-700 p-2 rounded-xl">
                                        <UtilityPole size="32" />
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-800">Heavy Load</h3>
                                        <p class="text-sm text-gray-500">{{ generatorLoad }} W</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-0">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-100 text-green-700 p-2 rounded-xl">
                                        <PlugZap size="32" />
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-800">Total Load</h3>
                                        <p class="text-sm text-gray-500">{{ totalLoad }} W</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-0">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-100 text-green-700 p-2 rounded-xl">
                                        <Grid3x3 size="32" />
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-800">Required Solar</h3>
                                        <p class="text-sm text-gray-500">{{ requiredSolarCapacity }} W</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-0">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-100 text-green-700 p-2 rounded-xl">
                                        <BatteryCharging size="32" />
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-800">Required Battery</h3>
                                        <div class="flex gap-3">
                                            <p class="text-sm text-gray-500">{{ requiredBatteryCapacity }} W</p>
                                            <input
                                                type="number"
                                                min="0"
                                                max="16"
                                                v-model.number="backup_hours"
                                                class="w-full max-w-[60px] border border-green-200"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-0">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-100 text-green-700 p-2 rounded-xl">
                                        <Grid3x3 size="32" />
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-800">Solar Panels</h3>
                                        <div class="flex gap-3">
                                            <p class="text-sm text-gray-500">{{ totalPanels }}</p>
                                            <select
                                                v-model.number="selectedPanelWatt"
                                                class="w-full max-w-[70px] border border-green-200 text-center"
                                            >
                                                <option :value="450">450W</option>
                                                <option :value="550">550W</option>
                                                <option :value="585">585W</option>
                                                <option :value="635">635W</option>
                                                <option :value="650">650W</option>
                                                <option :value="670">670W</option>
                                                <option :value="720">720W</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-0">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-100 text-green-700 p-2 rounded-xl">
                                        <Sheet size="32" />
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-800">Spare Capacity</h3>
                                        <p class="text-sm text-gray-500">{{ spareCapacity }} W</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-0">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-100 text-green-700 p-2 rounded-xl">
                                        <Zap size="32" />
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-800">Total Capacity</h3>
                                        <p class="text-sm text-gray-500">{{ installedCapacity }} W</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <!-- Add Appliance -->
                        <div class="flex flex-wrap gap-2 mb-6" style="display: none;">
                            <button
                                v-for="item in applianceCatalog"
                                :key="item.id"
                                @click="addAppliance(item)"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg flex items-center gap-2"
                                :title="`${item.name} - ${item.watts}W`"
                            >
                                <component :is="item.icon" :size="18" />
                                <span class="text-sm">
                                    {{ item.name }} 
                                    <!-- ({{ item.watts }}W) -->
                                </span>
                            </button>
                        </div>

                        <div
                            v-if="showApplianceModal"
                            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                        >
                            <div class="bg-white rounded-lg shadow-lg w-[95%] sm:w-full max-w-3xl p-4 sm:p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-xl font-semibold">
                                        Select Appliance
                                    </h2>

                                    <button
                                        @click="showApplianceModal = false"
                                        class="text-gray-500 hover:text-gray-700"
                                    >
                                        <X />
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    <button
                                        v-for="item in applianceCatalog"
                                        :key="item.id"
                                        @click="
                                            addAppliance(item);
                                            showApplianceModal = false;
                                        "
                                        class="hover:bg-gray-200 p-3 rounded-lg flex flex-col items-center gap-2"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="bg-gray-100 text-green-700 p-2 rounded-xl">
                                                <component :is="item.icon" :size="24" />
                                            </div>

                                            <div>
                                                <p class="text-sm font-semibold text-gray-800">{{ item.name }}</p>
                                                <p class="text-xs text-gray-500">{{ item.watts }}W</p>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Appliances List -->
                        <div class="overflow-x-auto">
                            <div>
                                <button
                                    @click="showApplianceModal = true"
                                    class="fixed bottom-6 right-6 bg-gradient-to-r from-green-500 to-yellow-200 text-white rounded-full w-14 h-14 shadow-xl flex items-center justify-center z-50 cursor-pointer"
                                >
                                    <Plus />
                                </button>
                            </div>
                            
                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <div
                                    v-for="(row,index) in rows"
                                    :key="row.uid"
                                    class="bg-gradient-to-br from-green-100 to-yellow-50 border-2 border-green-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition"
                                >
                                    <!-- Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-green-100 text-green-700 p-2 rounded-xl">
                                                <component
                                                    :is="getAppliance(row.appliance_id)?.icon"
                                                    :size="22"
                                                />
                                            </div>

                                            <div>
                                                <h3 class="font-semibold text-gray-800">
                                                    {{ getAppliance(row.appliance_id)?.name }}
                                                </h3>

                                                <p class="text-xs text-gray-500">
                                                    Appliance #{{ index + 1 }}
                                                </p>
                                            </div>
                                        </div>

                                        <button
                                            @click="removeRow(index)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <Trash2 :size="18" />
                                        </button>
                                    </div>

                                    <!-- Inputs -->
                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                        <div>
                                            <label class="text-xs text-gray-500 block mb-1">
                                                Watts
                                            </label>

                                            <input
                                                type="number"
                                                v-model.number="row.watts"
                                                class="w-full rounded-lg border-green-200 focus:border-green-500 focus:ring-green-500"
                                            />
                                        </div>

                                        <div>
                                            <label class="text-xs text-gray-500 block mb-1">
                                                Quantity
                                            </label>

                                            <input
                                                type="number"
                                                v-model.number="row.quantity"
                                                class="w-full rounded-lg border-green-200 focus:border-green-500 focus:ring-green-500"
                                            />
                                        </div>

                                        <div>
                                            <label class="text-xs text-gray-500 block mb-1">
                                                Total Load
                                            </label>

                                            <input
                                                type="number"
                                                :value="row.watts * row.quantity"
                                                class="w-full rounded-lg border-green-200 focus:border-green-500 focus:ring-green-500"
                                            />
                                        </div>
                                    </div>

                                    <!-- Type Toggle -->
                                    <div
                                        class="flex items-center justify-between border border-yellow-200 rounded-xl p-3"
                                    >
                                        <span
                                            class="font-medium"
                                            :class="row.type === 'essential'
                                                ? 'text-green-700'
                                                : 'text-gray-400'"
                                        >
                                            Essential
                                        </span>

                                        <button
                                            @click="toggleType(row)"
                                            class="relative w-12 h-6 bg-gray-300 rounded-full"
                                        >
                                            <div
                                                class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-all"
                                                :class="row.type === 'generator'
                                                    ? 'translate-x-6'
                                                    : ''"
                                            />
                                        </button>

                                        <span
                                            class="font-medium"
                                            :class="row.type === 'generator'
                                                ? 'text-yellow-600'
                                                : 'text-gray-400'"
                                        >
                                            Generator
                                        </span>
                                    </div>
                                </div>

                                <!-- Add Appliance Button -->
                                <div
                                    class="flex items-center justify-center bg-gradient-to-br from-green-100 to-yellow-50 border-2 border-dashed border-green-300 rounded-2xl p-6 shadow-sm hover:shadow-md transition cursor-pointer"
                                    @click="showApplianceModal = true"
                                >
                                    <div class="flex flex-col items-center justify-center text-center space-y-2">
                                        <div class="w-14 h-14 flex items-center justify-center rounded-full bg-gradient-to-r from-green-500 to-yellow-200 text-white shadow">
                                            <Plus :size="28" />
                                        </div>

                                        <span class="text-green-700 font-semibold text-sm">
                                            Add Appliance
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Units(kwh) -->
                            <div style="display: none;"> 
                                <div class="flex flex-wrap gap-2 mb-6">
                                    <button
                                        v-for="month in months"
                                        :key="month.id"
                                        @click="addMonth(month)"
                                        class="p-3 bg-white border rounded-lg hover:bg-gray-50 flex flex-col items-center gap-2"
                                        :title="`${month.name}`"
                                    >
                                        <span class="text-sm">
                                            {{ month.name }}
                                        </span>
                                    </button>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="font-semibold">Average Monthly Consumption</span>
                                    <span class="text-xl font-bold text-green-700">{{ averageUnits }} <span class="text-xl font-bold text-gray-400">kWh</span></span>

                                </div>
                                
                                <table v-if="unitsRows.length > 0" class="w-full border">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="p-3 text-left">Month</th>
                                            <th class="p-3 text-center">Units (kWh)</th>
                                            <th class="p-3 text-center">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr
                                            v-for="(row, index) in unitsRows"
                                            :key="row.id"
                                            class="border-t"
                                        >
                                            <td class="p-3">
                                                {{ row.name }}
                                            </td>

                                            <td class="p-3 text-center">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    v-model.number="row.units"
                                                    class="border rounded px-2 py-1 w-32 text-center"
                                                />
                                            </td>

                                            <td class="p-3 text-center">
                                                <button
                                                    @click="unitsRows.splice(index, 1)"
                                                    class="text-red-600"
                                                >
                                                    <Trash2 :size="18" />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
