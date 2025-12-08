<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    autocomplete: {
        type: String,
        default: 'off',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
    showPasswordToggle: {
        type: Boolean,
        default: false,
    },
    color: {
        type: String,
        default: 'emerald',
    },
});

defineEmits(['update:modelValue']);

const passwordVisible = ref(false);

const hasError = computed(() => !!props.error);

const computedType = computed(() => {
    if (props.type === 'password' && props.showPasswordToggle) {
        return passwordVisible.value ? 'text' : 'password';
    }
    return props.type;
});
</script>

<template>
    <div class="space-y-2">
        <label v-if="label" :for="id" class="block text-sm font-medium text-slate-300">
            {{ label }}
        </label>
        <div class="relative">
            <div v-if="$slots.icon" class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <slot name="icon" />
            </div>
            <input
                :id="id"
                :type="computedType"
                :value="modelValue"
                :required="required"
                :autocomplete="autocomplete"
                :placeholder="placeholder"
                :disabled="disabled"
                @input="$emit('update:modelValue', $event.target.value)"
                class="w-full py-3.5 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 transition-all duration-200"
                :class="[
                    $slots.icon ? 'pl-12' : 'pl-4',
                    showPasswordToggle ? 'pr-12' : 'pr-4',
                    hasError
                        ? 'border-red-500 focus:ring-red-500/50 focus:border-red-500'
                        : `focus:ring-${color}-500/50 focus:border-${color}-500`,
                    disabled ? 'opacity-50 cursor-not-allowed' : '',
                ]"
            />
            <button
                v-if="showPasswordToggle && type === 'password'"
                type="button"
                @click="passwordVisible = !passwordVisible"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-300 transition-colors"
            >
                <svg v-if="!passwordVisible" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            </button>
        </div>
        <p v-if="error" class="text-red-400 text-sm mt-1">{{ error }}</p>
    </div>
</template>



