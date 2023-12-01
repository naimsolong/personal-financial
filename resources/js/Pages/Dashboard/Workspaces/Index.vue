<script setup>
import { reactive, computed } from 'vue'
import { Link } from '@inertiajs/vue3';

import { Accordion, AccordionPanel, AccordionHeader, AccordionContent } from 'flowbite-vue'
import Dashboard from '@/Layouts/Dashboard.vue';
import Header from '@/Components/Dashboards/Header.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    workspaces: Array,
});
</script>

<template>
    <Dashboard title="Workspaces">
        <Header title="Workspaces"/>

        <div class="flow-root mb-3">  
            <div class="float-left">                
                <Link class="ml-3" :href="route('workspaces.create')">
                    <PrimaryButton>
                        Add New
                    </PrimaryButton>
                </Link>
            </div> 
        </div>
                  
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <tbody>
                <tr v-for="workspace in workspaces" class="bg-white border-b last:border-b-0 dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ workspace.name }}
                    </th>
                    <td class="px-6 py-4 text-right">
                        <Link :href="route('workspaces.edit', {'workspace': workspace.id})">
                            <PrimaryButton>
                                Edit
                            </PrimaryButton>
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
        
    </Dashboard>
</template>
