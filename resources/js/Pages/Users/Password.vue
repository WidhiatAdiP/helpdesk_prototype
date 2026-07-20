<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Lock,
    Eye,
    EyeOff,
    KeyRound,
    ShieldCheck,
} from 'lucide-vue-next';

const props = defineProps({
    user: Object,
});

const form = useForm({
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.patch(route('users.password.update', props.user.id), {
        onSuccess: () => form.reset(),
    });
};

const avatarColors = [
    'bg-indigo-100 text-indigo-700',
    'bg-pink-100 text-pink-700',
    'bg-emerald-100 text-emerald-700',
    'bg-amber-100 text-amber-700',
    'bg-sky-100 text-sky-700',
    'bg-violet-100 text-violet-700',
];

const avatarColor = (name) => {
    if (!name) return avatarColors[0];
    return avatarColors[name.charCodeAt(0) % avatarColors.length];
};

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

const passwordsMatch = computed(() => {
    if (!form.password_confirmation) return null;
    return form.password === form.password_confirmation;
});
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-xl p-6">
            <!-- Back link -->
            <Link
                :href="route('users.index')"
                class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 transition hover:text-gray-700"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Users
            </Link>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <!-- Header -->
                <div class="mb-6 flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-base font-semibold"
                        :class="avatarColor(user.name)"
                    >
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900">
                            Change Password
                        </h1>
                        <p class="mt-0.5 text-sm text-gray-500">
                            for <span class="font-medium text-gray-700">{{ user.name }}</span>
                        </p>
                    </div>
                </div>

                <div class="mb-6 flex items-start gap-2.5 rounded-xl bg-amber-50 p-3.5 text-xs text-amber-700 ring-1 ring-inset ring-amber-600/10">
                    <ShieldCheck class="h-4 w-4 shrink-0 translate-y-0.5" />
                    <p>
                        This will immediately change the password for this account. The user
                        will need to sign in again with the new password.
                    </p>
                </div>

                <form class="space-y-5" @submit.prevent="submit">
                    <!-- New Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            New Password
                        </label>

                        <div class="relative">
                            <Lock class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />

                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Enter new password"
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

                    <!-- Confirm Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Confirm Password
                        </label>

                        <div class="relative">
                            <Lock class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />

                            <input
                                v-model="form.password_confirmation"
                                :type="showPasswordConfirmation ? 'text' : 'password'"
                                placeholder="Confirm new password"
                                class="w-full rounded-xl border py-2.5 pl-10 pr-11 text-sm transition focus:outline-none focus:ring-2"
                                :class="passwordsMatch === false
                                    ? 'border-red-300 focus:ring-red-100'
                                    : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100'"
                            >

                            <button
                                type="button"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-gray-600"
                                @click="showPasswordConfirmation = !showPasswordConfirmation"
                            >
                                <EyeOff v-if="showPasswordConfirmation" class="h-4 w-4" />
                                <Eye v-else class="h-4 w-4" />
                            </button>
                        </div>

                        <p v-if="passwordsMatch === false" class="mt-1.5 text-sm text-red-500">
                            Passwords do not match.
                        </p>

                        <p v-else-if="form.errors.password_confirmation" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-6">
                        <Link
                            :href="route('users.index')"
                            class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                        >
                            Cancel
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing || passwordsMatch === false"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                        >
                            <KeyRound class="h-4 w-4" />
                            {{ form.processing ? 'Saving...' : 'Save Password' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>