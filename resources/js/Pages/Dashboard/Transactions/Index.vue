<script setup>
import axios from 'axios';
import debounce from 'lodash/debounce';
import { onMounted, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'

import { Dropdown } from 'flowbite';

import Dashboard from '@/Layouts/Dashboard.vue'
import Header from '@/Components/Dashboards/Header.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    data: Object,
});

const transactions = reactive({
    next_page: props.data.next_page,
    data: props.data.transaction
});

onMounted(() => {
    // set the dropdown menu element
    const $targetEl = document.getElementById('addNewHover');

    // set the element that trigger the dropdown menu on click
    const $triggerEl = document.getElementById('addNewHoverButton');
        
    const dropdown = new Dropdown($targetEl, $triggerEl);

    window.addEventListener('scroll', debounce((e) => {
        let pixelsFromBottom = document.documentElement.offsetHeight - document.documentElement.scrollTop - window.innerHeight
        
        if(pixelsFromBottom < 400 && transactions.next_page != null) {
            axios.post(route('transactions.partial'), {'next_page': transactions.next_page}).then(response => {
                for (const index of Object.keys(response.data.transaction)) {
                    transactions.data[index] = response.data.transaction[index];
                }

                transactions.next_page = response.data.next_page
            })
        }
    }, 100))
})

const totalSummary = (transaction) => {
    let total = 0;
    
    let count = transaction.length

    for(let i = 0; i < count; i++) {
        total += transaction[i].amount
    }

    return transaction[0].currency + ' ' + total
}
</script>

<template>
    <Dashboard title="Transactions">
        <Header title="Transactions"/>

        <div class="mb-3">  
            <PrimaryButton id="addNewHoverButton" data-dropdown-toggle="addNewHover" data-dropdown-trigger="click" type="button">
                Add New
                <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </PrimaryButton>

            <!-- Dropdown menu -->
            <div id="addNewHover" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="addNewHoverButton">
                    <li>
                        <Link :href="route('transactions.create', {type: 'E'})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                            Expense
                        </Link>
                        <Link :href="route('transactions.create', {type: 'I'})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                            Income
                        </Link>
                        <Link :href="route('transactions.create', {type: 'T'})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                            Transfer
                        </Link>
                    </li>
                </ul>
            </div>
        </div>

        <div v-for="(data_by_date, date) in transactions.data" class="border bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-3 mb-3">
            <div class="border bg-white dark:bg-gray-800 sm:rounded-lg flow-root px-3 py-4">
                <div class="float-left">
                    {{ date }}
                </div>

                <div class="float-right">
                    {{ totalSummary(data_by_date) }}
                </div>
            </div>
            <div class="px-3 pb-4">
                <Link :href="route('transactions.edit', {'transaction': transaction.id})" v-for="(transaction, index) in data_by_date" class="flow-root my-5 hover:px-6 hover:py-3 hover:border hover:bg-white dark:hover:bg-gray-800 hover:shadow-md hover:rounded-lg hover:scale-105">
                    <div class="float-left">
                        {{ (transaction.type != 'T') ? transaction.category.name : '[TRANSFER]' }}
                        <br>
                        {{ transaction.account.name }}
                    </div>
                    <div class="float-right">
                        {{ transaction.currency }} {{ transaction.amount }}
                        <br>
                    </div>
                </Link>
            </div>
        </div>
    </Dashboard>
</template>
