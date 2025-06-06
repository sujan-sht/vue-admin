<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { type BreadcrumbItem } from '@/types';
import { useToast } from "primevue/usetoast";
import Toast from 'primevue/toast';
import { reactive } from 'vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Message from 'primevue/message';
import MultiSelect from 'primevue/multiselect';
import { Form } from '@primevue/forms';

const toast = useToast();

const props = defineProps({
    user: {
        type: Object,
        default: () => ({})

    },
    roles: {
        type: Object,
        default: () => ({})
    }
});
const isUserEmpty = !props.user.data || Object.keys(props.user.data).length === 0;
const initialValues = reactive({
    name: !isUserEmpty ? props.user.data.name : '',
    email: !isUserEmpty ? props.user.data.email : '',
    password: '',
    confirm_password: '',
    role: !isUserEmpty ? props.user.data.roles : [],
});

const resolver = ({ values }) => {
    const errors = {};

    if (!values.name) {
        errors.name = [{ message: 'Name is required.' }];
    }

    if (!values.email) {
        errors.email = [{ message: 'Email is required.' }];
    }

    if (!values.password) {
        errors.password = [{ message: 'Password is required.' }];
    }

    if (!values.confirm_password) {
        errors.confirm_password = [{ message: 'Confirm password is required.' }];
    } else if (values.password !== values.confirm_password) {
        errors.confirm_password = [{ message: 'Passwords must match.' }];
    }

    if (!values.role || values.role.length === 0) {
        errors.role = [{ message: 'Please select at least one role.' }];
    }

    return { values, errors };
};

const onFormSubmit = ({ valid, values }) => {
    if (valid) {
        // Transform role objects to an array of IDs
        values.role = values.role.map(role => role.id);

        const callbacks = {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: `User ${isUserEmpty ? 'created' : 'updated'} successfully!`,
                    life: 3000
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Validation failed',
                    detail: 'Please check the form fields.',
                    life: 3000
                });
            }
        };
        const request = !isUserEmpty
            ? router.put(route('users.update', props.user.data), values, callbacks)
            : router.post(route('users.store'), values, callbacks);

        return request;
    }
};
const activePageLabel = !isUserEmpty ? 'Edit' : 'Create';
const url = !isUserEmpty ? route('users.edit', props.user.data) : route('users.create');
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Users', href: route('users.index') },
    { title: activePageLabel, href: url },

];
</script>

<template>

    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-end">
                <Link :href="route('users.index')">
                <Button label="Back" severity="info" raised />
                </Link>
            </div>

            <Toast />

            <div class="flex justify-center">
                <Form v-slot="$form" :initialValues :resolver @submit="onFormSubmit"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full md:w-[800px]">
                    <div class="flex flex-col gap-1">
                        <label for="name" class="font-medium">User Name</label>
                        <InputText id="name" name="name" type="text" placeholder="Enter User Name" fluid />
                        <Message v-if="$form.name?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.name.error?.message }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="email" class="font-medium">Email Address</label>
                        <InputText id="email" name="email" type="email" placeholder="Enter Email Address" fluid />
                        <Message v-if="$form.email?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.email.error?.message }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="password" class="font-medium">Password</label>
                        <Password id="password" name="password" promptLabel="Choose a password" weakLabel="Too simple"
                            mediumLabel="Average complexity" strongLabel="Complex password" class="w-full"
                            inputClass="w-full" />
                        <Message v-if="$form.password?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.password.error?.message }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="confirm_password" class="font-medium">Confirm Password</label>
                        <Password id="confirm_password" name="confirm_password" promptLabel="Choose a password"
                            weakLabel="Too simple" mediumLabel="Average complexity" strongLabel="Complex password"
                            class="w-full" inputClass="w-full" />
                        <Message v-if="$form.confirm_password?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.confirm_password.error?.message }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1 ">
                        <label for="role" class="font-medium">Role</label>
                        <MultiSelect id="role" name="role" :options="props.roles.roles" optionLabel="name" filter
                            placeholder="Select Role" :maxSelectedLabels="3" class="w-full" />
                        <Message v-if="$form.role?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.role.error?.message }}
                        </Message>
                    </div>

                    <div class="md:col-span-2 flex justify-center">
                        <Button type="submit" severity="success" :label="activePageLabel" />
                    </div>

                </Form>
            </div>


        </div>
    </AppLayout>
</template>
