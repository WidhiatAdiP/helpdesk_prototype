<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    PlusCircle,
    UserPlus,
    RefreshCw,
    MessageSquare,
    Activity,
} from 'lucide-vue-next';

defineProps({
    logs: Object,
});

const actionConfig = {
    created: {
        label: 'Created',
        icon: PlusCircle,
        classes: 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20',
        iconClass: 'text-green-600',
    },
    assigned: {
        label: 'Assigned',
        icon: UserPlus,
        classes: 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
        iconClass: 'text-blue-600',
    },
    status_changed: {
        label: 'Status Changed',
        icon: RefreshCw,
        classes: 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20',
        iconClass: 'text-yellow-600',
    },
    comment_added: {
        label: 'Comment Added',
        icon: MessageSquare,
        classes: 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/20',
        iconClass: 'text-gray-500',
    },
};

const fallbackAction = {
    label: 'Unknown',
    icon: Activity,
    classes: 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/20',
    iconClass: 'text-gray-500',
};

const getAction = (action) => actionConfig[action] ?? { ...fallbackAction, label: action };

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
    if (!name) return avatarColors[0];
    const index = name.charCodeAt(0) % avatarColors.length;
    return avatarColors[index];
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-6xl p-6">
            <!-- Header -->
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        Activity Logs
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        History of system activities
                    </p>
                </div>

                <div class="flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-1.5">
                    <Activity class="h-4 w-4 text-gray-400" />
                    <span class="text-sm font-medium text-gray-600">
                        {{ logs.total }} records
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
                                <th class="px-5 py-3.5">Ticket</th>
                                <th class="px-5 py-3.5">Action</th>
                                <th class="px-5 py-3.5">Description</th>
                                <th class="px-5 py-3.5">Time</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="log in logs.data"
                                :key="log.id"
                                class="group transition-colors hover:bg-gray-50/70"
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
                                                {{ log.user?.role ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-md bg-gray-100 px-2.5 py-1 font-mono text-xs font-medium text-gray-600">
                                        #{{ log.ticket_id }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium"
                                        :class="getAction(log.action).classes"
                                    >
                                        <component
                                            :is="getAction(log.action).icon"
                                            class="h-3.5 w-3.5"
                                            :class="getAction(log.action).iconClass"
                                        />
                                        {{ getAction(log.action).label }}
                                    </span>
                                </td>

                                <td class="max-w-xs truncate px-5 py-4 text-sm text-gray-600">
                                    {{ log.description }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-400">
                                    {{ log.time }}
                                </td>
                            </tr>

                            <tr v-if="logs.data.length === 0">
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <Activity class="mx-auto mb-3 h-8 w-8 text-gray-300" />
                                    <p class="text-sm font-medium text-gray-500">
                                        No activity logs found
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        Activities will appear here once something happens.
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
                        <span class="font-medium text-gray-700">{{ logs.from }}</span>
                        -
                        <span class="font-medium text-gray-700">{{ logs.to }}</span>
                        of
                        <span class="font-medium text-gray-700">{{ logs.total }}</span>
                    </p>

                    <div v-if="logs.links.length > 3" class="flex flex-wrap gap-1">
                        <Link
                            v-for="(link, index) in logs.links"
                            :key="index"
                            :href="link.url ?? '#'"
                            v-html="link.label"
                            class="min-w-[2.25rem] rounded-lg px-3 py-1.5 text-center text-sm font-medium transition-colors"
                            :class="[
                                link.active
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-gray-600 hover:bg-gray-200',
                                !link.url && 'pointer-events-none opacity-40',
                            ]"
                            preserve-scroll
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>