<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    User,
    Tag,
    Clock,
    ShieldCheck,
    Paperclip,
    MessageSquare,
    Activity,
    Send,
    UserPlus,
    ImagePlus,
    X,
    CalendarDays,
    LockIcon,
    AlertTriangle,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    ticket: Object,
    users: Array,
    resolution: Object,
});

const page = usePage();
const currentUser = page.props.auth.user;

const canManageTicket =
    currentUser.role === 'admin' ||
    currentUser.role === 'agent';

const isTicketLocked = computed(() =>
    props.ticket.status === 'closed' ||
    (
        props.ticket.status === 'resolved' &&
        currentUser.role !== 'admin'
    )
);

const isClosed = computed(() => props.ticket.status === 'closed');

// ── Status ────────────────────────────────────────────────
const statusForm = useForm({ status: props.ticket.status });
const currentStatus = ref(props.ticket.status);

const updateStatus = () => {

    if (
        currentStatus.value === 'in_progress' &&
        statusForm.status === 'resolved'
    ) {
        showResolveModal.value = true;
        return;
    }

    saveStatus();
};

const cancelResolve = () => {
    showResolveModal.value = false;
    statusForm.status = currentStatus.value;
};

const saveStatus = () => {

    statusForm.patch(route('tickets.status.update', props.ticket.id), {
        preserveScroll: true,

        onSuccess: () => {
            currentStatus.value = statusForm.status;
            showResolveModal.value = false;
        },
    });

};

// ── Assignee ──────────────────────────────────────────────
const assigneeForm = useForm({ assignee_id: props.ticket.assignee_id });
const currentAssigneeId = ref(props.ticket.assignee_id);
const currentAssigneeName = ref(props.ticket.assignee?.name ?? null);

const assignToMe = () => {

    if (isClosed.value) {
        return;
    }

    // kalau tiket belum ada assignee langsung assign
    if (!currentAssigneeId.value) {
        executeAssign();
        return;
    }

    // kalau sudah milik sendiri tidak usah apa-apa
    if (currentAssigneeId.value === currentUser.id) {
        return;
    }

    // kalau milik orang lain tampilkan konfirmasi
    showTakeOverModal.value = true;
};

const executeAssign = () => {

    if (isClosed.value) {
        return;
    }

    assigneeForm.assignee_id = currentUser.id;

    assigneeForm.patch(route('tickets.assign', props.ticket.id), {
        preserveScroll: true,

        onSuccess: () => {
            currentAssigneeId.value = currentUser.id;
            currentAssigneeName.value = currentUser.name;
            showTakeOverModal.value = false;
        },
    });

};

const cancelTakeOver = () => {
    showTakeOverModal.value = false;
};

// ── Comment ───────────────────────────────────────────────
const form = useForm({ comment: '', image: null });
const imageInput = ref(null);
const imagePreview = ref(null);

const canSubmitComment = computed(() =>
    form.comment.trim().length > 0 || form.image !== null
);

const submitComment = () => {
    if (!canSubmitComment.value) return;
    form.post(route('tickets.comments.store', props.ticket.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
            if (imageInput.value) imageInput.value.value = '';
        },
    });
};

const onImageSelect = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.image = file;
    const reader = new FileReader();
    reader.onload = (ev) => { imagePreview.value = ev.target.result; };
    reader.readAsDataURL(file);
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
    if (imageInput.value) imageInput.value.value = '';
};

// ── Helpers ───────────────────────────────────────────────
const statusConfig = {
    open: 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
    in_progress: 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20',
    resolved: 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20',
    closed: 'bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-500/20',
};

const priorityConfig = {
    low: 'bg-gray-100 text-gray-600',
    medium: 'bg-blue-50 text-blue-700',
    high: 'bg-orange-50 text-orange-700',
    urgent: 'bg-red-50 text-red-700',
};

const statusLabel = {
    open: 'Open',
    in_progress: 'In Progress',
    resolved: 'Resolved',
    closed: 'Closed',
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
    return name.split(' ').map((p) => p[0]).slice(0, 2).join('').toUpperCase();
};

