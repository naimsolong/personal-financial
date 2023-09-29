<script setup>
import { useForm } from '@inertiajs/vue3';

import Dashboard from '@/Layouts/Dashboard.vue';
import Header from '@/Components/Dashboards/Header.vue';

import { Radio } from 'flowbite-vue'

import ActionMessage from '@/Components/ActionMessage.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    edit_mode: {
        type: Boolean,
        default: false
    },
    types: Array,
    data: Object
});

const form = useForm(props.data);

const submitForm = () => {
    let config = {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    }

    if(props.edit_mode)
        form.put(route('category.group.update', props.data.id), config);
    else
        form.post(route('category.group.store'), config);
};
</script>

<template>
    <Dashboard title="Category Group">
        <Header title="Add New - Category Group"/>

        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <div class="mb-3">
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="false"
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="mb-3">
                    <InputLabel for="type" value="Type" />                    
                    <ul class="items-center mt-1 block w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <li v-for="transaction_type in props.types" class="w-full !m-0 pl-3 flex border-gray-200 rounded-t-lg dark:border-gray-600"><Radio name="type" v-model="form.type" :value="transaction_type.value" :label="transaction_type.text" /></li>
                    </ul>
                    <InputError :message="form.errors.type" class="mt-2" />
                </div>
            </div>
            
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </form>
    </Dashboard>
</template>
