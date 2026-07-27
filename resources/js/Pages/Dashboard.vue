<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Ticket,
    CircleDot,
    Clock,
    CheckCircle2,
    XCircle,
    ArrowRight,
    TrendingUp,
    AlertTriangle,
    BarChart3,
    UserX,
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    priority: Object,
    recentTickets: Array,
    unassignedCount: Number,
});

const page = usePage();
const user = page.props.auth.user;

const canManage = user.role === 'admin' || user.role === 'agent';

const greeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 17) return 'Good afternoon';
    return 'Good evening';
};

const statCards = [
    {
        key: 'total',
        label: 'Total Tickets',
        icon: Ticket,
        wrapperClass: 'border-gray-200 bg-white',
        iconClass: 'bg-gray-100 text-gray-600',
        valueClass: 'text-gray-900',
        badgeClass: 'bg-gray-100 text-gray-600',
    },
    {
        key: 'open',
        label: 'Open',
        icon: CircleDot,
        wrapperClass: 'border-blue-100 bg-blue-50/40',
        iconClass: 'bg-blue-100 text-blue-600',
        valueClass: 'text-blue-700',
        badgeClass: 'bg-blue-100 text-blue-600',
    },
    {
        key: 'in_progress',
        label: 'In Progress',
        icon: Clock,
        wrapperClass: 'border-yellow-100 bg-yellow-50/40',
        iconClass: 'bg-yellow-100 text-yellow-600',
        valueClass: 'text-yellow-700',
        badgeClass: 'bg-yellow-100 text-yellow-600',
    },
    {
        key: 'resolved',
        label: 'Resolved',
        icon: CheckCircle2,
        wrapperClass: 'border-green-100 bg-green-50/40',
        iconClass: 'bg-green-100 text-green-600',
        valueClass: 'text-green-700',
        badgeClass: 'bg-green-100 text-green-600',
    },
    {
        key: 'closed',
        label: 'Closed',
        icon: XCircle,
        wrapperClass: 'border-gray-200 bg-gray-50/40',
        iconClass: 'bg-gray-100 text-gray-500',
        valueClass: 'text-gray-600',
        badgeClass: 'bg-gray-100 text-gray-500',
    },
];

const resolutionRate = () => {
    if (!props.stats.total) return 0;
    return Math.round(
        ((props.stats.resolved + props.stats.closed) / props.stats.total) * 100
    );
};

const activeRate = () => {
    if (!props.stats.total) return 0;
    return Math.round(
        ((props.stats.open + props.stats.in_progress) / props.stats.total) * 100
    );
};

const priorityBars = [
    { key: 'urgent', label: 'Urgent', barClass: 'bg-red-500', textClass: 'text-red-600' },
    { key: 'high',   label: 'High',   barClass: 'bg-orange-400', textClass: 'text-orange-600' },
    { key: 'medium', label: 'Medium', barClass: 'bg-blue-400',   textClass: 'text-blue-600' },
    { key: 'low',    label: 'Low',    barClass: 'bg-gray-300',   textClass: 'text-gray-500' },
];

const priorityWidth = (key) => {
    const total = props.stats.total;
    if (!total) return '0%';
    return Math.round((props.priority[key] / total) * 100) + '%';
};

const statusConfig = {
    open:        'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
    in_progress: 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20',
    resolved:    'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20',
    closed:      'bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-500/20',
};

const priorityConfig = {
    low:    'bg-gray-100 text-gray-600',
    medium: 'bg-blue-50 text-blue-700',
    high:   'bg-orange-50 text-orange-700',
    urgent: 'bg-red-50 text-red-700',
};

