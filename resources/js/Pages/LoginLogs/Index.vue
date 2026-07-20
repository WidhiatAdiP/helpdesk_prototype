<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    logs: Object,
});
</script>

<template>

<AppLayout>

<div class="p-6">

    <h1 class="mb-6 text-2xl font-bold">
        Login History
    </h1>


    <div class="overflow-hidden rounded-lg border bg-white shadow">

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

                    <!-- <th class="p-3 text-left">
                        Browser
                    </th> -->

                </tr>

            </thead>


            <tbody>

                <tr
                    v-for="log in logs.data"
                    :key="log.id"
                    class="border-t"
                >

                    <td class="p-3">

                        <div class="font-medium">
                            {{ log.user?.name }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ log.user?.email }}
                        </div>

                    </td>


                    <td class="p-3 capitalize">
                        {{ log.user?.role }}
                    </td>


                    <td class="p-3">
                        {{ log.ip_address }}
                    </td>


                    <td class="p-3">

                        {{
                            new Date(
                                log.login_at
                            ).toLocaleString()
                        }}

                    </td>


                    <!-- <td class="max-w-xs truncate p-3 text-sm text-gray-500">

                        {{ log.user_agent }}

                    </td> -->


                </tr>

            </tbody>

        </table>

    </div>

</div>

</AppLayout>

</template>