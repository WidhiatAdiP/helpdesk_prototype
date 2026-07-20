<!-- Profile/Index.vue -->
<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;

const avatarColors = [
    'bg-indigo-500',
    'bg-pink-500',
    'bg-emerald-500',
    'bg-amber-500',
    'bg-sky-500',
    'bg-violet-500',
];

const avatarColor = (name) => {
    if (!name) return avatarColors[0];
    return avatarColors[name.charCodeAt(0) % avatarColors.length];
};

const initials = (name) => {
    if (!name) return '?';
    return name.split(' ').map((w) => w[0]).slice(0, 2).join('').toUpperCase();
};

const roleStyle = {
    admin: 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20',
    agent: 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
    user: 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20',
};
</script>

<template>
    <Head title="Profile" />

    <AppLayout>
        <div class="mx-auto max-w-3xl p-6">
            <!-- Header Card -->
            <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <!-- Banner -->
                <div class="h-24 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500" />

                <div class="px-6 pb-6">
                    <!-- Avatar -->
                    <div class="flex items-end justify-between">
                        <div
                            class="-mt-10 flex h-20 w-20 items-center justify-center rounded-2xl text-xl font-bold text-white ring-4 ring-white"
                            :class="avatarColor(user.name)"
                        >
                            {{ initials(user.name) }}
                        </div>

                        <span
                            class="mb-1 inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize"
                            :class="roleStyle[user.role] ?? roleStyle.user"
                        >
                            {{ user.role }}
                        </span>
                    </div>

                    <div class="mt-3">
                        <h1 class="text-xl font-bold text-gray-900">
                            {{ user.name }}
                        </h1>
                        <p class="mt-0.5 text-sm text-gray-500">
                            {{ user.email }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="mb-2">
                <h2 class="text-lg font-bold tracking-tight text-gray-900">
                    Account Settings
                </h2>
                <p class="mt-0.5 text-sm text-gray-500">
                    Manage your profile and account information.
                </p>
            </div>

            <!-- Profile Form Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>
        </div>
    </AppLayout>
</template>