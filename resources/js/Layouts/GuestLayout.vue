<!-- GuestLayout.vue -->
<script setup>
import { Link } from '@inertiajs/vue3';
import { Ticket } from 'lucide-vue-next';
</script>

<template>
    <div class="flex min-h-screen bg-gray-50">
        <!-- Left panel — branding (hidden di mobile) -->
        <div class="hidden lg:flex lg:w-1/2 lg:flex-col lg:justify-between bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-700 p-12">
            <Link href="/" class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20">
                    <Ticket class="h-5 w-5 text-white" />
                </div>
                <span class="text-lg font-bold text-white">Helpdesk</span>
            </Link>

            <div>
                <h2 class="text-3xl font-bold leading-snug text-white">
                    Manage your support tickets with ease
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-indigo-200">
                    A modern helpdesk platform for teams that care about response time, accountability, and keeping customers happy.
                </p>

                <div class="mt-10 space-y-3">
                    <div
                        v-for="item in [
                            'Priority-based ticket routing',
                            'SLA tracking & monitoring',
                            'Real-time activity logging',
                        ]"
                        :key="item"
                        class="flex items-center gap-3 text-sm text-indigo-100"
                    >
                        <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white/20">
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        {{ item }}
                    </div>
                </div>
            </div>

            <p class="text-xs text-indigo-300">
                © {{ new Date().getFullYear() }} Helpdesk. All rights reserved.
            </p>
        </div>

        <!-- Right panel — form slot -->
        <div class="flex flex-1 flex-col items-center justify-center px-6 py-12">
            <!-- Logo mobile -->
            <Link href="/" class="mb-8 flex items-center gap-2 lg:hidden">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600">
                    <Ticket class="h-4 w-4 text-white" />
                </div>
                <span class="text-base font-bold text-gray-900">Helpdesk</span>
            </Link>

            <div class="w-full max-w-sm">
                <slot />
            </div>
        </div>
    </div>
</template>