<script setup>
import { onMounted, ref } from 'vue';


defineProps({

    isEdit: {
        type: Boolean,
    },

});
const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input :disabled="isEdit === true"
        class="rounded-md  shadow-sm focus:border-indigo-500 focus:ring-indigo-500  dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        :class="[
            isEdit === true?'border-white bg-gray-50 dark:border-gray-700':'border-gray-300 bg-white dark:border-gray-700'
        ]"
        v-model="model"
        ref="input"
    />
</template>
