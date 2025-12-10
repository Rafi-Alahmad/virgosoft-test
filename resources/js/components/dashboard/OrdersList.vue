<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useApi } from "@/composables/useApi";
import AppCard from "@/components/ui/AppCard.vue";

const { get } = useApi();
const authStore = useAuthStore();
const ordersData = ref([]);
const loading = ref(true);

const statusLabels = {
    1: { text: "Open", color: "text-blue-400 bg-blue-500/10" },
    2: { text: "Filled", color: "text-emerald-400 bg-emerald-500/10" },
    3: { text: "Cancelled", color: "text-slate-400 bg-slate-500/10" },
};

const sideLabels = {
    buy: { text: "Buy", color: "text-emerald-400" },
    sell: { text: "Sell", color: "text-rose-400" },
};

const sides = [
    { text: "Side", value: null },
    { text: "Buy", value: "buy" },
    { text: "Sell", value: "sell" },
];

const statuses = [
    { text: "Status", value: null },
    { text: "Open", value: 1 },
    { text: "Filled", value: 2 },
    { text: "Cancelled", value: 3 },
];

const symbols = [
    { text: "Asset", value: null },
    { text: "BTC", value: "BTC" },
    { text: "ETH", value: "ETH" },
];
const status = ref(null);
const side = ref(null);
const symbol = ref(null);
const fetchOrders = async (withLoading = true) => {
    if (withLoading) {
        loading.value = true;
    }
    const { data, error } = await get(`/orders?status=${status.value}&side=${side.value}&symbol=${symbol.value}&per_page=50`);

    if (data && !error) {
        ordersData.value = data.data || [];
    }

    if (withLoading) {
        loading.value = false;
    }
};

const orders = computed(() => {
    return ordersData.value.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

watch([status, side, symbol], () => {
    fetchOrders();
});

onMounted(async () => {
    fetchOrders();

    echo.private(`user.${authStore.user?.id}`).listen(".order-matched", (e) => {
        fetchOrders(false);
    });

    echo.channel(`orders`).listen(".order-placed", (e) => {
        ordersData.value.push(e.order);
    });
});

onUnmounted(() => {
    echo.leave(`user.${authStore.user?.id}`);
    echo.leave(`orders`);
});
</script>

<template>
    <AppCard title="All Orders" class="h-[500px]">
        <template #title>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
                <span class="text-nowrap"> All Orders </span>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                    <select
                        v-model="status"
                        class="px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 flex-1 sm:flex-none min-w-0"
                    >
                        <option
                            v-for="status in statuses"
                            :key="status.value"
                            :value="status.value"
                        >
                            {{ status.text }}
                        </option>
                    </select>
                    <select
                        v-model="symbol"
                        class="px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 flex-1 sm:flex-none min-w-0"
                    >
                        <option
                            v-for="symbol in symbols"
                            :key="symbol.value"
                            :value="symbol.value"
                        >
                            {{ symbol.text }}
                        </option>
                    </select>
                    <select
                        v-model="side"
                        class="px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 flex-1 sm:flex-none min-w-0"
                    >
                        <option
                            v-for="side in sides"
                            :key="side.value"
                            :value="side.value"
                        >
                            {{ side.text }}
                        </option>
                    </select>
                </div>
            </div>
        </template>

        <div v-if="loading" class="space-y-3">
            <div
                v-for="i in 5"
                :key="i"
                class="h-18 bg-slate-800/50 rounded-lg animate-pulse"
            ></div>
        </div>

        <div v-else-if="orders.length === 0" class="text-center py-30">
            <p class="text-slate-500">No orders found</p>
        </div>

        <div v-else class="space-y-3 max-h-[400px] overflow-y-auto">
            <div
                v-for="order in orders"
                :key="order.id"
                class="p-4 rounded-lg border border-slate-700/50 bg-slate-800/30 hover:bg-slate-800/50 transition-colors"
            >
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <span
                            class="px-2 py-1 rounded text-xs font-medium"
                            :class="sideLabels[order.side].color"
                        >
                            {{ sideLabels[order.side].text }}
                        </span>
                        <span class="text-sm font-medium text-white">
                            {{ order.symbol }}
                        </span>
                    </div>
                    <span
                        class="px-2 py-1 rounded text-xs font-medium"
                        :class="statusLabels[order.status].color"
                    >
                        {{ statusLabels[order.status].text }}
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500 text-xs mb-1">Price</p>
                        <p class="text-white font-medium">
                            ${{ formatPrice(order.price) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs mb-1">Amount</p>
                        <p class="text-white font-medium">
                            {{ formatAmount(order.amount) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs mb-1">Date</p>
                        <p class="text-white font-medium text-xs">
                            {{ order.created_at }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppCard>
</template>