const statusLabel = {
    open:        'Open',
    in_progress: 'In Progress',
    resolved:    'Resolved',
    closed:      'Closed',
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

const initials = (name) => {
    if (!name) return '?';
    return name.split(' ').map((w) => w[0]).slice(0, 2).join('').toUpperCase();
};

const timeAgo = (value) => {
    if (!value) return '';
    const diff = Date.now() - new Date(value).getTime();
    const minutes = Math.floor(diff / 60000);
    if (minutes < 1) return 'just now';
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    return `${Math.floor(hours / 24)}d ago`;
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-6xl p-6">

            <!-- Header -->
            <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-indigo-600">
                        {{ greeting() }}, {{ user.name.split(' ')[0] }}
                    </p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                        Helpdesk Dashboard
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Here's what's happening with your support tickets today.
                    </p>
                </div>

                <Link
                    :href="route('tickets.index')"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    View All Tickets
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <!-- Alert unassigned (admin/agent only) -->
            <div
                v-if="canManage && unassignedCount > 0"
                class="mb-6 flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4"
            >
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100">
                    <UserX class="h-5 w-5 text-amber-600" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-amber-800">
                        {{ unassignedCount }} unassigned ticket{{ unassignedCount > 1 ? 's' : '' }}
                    </p>
                    <p class="text-xs text-amber-600">
                        These tickets need to be assigned to an agent.
                    </p>
                </div>
                <Link
                    :href="route('tickets.index')"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                >
                    View
                    <ArrowRight class="h-3.5 w-3.5" />
                </Link>
            </div>

            <!-- Stat Cards -->
            <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div
                    v-for="card in statCards"
                    :key="card.key"
                    class="rounded-2xl border p-5 shadow-sm transition hover:shadow-md"
                    :class="card.wrapperClass"
                >
                    <div class="flex items-center justify-between">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl"
                            :class="card.iconClass"
                        >
                            <component :is="card.icon" class="h-5 w-5" />
                        </div>

                        <span
                            v-if="stats.total"
                            class="rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="card.badgeClass"
                        >
                            {{ card.key === 'total'
                                ? '100%'
                                : Math.round((stats[card.key] / stats.total) * 100) + '%'
                            }}
                        </span>
                    </div>

                    <p class="mt-4 text-3xl font-bold" :class="card.valueClass">
                        {{ stats[card.key] ?? 0 }}
                    </p>

                    <p class="mt-1 text-sm font-medium text-gray-500">
                        {{ card.label }}
                    </p>
                </div>
            </div>

            <!-- Row 2: Resolution + Active + Priority -->
            <div class="mb-6 grid gap-4 lg:grid-cols-3">
                <!-- Resolution Rate -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50">
                                <TrendingUp class="h-4 w-4 text-green-600" />
                            </div>
                            <h2 class="text-sm font-semibold text-gray-700">
                                Resolution Rate
                            </h2>
                        </div>
                        <span class="text-2xl font-bold text-green-600">
                            {{ resolutionRate() }}%
                        </span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-100">
                        <div
                            class="h-full rounded-full bg-green-500 transition-all duration-700"
                            :style="{ width: resolutionRate() + '%' }"
                        />
                    </div>
                    <p class="mt-2.5 text-xs text-gray-400">
                        {{ stats.resolved + stats.closed }} of {{ stats.total }} tickets resolved or closed
                    </p>
                </div>

                <!-- Active Tickets -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50">
                                <Clock class="h-4 w-4 text-blue-600" />
                            </div>
                            <h2 class="text-sm font-semibold text-gray-700">
                                Active Tickets
                            </h2>
                        </div>
                        <span class="text-2xl font-bold text-blue-600">
                            {{ activeRate() }}%
                        </span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-100">
                        <div
                            class="h-full rounded-full bg-blue-500 transition-all duration-700"
                            :style="{ width: activeRate() + '%' }"
                        />
                    </div>
                    <p class="mt-2.5 text-xs text-gray-400">
                        {{ stats.open + stats.in_progress }} tickets still open or in progress
                    </p>
                </div>

                <!-- Priority Breakdown -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                            <BarChart3 class="h-4 w-4 text-indigo-600" />
                        </div>
                        <h2 class="text-sm font-semibold text-gray-700">
                            Priority Breakdown
                        </h2>
                    </div>

                    <div class="space-y-2.5">
                        <div
                            v-for="bar in priorityBars"
                            :key="bar.key"
                            class="flex items-center gap-2"
                        >
                            <span class="w-14 text-xs font-medium" :class="bar.textClass">
                                {{ bar.label }}
                            </span>
                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-100">
                                <div
                                    class="h-full rounded-full transition-all duration-700"
                                    :class="bar.barClass"
                                    :style="{ width: priorityWidth(bar.key) }"
                                />
                            </div>
                            <span class="w-6 text-right text-xs text-gray-500">
                                {{ priority[bar.key] ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tickets -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div class="flex items-center gap-2">
                        <Ticket class="h-4 w-4 text-gray-400" />
                        <h2 class="text-sm font-semibold text-gray-700">
                            Recent Tickets
                        </h2>
                    </div>
                    <Link
                        :href="route('tickets.index')"
                        class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 transition hover:text-indigo-800"
                    >
                        View all
                        <ArrowRight class="h-3.5 w-3.5" />
                    </Link>
                </div>

                <div class="divide-y divide-gray-100">
                    <div
                        v-if="!recentTickets.length"
                        class="px-6 py-10 text-center"
                    >
                        <Ticket class="mx-auto mb-2 h-7 w-7 text-gray-200" />
                        <p class="text-sm text-gray-400">No tickets yet.</p>
                    </div>

                    <Link
                        v-for="ticket in recentTickets"
                        :key="ticket.id"
                        :href="route('tickets.show', ticket.id)"
                        class="flex items-center gap-4 px-6 py-3.5 transition hover:bg-gray-50/70"
                    >
                        <!-- Avatar user pembuat -->
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold"
                            :class="avatarColor(ticket.user?.name)"
                        >
                            {{ initials(ticket.user?.name) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-800">
                                {{ ticket.title }}
                            </p>
                            <p class="mt-0.5 text-xs text-gray-400">
                                #{{ ticket.id }} · {{ timeAgo(ticket.created_at) }}
                                <span v-if="ticket.assignee">
                                    · assigned to {{ ticket.assignee.name }}
                                </span>
                                <span v-else class="text-amber-500">· unassigned</span>
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                :class="priorityConfig[ticket.priority] ?? priorityConfig.medium"
                            >
                                {{ ticket.priority }}
                            </span>
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                :class="statusConfig[ticket.status] ?? statusConfig.open"
                            >
                                {{ statusLabel[ticket.status] ?? ticket.status }}
                            </span>
                        </div>
                    </Link>
                </div>
            </div>

        </div>
    </AppLayout>
</template>