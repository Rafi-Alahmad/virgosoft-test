<script setup>
import { reactive, ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useApi } from "@/composables/useApi";
import { useToast } from "@/composables/useToast";

const router = useRouter();
const { post, get } = useApi();
const toast = useToast();

const { symbol, side, price, amount } = router.currentRoute.value.query;
const form = reactive({
    symbol: symbol || "BTC",
    side: side || "buy",
    price: price || "",
    amount: amount || "",
});

const loading = ref(false);
const availableSymbols = ref(["BTC", "ETH"]);

const totalCost = computed(() => {
    const price = parseFloat(form.price) || 0;
    const amount = parseFloat(form.amount) || 0;
    return (price * amount).toFixed(2);
});

const handleSubmit = async () => {
    if (!form.price || !form.amount) {
        toast.error("Please fill in all fields");
        return;
    }

    const price = parseFloat(form.price);
    const amount = parseFloat(form.amount);

    if (price <= 0 || amount <= 0) {
        toast.error("Price and amount must be greater than 0");
        return;
    }

    loading.value = true;

    const { data, error } = await post("/orders", {
        symbol: form.symbol,
        side: form.side,
        price: price,
        amount: amount,
    });

    loading.value = false;

    if (data && !error) {
        toast.success("Order placed successfully!");
        router.push("/dashboard");
    }
};

</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Symbol Dropdown -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-slate-300">
                Symbol
            </label>
            <select
                v-model="form.symbol"
                class="w-full py-3.5 px-4 bg-slate-800/50 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all"
            >
                <option
                    v-for="symbol in availableSymbols"
                    :key="symbol"
                    :value="symbol"
                >
                    {{ symbol }}
                </option>
            </select>
        </div>

        <!-- Side Selection -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-slate-300">
                Side
            </label>
            <div class="grid grid-cols-2 gap-4">
                <button
                    type="button"
                    @click="form.side = 'buy'"
                    class="py-3.5 px-4 rounded-xl font-semibold transition-all"
                    :class="
                        form.side === 'buy'
                            ? 'bg-emerald-500/20 border-2 border-emerald-500 text-emerald-400'
                            : 'bg-slate-800/50 border-2 border-slate-700 text-slate-400 hover:border-slate-600'
                    "
                >
                    Buy
                </button>
                <button
                    type="button"
                    @click="form.side = 'sell'"
                    class="py-3.5 px-4 rounded-xl font-semibold transition-all"
                    :class="
                        form.side === 'sell'
                            ? 'bg-rose-500/20 border-2 border-rose-500 text-rose-400'
                            : 'bg-slate-800/50 border-2 border-slate-700 text-slate-400 hover:border-slate-600'
                    "
                >
                    Sell
                </button>
            </div>
        </div>

        <!-- Price Input -->
        <AppInput
            id="price"
            v-model="form.price"
            type="number"
            label="Price (USD)"
            placeholder="0.00"
            step="0.01"
            min="0.01"
            required
        >
            <template #icon>
                <svg
                    class="w-5 h-5 text-slate-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </template>
        </AppInput>

        <!-- Amount Input -->
        <AppInput
            id="amount"
            v-model="form.amount"
            type="number"
            label="Amount"
            placeholder="0.00000000"
            step="0.00000001"
            min="0.00000001"
            required
        >
            <template #icon>
                <svg
                    class="w-5 h-5 text-slate-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                    />
                </svg>
            </template>
        </AppInput>

        <!-- Total Cost -->
        <div class="p-4 rounded-lg bg-slate-800/50 border border-slate-700">
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-400">Total Cost</span>
                <span class="text-lg font-semibold text-white">
                    ${{ totalCost }}
                </span>
            </div>
        </div>

        <!-- Submit Button -->
        <AppButton
            type="submit"
            variant="primary"
            :loading="loading"
            loading-text="Placing order..."
            :show-arrow="true"
        >
            Place Order
        </AppButton>
    </form>
</template>
