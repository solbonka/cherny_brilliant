<script setup lang="ts">
import {ref, computed, onMounted} from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AppHeadLogo from "@/layouts/app/AppHeadLogo.vue";
import { useFavoritesStore } from '@/stores/favorites';

const favoritesStore = useFavoritesStore();

onMounted(() => {
    favoritesStore.load();
});

const isSidebarOpen = ref<boolean>(false);
const isScrolled = ref<boolean>(false);
const isSearchOpen = ref<boolean>(false);

const favoritesCount = computed(() => favoritesStore.count);

const page = usePage();
const isHomePage = computed(() => {
    return page.component === 'Main/Index' || page.url === '/' || page.url.startsWith('/?');
});

const toggleSidebar = (): void => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleSearch = (): void => {
    isSearchOpen.value = !isSearchOpen.value;
};

const navigateToSection = (sectionId: string): void => {
    isSidebarOpen.value = false;

    if (isHomePage.value) {
        setTimeout(() => scrollToElement(sectionId), 50);
    } else {
        router.visit('/', {
            data: { section: sectionId },
            preserveState: false,
            preserveScroll: false,
            onSuccess: () => {
                setTimeout(() => scrollToElement(sectionId), 300);
            }
        });
    }
};

const scrollToElement = (sectionId: string): void => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

window.addEventListener('scroll', () => {
    isScrolled.value = window.scrollY > 20;
});

window.addEventListener('storage', (e) => {
    if (e.key === 'favorites') {
        favoritesStore.load();
    }
});
</script>

