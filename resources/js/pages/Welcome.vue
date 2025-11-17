<!-- App.vue -->
<template>
    <DefaultLayout>
        <Head title="ЧЕРНЫЙ БРИЛЛИАНТ"></Head>

        <div class="black-diamond-site">

            <MainHeader></MainHeader>

            <!-- Hero Section -->
            <section id="home" class="hero">
                <div class="hero-content">
                    <h1 class="animate-fade-in-up">ЧЕРНЫЙ БРИЛЛИАНТ</h1>
                    <p class="subtitle animate-fade-in-up-delay-1">Роскошные норковые шубы и верхняя одежда</p>
                    <p class="description animate-fade-in-up-delay-2">
                        Салон тщательно подготовил коллекцию для ваших новых красивых историй.
                        Стильная и комфортная верхняя одежда, соответствующая последним модным тенденциям
                    </p>
                    <div class="cta-buttons animate-fade-in-up-delay-3">
                        <button class="btn btn-primary" @click="scrollToSection('collection')">Смотреть коллекцию</button>
                        <button class="btn btn-secondary" @click="scrollToSection('contact')">Связаться с нами</button>
                    </div>
                </div>
            </section>

            <!-- Experience Section -->
            <section class="experience">
                <h2>НАШ ОПЫТ</h2>
                <div class="stats">
                    <div v-for="stat in stats" :key="stat.label" class="stat-item">
                        <div class="stat-number">{{ stat.number }}</div>
                        <div class="stat-label">{{ stat.label }}</div>
                    </div>
                </div>
            </section>

            <!-- Products Section -->
            <section id="collection" class="products">
                <h2 class="section-title">НАША КОЛЛЕКЦИЯ</h2>
                <p class="section-subtitle">
                    Здесь вы найдете модные шубы и парки, стильные дубленки и элегантные пальто,
                    качественные пуховики и куртки, тренчи и жилеты
                </p>

                <div class="product-grid">
                    <div v-for="product in products" :key="product.id" class="product-card">
                        <div class="product-image">
                            {{ product.icon }}
                            <button
                                class="favorite-btn"
                                :class="{ 'active': isFavorite(product.id) }"
                                @click.stop="toggleFavorite(product.id)"
                                :title="isFavorite(product.id) ? 'Удалить из избранного' : 'Добавить в избранное'"
                            >
                                {{ isFavorite(product.id) ? '❤️' : '🤍' }}
                            </button>
                        </div>
                        <div class="product-info">
                            <div class="product-name">{{ product.name }}</div>
                            <div class="product-description">{{ product.description }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- About Section -->
            <section id="about" class="about">
                <div class="about-content">
                    <div class="about-text">
                        <h2>О САЛОНЕ</h2>
                        <p>
                            Салон "Черный Бриллиант" — это синоним роскоши и качества в мире верхней одежды.
                            Уже 27 лет мы радуем наших покупателей эксклюзивными коллекциями.
                        </p>
                        <p>
                            Мы постоянно следим за мировыми трендами, посещаем международные выставки и работаем
                            только с ведущими фабриками, чтобы предложить вам самое лучшее.
                        </p>

                        <ul class="about-features">
                            <li v-for="feature in features" :key="feature">{{ feature }}</li>
                        </ul>
                    </div>
                    <div class="about-image">
                        <img src="black_diamond.jpg" alt="лого">
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <Contact></Contact>
        </div>
    </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainHeader from '@/pages/main/MainHeader.vue';
import Contact from "@/pages/main/Contact.vue";
import DefaultLayout from "@/pages/main/DefaultLayout.vue";

// Типы данных
interface Product {
    id: number;
    icon: string;
    name: string;
    description: string;
}

interface Stat {
    number: string;
    label: string;
}

// Реактивные переменные
const favorites = ref<Set<number>>(new Set());

// Данные
const products = ref<Product[]>([
    { id: 1, icon: '🦊', name: 'New collection 2025/2026', description: 'Коллекция 2025/2026 — гармония стиля, тепла и натурального меха' },
    { id: 2, icon: '🦊', name: 'Норковые шубы', description: 'Роскошные шубы из натуральной норки премиум качества' },
    { id: 3, icon: '👔', name: 'Дубленки', description: 'Стильные дубленки из натуральной кожи' },
    { id: 4, icon: '❄️', name: 'Пуховики', description: 'Качественные пуховики с современным дизайном' },
]);

const stats = ref<Stat[]>([
    { number: '27', label: 'ЛЕТ НА РЫНКЕ' },
    { number: '1000+', label: 'ДОВОЛЬНЫХ КЛИЕНТОВ' },
    { number: '100%', label: 'КАЧЕСТВО' }
]);

const features = ref<string[]>([
    'Постоянное обновление коллекций',
    'Работа с ведущими мировыми фабриками',
    'Посещение международных выставок',
    'Индивидуальный подход к каждому клиенту',
    'Гарантия качества на всю продукцию'
]);

const toggleFavorite = (productId: number): void => {
    if (favorites.value.has(productId)) {
        favorites.value.delete(productId);
    } else {
        favorites.value.add(productId);
    }
    // Сохраняем в localStorage
    saveFavoritesToStorage();
};

const isFavorite = (productId: number): boolean => {
    return favorites.value.has(productId);
};

const saveFavoritesToStorage = (): void => {
    localStorage.setItem('favorites', JSON.stringify(Array.from(favorites.value)));
};

const loadFavoritesFromStorage = (): void => {
    const saved = localStorage.getItem('favorites');
    if (saved) {
        favorites.value = new Set(JSON.parse(saved));
    }
};

const scrollToSection = (id: string): void => {
    const el = document.getElementById(id);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

// Lifecycle hooks
onMounted(() => {
    loadFavoritesFromStorage();
});

onUnmounted(() => {
    // Удаляем обработчик, если нужно
});
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.black-diamond-site {
    font-family: 'Montserrat', 'Segoe UI', sans-serif;
    background: #ffffff;
    color: #000000;
    overflow-x: hidden;
    line-height: 1.6;
}

/* Scroll offset for fixed header */
section {
    scroll-margin-top: 120px;
}

/* Hero Section */
.hero {
    min-height: 100vh;
    background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
    linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding-top: 120px;
}

.hero-content {
    text-align: center;
    color: #ffffff;
    max-width: 900px;
    padding: 20px;
}

.hero h1 {
    font-size: 64px;
    font-weight: 700;
    margin-bottom: 20px;
    letter-spacing: 4px;
    text-transform: uppercase;
}

.hero .subtitle {
    font-size: 24px;
    margin-bottom: 15px;
    font-weight: 300;
    letter-spacing: 2px;
}

.hero .description {
    font-size: 18px;
    margin-bottom: 40px;
    line-height: 1.8;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 1s ease;
}

.animate-fade-in-up-delay-1 {
    animation: fadeInUp 1.2s ease;
}

.animate-fade-in-up-delay-2 {
    animation: fadeInUp 1.4s ease;
}

.animate-fade-in-up-delay-3 {
    animation: fadeInUp 1.6s ease;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 18px 45px;
    text-decoration: none;
    font-size: 16px;
    font-weight: 600;
    border: 2px solid;
    transition: all 0.3s;
    letter-spacing: 1px;
    cursor: pointer;
    background: none;
}

.btn-primary {
    background: #ffffff;
    color: #000000;
    border-color: #ffffff;
}

.btn-primary:hover {
    background: transparent;
    color: #ffffff;
}

.btn-secondary {
    background: transparent;
    color: #ffffff;
    border-color: #ffffff;
}

.btn-secondary:hover {
    background: #ffffff;
    color: #000000;
}

/* Experience Section */
.experience {
    background: #000000;
    color: #ffffff;
    padding: 80px 80px;
    text-align: center;
}

.experience h2 {
    font-size: 48px;
    margin-bottom: 60px;
    font-weight: 700;
    letter-spacing: 3px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 60px;
    max-width: 1200px;
    margin: 0 auto;
}

.stat-item {
    padding: 40px 20px;
    border: 2px solid #ffffff;
    transition: all 0.3s;
}

.stat-item:hover {
    background: #ffffff;
    color: #000000;
    transform: translateY(-10px);
}

.stat-number {
    font-size: 56px;
    font-weight: 700;
    margin-bottom: 15px;
}

.stat-label {
    font-size: 18px;
    letter-spacing: 1px;
    font-weight: 300;
}

/* Products Section */
.products {
    padding: 100px 80px;
    background: #ffffff;
}

.section-title {
    text-align: center;
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 20px;
    letter-spacing: 3px;
}

.section-subtitle {
    text-align: center;
    font-size: 18px;
    color: #666666;
    margin-bottom: 60px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 40px;
    max-width: 1400px;
    margin: 0 auto;
}

.product-card {
    background: #000000;
    color: #ffffff;
    overflow: hidden;
    transition: all 0.3s;
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.product-image {
    width: 100%;
    height: 350px;
    background: linear-gradient(135deg, #1a1a1a, #2a2a2a);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 64px;
    transition: all 0.3s;
    position: relative;
}

.favorite-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    z-index: 10;
}

.favorite-btn:hover {
    transform: scale(1.15);
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.favorite-btn.active {
    animation: heartbeat 0.3s ease;
}

@keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

.product-card:hover .product-image {
    background: linear-gradient(135deg, #2a2a2a, #3a3a3a);
}

.product-info {
    padding: 30px;
}

.product-name {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 10px;
    letter-spacing: 1px;
}

.product-description {
    font-size: 15px;
    color: #cccccc;
    line-height: 1.6;
}

/* About Section */
.about {
    background: #f8f8f8;
    padding: 100px 80px;
}

.about-content {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

.about-text h2 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 30px;
    letter-spacing: 2px;
}

.about-text p {
    font-size: 17px;
    line-height: 1.8;
    color: #333333;
    margin-bottom: 20px;
}

.about-features {
    list-style: none;
    margin-top: 30px;
}

.about-features li {
    font-size: 16px;
    padding: 15px 0;
    border-bottom: 1px solid #dddddd;
    display: flex;
    align-items: center;
    gap: 15px;
}

.about-features li::before {
    content: '◆';
    font-size: 20px;
}

.about-image {
    height: 500px;
    background: linear-gradient(135deg, #000000, #2a2a2a);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 120px;
    color: #ffffff;
}

.about-image img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* заполняет контейнер, обрезая лишнее */
    display: block;
}

/* Tablet Styles */
@media (max-width: 1024px) {
    .hero h1 {
        font-size: 48px;
    }

    .hero .subtitle {
        font-size: 20px;
    }

    .products, .about, .experience {
        padding: 80px 40px;
    }

    .stats {
        gap: 40px;
    }

    .about-content {
        gap: 50px;
    }
}

/* Mobile Styles */
@media (max-width: 768px) {
    section {
        scroll-margin-top: 80px;
    }

    .hero {
        min-height: 100vh;
        background-attachment: scroll;
        padding-top: 100px;
    }

    .hero h1 {
        font-size: 36px;
        letter-spacing: 2px;
    }

    .hero .subtitle {
        font-size: 18px;
    }

    .hero .description {
        font-size: 16px;
    }

    .cta-buttons {
        flex-direction: column;
        align-items: stretch;
        padding: 0 20px;
    }

    .btn {
        padding: 15px 30px;
        font-size: 15px;
    }

    .experience, .products, .about {
        padding: 60px 20px;
    }

    .section-title, .about h2, .experience h2 {
        font-size: 32px;
    }

    .section-subtitle {
        font-size: 16px;
    }

    .stats {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .stat-number {
        font-size: 42px;
    }

    .product-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .product-image {
        height: 300px;
    }

    .about-content {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .about-image {
        height: 350px;
        font-size: 80px;
    }

    footer {
        padding: 30px 20px;
    }
}

/* Small Mobile */
@media (max-width: 480px) {
    .hero h1 {
        font-size: 28px;
    }

    .hero .subtitle {
        font-size: 16px;
    }

    .section-title, .about h2, .experience h2 {
        font-size: 28px;
    }

    .stat-item {
        padding: 30px 15px;
    }

    .product-info {
        padding: 20px;
    }

    .product-name {
        font-size: 20px;
    }
}

/* Extra Small Mobile */
@media (max-width: 448px) {
    section {
        scroll-margin-top: 150px;
    }

    .hero {
        padding-top: 150px;
    }
}
/* Ultra Small Mobile */
@media (max-width: 268px) {
    section {
        scroll-margin-top: 170px;
    }

    .hero {
        padding-top: 170px;
    }
}
</style>
