<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Search,
    Ticket,
    Plus,
    Filter,
    ChevronDown,
    User,
    CalendarDays,
    Tag,
    Clock,
    MessageSquare,
    Paperclip,
    ShieldCheck,
} from 'lucide-vue-next';

const page = usePage();

const props = defineProps({
    tickets: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');
const category = ref(props.filters?.category ?? '');

const expandedId = ref(null);

const toggleExpand = (id) => {
    expandedId.value = expandedId.value === id ? null : id;
};

const truncate = (text, length = 140) => {
    if (!text) return '-';
    return text.length > length ? text.slice(0, length) + '…' : text;
};

const formatDate = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const timeAgo = (value) => {
    if (!value) return '';
    const diff = Date.now() - new Date(value).getTime();
    const minutes = Math.floor(diff / 60000);
    if (minutes < 1) return 'just now';
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return `${days}d ago`;
};

const doSearch = () => {
    router.get(
        route('tickets.index'),
        {
            search: search.value,
            status: status.value,
            category: category.value,
        },
        { preserveState: true, replace: true }
    );
};

const statusConfig = {
    open: 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
    in_progress: 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20',
    resolved: 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20',
    closed: 'bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-500/20',
};

const priorityConfig = {
    low: 'bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-500/20',
    medium: 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
    high: 'bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20',
    urgent: 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20',
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
                        Tickets
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ tickets.total }} tickets total
                    </p>
                </div>

                <Link
                    v-if="page.props.auth.user.role === 'user'"
                    :href="route('tickets.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    <Plus class="h-4 w-4" />
                    New Ticket
                </Link>
            </div>

            <!-- Search & Filter -->
            <div class="mb-5 flex flex-wrap gap-3">
                <div class="relative min-w-0 flex-1">
                    <Search class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search ticket..."
                        class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        @input="doSearch"
                    >
                </div>

                <select
                    v-model="category"
                    class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                    @change="doSearch"
                >
                    <option value="">All Categories</option>
                    <option value="hardware">Hardware</option>
                    <option value="software">Software</option>
                    <option value="network">Network</option>
                    <option value="email">Email</option>
                    <option value="printer">Printer</option>
                    <option value="account">Account</option>
                    <option value="other">Other</option>
                </select>

                <select
                    v-model="status"
                    class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                    @change="doSearch"
                >
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/80 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-3.5">#</th>
                                <th class="px-5 py-3.5">Ticket ID</th>
                                <th class="px-5 py-3.5">Title</th>
                                <th class="px-5 py-3.5">Category</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Priority</th>
                                <th class="px-5 py-3.5">Assigned To</th>
                                <th class="w-10 px-5 py-3.5"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <template
                                v-for="(ticket, index) in tickets.data"
                                :key="ticket.id"
                            >
                                <tr
                                    class="cursor-pointer transition-colors hover:bg-gray-50/70"
                                    @click="toggleExpand(ticket.id)"
                                >
                                    <td class="px-5 py-4 text-sm text-gray-400">
                                        {{ tickets.from + index }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="font-mono text-xs font-medium text-gray-500">
                                            #{{ ticket.id }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <Link
                                            :href="route('tickets.show', ticket.id)"
                                            class="font-medium text-indigo-600 transition hover:text-indigo-800 hover:underline"
                                            @click.stop
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

                                    <td class="px-5 py-4">
                                        <span v-if="ticket.assignee" class="text-sm text-gray-700">
                                            {{ ticket.assignee.name }}
                                        </span>
                                        <span v-else class="text-xs text-gray-400">
                                            Unassigned
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-right">
                                        <ChevronDown
                                            class="ml-auto h-4 w-4 text-gray-400 transition-transform duration-200"
                                            :class="expandedId === ticket.id ? 'rotate-180' : ''"
                                        />
                                    </td>
                                </tr>

                                <!-- Dropdown detail singkat -->
                                <tr v-if="expandedId === ticket.id">
                                    <td colspan="8" class="bg-gray-50/60 px-5 py-5">
                                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                            <div>
                                                <p class="mb-1 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                                                    <User class="h-3.5 w-3.5" /> Created By
                                                </p>
                                                <p class="text-sm text-gray-700">{{ ticket.user?.name ?? '-' }}</p>
                                            </div>

                                            <div>
                                                <p class="mb-1 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                                                    <CalendarDays class="h-3.5 w-3.5" /> Created At
                                                </p>
                                                <p class="text-sm text-gray-700">
                                                    {{ formatDate(ticket.created_at) }}
                                                    <span class="text-xs text-gray-400">({{ timeAgo(ticket.created_at) }})</span>
                                                </p>
                                            </div>

                                            <div>
                                                <p class="mb-1 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                                                    <Clock class="h-3.5 w-3.5" /> Last Updated
                                                </p>
                                                <p class="text-sm text-gray-700">
                                                    {{ ticket.updated_at ? timeAgo(ticket.updated_at) : '-' }}
                                                </p>
                                            </div>

                                            <div v-if="ticket.resolution">
                                                <p class="mb-1 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                                                    <ShieldCheck class="h-3.5 w-3.5" /> Resolution
                                                </p>
                                                <p class="text-sm text-gray-700">
                                                    {{ ticket.resolution.text }}
                                                    <span
                                                        class="ml-1 rounded px-1.5 py-0.5 text-xs"
                                                        :class="ticket.resolution.within_sla
                                                            ? 'bg-green-100 text-green-700'
                                                            : 'bg-red-100 text-red-700'"
                                                    >
                                                        {{ ticket.resolution.within_sla ? 'Within SLA' : 'SLA Breached' }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div v-else>
                                                <p class="mb-1 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                                                    <ShieldCheck class="h-3.5 w-3.5" /> Resolution
                                                </p>
                                                <p class="text-sm text-gray-400">Not resolved yet</p>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <p class="mb-1 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                                                <Tag class="h-3.5 w-3.5" /> Description
                                            </p>
                                            <p class="text-sm leading-relaxed text-gray-600">
                                                {{ truncate(ticket.description, 280) }}
                                            </p>
                                        </div>

                                        <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-gray-500">
                                            <span class="inline-flex items-center gap-1.5">
                                                <MessageSquare class="h-3.5 w-3.5 text-gray-400" />
                                                {{ ticket.comments_count ?? 0 }} comment{{ (ticket.comments_count ?? 0) === 1 ? '' : 's' }}
                                            </span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <Paperclip class="h-3.5 w-3.5 text-gray-400" />
                                                {{ ticket.attachments_count ?? 0 }} attachment{{ (ticket.attachments_count ?? 0) === 1 ? '' : 's' }}
                                            </span>
                                        </div>

                                        <div class="mt-4">
                                            <Link
                                                :href="route('tickets.show', ticket.id)"
                                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline"
                                            >
                                                Lihat detail lengkap →
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <tr v-if="tickets.data.length === 0">
                                <td colspan="8" class="px-5 py-16 text-center">
                                    <Ticket class="mx-auto mb-3 h-8 w-8 text-gray-300" />
                                    <p class="text-sm font-medium text-gray-500">No tickets found</p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        Try adjusting your search or filter.
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