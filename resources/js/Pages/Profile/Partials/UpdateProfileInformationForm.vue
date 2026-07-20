<!-- Profile/Partials/UpdateProfileInformationForm.vue -->
<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { User, Mail, MailCheck, Save } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <div class="mb-6 flex items-center gap-3">
            <User class="h-4 w-4 text-gray-400" />
            <div>
                <h2 class="text-sm font-semibold text-gray-700">
                    Profile Information
                </h2>
                <p class="text-xs text-gray-400">
                    Update your account name and email address.
                </p>
            </div>
        </div>

        <form class="space-y-5" @submit.prevent="form.patch(route('profile.update'))">
            <!-- Name -->
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Full Name
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
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="username"
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

            <!-- Email verification warning -->
            <div
                v-if="mustVerifyEmail && user.email_verified_at === null"
                class="flex items-start gap-2.5 rounded-xl bg-amber-50 p-3.5 text-xs text-amber-700 ring-1 ring-inset ring-amber-600/10"
            >
                <MailCheck class="h-4 w-4 shrink-0 translate-y-0.5" />
                <div>
                    <p>
                        Your email address is not verified.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="font-semibold underline hover:text-amber-900"
                        >
                            Resend verification email.
                        </Link>
                    </p>
                    <p
                        v-show="status === 'verification-link-sent'"
                        class="mt-1.5 font-medium text-green-600"
                    >
                        Verification link sent successfully.
                    </p>
                </div>
            </div>

            <!-- Button -->
            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-5">
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-1"
                    leave-active-class="transition ease-in duration-150"
                    leave-to-class="opacity-0 translate-y-1"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                        Saved successfully.
                    </p>
                </Transition>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                >
                    <Save class="h-4 w-4" />
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </form>
    </section>
</template>