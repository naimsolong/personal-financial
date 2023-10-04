<script setup>
import { onMounted, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

import Dashboard from '@/Layouts/Dashboard.vue'
import Header from '@/Components/Dashboards/Header.vue'
import axios from 'axios';
import debounce from 'lodash/debounce';

const props = defineProps({
    data: Object,
});

const transactions = reactive({
    next_page: props.data.next_page,
    data: props.data.transaction
});

onMounted(() => {
    window.addEventListener('scroll', debounce((e) => {
        let pixelsFromBottom = document.documentElement.offsetHeight - document.documentElement.scrollTop - window.innerHeight
        
        if(pixelsFromBottom < 400 && transactions.next_page != null) {
            axios.post(route('transactions.index'), {'next_page': transactions.next_page}).then(response => {
                
                for (const index of Object.keys(response.data.transaction)) {
                    transactions.data[index] = response.data.transaction[index];
                }

                transactions.next_page = response.data.next_page
                console.log(transactions)
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

        <ul>
            <li v-for="(data_by_date, date) in transactions.data" class="border bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-3 mb-3">
                <div class="border bg-white dark:bg-gray-800 sm:rounded-lg flow-root px-3 py-4">
                    <div class="float-left">
                        {{ date }}
                    </div>

                    <div class="float-right">
                        {{ totalSummary(data_by_date) }}
                    </div>
                </div>
                <div class="px-3 pb-4">
                    <div v-for="(transaction, index) in data_by_date" class="flow-root my-5">
                        <div class="float-left">
                            {{ (transaction.type != 'T') ? transaction.category.name : '[TRANSFER]' }}
                            <br>
                            {{ transaction.account.name }}
                        </div>
                        <div class="float-right">
                            {{ transaction.currency }} {{ transaction.amount }}
                            <br>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </Dashboard>
</template>
