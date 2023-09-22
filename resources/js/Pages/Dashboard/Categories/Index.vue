<script setup>
import { reactive, computed } from 'vue'
import { Link } from '@inertiajs/vue3';

import { Accordion, AccordionPanel, AccordionHeader, AccordionContent } from 'flowbite-vue'
import Dashboard from '@/Layouts/Dashboard.vue';
import Header from '@/Components/Dashboards/Header.vue';

const props = defineProps({
    categories: Array,
});

const state  = reactive({
    expense_flag: true
})

const data = computed(() => {
    return state.expense_flag == true ? props.categories.expense : props.categories.income
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
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" value="" class="sr-only peer" @click="switch_toggle">
                    <div class="w-11 h-6 bg-red-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 dark:peer-focus:ring-gray-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{ category_type }}</span>
                </label>
            </div> 
            <Link class="float-right" :href="route('category.group')">Category Group</Link>
        </div>


                            
        <Accordion>
            <accordion-panel v-for="category_group in data">
                <accordion-header>
                    {{ category_group.name }}
                </accordion-header>
                <accordion-content>
                    <div v-for="category in category_group.categories" class="ml-5 mb-3 w-full flex flex-row"> 
                        {{ category.name }}
                        <svg class="scale-[0.7] ml-2 w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                            <path d="M12.687 14.408a3.01 3.01 0 0 1-1.533.821l-3.566.713a3 3 0 0 1-3.53-3.53l.713-3.566a3.01 3.01 0 0 1 .821-1.533L10.905 2H2.167A2.169 2.169 0 0 0 0 4.167v11.666A2.169 2.169 0 0 0 2.167 18h11.666A2.169 2.169 0 0 0 16 15.833V11.1l-3.313 3.308Zm5.53-9.065.546-.546a2.518 2.518 0 0 0 0-3.56 2.576 2.576 0 0 0-3.559 0l-.547.547 3.56 3.56Z"/>
                            <path d="M13.243 3.2 7.359 9.081a.5.5 0 0 0-.136.256L6.51 12.9a.5.5 0 0 0 .59.59l3.566-.713a.5.5 0 0 0 .255-.136L16.8 6.757 13.243 3.2Z"/>
                        </svg>
                    </div>
                </accordion-content>
            </accordion-panel>
        </Accordion>
        
    </Dashboard>
</template>
