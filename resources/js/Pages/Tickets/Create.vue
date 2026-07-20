<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Ticket,
    AlignLeft,
    Tag,
    BarChart2,
    Paperclip,
    Send,
} from 'lucide-vue-next';

const form = useForm({
    title: '',
    description: '',
    priority: 'medium',
    category: '',
    attachment: null,
});

const submit = () => {
    form.post(route('tickets.store'), { forceFormData: true });
};

const priorityOptions = [
    {
        value: 'low',
        label: 'Low',
        classes: 'bg-gray-100 text-gray-600 ring-1 ring-gray-500/20',
        activeClasses: 'border-gray-400 bg-gray-50',
    },
    {
        value: 'medium',
        label: 'Medium',
        classes: 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20',
        activeClasses: 'border-blue-500 bg-blue-50',
    },
    {
        value: 'high',
        label: 'High',
        classes: 'bg-orange-50 text-orange-700 ring-1 ring-orange-600/20',
        activeClasses: 'border-orange-500 bg-orange-50',
    },
    {
        value: 'urgent',
        label: 'Urgent',
        classes: 'bg-red-50 text-red-700 ring-1 ring-red-600/20',
        activeClasses: 'border-red-500 bg-red-50',
    },
];
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        Create Ticket
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Submit a new support request.
                    </p>
                </div>

                <Link
                    :href="route('tickets.index')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back
                </Link>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <form class="space-y-6" @submit.prevent="submit">
                    <!-- Title -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Title
                        </label>
                        <div class="relative">
                            <Ticket class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Brief summary of your issue"
                                class="w-full rounded-xl border py-2.5 pl-10 pr-4 text-sm transition focus:outline-none focus:ring-2"
                                :class="form.errors.title
                                    ? 'border-red-300 focus:ring-red-100'
                                    : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100'"
                            >
                        </div>
                        <p v-if="form.errors.title" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Description
                        </label>
                        <div class="relative">
                            <AlignLeft class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                            <textarea
                                v-model="form.description"
                                rows="5"
                                placeholder="Describe your issue in detail..."
                                class="w-full rounded-xl border py-2.5 pl-10 pr-4 text-sm transition focus:outline-none focus:ring-2"
                                :class="form.errors.description
                                    ? 'border-red-300 focus:ring-red-100'
                                    : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100'"
                            />
                        </div>
                        <p v-if="form.errors.description" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="mb-2.5 block text-sm font-semibold text-gray-700">
                            Priority
                        </label>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            <label
                                v-for="option in priorityOptions"
                                :key="option.value"
                                class="cursor-pointer rounded-xl border p-3 text-center transition hover:border-indigo-300"
                                :class="form.priority === option.value
                                    ? option.activeClasses
                                    : 'border-gray-200'"
                            >
                                <input
                                    v-model="form.priority"
                                    :value="option.value"
                                    type="radio"
                                    class="hidden"
                                >
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="option.classes"
                                >
                                    {{ option.label }}
                                </span>
                            </label>
                        </div>
                        <p v-if="form.errors.priority" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.priority }}
                        </p>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Category
                        </label>
                        <div class="relative">
                            <Tag class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <select
                                v-model="form.category"
                                class="w-full appearance-none rounded-xl border py-2.5 pl-10 pr-4 text-sm transition focus:outline-none focus:ring-2"
                                :class="form.errors.category
                                    ? 'border-red-300 focus:ring-red-100'
                                    : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100'"
                            >
                                <option value="">Select Category</option>
                                <option value="hardware">Hardware</option>
                                <option value="software">Software</option>
                                <option value="network">Network</option>
                                <option value="email">Email</option>
                                <option value="printer">Printer</option>
                                <option value="account">Account</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <p v-if="form.errors.category" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.category }}
                        </p>
                    </div>

                    <!-- Attachment -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Attachment
                            <span class="ml-1 font-normal text-gray-400">(optional)</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-gray-300 px-4 py-3.5 transition hover:border-indigo-400 hover:bg-indigo-50/30">
                            <Paperclip class="h-5 w-5 shrink-0 text-gray-400" />
                            <span class="text-sm text-gray-500">
                                {{ form.attachment ? form.attachment.name : 'Click to attach a file' }}
                            </span>
                            <input
                                type="file"
                                class="hidden"
                                @input="form.attachment = $event.target.files[0]"
                            >
                        </label>
                        <p v-if="form.errors.attachment" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.attachment }}
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                        <Link
                            :href="route('tickets.index')"
                            class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                        >
                            <Send class="h-4 w-4" />
                            {{ form.processing ? 'Submitting...' : 'Submit Ticket' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>