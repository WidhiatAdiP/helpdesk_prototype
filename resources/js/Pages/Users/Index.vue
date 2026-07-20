<script setup>
import { computed, ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import {
    Search,
    UserPlus,
    Pencil,
    KeyRound,
    Trash2,
    Users as UsersIcon,
    ShieldCheck,
    Headset,
    User,
    AlertTriangle,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    users: Array,
});

const form = useForm({});

const keyword = ref('');
const currentPage = ref(1);
const perPage = 10;

// State modal konfirmasi delete
const showDeleteModal = ref(false);
const userToDelete = ref(null);

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    userToDelete.value = null;
};

const executeDelete = () => {
    if (!userToDelete.value) return;

    form.delete(route('users.destroy', userToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        },
    });
};

const filteredUsers = computed(() => {
    return props.users.filter((user) => {
        const q = keyword.value.toLowerCase();
        return (
            user.name.toLowerCase().includes(q) ||
            user.email.toLowerCase().includes(q) ||
            user.role.toLowerCase().includes(q)
        );
    });
});

watch(keyword, () => {
    currentPage.value = 1;
});

const totalPages = computed(() =>
    Math.max(1, Math.ceil(filteredUsers.value.length / perPage))
);

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredUsers.value.slice(start, start + perPage);
});

const pageStart = computed(() =>
    filteredUsers.value.length === 0 ? 0 : (currentPage.value - 1) * perPage + 1
);

const pageEnd = computed(() =>
    Math.min(currentPage.value * perPage, filteredUsers.value.length)
);

const pageNumbers = computed(() => {
    const total = totalPages.value;
    const current = currentPage.value;
    const pages = [];

    if (total <= 5) {
        for (let i = 1; i <= total; i++) pages.push(i);
        return pages;
    }

    pages.push(1);
    if (current > 3) pages.push('...');

    const start = Math.max(2, current - 1);
    const end = Math.min(total - 1, current + 1);
    for (let i = start; i <= end; i++) pages.push(i);

    if (current < total - 2) pages.push('...');
    pages.push(total);

    return pages;
});

const goToPage = (page) => {
    if (page === '...' || page < 1 || page > totalPages.value) return;
    currentPage.value = page;
};

const totalAdmin = computed(() =>
    props.users.filter((u) => u.role === 'admin').length
);

const totalAgent = computed(() =>
    props.users.filter((u) => u.role === 'agent').length
);

const totalUser = computed(() =>
    props.users.filter((u) => u.role === 'user').length
);

