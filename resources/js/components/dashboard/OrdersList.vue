<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '@/composables/useApi';
import AppCard from '@/components/ui/AppCard.vue';

const { get } = useApi();

const orders = ref([]);
const loading = ref(true);

const statusLabels = {
    1: { text: 'Open', color: 'text-blue-400 bg-blue-500/10' },
    2: { text: 'Filled', color: 'text-emerald-400 bg-emerald-500/10' },
    3: { text: 'Cancelled', color: 'text-slate-400 bg-slate-500/10' },
};

const sideLabels = {
    buy: { text: 'Buy', color: 'text-emerald-400' },
    sell: { text: 'Sell', color: 'text-rose-400' },
};

const fetchOrders = async () => {
    loading.value = true;
    const { data, error } = await get('/orders?per_page=50');

    if (data && !error) {
        orders.value = data.data || [];
    }

    loading.value = false;
};

onMounted(() => {
    fetchOrders();
});
</script>

<template>
    <AppCard title="All Orders" class="h-[500px]">
        <template #icon>
            <svg
                class="w-6 h-6 text-slate-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
            </svg>
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
                        <p class="text-white font-medium">${{ formatPrice(order.price) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs mb-1">Amount</p>
                        <p class="text-white font-medium">{{ formatAmount(order.amount) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs mb-1">Date</p>
                        <p class="text-white font-medium text-xs">{{ order.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppCard>
</template>

