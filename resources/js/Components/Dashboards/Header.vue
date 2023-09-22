<script setup>
const props = defineProps({
    title: String,
});
</script>

<template>
    <h1 class="text-lg font-bold">{{ props.title }}</h1>
    <hr class="my-4">
</template>