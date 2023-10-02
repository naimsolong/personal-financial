<script setup>
import { reactive, ref } from 'vue';
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

import VueTailwindDatepicker from "vue-tailwind-datepicker";

const props = defineProps({
    edit_mode: {
        type: Boolean,
        default: false
    },
    account_group: Object,
    types: Array,
    data: Object
});

const confirmingAccountDeletion = ref(false);
const form = useForm(props.data);

const state  = reactive({
    account_group: props.account_group.assets
})

const switchAccountGroup = () => {
    state.account_group = form.type == 'E' ? props.account_group.assets : props.account_group.liabilities
};

const confirmAccountDeletion = () => {
    confirmingAccountDeletion.value = true;
};

const closeModal = () => {
    confirmingAccountDeletion.value = false;
};

const submitForm = () => {
    let config = {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    }

    console.log(form)
    if(props.edit_mode)
        form.put(route('accounts.update', props.data.id), config);
    else
        form.post(route('accounts.store'), config);
};

const deleteAccount = () => {
    let config = {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    }

    if(props.edit_mode)
        form.delete(route('accounts.destroy', props.data.id), config);
    else
        console.log('Nothing happen');
};
</script>

<template>
    <Dashboard title="Accounts">
        <Header :title="(props.edit_mode ? 'Edit' : 'New') + ' - Accounts'"/>

        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <div class="mb-3">
                    <InputLabel for="account_group" value="Account Group" />
                    <SelectInput
                        id="account_group"
                        :select-options="state.account_group"
                        v-model="form.account_group"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="false"
                    />
                    <InputError :message="form.errors.account_group" class="mt-2" />
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
                    <ul class="items-center mt-1 block w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white" @change="switchAccountGroup">
                        <li v-for="account_type in props.types" class="w-full !m-0 pl-3 flex border-gray-200 rounded-t-lg dark:border-gray-600"><Radio name="type" v-model="form.type" :value="account_type.value" :label="account_type.text" /></li>
                    </ul>
                    <InputError :message="form.errors.type" class="mt-2" />
                </div>

                <div class="mb-3">
                    <InputLabel for="opening_date" value="Opening Date" />
                    <vue-tailwind-datepicker
                        v-model="form.opening_date"
                        as-single
                        placeholder="Select Date"
                        :formatter="{
                            date: 'DD/MM/YYYY',
                            month: 'MMM',
                        }"
                    />
                    <InputError :message="form.errors.opening_date" class="mt-2" />
                </div>

                <div class="mb-3">
                    <InputLabel for="starting_balance" value="Starting Balance" />
                    <TextInput
                        id="starting_balance"
                        v-model="form.starting_balance"
                        type="number"
                        class="mt-1 block w-full"
                        autocomplete="false"
                    />
                    <InputError :message="form.errors.starting_balance" class="mt-2" />
                </div>

                <div class="mb-3">
                    <InputLabel for="currency" value="Currency" />
                    <TextInput
                        id="currency"
                        v-model="form.currency"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="false"
                    />
                    <InputError :message="form.errors.currency" class="mt-2" />
                </div>

                <div class="mb-3">
                    <InputLabel for="notes" value="Notes" />
                    <TextInput
                        id="notes"
                        v-model="form.notes"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="false"
                    />
                    <InputError :message="form.errors.notes" class="mt-2" />
                </div>
            </div>
            
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <div class="flow-root">
                <div v-if="props.edit_mode" class="float-left">
                    <DangerButton @click="confirmAccountDeletion">
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

    <!-- Delete Account Modal -->
    <DialogModal :show="confirmingAccountDeletion" @close="closeModal">
        <template #title>
            Delete Account
        </template>

        <template #content>
            Are you sure you want to delete this account?
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancel
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteAccount"
            >
                Yes
            </DangerButton>
        </template>
    </DialogModal>
</template>
