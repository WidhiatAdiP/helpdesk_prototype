<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    User,
    Mail,
    ShieldCheck,
    Headset,
    UserCircle,
    Save,
    Lock,
    CheckCircle2,
    AlertTriangle,
} from 'lucide-vue-next';

const props = defineProps({
    user: Object,
});

const page = usePage();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
});

const updateUser = () => {
    form.patch(route('users.update', props.user.id), {
        preserveScroll: true,
    });
};

// Cegah admin menurunkan role akunnya sendiri (menghindari admin terkunci akses)
const isEditingSelf = computed(
    () => page.props.auth.user.id === props.user.id
);

const isSelfAdminLocked = computed(
    () => isEditingSelf.value && props.user.role === 'admin'
);

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

const isOptionDisabled = (optionValue) =>
    isSelfAdminLocked.value && optionValue !== 'admin';

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
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-base font-semibold"
                        :class="avatarColor(user.name)"
                    >
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                            Edit User
                        </h1>
                        <p class="mt-0.5 text-sm text-gray-500">
                            {{ user.name }} &middot; {{ user.email }}
                        </p>
                    </div>
                </div>

                <Link
                    :href="route('users.index')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back
                </Link>
            </div>

            <!-- Success toast -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-5 flex items-center gap-2.5 rounded-xl bg-green-50 p-3.5 text-sm text-green-700 ring-1 ring-inset ring-green-600/10"
            >
                <CheckCircle2 class="h-4 w-4 shrink-0" />
                {{ $page.props.flash.success }}
            </div>

            <!-- Error toast -->
            <div
                v-if="$page.props.flash?.error"
                class="mb-5 flex items-center gap-2.5 rounded-xl bg-red-50 p-3.5 text-sm text-red-700 ring-1 ring-inset ring-red-600/10"
            >
                <AlertTriangle class="h-4 w-4 shrink-0" />
                {{ $page.props.flash.error }}
            </div>

            <!-- Edit Info Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="mb-5 flex items-center gap-2">
                    <User class="h-4.5 w-4.5 text-gray-400" />
                    <h2 class="text-sm font-semibold text-gray-700">
                        Account Information
                    </h2>
                </div>

                <form class="space-y-5" @submit.prevent="updateUser">
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

                    <!-- Role -->
                    <div>
                        <label class="mb-2.5 block text-sm font-semibold text-gray-700">
                            User Role
                        </label>

                        <!-- Warning kalau admin mengedit akunnya sendiri -->
                        <div
                            v-if="isSelfAdminLocked"
                            class="mb-3 flex items-start gap-2.5 rounded-xl bg-amber-50 p-3.5 text-xs text-amber-700 ring-1 ring-inset ring-amber-600/10"
                        >
                            <Lock class="h-4 w-4 shrink-0 translate-y-0.5" />
                            <p>
                                You cannot change your own role away from Admin.
                                This prevents you from being locked out of the system.
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <label
                                v-for="option in roleOptions"
                                :key="option.value"
                                class="relative rounded-xl border p-4 transition"
                                :class="[
                                    isOptionDisabled(option.value)
                                        ? 'cursor-not-allowed border-gray-100 bg-gray-50 opacity-60'
                                        : 'cursor-pointer hover:border-indigo-300',
                                    form.role === option.value && !isOptionDisabled(option.value)
                                        ? option.activeClasses
                                        : !isOptionDisabled(option.value) ? 'border-gray-200' : '',
                                ]"
                            >
                                <input
                                    v-model="form.role"
                                    :value="option.value"
                                    type="radio"
                                    class="hidden"
                                    :disabled="isOptionDisabled(option.value)"
                                >

                                <div
                                    v-if="isOptionDisabled(option.value)"
                                    class="absolute right-3 top-3"
                                    title="Disabled to prevent self lock-out"
                                >
                                    <Lock class="h-3.5 w-3.5 text-gray-400" />
                                </div>

                                <component
                                    :is="option.icon"
                                    class="h-7 w-7"
                                    :class="isOptionDisabled(option.value) ? 'text-gray-300' : option.iconClasses"
                                />

                                <h3
                                    class="mt-2.5 text-sm font-bold"
                                    :class="isOptionDisabled(option.value) ? 'text-gray-400' : 'text-gray-800'"
                                >
                                    {{ option.label }}
                                </h3>

                                <p
                                    class="mt-0.5 text-xs"
                                    :class="isOptionDisabled(option.value) ? 'text-gray-300' : 'text-gray-500'"
                                >
                                    {{ option.description }}
                                </p>
                            </label>
                        </div>

                        <p v-if="form.errors.role" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.role }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
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
                            <Save class="h-4 w-4" />
                            {{ form.processing ? 'Saving...' : 'Update User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>