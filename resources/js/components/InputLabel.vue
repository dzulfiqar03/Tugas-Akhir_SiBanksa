<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1.5 ml-1">
        <span v-if="value">{{ value }}*</span>
        <span v-else>
            <slot />*
        </span>
    </label>
</template>
