<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        default: '',
    },
    mono: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="space-y-1">
        <label class="text-sm font-medium text-slate-500">{{ label }}</label>
        <p class="text-lg text-white" :class="{ 'font-mono': mono }">{{ value }}</p>
    </div>
</template>

