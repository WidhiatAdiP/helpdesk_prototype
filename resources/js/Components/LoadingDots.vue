<template>
    <Transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-white/60 backdrop-blur-sm"
        >
            <div class="flex space-x-2">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </Transition>
</template>

<script setup>
defineProps({
    show: Boolean,
});
</script>

<style scoped>
.dot {
    width: 12px;
    height: 12px;
    border-radius: 9999px;
    background: #2563eb;
    animation: bounce 0.8s infinite ease-in-out;
}

.dot:nth-child(2) {
    animation-delay: .15s;
}

.dot:nth-child(3) {
    animation-delay: .30s;
}

@keyframes bounce {
    0%,80%,100% {
        transform: scale(.5);
        opacity:.4;
    }
    40% {
        transform: scale(1);
        opacity:1;
    }
}

.fade-enter-active,
.fade-leave-active{
    transition:.15s;
}

.fade-enter-from,
.fade-leave-to{
    opacity:0;
}
</style>