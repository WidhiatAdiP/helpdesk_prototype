<!-- Auth/Login.vue -->
<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Mail, Lock, Eye, EyeOff, LogIn } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Welcome back
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Sign in to your account to continue.
            </p>
        </div>

        <!-- Status message -->
        <div
            v-if="status"
            class="mb-5 flex items-center gap-2.5 rounded-xl bg-green-50 p-3.5 text-sm text-green-700 ring-1 ring-inset ring-green-600/10"
        >
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            {{ status }}
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <!-- Email -->
            <div>
                <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Email Address
                </label>

                <div class="relative">
                    <Mail class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@example.com"
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
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="password" class="text-sm font-semibold text-gray-700">
                        Password
                    </label>
                    <!-- <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-medium text-indigo-600 transition hover:text-indigo-800"
                    >
                        Forgot password?
                    </Link> -->
                </div>

                <div class="relative">
                    <Lock class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="w-full rounded-xl border py-2.5 pl-10 pr-11 text-sm transition focus:outline-none focus:ring-2"
                        :class="form.errors.password
                            ? 'border-red-300 focus:ring-red-100'
                            : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100'"
                    >
                    <button
                        type="button"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-gray-600"
                        @click="showPassword = !showPassword"
                    >
                        <EyeOff v-if="showPassword" class="h-4 w-4" />
                        <Eye v-else class="h-4 w-4" />
                    </button>
                </div>

                <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-500">
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Remember me -->
            <!-- `<div class="flex items-center gap-2.5">
                <input
                    id="remember"
                    v-model="form.remember"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                >
                <label for="remember" class="text-sm text-gray-600 cursor-pointer">
                    Remember me
                </label>
            </div>` -->

            <!-- Submit -->
            <button
                type="submit"
                :disabled="form.processing"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
            >
                <LogIn class="h-4 w-4" />
                {{ form.processing ? 'Signing in...' : 'Sign In' }}
            </button>
        </form>
    </GuestLayout>
</template>