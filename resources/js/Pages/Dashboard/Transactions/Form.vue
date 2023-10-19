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
import GroupSelectInput from '@/Components/GroupSelectInput.vue';

import VueTailwindDatepicker from "vue-tailwind-datepicker";

const props = defineProps({
    edit_mode: {
        type: Boolean,
        default: false
    },
    accounts: Object,
    categories: Object,
    types: Object,
    statuses: Object,
    data: Object,
});

const confirmingTransactionDeletion = ref(false);
const form = useForm(props.data);

const state  = reactive({
    categories: props.categories.expense
})

onMounted(() => {
    switchCategoriesGroup()
})

const getTransactionType = () => {
    if(form.type == 'E')
        return 'Expense'
    else if(form.type == 'I')
        return 'Income'
    else
        return 'Transfer'
}

const switchCategoriesGroup = () => {
    state.categories = form.type == 'E' ? props.categories.expense : props.categories.income
};

const confirmTransactionDeletion = () => {
    confirmingTransactionDeletion.value = true;
};

const closeModal = () => {
    confirmingTransactionDeletion.value = false;
};

const submitForm = () => {
    let config = {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    }

    if(props.edit_mode)
        form.put(route('transactions.update', props.data.id), config);
    else
        form.post(route('transactions.store'), config);
};

const deleteTransaction = () => {
    let config = {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    }

    if(props.edit_mode)
        form.delete(route('transactions.destroy', props.data.id), config);
    else
        console.log('Nothing happen');
};
</script>

<template>
    <Dashboard title="Transaction">
        <Header :title="(props.edit_mode ? 'Edit ' : 'New ') + getTransactionType() +  ' - Transaction'"/>

        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <div class="mb-3">
                    <InputLabel for="amount" value="Amount" />
                    <TextInput
                        id="amount"
                        v-model="form.amount"
                        type="number"
                        class="mt-1 block w-full"
                        autocomplete="false"
                    />
                    <InputError :message="form.errors.amount" class="mt-2" />
                </div>

                <div class="grid md:grid-cols-2 gap-x-3">
                    <div class="mb-3">
                        <InputLabel for="due_date" value="Date" />
                        <vue-tailwind-datepicker
                            v-model="form.due_date"
                            as-single
                            placeholder="Select Date"
                            :formatter="{
                                date: 'DD/MM/YYYY',
                                month: 'MMM',
                            }"
                        />
                        <InputError :message="form.errors.due_date" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="due_time" value="Time" />
                        <TextInput
                            id="due_time"
                            v-model="form.due_time"
                            type="time"
                            class="block w-full"
                            autocomplete="false"
                        />
                        <InputError :message="form.errors.due_time" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-x-3">
                    <div class="basis-1/2 mb-3">
                        <InputLabel for="account_from" :value="(form.type == 'T' ? 'From ': '') + 'Accounts'" />
                        <GroupSelectInput
                            id="account_from"
                            :select-options="props.accounts"
                            option-key="accounts"
                            v-model="form.account_from"
                            class="mt-1 block w-full"
                            autocomplete="false"
                        />
                        <InputError :message="form.errors.account_from" class="mt-2" />
                    </div>

                    <div class="hidden md:block pt-8">
                        <svg v-if="form.type == 'E' || form.type == 'T'" class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                        <svg v-else class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                        </svg>
                    </div>

                    <div class="basis-1/2 mb-3" v-if="form.type != 'T'">
                        <InputLabel for="category" value="Categories" />
                        <GroupSelectInput
                            id="category"
                            :select-options="state.categories"
                            option-key="categories"
                            v-model="form.category"
                            class="mt-1 block w-full"
                            autocomplete="false"
                        />
                        <InputError :message="form.errors.category" class="mt-2" />
                    </div>
                    
                    <div class="basis-1/2 mb-3" v-else>
                        <InputLabel for="account_to" :value="(form.type == 'T' ? 'To ': '') + 'Accounts'" />
                        <GroupSelectInput
                            id="account_to"
                            :select-options="props.accounts"
                            option-key="accounts"
                            v-model="form.account_to"
                            class="mt-1 block w-full"
                            autocomplete="false"
                        />
                        <InputError :message="form.errors.account_to" class="mt-2" />
                    </div>
                </div>

                <div class="mb-3">
                    <InputLabel for="status" value="Status" />                    
                    <ul class="items-center mt-1 block w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <li v-for="transaction_status in props.statuses" class="w-full !m-0 pl-3 flex border-gray-200 rounded-t-lg dark:border-gray-600"><Radio name="status" v-model="form.status" :value="transaction_status.value" :label="transaction_status.text" /></li>
                    </ul>
                    <InputError :message="form.errors.type" class="mt-2" />
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
                    <DangerButton @click="confirmTransactionDeletion">
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
    <DialogModal :show="confirmingTransactionDeletion" @close="closeModal">
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
                @click="deleteTransaction"
            >
                Yes
            </DangerButton>
        </template>
    </DialogModal>
</template>
