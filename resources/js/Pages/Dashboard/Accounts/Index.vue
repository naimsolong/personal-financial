<script setup>
import { reactive, computed } from 'vue'
import { Link } from '@inertiajs/vue3';

import { Accordion, AccordionPanel, AccordionHeader, AccordionContent } from 'flowbite-vue'
import Dashboard from '@/Layouts/Dashboard.vue';
import Header from '@/Components/Dashboards/Header.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    accounts: Array,
});

const state  = reactive({
    asset_flag: true
})

const data = computed(() => {
    return state.asset_flag == true ? props.accounts.assets : props.accounts.liabilities
})

const account_type = computed(() => {
    return state.asset_flag == true ? 'Assets' : 'Liabilities'
})

const switch_toggle = () => {
    state.asset_flag = !state.asset_flag
}
</script>

<template>
    <Dashboard title="Accounts">
        <Header title="Accounts"/>

        <div class="flow-root mb-3">  
            <div class="float-left">
                <label class="relative inline-flex items-center align-middle cursor-pointer pr-3 border-r-4">
                    <input type="checkbox" value="" class="sr-only peer" @click="switch_toggle">
                    <div class="w-11 h-6 bg-blue-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 dark:peer-focus:ring-gray-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-red-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{ account_type }}</span>
                </label>
                
                <Link class="ml-3" :href="route('accounts.create', {'type': state.asset_flag ? 'A' : 'L'})">
                    <PrimaryButton>
                        Add New
                    </PrimaryButton>
                </Link>
            </div> 
            <Link class="float-right" :href="route('account.group.index')">
                <PrimaryButton>
                    Account Group
                </PrimaryButton>
            </Link>
        </div>
                  
        <Accordion :open-first-item="false">
            <accordion-panel v-for="account_group in data">
                <accordion-header>
                    {{ account_group.name }}
                </accordion-header>
                <accordion-content>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <tbody>
                            <tr v-for="account in account_group.accounts" class="bg-white border-b last:border-b-0 dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ account.name }}
                                    <br>
                                    {{ account.details.currency }} {{ account.details.latest_balance }}
                                </th>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="route('accounts.edit', {'account': account.id})">
                                        <PrimaryButton>
                                            Edit
                                        </PrimaryButton>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </accordion-content>
            </accordion-panel>
        </Accordion>
        
    </Dashboard>
</template>
