<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Ticket, Shield, Headset, ArrowRight, CheckCircle2 } from 'lucide-vue-next';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const features = [
    {
        icon: Ticket,
        title: 'Ticket Management',
        description: 'Create, track, and resolve support tickets efficiently with a streamlined workflow.',
        iconClass: 'bg-indigo-50 text-indigo-600',
    },
    {
        icon: Shield,
        title: 'Role-Based Access',
        description: 'Separate access levels for admins, agents, and users to keep things organized.',
        iconClass: 'bg-emerald-50 text-emerald-600',
    },
    {
        icon: Headset,
        title: 'Agent Assignment',
        description: 'Assign tickets to the right agents and track resolution time with SLA monitoring.',
        iconClass: 'bg-violet-50 text-violet-600',
    },
];

const highlights = [
    'Priority-based ticket routing',
    'Real-time activity logging',
    'SLA tracking & monitoring',
    'Multi-category support',
    'File attachment support',
    'Login history tracking',
];
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-gray-50">
        <!-- Navbar -->
        <header class="border-b border-gray-100 bg-white/80 backdrop-blur-sm sticky top-0 z-50">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600">
                        <Ticket class="h-4 w-4 text-white" />
                    </div>
                    <span class="text-base font-bold tracking-tight text-gray-900">
                        Helpdesk
                    </span>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                    >
                        Go to Dashboard
                        <ArrowRight class="h-3.5 w-3.5" />
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        >
                            Get Started
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main>
            <!-- Hero -->
            <section class="mx-auto max-w-6xl px-6 py-20 text-center">
                <div class="mx-auto mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-4 py-1.5 text-xs font-semibold text-indigo-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
                    Helpdesk Support System
                </div>

                <h1 class="mx-auto max-w-3xl text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                    Resolve issues
                    <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">
                        faster
                    </span>
                    with smart ticketing
                </h1>

                <p class="mx-auto mt-6 max-w-xl text-base leading-relaxed text-gray-500">
                    A modern helpdesk platform for managing support tickets, tracking resolutions, and keeping your team aligned — all in one place.
                </p>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="route('login')"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        Get Started
                        <ArrowRight class="h-4 w-4" />
                    </Link>

                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        Go to Dashboard
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </section>

            <!-- Features -->
            <section class="mx-auto max-w-6xl px-6 pb-16">
                <div class="grid gap-5 sm:grid-cols-3">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md"
                    >
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl"
                            :class="feature.iconClass"
                        >
                            <component :is="feature.icon" class="h-5 w-5" />
                        </div>

                        <h3 class="text-base font-semibold text-gray-900">
                            {{ feature.title }}
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-gray-500">
                            {{ feature.description }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Highlights -->
            <section class="border-y border-gray-100 bg-white py-16">
                <div class="mx-auto max-w-6xl px-6">
                    <div class="grid items-center gap-12 lg:grid-cols-2">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                                Everything you need to manage support
                            </h2>
                            <p class="mt-4 text-sm leading-relaxed text-gray-500">
                                Built for teams that care about response time, accountability, and keeping customers happy.
                            </p>

                            <ul class="mt-8 grid gap-3 sm:grid-cols-2">
                                <li
                                    v-for="item in highlights"
                                    :key="item"
                                    class="flex items-center gap-2.5 text-sm text-gray-600"
                                >
                                    <CheckCircle2 class="h-4 w-4 shrink-0 text-indigo-500" />
                                    {{ item }}
                                </li>
                            </ul>

                            <div class="mt-10">
                                <Link
                                    v-if="!$page.props.auth.user"
                                    :href="route('login')"
                                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                                >
                                    Start Now
                                    <ArrowRight class="h-4 w-4" />
                                </Link>
                                <Link
                                    v-else
                                    :href="route('dashboard')"
                                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                                >
                                    Open Dashboard
                                    <ArrowRight class="h-4 w-4" />
                                </Link>
                            </div>
                        </div>

                        <!-- Visual panel -->
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <!-- Fake ticket cards -->
                            <div class="space-y-3">
                                <div
                                    v-for="(item, i) in [
                                        { id: 1042, title: 'Cannot access email account', status: 'open', priority: 'high', color: 'bg-blue-50 text-blue-700 ring-blue-600/20', pcolor: 'bg-orange-50 text-orange-700' },
                                        { id: 1041, title: 'Printer not responding on floor 2', status: 'in_progress', priority: 'medium', color: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20', pcolor: 'bg-blue-50 text-blue-700' },
                                        { id: 1040, title: 'Software installation request', status: 'resolved', priority: 'low', color: 'bg-green-50 text-green-700 ring-green-600/20', pcolor: 'bg-gray-100 text-gray-600' },
                                        { id: 1039, title: 'Network connectivity issue', status: 'closed', priority: 'urgent', color: 'bg-gray-100 text-gray-600 ring-gray-500/20', pcolor: 'bg-red-50 text-red-700' },
                                    ]"
                                    :key="item.id"
                                    class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm"
                                >
                                    <span class="font-mono text-xs text-gray-400">#{{ item.id }}</span>

                                    <p class="min-w-0 flex-1 truncate text-sm font-medium text-gray-700">
                                        {{ item.title }}
                                    </p>

                                    <span
                                        class="inline-flex shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset capitalize"
                                        :class="item.color"
                                    >
                                        {{ item.status.replace('_', ' ') }}
                                    </span>

                                    <span
                                        class="inline-flex shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                        :class="item.pcolor"
                                    >
                                        {{ item.priority }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between rounded-xl bg-indigo-50 px-4 py-3">
                                <p class="text-xs font-medium text-indigo-700">
                                    4 tickets · 1 resolved today
                                </p>
                                <span class="text-xs text-indigo-500">
                                    Resolution rate: 50%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-100 bg-white py-8">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-6">
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded-md bg-indigo-600">
                        <Ticket class="h-3 w-3 text-white" />
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Helpdesk</span>
                </div>

                <p class="text-xs text-gray-400">
                    Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }}
                </p>
            </div>
        </footer>
    </div>
</template>