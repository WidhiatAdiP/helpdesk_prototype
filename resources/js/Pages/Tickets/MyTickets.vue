<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Ticket, Plus } from 'lucide-vue-next';

defineProps({
    tickets: Object,
});

const statusConfig = {
    open: 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
    in_progress: 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20',
    resolved: 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20',
    closed: 'bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-500/20',
};

const priorityConfig = {
    low: 'bg-gray-100 text-gray-600',
    medium: 'bg-blue-50 text-blue-700',
    high: 'bg-orange-50 text-orange-700',
    urgent: 'bg-red-50 text-red-700',
};

const statusLabel = {
    open: 'Open',
    in_progress: 'In Progress',
    resolved: 'Resolved',
    closed: 'Closed',
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-6xl p-6">
            <!-- Header -->
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        My Tickets
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Tickets that you have submitted
                    </p>
                </div>

                <Link
                    :href="route('tickets.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    <Plus class="h-4 w-4" />
                    New Ticket
                </Link>
            </div>

            <!-- Table card -->
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/80 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-3.5">Ticket ID</th>
                                <th class="px-5 py-3.5">Title</th>
                                <th class="px-5 py-3.5">Category</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Priority</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="ticket in tickets.data"
                                :key="ticket.id"
                                class="transition-colors hover:bg-gray-50/70"
                            >
                                <td class="px-5 py-4">
                                    <span class="font-mono text-xs font-medium text-gray-500">
                                        #{{ ticket.id }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <Link
                                        :href="route('tickets.show', ticket.id)"
                                        class="font-medium text-indigo-600 transition hover:text-indigo-800 hover:underline"
                                    >
                                        {{ ticket.title }}
                                    </Link>
                                </td>

                                <td class="px-5 py-4 text-sm capitalize text-gray-600">
                                    {{ ticket.category || '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                        :class="statusConfig[ticket.status] ?? statusConfig.open"
                                    >
                                        {{ statusLabel[ticket.status] ?? ticket.status }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                        :class="priorityConfig[ticket.priority] ?? priorityConfig.medium"
                                    >
                                        {{ ticket.priority }}
                                    </span>
                                </td>
                            </tr>

                            <tr v-if="tickets.data.length === 0">
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <Ticket class="mx-auto mb-3 h-8 w-8 text-gray-300" />
                                    <p class="text-sm font-medium text-gray-500">
                                        No tickets yet
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        Submit your first ticket using the button above.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="tickets.total > 0"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 bg-gray-50/60 px-5 py-3.5"
                >
                    <p class="text-sm text-gray-500">
                        Showing
                        <span class="font-medium text-gray-700">{{ tickets.from }}</span>
                        -
                        <span class="font-medium text-gray-700">{{ tickets.to }}</span>
                        of
                        <span class="font-medium text-gray-700">{{ tickets.total }}</span>
                    </p>

                    <div v-if="tickets.links.length > 3" class="flex flex-wrap gap-1">
                        <Link
                            v-for="(link, index) in tickets.links"
                            :key="`${link.label}-${index}`"
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