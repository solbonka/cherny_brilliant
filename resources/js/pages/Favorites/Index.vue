<template>
    <DefaultLayout>
        <Head title="Избранное - ЧЕРНЫЙ БРИЛЛИАНТ"></Head>

        <div class="black-diamond-site">
            <MainHeader></MainHeader>

            <!-- Favorites Section -->
            <section class="favorites-page">
                <div class="favorites-header">
                    <h1>ИЗБРАННОЕ</h1>
                    <p v-if="favoriteProducts.length === 0" class="empty-message">
                        У вас пока нет избранных товаров
                    </p>
                    <p v-else class="count-message">
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

                <div v-else class="empty-state">
                    <div class="empty-icon">🤍</div>
                    <p>Добавьте товары в избранное на главной странице</p>
                    <Link href="/" class="btn btn-primary">Перейти в каталог</Link>
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

/* Favorites Section */
.favorites-page {
    flex: 1;
    padding: 150px 80px 100px;
    background: #ffffff;
}

.favorites-header {
    text-align: center;
    margin-bottom: 60px;
}

.favorites-header h1 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 20px;
    letter-spacing: 3px;
}

.empty-message {
    font-size: 18px;
    color: #666666;
}

.count-message {
    font-size: 18px;
    color: #333333;
    font-weight: 500;
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

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-icon {
    font-size: 120px;
    margin-bottom: 30px;
    opacity: 0.3;
}

.empty-state p {
    font-size: 20px;
    color: #666666;
    margin-bottom: 30px;
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
    display: inline-block;
}

.btn-primary {
    background: #000000;
    color: #ffffff;
    border-color: #000000;
}

.btn-primary:hover {
    background: transparent;
    color: #000000;
}

/* Tablet Styles */
@media (max-width: 1024px) {
    .favorites-page {
        padding: 130px 40px 80px;
    }

    .favorites-header h1 {
        font-size: 40px;
    }
}

/* Mobile Styles */
@media (max-width: 768px) {
    .favorites-page {
        padding: 110px 20px 60px;
    }

    .favorites-header h1 {
        font-size: 32px;
    }

    .product-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .product-image {
        height: 300px;
    }

    .empty-icon {
        font-size: 80px;
    }

    .empty-state p {
        font-size: 18px;
    }

    footer {
        padding: 30px 20px;
    }
}

/* Small Mobile */
@media (max-width: 480px) {
    .favorites-header h1 {
        font-size: 28px;
    }

    .product-info {
        padding: 20px;
    }

    .product-name {
        font-size: 20px;
    }
}
</style>
