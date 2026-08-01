<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    BarChart3,
    CalendarRange,
    CheckCircle2,
    Clock,
    AlertTriangle,
    Ticket,
    Globe,
    Info,
} from 'lucide-vue-next';

const props = defineProps({
    daily: Array,
    summary: Object,
    status_summary: Object,
    priority_summary: Object,
    filters: Object,
});

const todayStr = new Date().toISOString().split('T')[0];
const yesterdayStr = new Date(Date.now() - 86400000).toISOString().split('T')[0];

// Tentukan pilihan awal berdasarkan filter yang dikirim dari backend
const initialRange = (() => {
    const { start_date, end_date } = props.filters;
    if (start_date === todayStr && end_date === todayStr) return 'today';
    if (start_date === yesterdayStr && end_date === yesterdayStr) return 'yesterday';
    return 'both';
})();

const rangeOption = ref(initialRange);

const rangeOptions = [
    { value: 'yesterday', label: 'Yesterday' },
    { value: 'today', label: 'Today' },
    { value: 'both', label: 'Yesterday & Today' },
];

const applyFilter = () => {
    let startDate = todayStr;
    let endDate = todayStr;

    if (rangeOption.value === 'yesterday') {
        startDate = yesterdayStr;
        endDate = yesterdayStr;
    } else if (rangeOption.value === 'both') {
        startDate = yesterdayStr;
        endDate = todayStr;
    }
    // 'today' already defaults to todayStr for both

    router.get(
        route('reports.index'),
        {
            start_date: startDate,
            end_date: endDate,
        },
        { preserveState: true, replace: true }
    );
};

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

const dayBadge = (day) => {
    if (day.date === todayStr) return 'Today';
    if (day.date === yesterdayStr) return 'Yesterday';
    return null;
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-6xl p-6">
            <!-- Header -->
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="flex items-center gap-2 text-2xl font-bold tracking-tight text-gray-900">
                        <BarChart3 class="h-6 w-6 text-indigo-600" />
                        Ticket Report
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Daily ticket statistics based on creation and resolution dates
                    </p>
                </div>
            </div>

            <!-- Tab Switch -->
            <div class="mb-6 inline-flex rounded-xl border border-gray-200 bg-white p-1 shadow-sm">
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">
                    <CalendarRange class="h-3.5 w-3.5" />
                    Daily
                </span>
                <Link
                    :href="route('reports.overview')"
                    class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-gray-500 transition hover:bg-gray-100"
                >
                    <Globe class="h-3.5 w-3.5" />
                    Overview
                </Link>
            </div>

            <!-- Filter -->
            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="inline-flex rounded-xl bg-gray-100 p-1">
                        <button
                            v-for="opt in rangeOptions"
                            :key="opt.value"
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition"
                            :class="rangeOption === opt.value
                                ? 'bg-white text-indigo-600 shadow-sm'
                                : 'text-gray-500 hover:text-gray-700'"
                            @click="rangeOption = opt.value"
                        >
                            {{ opt.label }}
                        </button>
                    </div>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                        @click="applyFilter"
                    >
                        <CalendarRange class="h-4 w-4" />
                        Apply
                    </button>
                </div>

                <p class="mt-3 flex items-center gap-1.5 text-xs text-gray-400">
                    <Info class="h-3.5 w-3.5 shrink-0" />
                    This report only covers Yesterday and Today. For historical data, see the Overview report.
                </p>
            </div>

            <!-- Summary Cards -->
            <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-medium text-gray-400">
                        <Ticket class="h-3.5 w-3.5" /> Tickets Created
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ summary.total_created }}</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-medium text-gray-400">
                        <CheckCircle2 class="h-3.5 w-3.5" /> Tickets Resolved
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

            <!-- Daily Comparison Cards -->
            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-gray-700">Daily Breakdown</h2>

                <div v-if="daily.length === 0" class="py-10 text-center text-sm text-gray-400">
                    No data available for this range.
                </div>

                <div v-else class="grid gap-4" :class="daily.length > 1 ? 'sm:grid-cols-2' : 'sm:grid-cols-1'">
                    <div
                        v-for="day in daily"
                        :key="day.date"
                        class="rounded-xl border border-gray-100 bg-gray-50/60 p-5"
                    >
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ day.label }}</p>
                                <span
                                    v-if="dayBadge(day)"
                                    class="mt-0.5 inline-block rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-600 ring-1 ring-inset ring-indigo-600/10"
                                >
                                    {{ dayBadge(day) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-400">Created</p>
                                <p class="mt-1 text-xl font-bold text-indigo-600">{{ day.created_count }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Resolved</p>
                                <p class="mt-1 text-xl font-bold text-green-600">{{ day.resolved_count }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">SLA Met</p>
                                <p class="mt-1 text-lg font-semibold text-green-600">{{ day.sla_met_count }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">SLA Breach</p>
                                <p class="mt-1 text-lg font-semibold text-red-500">{{ day.sla_breach_count }}</p>
                            </div>
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
                    <p class="mt-4 text-xs text-gray-400">
                        Based on tickets created within the selected range.
                    </p>
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
                    <p class="mt-4 text-xs text-gray-400">
                        Based on tickets created within the selected range.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>