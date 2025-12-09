<script setup>
const props = defineProps({
    name: {
        type: String,
        required: true,
    },
});
</script>

<template>
     <!-- Welcome Header -->
     <div class="mb-12">
        <h1 class="text-4xl font-bold text-white mb-2">
            Welcome,
            <span
                class="bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent"
                >{{ props.name }}</span
            >! 👋
        </h1>
        <p class="text-slate-500 text-lg">
            Are you ready to trade today?
        </p>
    </div>
</template>
