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
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
});

const page = usePage();
const user = page.props.auth.user;

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

            <!-- Summary row -->
            <div class="grid gap-4 sm:grid-cols-2">
                <!-- Resolution rate -->
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
                        {{ stats.resolved + stats.closed }} out of {{ stats.total }} tickets resolved or closed
                    </p>
                </div>

                <!-- Active tickets -->
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
            </div>

        </div>
    </AppLayout>
</template>