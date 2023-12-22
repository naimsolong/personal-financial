<script setup>
import { reactive, computed } from 'vue'
import { Link } from '@inertiajs/vue3';

import { Accordion, AccordionPanel, AccordionHeader, AccordionContent } from 'flowbite-vue'
import Dashboard from '@/Layouts/Dashboard.vue';
import Header from '@/Components/Dashboards/Header.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    categories: Array,
});

const state  = reactive({
    expense_flag: true,
    show_add_button: true
})

const data = computed(() => {
    let category_data = state.expense_flag == true ? props.categories.expense : props.categories.income

    state.show_add_button = Object.keys(category_data).length

    return category_data
})

const category_type = computed(() => {
    return state.expense_flag == true ? 'Expense' : 'Income'
})

const switch_toggle = () => {
    state.expense_flag = !state.expense_flag
}

</script>

<template>
    <Dashboard title="Categories">
        <Header title="Categories"/>

        <div class="flow-root mb-3">  
            <div class="float-left">
                <label class="relative inline-flex items-center align-middle cursor-pointer mr-3">
                    <input type="checkbox" value="" class="sr-only peer" @click="switch_toggle">
                    <div class="w-11 h-6 bg-red-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 dark:peer-focus:ring-gray-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{ category_type }}</span>
                </label>
                
                <Link v-if="state.show_add_button" class="border-l-4 pl-3" :href="route('categories.create', {'type': state.expense_flag ? 'E' : 'I'})">
                    <PrimaryButton>
                        Add New
                    </PrimaryButton>
                </Link>
            </div> 
            <Link class="float-right" :href="route('category.group.index')">
                <PrimaryButton>
                    Category Group
                </PrimaryButton>
            </Link>
        </div>
                  
        <Accordion :open-first-item="false">
            <accordion-panel v-for="category_group in data">
                <accordion-header>
                    {{ category_group.name }}
                </accordion-header>
                <accordion-content>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <tbody>
                            <tr v-for="category in category_group.categories" class="bg-white border-b last:border-b-0 dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ category.name }}
                                </th>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="route('categories.edit', {'category': category.id})">
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