const formatDate = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const timeAgo = (value) => {
    if (!value) return '';
    const diff = Date.now() - new Date(value).getTime();
    const minutes = Math.floor(diff / 60000);
    if (minutes < 1) return 'just now';
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return `${days}d ago`;
};

const showResolveModal = ref(false);
const showTakeOverModal = ref(false);
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-4xl p-6">
            <!-- Back -->
            <Link
                :href="route('tickets.index')"
                class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 transition hover:text-gray-700"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Tickets
            </Link>

            <!-- Ticket Info Card -->
            <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">
                                {{ ticket.title }}
                            </h1>
                            <p class="mt-1 font-mono text-xs text-gray-400">
                                Ticket #{{ ticket.id }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-medium capitalize"
                                :class="statusConfig[currentStatus] ?? statusConfig.open"
                            >
                                {{ statusLabel[currentStatus] ?? currentStatus }}
                            </span>
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-medium capitalize"
                                :class="priorityConfig[ticket.priority] ?? priorityConfig.medium"
                            >
                                {{ ticket.priority }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Metadata grid -->
                <div class="grid gap-px bg-gray-100 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Status -->
                    <div class="bg-white px-5 py-4">
                        <p class="mb-2 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                            <ShieldCheck class="h-3.5 w-3.5" /> Status
                        </p>
                        <template v-if="canManageTicket">
                            <div class="flex items-center gap-2">
                                <select
                                    v-model="statusForm.status"
                                    :disabled="isTicketLocked"
                                    :class="[
                                        'flex-1 rounded-xl border px-3 py-2 text-sm font-medium shadow-sm transition-all duration-200',
                                        isTicketLocked
                                            ? 'cursor-not-allowed border-gray-300 bg-gray-100 text-gray-500 shadow-none'
                                            : 'border-gray-300 bg-white text-gray-700 hover:border-indigo-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100'
                                    ]"
                                >
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option
                                        v-if="currentUser.role === 'admin'"
                                        value="closed"
                                    >
                                        Closed
                                    </option>
                                </select>
                                <button
                                    type="button"
                                    :disabled="statusForm.processing || statusForm.status === currentStatus"
                                    class="rounded-lg bg-indigo-600 px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                                    @click="updateStatus"
                                >
                                    {{ statusForm.processing ? '...' : 'Save' }}
                                </button>
                            </div>
                        </template>
                        <template v-else>
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                :class="statusConfig[currentStatus] ?? statusConfig.open"
                            >
                                {{ statusLabel[currentStatus] ?? currentStatus }}
                            </span>
                        </template>
                    </div>

                    <!-- Priority -->
                    <div class="bg-white px-5 py-4">
                        <p class="mb-2 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                            <Tag class="h-3.5 w-3.5" /> Priority
                        </p>
                        <span
                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                            :class="priorityConfig[ticket.priority] ?? priorityConfig.medium"
                        >
                            {{ ticket.priority }}
                        </span>
                    </div>

                    <!-- Category -->
                    <div class="bg-white px-5 py-4">
                        <p class="mb-2 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                            <Tag class="h-3.5 w-3.5" /> Category
                        </p>
                        <p class="text-sm capitalize text-gray-700">
                            {{ ticket.category || '-' }}
                        </p>
                    </div>

                    <!-- Resolution Time -->
                    <div class="bg-white px-5 py-4">
                        <p class="mb-2 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                            <Clock class="h-3.5 w-3.5" /> Resolution Time
                        </p>
                        <p v-if="resolution" class="text-sm text-gray-700">
                            {{ resolution.text }}
                            <span
                                class="ml-1.5 rounded px-1.5 py-0.5 text-xs"
                                :class="resolution.within_sla
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'"
                            >
                                {{ resolution.within_sla ? 'Within SLA' : 'SLA Breached' }}
                            </span>
                        </p>
                        <p v-else class="text-sm text-gray-400">Not resolved yet</p>
                    </div>

                    <!-- Created By -->
                    <div class="bg-white px-5 py-4">
                        <p class="mb-2 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                            <User class="h-3.5 w-3.5" /> Created By
                        </p>
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold"
                                :class="avatarColor(ticket.user?.name)"
                            >
                                {{ initials(ticket.user?.name) }}
                            </div>
                            <span class="text-sm text-gray-700">{{ ticket.user?.name ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="bg-white px-5 py-4">
                        <p class="mb-2 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                            <CalendarDays class="h-3.5 w-3.5" /> Created At
                        </p>
                        <p class="text-sm text-gray-700">
                            {{ formatDate(ticket.created_at) }}
                        </p>
                    </div>

                    <!-- Assigned To -->
                    <div class="bg-white px-5 py-4 sm:col-span-2">
                        <p class="mb-2 flex items-center gap-1.5 text-xs font-medium text-gray-400">
                            <UserPlus class="h-3.5 w-3.5" /> Assigned To
                        </p>

                        <template v-if="canManageTicket">
                            <!-- Belum ada assignee -->
                            <template v-if="!currentAssigneeId">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex items-center gap-2 rounded-lg border border-dashed border-gray-200 bg-gray-50 px-3 py-2"
                                    >
                                        <div
                                            class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold opacity-50"
                                            :class="avatarColor(currentUser.name)"
                                        >
                                            {{ initials(currentUser.name) }}
                                        </div>

                                        <span class="text-sm text-gray-400">
                                            {{ currentUser.name }}
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        :disabled="assigneeForm.processing || isClosed"
                                        :class="[
                                            'inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition',
                                            isClosed
                                                ? 'cursor-not-allowed bg-gray-300 text-white'
                                                : 'bg-indigo-600 text-white hover:bg-indigo-700'
                                        ]"
                                        @click="assignToMe"
                                    >
                                        <UserPlus class="h-3.5 w-3.5" />

                                        {{
                                            assigneeForm.processing
                                                ? 'Assigning...'
                                                : isClosed
                                                    ? 'Assign Disabled'
                                                    : 'Assign to Me'
                                        }}
                                    </button>
                                </div>

                                <p
                                    class="mt-1.5 text-xs"
                                    :class="isClosed ? 'font-medium text-red-500' : 'text-gray-400'"
                                >
                                    {{
                                        isClosed
                                            ? 'This ticket has been closed and cannot be assigned.'
                                            : 'No one is assigned yet. Click to take this ticket.'
                                    }}
                                </p>
                            </template>

                            <!-- Sudah di-assign ke user yang login -->
                            <template v-else-if="currentAssigneeId === currentUser.id">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold"
                                        :class="avatarColor(currentUser.name)"
                                    >
                                        {{ initials(currentUser.name) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ currentUser.name }}</span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-600 ring-1 ring-inset ring-green-600/20">
                                        Assigned to you
                                    </span>
                                </div>
                            </template>

                            <!-- Sudah di-assign ke orang lain -->
                            <template v-else>
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold"
                                            :class="avatarColor(currentAssigneeName)"
                                        >
                                            {{ initials(currentAssigneeName) }}
                                        </div>

                                        <span class="text-sm font-medium text-gray-700">
                                            {{ currentAssigneeName }}
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        :disabled="assigneeForm.processing || isClosed"
                                        :class="[
                                            'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                                            isClosed
                                                ? 'cursor-not-allowed border border-gray-300 bg-gray-100 text-gray-400'
                                                : 'border border-indigo-200 bg-indigo-50 text-indigo-600 hover:bg-indigo-100'
                                        ]"
                                        @click="assignToMe"
                                    >
                                        <UserPlus class="h-3.5 w-3.5" />

                                        {{
                                            assigneeForm.processing
                                                ? 'Assigning...'
                                                : isClosed
                                                    ? 'Take Over Disabled'
                                                    : 'Take Over'
                                        }}
                                    </button>
                                </div>

                                <p
                                    class="mt-1.5 text-xs"
                                    :class="isClosed ? 'font-medium text-red-500' : 'text-gray-400'"
                                >
                                    {{
                                        isClosed
                                            ? 'This ticket has been closed and can no longer be reassigned.'
                                            : 'Click "Take over" to reassign this ticket to yourself.'
                                    }}
                                </p>
                            </template>
                        </template>

                        <!-- User biasa -->
                        <template v-else>
                            <div class="flex items-center gap-2">
                                <div
                                    v-if="currentAssigneeName"
                                    class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold"
                                    :class="avatarColor(currentAssigneeName)"
                                >
                                    {{ initials(currentAssigneeName) }}
                                </div>
                                <span class="text-sm text-gray-700">
                                    {{ currentAssigneeName ?? 'Unassigned' }}
                                </span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Description -->
                <div class="border-t border-gray-100 px-6 py-5">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700">Description</h2>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm leading-relaxed text-gray-600">
                        {{ ticket.description }}
                    </div>
                </div>

                <!-- Attachments -->
                <div
                    v-if="ticket.attachments?.length"
                    class="border-t border-gray-100 px-6 py-5"
                >
                    <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700">
                        <Paperclip class="h-4 w-4 text-gray-400" />
                        Attachments
                    </h2>

                    <div class="flex flex-wrap gap-2">
                        <a
                            v-for="file in ticket.attachments"
                            :key="file.id"
                            :href="`/storage/${file.path}`"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-indigo-600 transition hover:bg-indigo-50"
                        >
                            <Paperclip class="h-3.5 w-3.5" />
                            {{ file.filename }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Comments -->
            <div class="mb-6 rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center gap-2 border-b border-gray-100 px-6 py-4">
                    <MessageSquare class="h-4 w-4 text-gray-400" />
                    <h2 class="text-sm font-semibold text-gray-700">
                        Comments
                        <span class="ml-1.5 text-gray-400">({{ ticket.comments.length }})</span>
                    </h2>
                </div>

                <div class="divide-y divide-gray-100">
                    <div v-if="ticket.comments.length === 0" class="px-6 py-10 text-center">
                        <MessageSquare class="mx-auto mb-2 h-7 w-7 text-gray-200" />
                        <p class="text-sm text-gray-400">No comments yet.</p>
                    </div>

                    <div
                        v-for="comment in ticket.comments"
                        :key="comment.id"
                        class="flex gap-3 px-6 py-4"
                    >
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold"
                            :class="avatarColor(comment.user?.name)"
                        >
                            {{ initials(comment.user?.name) }}
                        </div>
                        <div class="flex-1">
                            <div class="mb-1 flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-800">
                                    {{ comment.user?.name }}
                                </span>
                                <span
                                    class="text-xs text-gray-400"
                                    :title="formatDate(comment.created_at)"
                                >
                                    {{ timeAgo(comment.created_at) }}
                                </span>
                            </div>
                            <p class="text-sm leading-relaxed text-gray-600">
                                {{ comment.comment }}
                            </p>
                            <a
                                v-if="comment.image_path"
                                :href="`/storage/${comment.image_path}`"
                                target="_blank"
                                class="mt-2 inline-block"
                            >
                                <img
                                    :src="`/storage/${comment.image_path}`"
                                    class="max-h-72 rounded-xl border border-gray-200 shadow-sm transition hover:opacity-90"
                                />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Add Comment -->
                <div class="border-t border-gray-100 px-6 py-5">
                    <!-- Warning tiket locked -->
                    <div
                        v-if="isTicketLocked"
                        class="mb-4 flex items-start gap-2.5 rounded-xl bg-amber-50 p-3.5 text-sm text-amber-700 ring-1 ring-inset ring-amber-600/10"
                    >
                        <LockIcon class="h-4 w-4 shrink-0 translate-y-0.5" />
                        <p>
                            This ticket is <span class="font-semibold capitalize">{{ currentStatus }}</span>.
                            Comments are still allowed but the ticket is no longer active.
                        </p>
                    </div>

                    <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700">
                        <Send class="h-3.5 w-3.5 text-gray-400" />
                        Add Comment
                    </h3>

                    <form @submit.prevent="submitComment">
                        <textarea
                            v-model="form.comment"
                            rows="3"
                            placeholder="Write your comment..."
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                            :class="form.errors.comment ? 'border-red-300 focus:ring-red-100' : ''"
                        />
                        <p v-if="form.errors.comment" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.comment }}
                        </p>

                        <!-- Image preview -->
                        <div v-if="imagePreview" class="relative mt-3 inline-block">
                            <img
                                :src="imagePreview"
                                class="max-h-48 rounded-xl border border-gray-200 object-cover shadow-sm"
                            />
                            <button
                                type="button"
                                class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow transition hover:bg-red-600"
                                @click="removeImage"
                            >
                                <X class="h-3.5 w-3.5" />
                            </button>
                        </div>

                        <p v-if="form.errors.image" class="mt-1.5 text-sm text-red-500">
                            {{ form.errors.image }}
                        </p>

                        <div class="mt-3 flex items-center justify-between gap-3">
                            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-500 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600">
                                <ImagePlus class="h-4 w-4" />
                                <span>{{ form.image ? 'Change image' : 'Attach image' }}</span>
                                <input
                                    ref="imageInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="onImageSelect"
                                >
                            </label>

                            <button
                                type="submit"
                                :disabled="form.processing || !canSubmitComment"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                            >
                                <Send class="h-4 w-4" />
                                {{ form.processing ? 'Sending...' : 'Submit Comment' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center gap-2 border-b border-gray-100 px-6 py-4">
                    <Activity class="h-4 w-4 text-gray-400" />
                    <h2 class="text-sm font-semibold text-gray-700">Activity Log</h2>
                </div>

                <div class="divide-y divide-gray-100">
                    <div v-if="!ticket.activity_logs?.length" class="px-6 py-10 text-center">
                        <Activity class="mx-auto mb-2 h-7 w-7 text-gray-200" />
                        <p class="text-sm text-gray-400">No activity yet.</p>
                    </div>

                    <div
                        v-for="log in ticket.activity_logs"
                        :key="log.id"
                        class="flex gap-3 px-6 py-3.5"
                    >
                        <div
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold"
                            :class="avatarColor(log.user?.name)"
                        >
                            {{ initials(log.user?.name) }}
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-700">{{ log.user?.name }}</span>
                            <span class="mx-1.5 text-sm text-gray-500">{{ log.description }}</span>
                            <p
                                class="mt-0.5 text-xs text-gray-400"
                                :title="formatDate(log.created_at)"
                            >
                                {{ timeAgo(log.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                leave-active-class="transition duration-150 ease-in"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showResolveModal"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 backdrop-blur-sm"
                    @click.self="cancelResolve"
                >
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        leave-active-class="transition duration-150 ease-in"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="showResolveModal"
                            class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5"
                        >

                            <!-- Header -->
                            <div class="flex items-start justify-between border-b border-gray-100 px-6 py-5">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-50">
                                        <AlertTriangle class="h-5 w-5 text-amber-600" />
                                    </div>

                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">
                                            Resolve Ticket
                                        </h3>

                                        <p class="text-xs text-gray-400">
                                            This action cannot be undone
                                        </p>
                                    </div>

                                </div>

                                <button
                                    class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                                    @click="cancelResolve"
                                >
                                    <X class="h-4 w-4" />
                                </button>

                            </div>

                            <!-- Body -->
                            <div class="px-6 py-5">

                                <p class="text-sm text-gray-600">
                                    Once this ticket is marked as
                                    <span class="font-semibold text-green-600">
                                        Resolved
                                    </span>,
                                    it cannot be changed back to
                                    <strong>Open</strong> or
                                    <strong>In Progress</strong>.
                                </p>

                                <div
                                    class="mt-4 rounded-xl border border-gray-100 bg-gray-50 p-4"
                                >

                                    <div class="text-xs text-gray-400">
                                        Ticket
                                    </div>

                                    <div class="mt-1 font-semibold text-gray-800">
                                        #{{ ticket.id }} - {{ ticket.title }}
                                    </div>

                                    <div class="mt-3 flex justify-between text-sm">

                                        <span class="text-gray-500">
                                            Current Status
                                        </span>

                                        <span class="font-medium capitalize">
                                            {{ statusLabel[currentStatus] }}
                                        </span>

                                    </div>

                                    <div class="mt-2 flex justify-between text-sm">

                                        <span class="text-gray-500">
                                            New Status
                                        </span>

                                        <span class="font-semibold text-green-600">
                                            Resolved
                                        </span>

                                    </div>

                                </div>

                            </div>

                            <!-- Footer -->
                            <div class="flex justify-end gap-2.5 border-t border-gray-100 bg-gray-50/60 px-6 py-4">

                                <button
                                    class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100"
                                    @click="cancelResolve"
                                >
                                    Cancel
                                </button>

                                <button
                                    class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 disabled:opacity-50"
                                    :disabled="statusForm.processing"
                                    @click="saveStatus"
                                >
                                    <AlertTriangle class="h-4 w-4" />

                                    {{ statusForm.processing ? 'Saving...' : 'Resolve Ticket' }}

                                </button>

                            </div>

                        </div>
                    </Transition>
                </div>
            </Transition>
            <!-- Take Over Confirmation -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                leave-active-class="transition duration-150 ease-in"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showTakeOverModal"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 backdrop-blur-sm"
                    @click.self="cancelTakeOver"
                >
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        leave-active-class="transition duration-150 ease-in"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="showTakeOverModal"
                            class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5"
                        >

                            <!-- Header -->
                            <div class="flex items-start justify-between border-b border-gray-100 px-6 py-5">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-50">
                                        <AlertTriangle class="h-5 w-5 text-amber-600" />
                                    </div>

                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">
                                            Take Over Ticket
                                        </h3>

                                        <p class="text-xs text-gray-400">
                                            This action will reassign the ticket
                                        </p>
                                    </div>

                                </div>

                                <button
                                    class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                                    @click="cancelTakeOver"
                                >
                                    <X class="h-4 w-4" />
                                </button>

                            </div>

                            <!-- Body -->
                            <div class="px-6 py-5">

                                <p class="text-sm text-gray-600">
                                    This ticket is currently assigned to
                                    <span class="font-semibold">
                                        {{ currentAssigneeName }}
                                    </span>.
                                    <br><br>
                                    Are you sure you want to take over this ticket?
                                </p>

                                <div
                                    class="mt-4 rounded-xl border border-gray-100 bg-gray-50 p-4"
                                >
                                    <div class="text-xs text-gray-400">
                                        Ticket
                                    </div>

                                    <div class="mt-1 font-semibold text-gray-800">
                                        #{{ ticket.id }} - {{ ticket.title }}
                                    </div>

                                    <div class="mt-3 flex justify-between text-sm">
                                        <span class="text-gray-500">Current Assignee</span>

                                        <span class="font-medium">
                                            {{ currentAssigneeName }}
                                        </span>
                                    </div>

                                    <div class="mt-2 flex justify-between text-sm">
                                        <span class="text-gray-500">New Assignee</span>

                                        <span class="font-semibold text-indigo-600">
                                            {{ currentUser.name }}
                                        </span>
                                    </div>

                                </div>

                            </div>

                            <!-- Footer -->
                            <div class="flex justify-end gap-2.5 border-t border-gray-100 bg-gray-50/60 px-6 py-4">

                                <button
                                    class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100"
                                    @click="cancelTakeOver"
                                >
                                    Cancel
                                </button>

                                <button
                                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                                    :disabled="assigneeForm.processing"
                                    @click="executeAssign"
                                >
                                    <UserPlus class="h-4 w-4" />

                                    {{ assigneeForm.processing ? 'Taking Over...' : 'Take Over Ticket' }}
                                </button>

                            </div>

                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>