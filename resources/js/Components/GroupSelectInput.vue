<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    selectOptions: Array,
    optionKey: String,
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    // if (input.value.hasAttribute('autofocus')) {
    //     input.value.focus();
    // }
});

defineExpose({ focus: () => input.value.focus() });

const test = (entries) => {
    console.log(Object.fromEntries(Object.entries(entries)))
    return entries.categories
}
</script>

<template>
    <select
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
        <optgroup v-for="selectOption in selectOptions" :label="selectOption.label">
            <option v-for="option in selectOption[optionKey]" v-bind:value="option.value">{{ option.text }}</option>
        </optgroup>
    </select>
</template>
