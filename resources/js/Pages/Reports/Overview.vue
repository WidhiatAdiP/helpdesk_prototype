<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    BarChart3,
    CheckCircle2,
    Clock,
    AlertTriangle,
    Ticket,
    Globe,
    CalendarRange,
    History,
} from 'lucide-vue-next';

const props = defineProps({
    monthly: Array,
    summary: Object,
    status_summary: Object,
    priority_summary: Object,
    first_ticket_date: String,
});

const maxMonthlyValue = computed(() => {
    return Math.max(
        ...props.monthly.map((m) => Math.max(m.created_count, m.resolved_count)),
        1
    );
});

const barHeight = (value) => `${(value / maxMonthlyValue.value) * 100}%`;

const statusConfig = {
    open: { label: 'Open', color: 'bg-blue-500' },
    in_progress: { label: 'In Progress', color: 'bg-yellow-500' },
    resolved: { label: 'Resolved', color: 'bg-green-500' },
    closed: { label: 'Closed', color: 'bg-gray-500' },
};

const priorityConfig = {
    low: { label: 'Low', color: 'bg-gray-400' },
    medium: { label: 'Medium', color: 'bg-blue-400' },
    high: { label: 'High', color: 'bg-orange-400' },
    urgent: { label: 'Urgent', color: 'bg-red-500' },
};

const maxStatusValue = computed(() =>
    Math.max(...Object.values(props.status_summary), 1)
);

const maxPriorityValue = computed(() =>
    Math.max(...Object.values(props.priority_summary), 1)
);
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-6xl p-6">
            <!-- Header -->
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="flex items-center gap-2 text-2xl font-bold tracking-tight text-gray-900">
                        <Globe class="h-6 w-6 text-indigo-600" />
                        Overview Report
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        All-time ticket statistics
                        <span v-if="first_ticket_date">({{ first_ticket_date }} - now)</span>
                    </p>
                </div>
            </div>

            <!-- Tab Switch -->
            <div class="mb-6 inline-flex rounded-xl border border-gray-200 bg-white p-1 shadow-sm">
                <Link
                    :href="route('reports.index')"
                    class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-gray-500 transition hover:bg-gray-100"
                >
                    <CalendarRange class="h-3.5 w-3.5" />
                    Daily
                </Link>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">
                    <Globe class="h-3.5 w-3.5" />
                    Overview
                </span>
            </div>

            <!-- Summary Cards -->
            <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-medium text-gray-400">
                        <Ticket class="h-3.5 w-3.5" /> Total Tickets
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ summary.total_created }}</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-medium text-gray-400">
                        <CheckCircle2 class="h-3.5 w-3.5" /> Total Resolved
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ summary.total_resolved }}</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-medium text-gray-400">
                        <Clock class="h-3.5 w-3.5" /> Avg. Resolution Time
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ summary.avg_resolution_text }}</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-medium text-gray-400">
                        <AlertTriangle class="h-3.5 w-3.5" /> SLA Compliance
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ summary.sla_compliance_rate !== null ? summary.sla_compliance_rate + '%' : '-' }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                        {{ summary.total_sla_met }} met • {{ summary.total_sla_breach }} breached
                    </p>
                </div>
            </div>

            <!-- Monthly Bar Chart -->
            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="flex items-center gap-1.5 text-sm font-semibold text-gray-700">
                        <History class="h-3.5 w-3.5 text-gray-400" />
                        Last 12 Months Trend
                    </h2>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-sm bg-indigo-500"></span> Created
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-sm bg-green-500"></span> Resolved
                        </span>
                    </div>
                </div>

                <div v-if="monthly.length === 0" class="py-10 text-center text-sm text-gray-400">
                    No data yet.
                </div>

                <div v-else class="overflow-x-auto">
                    <div class="flex h-48 min-w-max items-end gap-4 border-b border-gray-100 pb-2">
                        <div
                            v-for="m in monthly"
                            :key="m.month"
                            class="flex w-12 flex-col items-center justify-end gap-1"
                        >
                            <div class="flex h-40 w-full items-end justify-center gap-1.5">
                                <div
                                    class="w-4 rounded-t bg-indigo-500 transition-all"
                                    :style="{ height: barHeight(m.created_count) }"
                                    :title="`${m.created_count} created`"
                                ></div>
                                <div
                                    class="w-4 rounded-t bg-green-500 transition-all"
                                    :style="{ height: barHeight(m.resolved_count) }"
                                    :title="`${m.resolved_count} resolved`"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 flex min-w-max gap-4">
                        <div
                            v-for="m in monthly"
                            :key="`label-${m.month}`"
                            class="w-12 text-center text-[10px] text-gray-400"
                        >
                            {{ m.label }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Priority Breakdown -->
            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Breakdown by Status</h2>
                    <div class="space-y-3">
                        <div v-for="(count, key) in status_summary" :key="key">
                            <div class="mb-1 flex items-center justify-between text-xs">
                                <span class="font-medium text-gray-600">{{ statusConfig[key]?.label ?? key }}</span>
                                <span class="text-gray-400">{{ count }}</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                                <div
                                    class="h-full rounded-full transition-all"
                                    :class="statusConfig[key]?.color ?? 'bg-gray-400'"
                                    :style="{ width: `${(count / maxStatusValue) * 100}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Breakdown by Priority</h2>
                    <div class="space-y-3">
                        <div v-for="(count, key) in priority_summary" :key="key">
                            <div class="mb-1 flex items-center justify-between text-xs">
                                <span class="font-medium capitalize text-gray-600">{{ priorityConfig[key]?.label ?? key }}</span>
                                <span class="text-gray-400">{{ count }}</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                                <div
                                    class="h-full rounded-full transition-all"
                                    :class="priorityConfig[key]?.color ?? 'bg-gray-400'"
                                    :style="{ width: `${(count / maxPriorityValue) * 100}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>