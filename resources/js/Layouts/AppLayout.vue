<script setup>
import Toast from '@/Components/Toast.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/core';
import { ref, onMounted, onUnmounted } from 'vue';
import {
    LayoutDashboard,
    Ticket,
    Users,
    ScrollText,
    LogIn,
    Activity,
    User,
    LogOut,
    Menu,
    X,
    ChevronDown,
    ChevronUp,
    ChevronsLeft,
    ChevronsRight,
    BarChart3,
    CalendarRange,
    Globe,
} from 'lucide-vue-next';

const page = usePage();
const user = page.props.auth.user;

const sidebarOpen = ref(false);
const logsOpen = ref(route().current('logs.*'));
const reportsOpen = ref(route().current('reports.*'));
const showLogoutModal = ref(false);
const showUserDropdown = ref(false);
const isLoading = ref(false);
const headerVisible = ref(true);

const sidebarCollapsed = ref(localStorage.getItem('sidebar_collapsed') === '1');
const hoveredMenu = ref(null); // 'reports' | 'logs' | null
const flyoutStyle = ref({});

const toggleCollapse = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('sidebar_collapsed', sidebarCollapsed.value ? '1' : '0');
    showUserDropdown.value = false;
};

// Flyout dihitung posisinya via getBoundingClientRect + Teleport ke body,
// supaya tidak ke-clip oleh overflow-y-auto pada <nav>.
const openFlyout = (name, event) => {
    if (!sidebarCollapsed.value) return;
    const rect = event.currentTarget.getBoundingClientRect();
    flyoutStyle.value = {
        top: `${rect.top}px`,
        left: `${rect.right + 8}px`,
    };
    hoveredMenu.value = name;
};

const closeFlyout = () => {
    hoveredMenu.value = null;
};

let removeStart = null;
let removeFinish = null;
let loadingTimer = null;

let lastScrollY = 0;
let ticking = false;

const toggleSidebar = () => { sidebarOpen.value = !sidebarOpen.value; };
const toggleLogs = () => { logsOpen.value = !logsOpen.value; };
const toggleReports = () => { reportsOpen.value = !reportsOpen.value; };
const toggleUserDropdown = () => { showUserDropdown.value = !showUserDropdown.value; };
const closeUserDropdown = () => { showUserDropdown.value = false; };

const confirmLogout = () => {
    showUserDropdown.value = false;
    showLogoutModal.value = true;
};

const cancelLogout = () => {
    showLogoutModal.value = false;
};

const executeLogout = () => {
    showLogoutModal.value = false;
    router.post(route('logout'));
};

const onScroll = () => {
    if (ticking) return;
    window.requestAnimationFrame(() => {
        const currentScrollY = window.scrollY;
        const scrollDiff = currentScrollY - lastScrollY;

        if (currentScrollY < 60 || scrollDiff < -8) {
            headerVisible.value = true;
        } else if (scrollDiff > 8) {
            headerVisible.value = false;
            showUserDropdown.value = false;
        }

        lastScrollY = currentScrollY;
        ticking = false;
    });
    ticking = true;
};

// Named functions supaya bisa di-remove dengan benar
const handleStart = () => {

    isLoading.value = true;

    showUserDropdown.value = false;
    sidebarOpen.value = false;
};

const handleFinish = () => {

    setTimeout(() => {
        isLoading.value = false;
    }, 300);

    logsOpen.value = route().current('logs.*');
    reportsOpen.value = route().current('reports.*');
};

onMounted(() => {
    removeStart = router.on('start', handleStart);
    removeFinish = router.on('finish', handleFinish);

    window.addEventListener('scroll', onScroll, {
        passive: true,
    });
});

onUnmounted(() => {
    removeStart?.();
    removeFinish?.();

    window.removeEventListener('scroll', onScroll);
});

const avatarColors = [
    'bg-indigo-500', 'bg-pink-500', 'bg-emerald-500',
    'bg-amber-500', 'bg-sky-500', 'bg-violet-500',
];

const avatarColor = (name) => {
    if (!name) return avatarColors[0];
    return avatarColors[name.charCodeAt(0) % avatarColors.length];
};

const initials = (name) => {
    if (!name) return '?';
    return name.split(' ').map((w) => w[0]).slice(0, 2).join('').toUpperCase();
};

