<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    logs: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },
});

const formatDate = (date) => {
    if (!date) return '-';

    return new Date(date).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>

<AppLayout>

<div class="p-6">

    <h1 class="mb-6 text-2xl font-bold">
        Login History
    </h1>


    <div class="overflow-x-auto rounded-lg border bg-white shadow">

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="p-3 text-left">
                        User
                    </th>

                    <th class="p-3 text-left">
                        Role
                    </th>

                    <th class="p-3 text-left">
                        IP Address
                    </th>

                    <th class="p-3 text-left">
                        Login Time
                    </th>
                </tr>
            </thead>

            <tbody v-if="logs?.data?.length">
                <tr
                    v-for="log in logs.data"
                    :key="log.id"
                    class="border-t"
                >

                    <td class="p-3">

                        <div class="font-medium">
                            {{ log.user?.name ?? 'Deleted User' }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ log.user?.email ?? '-' }}
                        </div>

                    </td>

                    <td class="p-3 capitalize">
                        {{ log.user?.role ?? '-' }}
                    </td>

                    <td class="p-3">
                        {{ log.ip_address ?? '-' }}
                    </td>

                    <td class="p-3">
                        {{ formatDate(log.login_at) }}
                    </td>
                </tr>
            </tbody>

            <tbody v-else>
                <tr>
                    <td
                        colspan="4"
                        class="p-10 text-center text-gray-400"
                    >
                        No login history found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</AppLayout>

</template>