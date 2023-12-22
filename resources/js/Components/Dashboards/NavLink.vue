<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: String,
    href: String,
    active: Boolean,
    message: { type: String, default: ''},
    count: { type: Number, default: 0},
});

const classes = computed(() => {
    return props.active
        ? 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white bg-gray-100 dark:bg-gray-700 group'
        : 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
        <span class="flex-1 ml-3 whitespace-nowrap">{{ props.title }}</span>
        <span v-if="props.message != ''" class="inline-flex items-center justify-center px-2 ml-3 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">{{ props.message }}</span>
        <span v-if="props.count > 0" class="inline-flex items-center justify-center w-3 h-3 p-3 ml-3 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-900 dark:text-gray-300">{{ props.count }}</span>
    </Link>
</template>