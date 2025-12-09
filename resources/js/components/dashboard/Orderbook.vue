<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useApi } from "@/composables/useApi";
import AppCard from "@/components/ui/AppCard.vue";

const symbol = defineModel("symbol", {
    type: String,
    default: "BTC",
});
const props = defineProps({
    assets: {
        type: Array,
        default: () => [],
    },
});

const { get } = useApi();

const ordersData = ref([]);
const loading = ref(true);

const orders = computed(() => {
    return ordersData.value.sort(
        (a, b) => parseFloat(a.price) - parseFloat(b.price)
    );
});

const fetchOrderbook = async () => {
    loading.value = true;
    const { data, error } = await get(
        `/orders/open?symbol=${symbol.value}&per_page=100`
    );

    if (data && !error) {
        ordersData.value = data.data || [];
    }

    loading.value = false;
};

watch(
    () => symbol.value,
    () => {
        fetchOrderbook();
    }
);

onMounted(() => {
    fetchOrderbook();
});

</script>

<template>
    <AppCard class="h-[500px]">
        <template #title>
            <div class="flex items-center gap-3">
                Orderbook
                <select
                    v-model="symbol"
                    class="px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <option
                        v-for="asset in assets"
                        :key="asset.symbol"
                        :value="asset.symbol"
                    >
                        {{ asset.symbol }}
                    </option>
                    <option v-if="props.assets.length === 0" value="BTC">
                        BTC
                    </option>
                    <option v-if="props.assets.length === 0" value="ETH">
                        ETH
                    </option>
                </select>
            </div>
            <router-link
                :to="{name: 'order-form'}"
                class="ml-auto px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/20 transition-colors text-sm font-medium"
            >
                New Order
            </router-link>
        </template>
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
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                />
            </svg>
        </template>

        <div v-if="loading" class="space-y-4">
            <div
                v-for="i in 7"
                :key="i"
                class="h-10 bg-slate-800/50 rounded-lg animate-pulse"
            ></div>
        </div>

        <div v-else class="space-y-6">
            <div>
                <div
                    v-if="orders.length === 0"
                    class="text-center py-30 text-slate-500"
                >
                    No orders found
                </div>
                <div v-else class="space-y-1 max-h-[400px] overflow-y-auto">
                    <div
                        v-for="order in orders.slice(0, 20)"
                        :key="`sell-${order.id}`"
                        class="flex items-center justify-between p-2 rounded hover:bg-slate-800/50 transition-colors"
                    >
                        <span class="text-sm" :class="order.side === 'buy' ? 'text-emerald-400' : 'text-rose-400'">
                            {{ formatAmount(order.amount) }} {{ order.symbol }}
                        </span>
                        <span class="text-sm font-medium" :class="order.side === 'buy' ? 'text-emerald-400' : 'text-rose-400'">
                            ${{ formatPrice(order.price) }}
                        </span>

                        <div >
                            <router-link :to="{
                                name: 'order-form',
                                query: {
                                    symbol: order.symbol,
                                    side: order.side == 'buy' ? 'sell' : 'buy',
                                    price: order.price,
                                    amount: order.amount,
                                },
                            }" class="px-2 py-1 rounded text-xs font-medium bg-emerald-500/10 text-emerald-400">
                                Create Match
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppCard>
</template>