<template>
    <header :class="{ 'scrolled': isScrolled }">
        <div class="header-container">
            <!-- Left: Menu button -->
            <button class="menu-toggle" :class="{ 'active': isSidebarOpen }" @click="toggleSidebar" aria-label="Меню">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Center: Logo -->
            <div class="logo-center">
                <Link href="/" class="logo-link">
                    <AppHeadLogo />
                </Link>
            </div>

            <div class="header-actions">
                <!-- Telegram -->
                <a href="https://t.me/mikhaylova_evgenia" target="_blank" class="icon-btn icon-telegram" aria-label="Telegram">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM16.64 8.8C16.49 10.38 15.84 14.22 15.51 15.99C15.37 16.74 15.09 16.99 14.83 17.01C14.25 17.07 13.81 16.64 13.25 16.27C12.37 15.69 11.87 15.33 11.02 14.77C10.03 14.12 10.67 13.76 11.24 13.18C11.39 13.03 13.95 10.7 14 10.49C14.0069 10.4582 14.006 10.4252 13.9973 10.3938C13.9886 10.3624 13.9724 10.3337 13.95 10.31C13.89 10.26 13.81 10.28 13.74 10.29C13.65 10.31 12.25 11.24 9.52 13.08C9.12 13.35 8.76 13.49 8.44 13.48C8.08 13.47 7.4 13.28 6.89 13.11C6.26 12.91 5.77 12.8 5.81 12.45C5.83 12.27 6.08 12.09 6.55 11.9C9.47 10.63 11.41 9.79 12.38 9.39C15.16 8.23 15.73 8.03 16.11 8.03C16.19 8.03 16.38 8.05 16.5 8.15C16.6 8.23 16.63 8.34 16.64 8.42C16.63 8.48 16.65 8.66 16.64 8.8Z" fill="currentColor"/>
                    </svg>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/+79025647339" target="_blank" class="icon-btn icon-whatsapp" aria-label="WhatsApp">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67ZM8.53 7.33C8.37 7.33 8.1 7.39 7.87 7.64C7.65 7.89 7 8.5 7 9.71C7 10.93 7.89 12.1 8 12.27C8.14 12.44 9.76 14.94 12.25 16C12.84 16.27 13.3 16.42 13.66 16.53C14.25 16.72 14.79 16.69 15.22 16.63C15.7 16.56 16.68 16.03 16.89 15.45C17.1 14.87 17.1 14.38 17.04 14.27C16.97 14.17 16.81 14.11 16.56 14C16.31 13.86 15.09 13.26 14.87 13.18C14.64 13.1 14.5 13.06 14.31 13.3C14.15 13.55 13.67 14.11 13.53 14.27C13.38 14.44 13.24 14.46 13 14.34C12.74 14.21 11.94 13.95 11 13.11C10.26 12.45 9.77 11.64 9.62 11.39C9.5 11.15 9.61 11 9.73 10.89C9.84 10.78 10 10.6 10.1 10.45C10.23 10.31 10.27 10.2 10.35 10.04C10.43 9.87 10.39 9.73 10.33 9.61C10.27 9.5 9.77 8.26 9.56 7.77C9.36 7.29 9.16 7.35 9 7.34C8.86 7.34 8.7 7.33 8.53 7.33Z" fill="currentColor"/>
                    </svg>
                </a>

                <!-- Search -->
                <button class="icon-btn" @click="toggleSearch" aria-label="Поиск">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.5 14H14.71L14.43 13.73C15.41 12.59 16 11.11 16 9.5C16 5.91 13.09 3 9.5 3C5.91 3 3 5.91 3 9.5C3 13.09 5.91 16 9.5 16C11.11 16 12.59 15.41 13.73 14.43L14 14.71V15.5L19 20.49L20.49 19L15.5 14ZM9.5 14C7.01 14 5 11.99 5 9.5C5 7.01 7.01 5 9.5 5C11.99 5 14 7.01 14 9.5C14 11.99 11.99 14 9.5 14Z" fill="currentColor"/>
                    </svg>
                </button>

                <!-- Favorites -->
                <Link href="/favorites" class="icon-btn favorites-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.28 2 8.5C2 5.42 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.09C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.42 22 8.5C22 12.28 18.6 15.36 13.45 20.04L12 21.35Z" fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span v-if="favoritesCount > 0" class="badge">{{ favoritesCount }}</span>
                </Link>

                <!-- Cart -->
                <Link href="/cart" class="icon-btn cart-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 18C5.9 18 5.01 18.9 5.01 20C5.01 21.1 5.9 22 7 22C8.1 22 9 21.1 9 20C9 18.9 8.1 18 7 18ZM1 2V4H3L6.6 11.59L5.25 14.04C5.09 14.32 5 14.65 5 15C5 16.1 5.9 17 7 17H19V15H7.42C7.28 15 7.17 14.89 7.17 14.75L7.2 14.63L8.1 13H15.55C16.3 13 16.96 12.59 17.3 11.97L20.88 5.48C20.96 5.34 21 5.17 21 5C21 4.45 20.55 4 20 4H5.21L4.27 2H1ZM17 18C15.9 18 15.01 18.9 15.01 20C15.01 21.1 15.9 22 17 22C18.1 22 19 21.1 19 20C19 18.9 18.1 18 17 18Z" fill="currentColor"/>
                    </svg>
                    <span class="badge">0</span>
                </Link>
            </div>
        </div>
    </header>

    <!-- Search Panel -->
    <div class="search-overlay" :class="{ 'active': isSearchOpen }" @click="toggleSearch"></div>
    <div class="search-panel" :class="{ 'active': isSearchOpen }">
        <div class="search-container">
            <div class="search-header">
                <div class="search-input-wrapper">
                    <svg class="search-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.5 14H14.71L14.43 13.73C15.41 12.59 16 11.11 16 9.5C16 5.91 13.09 3 9.5 3C5.91 3 3 5.91 3 9.5C3 13.09 5.91 16 9.5 16C11.11 16 12.59 15.41 13.73 14.43L14 14.71V15.5L19 20.49L20.49 19L15.5 14ZM9.5 14C7.01 14 5 11.99 5 9.5C5 7.01 7.01 5 9.5 5C11.99 5 14 7.01 14 9.5C14 11.99 11.99 14 9.5 14Z" fill="currentColor"/>
                    </svg>
                    <input type="text" placeholder="Поиск" class="search-input" autofocus />
                </div>
                <button class="close-search" @click="toggleSearch" aria-label="Закрыть">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>

            <div class="search-filters">
                <button class="filter-tag">Шубы</button>
                <button class="filter-tag">Дубленки</button>
                <button class="filter-tag">Пуховики</button>
            </div>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <div class="sidebar-overlay" :class="{ 'active': isSidebarOpen }" @click="toggleSidebar"></div>
    <aside class="sidebar" :class="{ 'active': isSidebarOpen }">
        <nav class="sidebar-nav">
            <a @click="navigateToSection('home')" class="sidebar-link">Главная</a>
            <a @click="navigateToSection('collection')" class="sidebar-link">Коллекция</a>
            <a @click="navigateToSection('about')" class="sidebar-link">О салоне</a>
            <a @click="navigateToSection('contact')" class="sidebar-link">Контакты</a>
            <Link href="/catalog" @click="isSidebarOpen = false" class="sidebar-link">Каталог</Link>
            <Link href="/favorites" @click="isSidebarOpen = false" class="sidebar-link">
                Избранное
                <span v-if="favoritesCount > 0" class="sidebar-badge">{{ favoritesCount }}</span>
            </Link>
        </nav>
    </aside>
</template>

<style scoped>
header {
    position: fixed;
    top: 0;
    width: 100%;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(10px);
    z-index: 1000;
    transition: all 0.3s;
}

