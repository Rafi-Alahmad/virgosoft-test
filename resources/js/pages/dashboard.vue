<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useApi } from "@/composables/useApi";

definePage({
    name: "dashboard",
    meta: {
        requiresAuth: true,
    },
});

const authStore = useAuthStore();
const { get } = useApi();

const balance = ref(0);
const assets = ref([]);
const loading = ref(true);
const selectedSymbol = ref("BTC");

const fetchProfile = async () => {
    loading.value = true;
    const { data, error } = await get("/profile");

    if (data.data && !error) {
        balance.value = data.data.balance || 0;
        assets.value = data.data.assets || [];

        // Set default symbol from first asset if available
        if (
            assets.value.length > 0 &&
            !assets.value.find((a) => a.symbol === selectedSymbol.value)
        ) {
            selectedSymbol.value = assets.value[0].symbol;
        }
    }

    loading.value = false;
};

onMounted(() => {
    fetchProfile();

    echo.private(`user.${authStore.user?.id}`).listen(".order-matched", (e) => {
        fetchProfile();
    });
});

onUnmounted(() => {
    echo.leave(`user.${authStore.user?.id}`);
});
</script>

<template>
    <WelcomeHeader :name="authStore.user?.name" v-if="authStore.user" />

    <!-- Stats Grid -->
    <div
        v-if="loading"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12"
    >
        <div v-for="i in 3" :key="i"
            class="rounded-2xl p-6 border border-slate-700/50 bg-slate-800/50 animate-pulse"
        >
            <div class="h-12 w-12 rounded-xl bg-slate-700 mb-4"></div>
            <div class="h-8 w-24 bg-slate-700 rounded mb-2"></div>
            <div class="h-4 w-32 bg-slate-700 rounded"></div>
        </div>
    </div>

    <div
        v-else
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12"
    >
        <!-- Balance Card -->
        <BalanceCard :balance="balance" />

        <!-- Asset Cards -->
        <AssetCard
            v-for="asset in assets"
            :key="asset.symbol"
            :symbol="asset.symbol"
            :amount="asset.amount"
            :locked-amount="asset.locked_amount"
        />
    </div>

    <!-- Orders and Orderbook Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Orders List -->
        <div class="space-y-4">
            <OrdersList />
        </div>
        <!-- Orderbook -->
        <div class="space-y-4">
            <Orderbook :symbol="selectedSymbol" :assets="assets" />
        </div>
    </div>
</template>
