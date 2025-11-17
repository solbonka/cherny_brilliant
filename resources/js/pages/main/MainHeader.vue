<script setup lang="ts">
import {ref, computed, onMounted, watch} from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AppHeadLogo from "@/layouts/app/AppHeadLogo.vue";
import { useFavoritesStore } from '@/stores/favorites';

const favoritesStore = useFavoritesStore();

// Загружаем избранное при старте приложения
onMounted(() => {
    favoritesStore.load();
});

// Реактивные переменные
const isMenuOpen = ref<boolean>(false);
const isScrolled = ref<boolean>(false);

// Текущее количество избранного (реактивно)
const favoritesCount = computed(() => favoritesStore.count);

// Получаем текущую страницу
const page = usePage();
const isHomePage = computed(() => {
    return page.component === 'Main/Index' || page.url === '/' || page.url.startsWith('/?');
});

// Методы
const toggleMenu = (): void => {
    isMenuOpen.value = !isMenuOpen.value;
};

const navigateToSection = (sectionId: string): void => {
    isMenuOpen.value = false;

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

// Скролл для изменения стиля шапки
window.addEventListener('scroll', () => {
    isScrolled.value = window.scrollY > 20;
});

// Подписка на изменения localStorage в других вкладках
window.addEventListener('storage', (e) => {
    if (e.key === 'favorites') {
        favoritesStore.load();
    }
});
</script>

<template>
    <header :class="{ 'scrolled': isScrolled }">
        <AppHeadLogo />

        <nav :class="{ 'active': isMenuOpen }">
            <a @click="navigateToSection('home')">Главная</a>
            <a @click="navigateToSection('collection')">Коллекция</a>
            <a @click="navigateToSection('about')">О салоне</a>
            <a @click="navigateToSection('contact')">Контакты</a>
            <Link href="/catalog" class="favorites-link" @click="isMenuOpen = false">
                Каталог
            </Link>
            <Link href="/favorites" class="favorites-link" @click="isMenuOpen = false">
                Избранное
                <span v-if="favoritesCount > 0" class="favorites-badge">{{ favoritesCount }}</span>
            </Link>

        </nav>

        <div class="menu-toggle" :class="{ 'active': isMenuOpen }" @click="toggleMenu">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </header>
</template>


<style scoped>
header {
    position: fixed;
    top: 0;
    width: 100%;
    padding: 20px 80px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(10px);
    transition: all 0.3s;
}

header.scrolled {
    padding: 10px 60px;
    background: rgba(0, 0, 0, 0.9);
}

nav {
    display: flex;
    gap: 40px;
    align-items: center;
}

nav a {
    color: #ffffff;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s;
    letter-spacing: 1px;
    cursor: pointer;
}

nav a:hover {
    color: #cccccc;
}

.favorites-link {
    position: relative;
    color: #ffffff;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s;
    letter-spacing: 1px;
}

.favorites-badge {
    position: absolute;
    top: -8px;
    right: -25px;
    background: #ff4444;
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 50%;
    min-width: 18px;
    text-align: center;
}

.menu-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    cursor: pointer;
}

.menu-toggle span {
    width: 30px;
    height: 3px;
    background: #ffffff;
    transition: all 0.3s;
}

@media (max-width: 1024px) {
    header {
        padding: 20px 40px;
    }

    nav {
        gap: 25px;
    }
}

@media (max-width: 768px) {
    header {
        padding: 15px 20px;
    }

    nav {
        position: fixed;
        top: 70px;
        left: -100%;
        width: 100%;
        height: calc(100vh - 70px);
        background: rgba(0, 0, 0, 0.98);
        flex-direction: column;
        justify-content: center;
        gap: 30px;
        transition: all 0.3s;
    }

    nav.active {
        left: 0;
    }

    nav a {
        font-size: 20px;
    }

    .menu-toggle {
        display: flex;
    }

    .menu-toggle.active span:nth-child(1) {
        transform: rotate(45deg) translate(8px, 8px);
    }

    .menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }

    .menu-toggle.active span:nth-child(3) {
        transform: rotate(-45deg) translate(8px, -8px);
    }
}
</style>
