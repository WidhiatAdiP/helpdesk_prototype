<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    User,
    Mail,
    Lock,
    Eye,
    EyeOff,
    UserCircle,
    Headset,
    ShieldCheck,
    CheckCircle2,
    Check,
} from 'lucide-vue-next';

const showPassword = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'user',
});

const submit = () => {
    form.post(route('users.store'));
};

const roleOptions = [
    {
        value: 'user',
        label: 'User',
        description: 'Can create tickets.',
        icon: UserCircle,
        activeClasses: 'border-green-500 bg-green-50',
        iconClasses: 'text-green-600',
    },
    {
        value: 'agent',
        label: 'Agent',
        description: 'Can manage tickets.',
        icon: Headset,
        activeClasses: 'border-blue-500 bg-blue-50',
        iconClasses: 'text-blue-600',
    },
    {
        value: 'admin',
        label: 'Admin',
        description: 'Full system access.',
        icon: ShieldCheck,
        activeClasses: 'border-red-500 bg-red-50',
        iconClasses: 'text-red-600',
    },
];

// Indikator kekuatan password sederhana
const passwordStrength = computed(() => {
    const value = form.password;
    if (!value) return { score: 0, label: '', color: '' };

    let score = 0;
    if (value.length >= 8) score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[0-9]/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;

    const levels = [
        { label: 'Very weak', color: 'bg-red-400' },
        { label: 'Weak', color: 'bg-orange-400' },
        { label: 'Fair', color: 'bg-yellow-400' },
        { label: 'Good', color: 'bg-lime-500' },
        { label: 'Strong', color: 'bg-green-500' },
    ];

    return { score, ...levels[score] };
});
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        Create New User
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Add a new administrator, agent or user.
                    </p>
                </div>

                <Link
                    :href="route('users.index')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back
                </Link>
            </div>

            <!-- Form Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <form class="space-y-6" @submit.prevent="submit">
                    <!-- Name -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Full Name
                        </label>

                        <div class="relative">
                            <User class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />

                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Enter full name"
                                class="w-full rounded-xl border py-2.5 pl-10 pr-4 text-sm transition focus:outline-none focus:ring-2"
                                :class="form.errors.name
                                    ? 'border-red-300 focus:ring-red-100'
                                    : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100'"
                            >
                        </div>

                        <p v-if="form.errors.name" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Email Address
                        </label>

                        <div class="relative">
                            <Mail class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />

                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="example@mail.com"
                                class="w-full rounded-xl border py-2.5 pl-10 pr-4 text-sm transition focus:outline-none focus:ring-2"
                                :class="form.errors.email
                                    ? 'border-red-300 focus:ring-red-100'
                                    : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100'"
                            >
                        </div>

                        <p v-if="form.errors.email" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Password
                        </label>

                        <div class="relative">
                            <Lock class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />

                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Enter password"
                                class="w-full rounded-xl border py-2.5 pl-10 pr-11 text-sm transition focus:outline-none focus:ring-2"
                                :class="form.errors.password
                                    ? 'border-red-300 focus:ring-red-100'
                                    : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100'"
                            >

                            <button
                                type="button"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-gray-600"
                                @click="showPassword = !showPassword"
                            >
                                <EyeOff v-if="showPassword" class="h-4 w-4" />
                                <Eye v-else class="h-4 w-4" />
                            </button>
                        </div>

                        <!-- Strength indicator -->
                        <div v-if="form.password" class="mt-2">
                            <div class="flex gap-1">
                                <div
                                    v-for="i in 4"
                                    :key="i"
                                    class="h-1.5 flex-1 rounded-full transition-colors"
                                    :class="i <= passwordStrength.score ? passwordStrength.color : 'bg-gray-100'"
                                ></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ passwordStrength.label }}
                            </p>
                        </div>

                        <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="mb-2.5 block text-sm font-semibold text-gray-700">
                            User Role
                        </label>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <label
                                v-for="option in roleOptions"
                                :key="option.value"
                                class="relative cursor-pointer rounded-xl border p-4 transition hover:border-indigo-300"
                                :class="form.role === option.value
                                    ? option.activeClasses
                                    : 'border-gray-200'"
                            >
                                <input
                                    v-model="form.role"
                                    :value="option.value"
                                    type="radio"
                                    class="hidden"
                                >

                                <div
                                    v-if="form.role === option.value"
                                    class="absolute right-3 top-3 flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-sm"
                                >
                                    <Check class="h-3.5 w-3.5" :class="option.iconClasses" />
                                </div>

                                <component
                                    :is="option.icon"
                                    class="h-7 w-7"
                                    :class="option.iconClasses"
                                />

                                <h3 class="mt-2.5 text-sm font-bold text-gray-800">
                                    {{ option.label }}
                                </h3>

                                <p class="mt-0.5 text-xs text-gray-500">
                                    {{ option.description }}
                                </p>
                            </label>
                        </div>

                        <p v-if="form.errors.role" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.role }}
                        </p>
                    </div>

                    <!-- Button -->
                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-6">
                        <Link
                            :href="route('users.index')"
                            class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                        >
                            Cancel
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                        >
                            <CheckCircle2 class="h-4 w-4" />
                            {{ form.processing ? 'Creating...' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>