const roleStyle = {
    admin: 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20',
    agent: 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
    user: 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20',
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
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-6xl p-6">
            <!-- Header -->
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        User Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage administrators, agents and users.
                    </p>
                </div>

                <Link
                    :href="route('users.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    <UserPlus class="h-4 w-4" />
                    Add New User
                </Link>
            </div>

            <!-- Statistics -->
            <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <UsersIcon class="h-5 w-5 text-gray-300" />
                    </div>
                    <h2 class="mt-2 text-3xl font-bold text-gray-900">
                        {{ users.length }}
                    </h2>
                </div>

                <div class="rounded-2xl border border-red-100 bg-red-50/60 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-red-600">Admin</p>
                        <ShieldCheck class="h-5 w-5 text-red-300" />
                    </div>
                    <h2 class="mt-2 text-3xl font-bold text-red-700">
                        {{ totalAdmin }}
                    </h2>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-blue-600">Agent</p>
                        <Headset class="h-5 w-5 text-blue-300" />
                    </div>
                    <h2 class="mt-2 text-3xl font-bold text-blue-700">
                        {{ totalAgent }}
                    </h2>
                </div>

                <div class="rounded-2xl border border-green-100 bg-green-50/60 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-green-600">User</p>
                        <User class="h-5 w-5 text-green-300" />
                    </div>
                    <h2 class="mt-2 text-3xl font-bold text-green-700">
                        {{ totalUser }}
                    </h2>
                </div>
            </div>

            <!-- Search -->
            <div class="relative mb-5">
                <Search class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <input
                    v-model="keyword"
                    placeholder="Search by name, email, or role..."
                    class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                >
            </div>

            <!-- Table card -->
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/80 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-3.5">#</th>
                                <th class="px-5 py-3.5">User</th>
                                <th class="px-5 py-3.5">Email</th>
                                <th class="px-5 py-3.5">Role</th>
                                <th class="px-5 py-3.5 text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="(user, index) in paginatedUsers"
                                :key="user.id"
                                class="transition-colors hover:bg-gray-50/70"
                            >
                                <td class="px-5 py-4 text-sm text-gray-400">
                                    {{ pageStart + index }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-semibold"
                                            :class="avatarColor(user.name)"
                                        >
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">
                                                {{ user.name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ user.email }}
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-medium capitalize"
                                        :class="roleStyle[user.role] ?? roleStyle.user"
                                    >
                                        {{ user.role }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex justify-center gap-1.5">
                                        <Link
                                            :href="route('users.edit', user.id)"
                                            title="Edit user"
                                            class="rounded-lg bg-amber-50 p-2 text-amber-600 transition hover:bg-amber-100"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Link>

                                        <Link
                                            :href="route('users.password.edit', user.id)"
                                            title="Change password"
                                            class="rounded-lg bg-indigo-50 p-2 text-indigo-600 transition hover:bg-indigo-100"
                                        >
                                            <KeyRound class="h-4 w-4" />
                                        </Link>

                                        <button
                                            title="Delete user"
                                            class="rounded-lg bg-red-50 p-2 text-red-600 transition hover:bg-red-100"
                                            @click="confirmDelete(user)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <UsersIcon class="mx-auto mb-3 h-8 w-8 text-gray-300" />
                                    <p class="text-sm font-medium text-gray-500">
                                        No users found
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        Try a different search keyword.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="filteredUsers.length > 0"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 bg-gray-50/60 px-5 py-3.5"
                >
                    <p class="text-sm text-gray-500">
                        Showing
                        <span class="font-medium text-gray-700">{{ pageStart }}</span>
                        -
                        <span class="font-medium text-gray-700">{{ pageEnd }}</span>
                        of
                        <span class="font-medium text-gray-700">{{ filteredUsers.length }}</span>
                    </p>

                    <div v-if="totalPages > 1" class="flex flex-wrap items-center gap-1">
                        <button
                            class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-gray-200 disabled:pointer-events-none disabled:opacity-40"
                            :disabled="currentPage === 1"
                            @click="goToPage(currentPage - 1)"
                        >
                            Prev
                        </button>

                        <button
                            v-for="(page, index) in pageNumbers"
                            :key="index"
                            class="min-w-[2.25rem] rounded-lg px-3 py-1.5 text-center text-sm font-medium transition-colors"
                            :class="[
                                page === currentPage
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : page === '...'
                                        ? 'pointer-events-none text-gray-400'
                                        : 'text-gray-600 hover:bg-gray-200',
                            ]"
                            @click="goToPage(page)"
                        >
                            {{ page }}
                        </button>

                        <button
                            class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-gray-200 disabled:pointer-events-none disabled:opacity-40"
                            :disabled="currentPage === totalPages"
                            @click="goToPage(currentPage + 1)"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                leave-active-class="transition duration-150 ease-in"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showDeleteModal"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 backdrop-blur-sm"
                    @click.self="cancelDelete"
                >
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        leave-active-class="transition duration-150 ease-in"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="showDeleteModal"
                            class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5"
                        >
                            <!-- Modal Header -->
                            <div class="flex items-start justify-between border-b border-gray-100 px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-50">
                                        <AlertTriangle class="h-5 w-5 text-red-600" />
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">
                                            Delete User
                                        </h3>
                                        <p class="text-xs text-gray-400">
                                            This action cannot be undone
                                        </p>
                                    </div>
                                </div>

                                <button
                                    class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                                    @click="cancelDelete"
                                >
                                    <X class="h-4 w-4" />
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="px-6 py-5">
                                <p class="text-sm text-gray-600">
                                    Are you sure you want to delete this user? All data associated with this account will be permanently removed.
                                </p>

                                <!-- User preview -->
                                <div
                                    v-if="userToDelete"
                                    class="mt-4 flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3"
                                >
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-semibold"
                                        :class="avatarColor(userToDelete.name)"
                                    >
                                        {{ userToDelete.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-gray-800">
                                            {{ userToDelete.name }}
                                        </p>
                                        <p class="truncate text-xs text-gray-400">
                                            {{ userToDelete.email }}
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                        :class="roleStyle[userToDelete.role] ?? roleStyle.user"
                                    >
                                        {{ userToDelete.role }}
                                    </span>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex justify-end gap-2.5 border-t border-gray-100 bg-gray-50/60 px-6 py-4">
                                <button
                                    class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100"
                                    @click="cancelDelete"
                                >
                                    Cancel
                                </button>

                                <button
                                    class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 disabled:opacity-50"
                                    :disabled="form.processing"
                                    @click="executeDelete"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    {{ form.processing ? 'Deleting...' : 'Delete User' }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>