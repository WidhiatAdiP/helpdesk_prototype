<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Mail, Shield, CalendarDays } from 'lucide-vue-next';

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

const roleIcon = {
    admin: '🛡️',
    agent: '🎧',
    user: '👤',
};

const bannerGradient = (name) => {
    const gradients = [
        'from-indigo-500 via-purple-500 to-pink-500',
        'from-pink-500 via-rose-500 to-orange-400',
        'from-emerald-500 via-teal-500 to-cyan-500',
        'from-amber-400 via-orange-500 to-red-500',
        'from-sky-500 via-blue-500 to-indigo-500',
        'from-violet-500 via-purple-500 to-fuchsia-500',
    ];
    if (!name) return gradients[0];
    return gradients[name.charCodeAt(0) % gradients.length];
};
</script>

<template>
    <Head title="Profile" />

    <AppLayout>
        <div class="mx-auto max-w-3xl p-6">

            <!-- Profile Header Card -->
            <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <!-- Banner — warna menyesuaikan nama user -->
                <div
                    class="h-32 bg-gradient-to-r"
                    :class="bannerGradient(user.name)"
                />

                <div class="px-6 pb-6">
                    <div class="flex items-end justify-between">
                        <!-- Avatar -->
                        <div
                            class="-mt-12 flex h-24 w-24 items-center justify-center rounded-2xl text-2xl font-bold text-white ring-4 ring-white shadow-md"
                            :class="avatarColor(user.name)"
                        >
                            {{ initials(user.name) }}
                        </div>

                        <!-- Role badge -->
                        <span
                            class="mb-1 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold capitalize"
                            :class="roleStyle[user.role] ?? roleStyle.user"
                        >
                            {{ roleIcon[user.role] ?? '👤' }}
                            {{ user.role }}
                        </span>
                    </div>

                    <!-- Nama & info -->
                    <div class="mt-4">
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ user.name }}
                        </h1>

                        <div class="mt-2 flex flex-wrap items-center gap-4">
                            <span class="flex items-center gap-1.5 text-sm text-gray-500">
                                <Mail class="h-3.5 w-3.5 text-gray-400" />
                                {{ user.email }}
                            </span>
                            <span class="flex items-center gap-1.5 text-sm text-gray-500">
                                <Shield class="h-3.5 w-3.5 text-gray-400" />
                                <span class="capitalize">{{ user.role }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats bar -->
                <div class="grid grid-cols-3 divide-x divide-gray-100 border-t border-gray-100">
                    <div class="px-5 py-3.5 text-center">
                        <p class="text-xs font-medium text-gray-400">Role</p>
                        <p class="mt-0.5 text-sm font-semibold capitalize text-gray-700">
                            {{ user.role }}
                        </p>
                    </div>
                    <div class="px-5 py-3.5 text-center">
                        <p class="text-xs font-medium text-gray-400">Status</p>
                        <p class="mt-0.5 text-sm font-semibold text-green-600">
                            Active
                        </p>
                    </div>
                    <div class="px-5 py-3.5 text-center">
                        <p class="text-xs font-medium text-gray-400">Account</p>
                        <p class="mt-0.5 text-sm font-semibold text-gray-700">
                            Verified
                        </p>
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="mb-4">
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