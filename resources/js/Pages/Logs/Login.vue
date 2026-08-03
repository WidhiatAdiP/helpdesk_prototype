<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    LogIn,
    Globe,
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
} from 'lucide-vue-next';

defineProps({
    logs: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            total: 0,
            from: 0,
            to: 0,
        }),
    },
});

const initials = (name) => {
    if (!name) return '?';
    return name
        .split(' ')
        .map((part) => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
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
    if (!name?.length) return avatarColors[0];
    return avatarColors[name.charCodeAt(0) % avatarColors.length];
};

const roleStyles = {
    admin: 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20',
    agent: 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
    user: 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/20',
};

const roleStyle = (role) => roleStyles[role] ?? roleStyles.user;

const monthNames = [
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
];

const formatDateTime = (value) => {
    if (!value) return '-';

    // Format masuk diasumsikan: "DD-MM-YYYY HH:mm:ss"
    const match = value.match(/^(\d{2})-(\d{2})-(\d{4})\s(\d{2}):(\d{2}):(\d{2})$/);
    if (!match) return value; // fallback kalau format tidak sesuai dugaan

    const [, day, month, year, hours, minutes, seconds] = match;
    const monthName = monthNames[parseInt(month, 10) - 1];

    return `${day}-${monthName}-${year} ${hours}:${minutes}:${seconds}`;
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-6xl p-6">
            <!-- Header -->
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        Login History
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Record of user sign-ins across the system
                    </p>
                </div>

                <div class="flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-1.5">
                    <LogIn class="h-4 w-4 text-gray-400" />
                    <span class="text-sm font-medium text-gray-600">
                        {{ logs.total ?? 0 }} logins
                    </span>
                </div>
            </div>

            <!-- Table card -->
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/80 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-3.5">User</th>
                                <th class="px-5 py-3.5">Role</th>
                                <th class="px-5 py-3.5">IP Address</th>
                                <th class="px-5 py-3.5">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="log in logs.data"
                                :key="log.id"
                                class="transition-colors hover:bg-gray-50/70"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-semibold"
                                            :class="avatarColor(log.user?.name)"
                                        >
                                            {{ initials(log.user?.name) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">
                                                {{ log.user?.name ?? 'Unknown user' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ log.user?.email ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                        :class="roleStyle(log.user?.role)"
                                    >
                                        {{ log.user?.role ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-gray-100 px-2.5 py-1 font-mono text-xs text-gray-600">
                                        <Globe class="h-3 w-3 text-gray-400" />
                                        {{ log.ip_address ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-1">

                                        <span
                                            v-if="log.status === 'Logged In'"
                                            class="inline-flex w-fit items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20"
                                        >
                                            Logged In
                                        </span>

                                        <span
                                            v-else
                                            class="inline-flex w-fit items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-inset ring-gray-500/20"
                                        >
                                            Logged Out
                                        </span>

                                        <span class="text-xs text-gray-500">
                                            {{ formatDateTime(log.status_time) }}
                                        </span>

                                    </div>
                                </td>
                            </tr>

                            <tr v-if="logs.data.length === 0">
                                <td colspan="4" class="px-5 py-16 text-center">
                                    <LogIn class="mx-auto mb-3 h-8 w-8 text-gray-300" />
                                    <p class="text-sm font-medium text-gray-500">
                                        No login history found
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        Login records will appear here once users sign in.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="logs.total > 0"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 bg-gray-50/60 px-5 py-3.5"
                >
                    <p class="text-sm text-gray-500">
                        Showing
                        <span class="font-medium text-gray-700">{{ logs.from ?? 0 }}</span>
                        –
                        <span class="font-medium text-gray-700">{{ logs.to ?? 0 }}</span>
                        of
                        <span class="font-medium text-gray-700">{{ logs.total ?? 0 }}</span>
                        records
                    </p>

                    <div class="flex items-center gap-2">
                        <!-- First -->
                        <Link
                            :href="logs.first_page_url ?? '#'"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 transition hover:bg-gray-100"
                            :class="{ 'pointer-events-none opacity-40': logs.current_page === 1 }"
                            preserve-scroll
                        >
                            <ChevronsLeft class="h-4 w-4" />
                        </Link>

                        <!-- Previous -->
                        <Link
                            :href="logs.prev_page_url ?? '#'"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 transition hover:bg-gray-100"
                            :class="{ 'pointer-events-none opacity-40': !logs.prev_page_url }"
                            preserve-scroll
                        >
                            <ChevronLeft class="h-4 w-4" />
                        </Link>

                        <!-- Current Page -->
                        <span class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700">
                            {{ logs.current_page ?? 1 }} / {{ logs.last_page ?? 1 }}
                        </span>

                        <!-- Next -->
                        <Link
                            :href="logs.next_page_url ?? '#'"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 transition hover:bg-gray-100"
                            :class="{ 'pointer-events-none opacity-40': !logs.next_page_url }"
                            preserve-scroll
                        >
                            <ChevronRight class="h-4 w-4" />
                        </Link>

                        <!-- Last -->
                        <Link
                            :href="logs.last_page_url ?? '#'"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 transition hover:bg-gray-100"
                            :class="{ 'pointer-events-none opacity-40': logs.current_page === logs.last_page }"
                            preserve-scroll
                        >
                            <ChevronsRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>