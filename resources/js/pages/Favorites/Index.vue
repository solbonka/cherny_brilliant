<template>
    <DefaultLayout>
        <Head title="Избранное - ЧЕРНЫЙ БРИЛЛИАНТ"></Head>

        <div class="black-diamond-site">
            <MainHeader></MainHeader>

            <!-- Favorites Section -->
            <section class="favorite-page">
                <div class="favorite-header text-center mb-12">
                    <h1 class="text-4xl sm:text-5xl font-extrabold uppercase mb-4">ИЗБРАННОЕ</h1>
                    <p v-if="favoriteProducts.length === 0" class="text-gray-500 text-lg">
                        У вас пока нет избранных товаров
                    </p>
                    <p v-else class="text-gray-500 text-lg">
                        Товаров в избранном: {{ favoriteProducts.length }}
                    </p>
                </div>

                <div v-if="favoriteProducts.length > 0" class="product-grid">
                    <div v-for="product in favoriteProducts" :key="product.id" class="product-card">
                        <div class="product-image">
                            {{ product.icon }}
                            <button
                                class="favorite-btn active"
                                @click.stop="removeFavorite(product.id)"
                                title="Удалить из избранного"
                            >
                                ❤️
                            </button>
                        </div>
                        <div class="product-info">
                            <div class="product-name">{{ product.name }}</div>
                            <div class="product-description">{{ product.description }}</div>
                        </div>
                    </div>
                </div>

                <div v-else class="empty-state text-center mt-20">
                    <div class="empty-icon">🤍</div>
                    <p class="text-gray-500 text-lg mb-8">Добавьте товары в избранное на главной странице</p>
                    <Link href="/" class="btn-category active">Перейти в каталог</Link>
                </div>
            </section>
        </div>
    </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MainHeader from '@/pages/main/MainHeader.vue';
import DefaultLayout from '@/pages/main/DefaultLayout.vue';

// Типы данных
interface Product {
    id: number;
    icon: string;
    name: string;
    description: string;
}

// Props
const props = defineProps<{
    products: Product[];
}>();

// Реактивные переменные
const favorites = ref<Set<number>>(new Set());

// Вычисляемое свойство для фильтрации избранных товаров
const favoriteProducts = computed(() => {
    return props.products.filter(product => favorites.value.has(product.id));
});

// Методы
const removeFavorite = (productId: number): void => {
    favorites.value.delete(productId);
    saveFavoritesToStorage();
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

// Lifecycle hooks
onMounted(() => {
    loadFavoritesFromStorage();
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
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.favorite-page {
    flex: 1;
    padding: 150px 80px 100px;
    background: #ffffff;
}

.favorite-header h1 {
    font-size: 34px;
    font-weight: 700;
    letter-spacing: 3px;
}

.text-center {
    text-align: center;
}

.mb-12 {
    margin-bottom: 3rem;
}

.mb-4 {
    margin-bottom: 1rem;
}

.mb-8 {
    margin-bottom: 2rem;
}

.mt-20 {
    margin-top: 5rem;
}

.text-4xl {
    font-size: 2.25rem;
    line-height: 2.5rem;
}

.font-extrabold {
    font-weight: 800;
}

.uppercase {
    text-transform: uppercase;
}

.text-gray-500 {
    color: #6b7280;
}

.text-lg {
    font-size: 1.125rem;
    line-height: 1.75rem;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 40px;
}

.product-card {
    background: #000;
    color: #fff;
    overflow: hidden;
    transition: all 0.3s;
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.product-image {
    width: 100%;
    height: 350px;
    background: linear-gradient(135deg, #1a1a1a, #2a2a2a);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 64px;
    position: relative;
    transition: all 0.3s;
}

.product-card:hover .product-image {
    background: linear-gradient(135deg, #2a2a2a, #3a3a3a);
}

.favorite-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.9);
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
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

/* Empty State */
.empty-state {
    padding: 80px 20px;
}

.empty-icon {
    font-size: 120px;
    margin-bottom: 30px;
    opacity: 0.3;
}

.btn-category {
    padding: 12px 30px;
    border: 2px solid #000;
    background: #fff;
    color: #000;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-category.active,
.btn-category:hover {
    background: #000;
    color: #fff;
}

/* Медиазапросы для адаптивности */

/* Планшеты и небольшие ноутбуки */
@media (max-width: 1024px) {
    .favorite-page {
        padding: 150px 40px 80px;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .product-image {
        height: 300px;
        font-size: 56px;
    }
}

/* Планшеты в портретной ориентации */
@media (max-width: 768px) {
    .favorite-page {
        padding: 150px 25px 60px;
    }

    .favorite-header h1 {
        font-size: 28px;
        letter-spacing: 2px;
    }

    .text-4xl {
        font-size: 1.875rem;
        line-height: 2.25rem;
    }

    .text-lg {
        font-size: 1rem;
        line-height: 1.5rem;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px;
    }

    .product-image {
        height: 280px;
        font-size: 52px;
    }

    .product-info {
        padding: 25px;
    }

    .product-name {
        font-size: 20px;
    }

    .product-description {
        font-size: 14px;
    }

    .empty-icon {
        font-size: 100px;
    }

    .empty-state {
        padding: 60px 20px;
    }

    .mb-12 {
        margin-bottom: 2rem;
    }

    .mt-20 {
        margin-top: 3rem;
    }
}

/* Мобильные устройства */
@media (max-width: 480px) {
    .favorite-page {
        padding: 150px 15px 50px;
    }

    .favorite-header h1 {
        font-size: 24px;
        letter-spacing: 1.5px;
    }

    .text-4xl {
        font-size: 1.5rem;
        line-height: 2rem;
    }

    .product-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-image {
        height: 250px;
        font-size: 48px;
    }

    .favorite-btn {
        width: 45px;
        height: 45px;
        font-size: 22px;
        top: 12px;
        right: 12px;
    }

    .product-info {
        padding: 20px;
    }

    .product-name {
        font-size: 18px;
        margin-bottom: 8px;
    }

    .product-description {
        font-size: 13px;
    }

    .empty-state {
        padding: 40px 15px;
    }

    .empty-icon {
        font-size: 80px;
        margin-bottom: 20px;
    }

    .btn-category {
        padding: 10px 25px;
        font-size: 14px;
    }

    .mb-12 {
        margin-bottom: 1.5rem;
    }

    .mb-8 {
        margin-bottom: 1.5rem;
    }

    .mt-20 {
        margin-top: 2rem;
    }
}

/* Очень маленькие экраны */
@media (max-width: 360px) {
    .favorite-page {
        padding: 150px 10px 40px;
    }

    .favorite-header h1 {
        font-size: 20px;
    }

    .product-image {
        height: 220px;
        font-size: 42px;
    }

    .favorite-btn {
        width: 40px;
        height: 40px;
        font-size: 20px;
        top: 10px;
        right: 10px;
    }

    .product-info {
        padding: 15px;
    }

    .product-name {
        font-size: 16px;
    }

    .product-description {
        font-size: 12px;
    }

    .empty-icon {
        font-size: 70px;
    }
}
</style>
