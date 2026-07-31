<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { User, Mail, Lock, Eye, EyeOff, UserPlus } from 'lucide-vue-next';

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Create an account
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Fill in your details to get started.
            </p>
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <!-- Name -->
            <div>
                <label for="name" class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Name
                </label>

                <div class="relative">
                    <User class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Your full name"
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
                <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Password
                </label>

                <div class="relative">
                    <Lock class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="Create a password"
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

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Confirm Password
                </label>

                <div class="relative">
                    <Lock class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="Repeat your password"
                        class="w-full rounded-xl border py-2.5 pl-10 pr-11 text-sm transition focus:outline-none focus:ring-2"
                        :class="form.errors.password_confirmation
                            ? 'border-red-300 focus:ring-red-100'
                            : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100'"
                    >
                    <button
                        type="button"
                        :aria-label="showPasswordConfirmation ? 'Hide password' : 'Show password'"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-gray-600"
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                    >
                        <EyeOff v-if="showPasswordConfirmation" class="h-4 w-4" />
                        <Eye v-else class="h-4 w-4" />
                    </button>
                </div>

                <p v-if="form.errors.password_confirmation" class="mt-1.5 text-sm text-red-500">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="form.processing"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
            >
                <UserPlus class="h-4 w-4" />
                {{ form.processing ? 'Creating account...' : 'Register' }}
            </button>

            <p class="text-center text-sm text-gray-500">
                Already have an account?
                <Link
                    :href="route('login')"
                    class="font-medium text-indigo-600 transition hover:text-indigo-800"
                >
                    Sign in
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>