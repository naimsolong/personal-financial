<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    selectOptions: Array,
    optionKey: String,
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <select
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        :value="props.modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
        <optgroup v-for="selectOption in props.selectOptions" :label="selectOption.label">
            <option v-for="option in selectOption[props.optionKey]" v-bind:value="option.value">{{ option.text }}</option>
        </optgroup>
    </select>
</template>