header.scrolled {
    background: rgba(0, 0, 0, 0.98);
}

.header-container {
    display: grid;
    grid-template-columns: 60px 1fr 240px;
    align-items: center;
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 60px;
}

.menu-toggle {
    display: flex;
    flex-direction: column;
    gap: 5px;
    cursor: pointer;
    background: transparent;
    border: none;
    padding: 8px;
    justify-self: start;
    position: relative;
    z-index: 1002;
}

.menu-toggle span {
    width: 28px;
    height: 2px;
    background: #ffffff;
    transition: all 0.3s;
}

.menu-toggle:hover span {
    background: #cccccc;
}

.menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(7px, 7px);
}

.menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -7px);
}

.logo-center {
    display: flex;
    justify-content: center;
}

.logo-link {
    display: inline-flex;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s;
}

.logo-link:hover {
    opacity: 0.8;
}

.header-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 15px;
}

.icon-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    color: #ffffff;
    cursor: pointer;
    transition: all 0.3s;
    border-radius: 50%;
}

.icon-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

.badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ff4444;
    color: #ffffff;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 5px;
    border-radius: 10px;
    min-width: 16px;
    text-align: center;
    line-height: 1;
}

/* Sidebar */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 998;
}

.sidebar-overlay.active {
    opacity: 1;
    visibility: visible;
}

.sidebar {
    position: fixed;
    top: 0;
    left: -320px;
    width: 320px;
    height: 100%;
    background: #1a1a1a;
    z-index: 999;
    transition: all 0.3s ease-in-out;
    overflow-y: auto;
}

.sidebar.active {
    left: 0;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    padding: 150px 0 40px 0;
}

.sidebar-link {
    position: relative;
    color: #ffffff;
    text-decoration: none;
    font-size: 16px;
    font-weight: 500;
    padding: 20px 40px;
    transition: all 0.3s;
    cursor: pointer;
    border-left: 3px solid transparent;
}

.sidebar-link:hover {
    background: rgba(255, 255, 255, 0.05);
    border-left-color: #ffffff;
}

.sidebar-badge {
    display: inline-block;
    background: #ff4444;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 12px;
    margin-left: 10px;
}

/* Search Panel */
.search-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 1002;
}

.search-overlay.active {
    opacity: 1;
    visibility: visible;
}

.search-panel {
    position: fixed;
    top: -100%;
    left: 0;
    width: 100%;
    background: #ffffff;
    z-index: 1003;
    transition: all 0.4s ease-out;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.search-panel.active {
    top: 0;
}

.search-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 30px 60px;
}

.search-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.search-input-wrapper {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    background: #f5f5f5;
    border-radius: 8px;
}

.search-icon {
    color: #666666;
    flex-shrink: 0;
}

.search-input {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 18px;
    color: #000000;
    outline: none;
}

.search-input::placeholder {
    color: #999999;
}

.close-search {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border: none;
    background: transparent;
    color: #000000;
    cursor: pointer;
    transition: all 0.3s;
    border-radius: 50%;
}

.close-search:hover {
    background: #f5f5f5;
}

.search-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.filter-tag {
    padding: 10px 20px;
    border: 1px solid #e0e0e0;
    background: transparent;
    color: #000000;
    font-size: 14px;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s;
}

.filter-tag:hover {
    background: #000000;
    color: #ffffff;
    border-color: #000000;
}

@media (max-width: 1024px) {
    .header-container {
        padding: 20px 40px;
        grid-template-columns: 60px 1fr 200px;
    }

    .search-container {
        padding: 25px 40px;
    }
}

@media (max-width: 768px) {
    .header-container {
        padding: 15px 20px;
        grid-template-columns: 50px 1fr auto;
    }

    .logo-center {
        min-width: 200px;
    }

    .header-actions {
        gap: 4px;
        row-gap: 4px;
        flex-wrap: wrap;
        max-width: 100px;
    }

    .header-actions .icon-telegram,
    .header-actions .icon-whatsapp {
        display: none !important;
    }

    .icon-btn {
        width: 30px;
        height: 30px;
    }

    .sidebar {
        width: 100%;
        left: -100%;
    }

    .sidebar.active {
        left: 0;
    }

    .sidebar-nav {
        padding: 140px 0 40px 0;
    }

    .sidebar-link {
        font-size: 15px;
        padding: 18px 30px;
    }

    .search-container {
        padding: 20px 20px;
    }

    .search-input-wrapper {
        padding: 12px 15px;
    }

    .search-input {
        font-size: 16px;
    }

    .filter-tag {
        padding: 8px 16px;
        font-size: 13px;
    }
}
</style>
