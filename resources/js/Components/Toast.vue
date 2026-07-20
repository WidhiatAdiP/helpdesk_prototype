<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, XCircle, AlertTriangle, X } from 'lucide-vue-next';

const page = usePage();
const toasts = ref([]);
let toastId = 0;

const DURATION = 4000;

const typeConfig = {
    success: {
        icon: CheckCircle2,
        title: 'Success',
        borderClass: 'border-l-emerald-500',
        iconWrapperClass: 'bg-emerald-50 text-emerald-600',
        titleClass: 'text-emerald-700',
        barClass: 'bg-emerald-500',
    },
    error: {
        icon: XCircle,
        title: 'Error',
        borderClass: 'border-l-red-500',
        iconWrapperClass: 'bg-red-50 text-red-600',
        titleClass: 'text-red-700',
        barClass: 'bg-red-500',
    },
    warning: {
        icon: AlertTriangle,
        title: 'Warning',
        borderClass: 'border-l-amber-500',
        iconWrapperClass: 'bg-amber-50 text-amber-600',
        titleClass: 'text-amber-700',
        barClass: 'bg-amber-500',
    },
};

const close = (id) => {
    const index = toasts.value.findIndex((t) => t.id === id);
    if (index === -1) return;
    const toast = toasts.value[index];
    clearTimeout(toast.timer);
    toasts.value.splice(index, 1);
};

const pause = (toast) => {
    if (toast.paused) return;
    toast.paused = true;
    toast.elapsed = Date.now() - toast.startedAt;
    clearTimeout(toast.timer);
};

const resume = (toast) => {
    if (!toast.paused) return;
    toast.paused = false;
    const remaining = DURATION - toast.elapsed;
    toast.startedAt = Date.now() - toast.elapsed;
    toast.timer = setTimeout(() => close(toast.id), remaining);
};

const showToast = (msg, type) => {
    const id = ++toastId;

    const toast = {
        id,
        message: msg,
        type,
        paused: false,
        elapsed: 0,
        startedAt: Date.now(),
        timer: setTimeout(() => close(id), DURATION),
    };

    toasts.value.push(toast);
};

// Simpan flash terakhir yang sudah ditampilkan
// Di-reset ke null setelah flash ditampilkan supaya
// flash yang sama bisa muncul lagi di request berikutnya
const shownFlash = { success: null, error: null, warning: null };

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;

        ['success', 'error', 'warning'].forEach((type) => {
            if (flash[type]) {
                // Selalu tampilkan kalau flash ada, kecuali
                // ini persis flash yang baru saja ditampilkan di watch cycle ini
                if (flash[type] !== shownFlash[type]) {
                    showToast(flash[type], type);
                    shownFlash[type] = flash[type];

                    // Reset setelah DURATION supaya pesan yang sama
                    // bisa muncul lagi di request berikutnya
                    setTimeout(() => {
                        shownFlash[type] = null;
                    }, DURATION + 500);
                }
            }
        });
    },
    { deep: true, immediate: true }
);
</script>

<template>
    <Teleport to="body">
        <div
            aria-live="polite"
            class="pointer-events-none fixed right-5 top-5 z-[9999] flex w-[360px] max-w-[calc(100vw-2.5rem)] flex-col items-end gap-2.5"
        >
            <TransitionGroup name="toast">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="pointer-events-auto w-full overflow-hidden rounded-2xl border-l-4 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.10)] ring-1 ring-black/[0.05]"
                    :class="typeConfig[toast.type].borderClass"
                    @mouseenter="pause(toast)"
                    @mouseleave="resume(toast)"
                >
                    <div class="flex items-start gap-3 p-4">
                        <div
                            class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl"
                            :class="typeConfig[toast.type].iconWrapperClass"
                        >
                            <component
                                :is="typeConfig[toast.type].icon"
                                class="h-4 w-4"
                            />
                        </div>

                        <div class="min-w-0 flex-1">
                            <p
                                class="text-sm font-semibold"
                                :class="typeConfig[toast.type].titleClass"
                            >
                                {{ typeConfig[toast.type].title }}
                            </p>
                            <p class="mt-0.5 text-sm leading-relaxed text-gray-500">
                                {{ toast.message }}
                            </p>
                        </div>

                        <button
                            class="mt-0.5 shrink-0 rounded-lg p-1 text-gray-300 transition-colors hover:bg-gray-100 hover:text-gray-500"
                            @click.stop="close(toast.id)"
                        >
                            <X class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    <!-- Progress bar: pure CSS, smooth 60fps -->
                    <div class="h-1 w-full bg-black/5">
                        <div
                            class="toast-bar h-full"
                            :class="typeConfig[toast.type].barClass"
                            :style="{
                                animationDuration: DURATION + 'ms',
                                animationPlayState: toast.paused ? 'paused' : 'running',
                            }"
                        />
                    </div>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
@keyframes shrink {
    from { width: 100%; }
    to   { width: 0%; }
}

.toast-bar {
    animation-name: shrink;
    animation-timing-function: linear;
    animation-fill-mode: forwards;
}

.toast-enter-active {
    transition:
        opacity 0.38s cubic-bezier(0.16, 1, 0.3, 1),
        transform 0.38s cubic-bezier(0.16, 1, 0.3, 1),
        filter 0.38s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-leave-active {
    transition:
        opacity 0.22s ease-in,
        transform 0.22s ease-in,
        filter 0.22s ease-in;
    position: absolute;
    right: 0;
    width: 100%;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%) scale(0.9);
    filter: blur(6px);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%) scale(0.95);
    filter: blur(3px);
}

.toast-move {
    transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>