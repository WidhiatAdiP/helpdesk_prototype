<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});
</script>

<template>

<AppLayout>

<div class="p-6">

    <h1 class="mb-6 text-2xl font-bold">
        System Logs
    </h1>


    <div class="grid gap-6 md:grid-cols-2">


        <!-- Activity Logs -->

        <Link
            :href="route('logs.activity')"
            class="rounded-lg border bg-white p-6 shadow transition hover:shadow-md"
        >

            <h2 class="text-lg font-semibold">
                Activity Logs
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Ticket changes, comments, assignments, and status updates.
            </p>

        </Link>



        <!-- Login Logs -->

        <Link
            :href="route('logs.login')"
            class="rounded-lg border bg-white p-6 shadow transition hover:shadow-md"
        >

            <h2 class="text-lg font-semibold">
                Login History
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                User login records and access history.
            </p>

        </Link>


    </div>

</div>

</AppLayout>

</template>