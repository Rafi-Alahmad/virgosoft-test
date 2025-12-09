<script setup>

definePage({
    name: 'order-form',
    meta: {
        requiresAuth: true,
    },
});
</script>

<template>
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Place Order</h1>
            <p class="text-slate-500">Create a new buy or sell order</p>
        </div>

        <AppCard>
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

            <OrderForm />
        </AppCard>
    </div>
</template>

