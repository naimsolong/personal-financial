<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import Dashboard from '@/Layouts/Dashboard.vue';
import Header from '@/Components/Dashboards/Header.vue';

import ActionMessage from '@/Components/ActionMessage.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    edit_mode: {
        type: Boolean,
        default: false
    },
    data: Object
});

const confirmingWorkspaceDeletion = ref(false);
const form = useForm(props.data);

const confirmWorkspaceDeletion = () => {
    confirmingWorkspaceDeletion.value = true;
};

const closeModal = () => {
    confirmingWorkspaceDeletion.value = false;
};

const submitForm = () => {
    let config = {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    }

    if(props.edit_mode)
        form.put(route('workspaces.update', props.data.id), config);
    else
        form.post(route('workspaces.store'), config);
};

const deleteWorkspace = () => {
    let config = {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    }

    if(props.edit_mode)
        form.delete(route('workspaces.destroy', props.data.id), config);
    else
        console.log('Nothing happen');
};
</script>

<template>
    <Dashboard title="Workspace">
        <Header :title="(props.edit_mode ? 'Edit' : 'New') + ' - Workspace'"/>

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
            </div>
            
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <div class="flow-root">
                <div v-if="props.edit_mode" class="float-left">
                    <DangerButton @click="confirmWorkspaceDeletion">
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

    <!-- Delete Workspace Modal -->
    <DialogModal :show="confirmingWorkspaceDeletion" @close="closeModal">
        <template #title>
            Delete Workspace
        </template>

        <template #content>
            Are you sure you want to delete this workspace?
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancel
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteWorkspace"
            >
                Yes
            </DangerButton>
        </template>
    </DialogModal>
</template>
