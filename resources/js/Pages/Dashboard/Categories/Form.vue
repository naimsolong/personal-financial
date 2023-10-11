<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import Dashboard from '@/Layouts/Dashboard.vue';
import Header from '@/Components/Dashboards/Header.vue';

import { Radio } from 'flowbite-vue'

import ActionMessage from '@/Components/ActionMessage.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    edit_mode: {
        type: Boolean,
        default: false
    },
    category_group: Object,
    types: Array,
    data: Object
});

const confirmingCategoryDeletion = ref(false);
const form = useForm(props.data);

const state  = reactive({
    category_group: props.category_group.expense
})

onMounted(() => {
    switchCategoryGroup()
})

const switchCategoryGroup = () => {
    state.category_group = form.type == 'E' ? props.category_group.expense : props.category_group.income
};

const confirmCategoryDeletion = () => {
    confirmingCategoryDeletion.value = true;
};

const closeModal = () => {
    confirmingCategoryDeletion.value = false;
};

const submitForm = () => {
    let config = {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    }

    if(props.edit_mode)
        form.put(route('categories.update', props.data.id), config);
    else
        form.post(route('categories.store'), config);
};

const deleteCategory = () => {
    let config = {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    }

    if(props.edit_mode)
        form.delete(route('categories.destroy', props.data.id), config);
    else
        console.log('Nothing happen');
};
</script>

<template>
    <Dashboard title="Categories">
        <Header :title="(props.edit_mode ? 'Edit' : 'New') + ' - Categories'"/>

        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <div class="mb-3">
                    <InputLabel for="category_group" value="Category Group" />
                    <SelectInput
                        id="category_group"
                        :select-options="state.category_group"
                        v-model="form.category_group"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="false"
                    />
                    <InputError :message="form.errors.category_group" class="mt-2" />
                </div>

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
                    <ul class="items-center mt-1 block w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white" @change="switchCategoryGroup">
                        <li v-for="transaction_type in props.types" class="w-full !m-0 pl-3 flex border-gray-200 rounded-t-lg dark:border-gray-600"><Radio name="type" v-model="form.type" :value="transaction_type.value" :label="transaction_type.text" /></li>
                    </ul>
                    <InputError :message="form.errors.type" class="mt-2" />
                </div>
            </div>
            
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <div class="flow-root">
                <div v-if="props.edit_mode" class="float-left">
                    <DangerButton @click="confirmCategoryDeletion">
                        Delete
                    </DangerButton>
                </div>
                <div class="float-right">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </Dashboard>

    <!-- Delete Category Modal -->
    <DialogModal :show="confirmingCategoryDeletion" @close="closeModal">
        <template #title>
            Delete Category
        </template>

        <template #content>
            Are you sure you want to delete this category?
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancel
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteCategory"
            >
                Yes
            </DangerButton>
        </template>
    </DialogModal>
</template>