const navLinkClass = (pattern) => [
    'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150',
    sidebarCollapsed.value ? 'lg:justify-center lg:px-2' : '',
    route().current(pattern)
        ? 'bg-indigo-600 text-white shadow-sm'
        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
];

const subLinkClass = (name) => [
    'flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-all duration-150',
    route().current(name)
        ? 'bg-indigo-50 font-semibold text-indigo-700'
        : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700',
];
</script>

<template>
    <Toast />

    <!-- Loading dots -->
    <!-- Loading -->
    <Transition
        enter-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        leave-active-class="transition-opacity duration-200"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isLoading"
            class="fixed inset-0 z-[9999] bg-white/20 backdrop-blur-[1px] pointer-events-none"
        >
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="loading-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </Transition>

        <!-- ── Fix #1: Layout pakai lg:flex di root, sidebar lg:static ── -->
        <div class="min-h-screen bg-gray-50 lg:flex">

            <!-- Mobile Overlay — z-30 -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                leave-active-class="transition duration-150 ease-in"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="sidebarOpen"
                    class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm lg:hidden"
                    @click="toggleSidebar"
                />
            </Transition>

            <!-- Sidebar — fixed mobile, static desktop, z-40 -->
            <aside
                :class="[
                    'fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-white border-r border-gray-100 shadow-xl transition-all duration-300 ease-in-out lg:sticky lg:top-0 lg:z-auto lg:h-screen lg:translate-x-0 lg:shadow-none lg:self-start',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                    sidebarCollapsed ? 'lg:w-20' : 'lg:w-64',
                ]"
            >
                <!-- Logo -->
                <div
                    class="flex h-16 shrink-0 items-center border-b border-gray-100 px-5"
                    :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : 'justify-between'"
                >
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600">
                            <Ticket class="h-4 w-4 text-white" />
                        </div>
                        <span
                            class="text-base font-bold tracking-tight text-gray-900"
                            :class="sidebarCollapsed ? 'lg:hidden' : ''"
                        >
                            Helpdesk
                        </span>
                    </div>

                    <!-- Close (mobile) -->
                    <button
                        class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 lg:hidden"
                        @click="toggleSidebar"
                    >
                        <X class="h-4 w-4" />
                    </button>

                    <!-- Collapse toggle (desktop) -->
                    <button
                        class="hidden rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 lg:block"
                        :class="sidebarCollapsed ? 'lg:hidden' : ''"
                        @click="toggleCollapse"
                        title="Collapse sidebar"
                    >
                        <ChevronsLeft class="h-4 w-4" />
                    </button>
                </div>

                <!-- Tombol expand saat collapsed, ditaruh terpisah di bawah logo biar center -->
                <div v-if="sidebarCollapsed" class="hidden justify-center border-b border-gray-100 py-2 lg:flex">
                    <button
                        class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                        @click="toggleCollapse"
                        title="Expand sidebar"
                    >
                        <ChevronsRight class="h-4 w-4" />
                    </button>
                </div>

                <!-- Nav -->
                <nav class="flex-1 overflow-y-auto px-3 py-4">
                    <div class="space-y-0.5">
                        <p
                            class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400"
                            :class="sidebarCollapsed ? 'lg:hidden' : ''"
                        >
                            Menu
                        </p>

                        <Link
        :href="route('dashboard')"
        :class="navLinkClass('dashboard')"
        :title="sidebarCollapsed ? 'Dashboard' : ''"
        @click="sidebarOpen = false"
    >
        <LayoutDashboard class="h-4 w-4 shrink-0" />
        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Dashboard</span>
            </Link>

            <Link
                :href="route('tickets.index')"
                :class="navLinkClass('tickets.*')"
                :title="sidebarCollapsed ? 'Tickets' : ''"
                @click="sidebarOpen = false"
            >
                <Ticket class="h-4 w-4 shrink-0" />
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Tickets</span>
            </Link>

            <Link
                v-if="user.role === 'admin'"
                :href="route('users.index')"
                :class="navLinkClass('users.*')"
                :title="sidebarCollapsed ? 'Users' : ''"
                @click="sidebarOpen = false"
            >
                <Users class="h-4 w-4 shrink-0" />
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Users</span>
            </Link>

                    <!-- Report -->
                    <div
                        v-if="['admin', 'agent'].includes(user.role)"
                        class="relative"
                        @mouseenter="openFlyout('reports', $event)"
                        @mouseleave="closeFlyout"
                    >
                        <button
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150"
                            :class="[
                                sidebarCollapsed ? 'lg:justify-center lg:px-2' : '',
                                route().current('reports.*')
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                            ]"
                            :title="sidebarCollapsed ? 'Report' : ''"
                            @click="!sidebarCollapsed && toggleReports()"
                        >
                            <BarChart3 class="h-4 w-4 shrink-0" />
                            <span class="flex-1 text-left" :class="sidebarCollapsed ? 'lg:hidden' : ''">Report</span>
                            <template v-if="!sidebarCollapsed">
                                <ChevronUp v-if="reportsOpen" class="h-3.5 w-3.5" />
                                <ChevronDown v-else class="h-3.5 w-3.5" />
                            </template>
                        </button>

                        <!-- Submenu inline (sidebar normal) -->
                        <Transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-1"
                            leave-active-class="transition duration-150 ease-in"
                            leave-to-class="opacity-0 -translate-y-1"
                        >
                            <div v-if="reportsOpen && !sidebarCollapsed" class="mt-0.5 space-y-0.5 pl-4">
                                <Link :href="route('reports.index')" :class="subLinkClass('reports.index')" @click="sidebarOpen = false">
                                    <CalendarRange class="h-3.5 w-3.5 shrink-0" />
                                    Daily
                                </Link>
                                <Link :href="route('reports.overview')" :class="subLinkClass('reports.overview')" @click="sidebarOpen = false">
                                    <Globe class="h-3.5 w-3.5 shrink-0" />
                                    Overview
                                </Link>
                            </div>
                        </Transition>
                    </div>

                    <!-- Logs -->
                    <div
                        v-if="['admin', 'agent'].includes(user.role)"
                        class="relative"
                        @mouseenter="openFlyout('logs', $event)"
                        @mouseleave="closeFlyout"
                    >
                        <button
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150"
                            :class="[
                                sidebarCollapsed ? 'lg:justify-center lg:px-2' : '',
                                route().current('logs.*')
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                            ]"
                            :title="sidebarCollapsed ? 'Logs' : ''"
                            @click="!sidebarCollapsed && toggleLogs()"
                        >
                            <ScrollText class="h-4 w-4 shrink-0" />
                            <span class="flex-1 text-left" :class="sidebarCollapsed ? 'lg:hidden' : ''">Logs</span>
                            <template v-if="!sidebarCollapsed">
                                <ChevronUp v-if="logsOpen" class="h-3.5 w-3.5" />
                                <ChevronDown v-else class="h-3.5 w-3.5" />
                            </template>
                        </button>

                        <Transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-1"
                            leave-active-class="transition duration-150 ease-in"
                            leave-to-class="opacity-0 -translate-y-1"
                        >
                            <div v-if="logsOpen && !sidebarCollapsed" class="mt-0.5 space-y-0.5 pl-4">
                                <Link :href="route('logs.login')" :class="subLinkClass('logs.login')" @click="sidebarOpen = false">
                                    <LogIn class="h-3.5 w-3.5 shrink-0" />
                                    Login Logs
                                </Link>
                                <Link :href="route('logs.activity')" :class="subLinkClass('logs.activity')" @click="sidebarOpen = false">
                                    <Activity class="h-3.5 w-3.5 shrink-0" />
                                    Activity Logs
                                </Link>
                            </div>
                        </Transition>
                    </div>
                </div>
                <Teleport to="body">
                    <Transition
                        enter-active-class="transition duration-150 ease-out"
                        enter-from-class="opacity-0 -translate-x-1"
                        leave-active-class="transition duration-100 ease-in"
                        leave-to-class="opacity-0 -translate-x-1"
                    >
                        <div
                            v-if="sidebarCollapsed && hoveredMenu === 'reports'"
                            class="fixed z-50 w-44 overflow-hidden rounded-xl border border-gray-100 bg-white p-1.5 shadow-lg ring-1 ring-black/5"
                            :style="flyoutStyle"
                            @mouseenter="hoveredMenu = 'reports'"
                            @mouseleave="closeFlyout"
                        >
                            <p class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-gray-400">Report</p>
                            <Link :href="route('reports.index')" :class="subLinkClass('reports.index')" @click="sidebarOpen = false; closeFlyout()">
                                <CalendarRange class="h-3.5 w-3.5 shrink-0" />
                                Daily
                            </Link>
                            <Link :href="route('reports.overview')" :class="subLinkClass('reports.overview')" @click="sidebarOpen = false; closeFlyout()">
                                <Globe class="h-3.5 w-3.5 shrink-0" />
                                Overview
                            </Link>
                        </div>
                    </Transition>

                    <Transition
                        enter-active-class="transition duration-150 ease-out"
                        enter-from-class="opacity-0 -translate-x-1"
                        leave-active-class="transition duration-100 ease-in"
                        leave-to-class="opacity-0 -translate-x-1"
                    >
                        <div
                            v-if="sidebarCollapsed && hoveredMenu === 'logs'"
                            class="fixed z-50 w-44 overflow-hidden rounded-xl border border-gray-100 bg-white p-1.5 shadow-lg ring-1 ring-black/5"
                            :style="flyoutStyle"
                            @mouseenter="hoveredMenu = 'logs'"
                            @mouseleave="closeFlyout"
                        >
                            <p class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-gray-400">Logs</p>
                            <Link :href="route('logs.login')" :class="subLinkClass('logs.login')" @click="sidebarOpen = false; closeFlyout()">
                                <LogIn class="h-3.5 w-3.5 shrink-0" />
                                Login Logs
                            </Link>
                            <Link :href="route('logs.activity')" :class="subLinkClass('logs.activity')" @click="sidebarOpen = false; closeFlyout()">
                                <Activity class="h-3.5 w-3.5 shrink-0" />
                                Activity Logs
                            </Link>
                        </div>
                    </Transition>
                </Teleport>
            </nav>

            <!-- ── Fix #5: Sidebar footer — versi lebih maintainable ── -->
            <div class="border-t border-gray-100 bg-white px-5 py-4" :class="sidebarCollapsed ? 'lg:px-2' : ''">
                <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'lg:justify-center' : ''">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">
                        H
                    </div>
                    <div class="min-w-0" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                        <p class="truncate text-sm font-semibold text-gray-800">Helpdesk</p>
                        <p class="text-xs text-gray-500">Version 1.0.0</p>
                        <p class="text-[11px] text-gray-400">Laravel 11 · Vue 3</p>
                    </div>
                </div>
                <div class="mt-3 border-t border-gray-100 pt-3" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                    <p class="text-[11px] text-gray-400">
                        © {{ new Date().getFullYear() }} Widhiat
                    </p>
                </div>
            </div>
        </aside>

        <!-- Main — tidak pakai lg:ml-64 lagi karena sidebar sudah lg:static -->
        <div class="flex min-w-0 flex-1 flex-col">

            <!-- ── Fix #2: Header sticky dengan scroll hide/show + cleanup ── -->
            <header
                class="sticky top-0 z-50 flex h-16 shrink-0 items-center justify-between border-b border-gray-100 bg-white/95 px-4 backdrop-blur-sm transition-all duration-300 ease-in-out lg:px-6"
                :class="headerVisible
                    ? 'translate-y-0 opacity-100 shadow-sm'
                    : '-translate-y-full opacity-0 shadow-none'"
            >
                <button
                    class="rounded-xl p-2 text-gray-500 transition hover:bg-gray-100 lg:hidden"
                    @click="toggleSidebar"
                >
                    <Menu class="h-5 w-5" />
                </button>

                <h2 class="text-base font-semibold text-gray-800 lg:text-lg">
                    Helpdesk Management
                </h2>

                <!-- User dropdown -->
                <div class="relative">
                    <button
                        class="flex items-center gap-2.5 rounded-xl px-3 py-2 transition hover:bg-gray-100"
                        @click="toggleUserDropdown"
                    >
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white"
                            :class="avatarColor(user.name)"
                        >
                            {{ initials(user.name) }}
                        </div>

                        <div class="hidden text-left lg:block">
                            <p class="text-sm font-semibold leading-tight text-gray-800">
                                {{ user.name }}
                            </p>
                            <p class="text-xs capitalize leading-tight text-gray-400">
                                {{ user.role }}
                            </p>
                        </div>

                        <ChevronDown
                            class="hidden h-3.5 w-3.5 text-gray-400 transition-transform duration-200 lg:block"
                            :class="showUserDropdown ? 'rotate-180' : ''"
                        />
                    </button>

                    <!-- ── Fix #6: Dropdown z-55 supaya di atas header z-50 ── -->
                    <Transition
                        enter-active-class="transition duration-150 ease-out"
                        enter-from-class="opacity-0 scale-95 translate-y-1"
                        leave-active-class="transition duration-100 ease-in"
                        leave-to-class="opacity-0 scale-95 translate-y-1"
                    >
                        <div
                            v-if="showUserDropdown"
                            class="absolute right-0 top-full mt-2 w-56 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg ring-1 ring-black/5"
                            style="z-index: 55;"
                        >
                            <div class="border-b border-gray-100 px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white"
                                        :class="avatarColor(user.name)"
                                    >
                                        {{ initials(user.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-gray-800">
                                            {{ user.name }}
                                        </p>
                                        <p class="truncate text-xs capitalize text-gray-400">
                                            {{ user.role }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-1.5">
                                <Link
                                    :href="route('profile.edit')"
                                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                                    @click="closeUserDropdown"
                                >
                                    <User class="h-4 w-4 shrink-0 text-gray-400" />
                                    View Profile
                                </Link>

                                <div class="my-1.5 border-t border-gray-100" />

                                <button
                                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-red-500 transition hover:bg-red-50 hover:text-red-600"
                                    @click="confirmLogout"
                                >
                                    <LogOut class="h-4 w-4 shrink-0" />
                                    Sign Out
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </header>

            <main class="flex-1 lg:p-6">
                <slot />
            </main>
        </div>
    </div>

    <!-- ── Fix #6: Overlay dropdown z-45 supaya di bawah dropdown ── -->
    <Teleport to="body">
        <div
            v-if="showUserDropdown"
            class="fixed inset-0"
            style="z-index: 45;"
            @click="closeUserDropdown"
        />
    </Teleport>

    <!-- Modal logout z-60 supaya di atas segalanya -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showLogoutModal"
                class="fixed inset-0 flex items-center justify-center bg-black/50 px-4 backdrop-blur-sm"
                style="z-index: 60;"
                @click.self="cancelLogout"
            >
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    leave-active-class="transition duration-150 ease-in"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="showLogoutModal"
                        class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5"
                    >
                        <div class="flex items-start justify-between border-b border-gray-100 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-50">
                                    <LogOut class="h-5 w-5 text-red-600" />
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Sign Out</h3>
                                    <p class="text-xs text-gray-400">You will be redirected to login page</p>
                                </div>
                            </div>
                            <button
                                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                                @click="cancelLogout"
                            >
                                <X class="h-4 w-4" />
                            </button>
                        </div>

                        <div class="px-6 py-5">
                            <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white"
                                    :class="avatarColor(user.name)"
                                >
                                    {{ initials(user.name) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-gray-800">{{ user.name }}</p>
                                    <p class="truncate text-xs capitalize text-gray-400">{{ user.role }}</p>
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-gray-500">
                                Are you sure you want to sign out? Any unsaved changes will be lost.
                            </p>
                        </div>

                        <div class="flex justify-end gap-2.5 border-t border-gray-100 bg-gray-50/60 px-6 py-4">
                            <button
                                class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100"
                                @click="cancelLogout"
                            >
                                Cancel
                            </button>
                            <button
                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
                                @click="executeLogout"
                            >
                                <LogOut class="h-4 w-4" />
                                Sign Out
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.loading-dots{
    display:flex;
    align-items:center;
    gap:8px;
}

.loading-dots span{
    width:10px;
    height:10px;
    border-radius:9999px;
    background:#4f46e5;
    animation:dotPulse .9s infinite ease-in-out;
}

.loading-dots span:nth-child(2){
    animation-delay:.15s;
}

.loading-dots span:nth-child(3){
    animation-delay:.30s;
}

@keyframes dotPulse{

    0%,
    80%,
    100%{
        transform:scale(.5);
        opacity:.25;
    }

    40%{
        transform:scale(1);
        opacity:1;
    }
}
</style>