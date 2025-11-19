<template>
    <DefaultLayout>
        <Head title="Каталог - ЧЕРНЫЙ БРИЛЛИАНТ"></Head>

        <div class="black-diamond-site">
            <MainHeader />

            <!-- Catalog Section -->
            <section class="catalog-page">
                <div class="catalog-header text-center mb-12">
                    <h1 class="text-4xl sm:text-5xl font-extrabold uppercase mb-4">Каталог</h1>
                    <p class="text-gray-500 text-lg">
                        Выберите категорию и найдите идеальные товары для себя
                    </p>
                </div>

                <!-- Categories -->
                <div v-if="categories.length" class="categories flex flex-wrap justify-center gap-4 mb-10">
                    <button
                        class="btn-category"
                        :class="{ 'active': selectedCategory === null }"
                        @click="selectCategory(null)"
                    >
                        Все
                    </button>
                    <button
                        v-for="cat in categories"
                        :key="cat.id"
                        class="btn-category"
                        :class="{ 'active': selectedCategory === cat.id }"
                        @click="selectCategory(cat.id)"
                    >
                        {{ cat.name }}
                    </button>
                </div>

                <!-- Products Grid -->
                <div v-if="filteredProducts.length" class="product-grid">
                    <div
                        v-for="product in filteredProducts"
                        :key="product.id"
                        class="product-card"
                    >
                        <div class="product-image">
                            {{ product.icon }}
                            <button
                                class="favorite-btn"
                                :class="{ active: isFavorite(product.id) }"
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

                <!-- Empty State -->
                <div v-else class="empty-state text-center mt-20">
                    <p class="text-gray-500 text-lg">Товары отсутствуют в выбранной категории</p>
                </div>
            </section>
        </div>
    </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainHeader from '@/pages/main/MainHeader.vue';
import DefaultLayout from "@/pages/main/DefaultLayout.vue";

// Типы данных
interface Category {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
    description: string;
    icon: string;
    category_id: number;
}

// Props
const props = defineProps<{
    categories?: Category[];
    products?: Product[];
}>();

// Реактивные переменные
const categories = ref<Category[]>(props.categories ?? []);
const products = ref<Product[]>(props.products ?? []);
const selectedCategory = ref<number | null>(null);

// Избранное
const favorites = ref<Set<number>>(new Set());

const filteredProducts = computed(() => {
    if (selectedCategory.value === null) return products.value;
    return products.value.filter(p => p.category_id === selectedCategory.value);
});

// Методы
const selectCategory = (id: number | null) => {
    selectedCategory.value = id;
};

const toggleFavorite = (productId: number) => {
    if (favorites.value.has(productId)) {
        favorites.value.delete(productId);
    } else {
        favorites.value.add(productId);
    }
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

// Lifecycle
onMounted(() => {
    loadFavoritesFromStorage();

    // Заглушки, если бэка нет
    if (!props.categories) {
        categories.value = [
            { id: 1, name: 'Норковые шубы' },
            { id: 2, name: 'Дубленки' },
            { id: 3, name: 'Пуховики' },
            { id: 4, name: 'Пальто' },
        ];
    }

    if (!props.products) {
        products.value = [
            { id: 1, name: 'Шуба 2025', description: 'Роскошная норковая шуба', icon: '🦊', category_id: 1 },
            { id: 2, name: 'Дубленка VIP', description: 'Стильная дубленка', icon: '👔', category_id: 2 },
            { id: 3, name: 'Пуховик Ultra', description: 'Тёплый пуховик', icon: '❄️', category_id: 3 },
            { id: 4, name: 'Пальто Классика', description: 'Элегантное пальто', icon: '🧥', category_id: 4 },
        ];
    }
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

.catalog-page {
    flex: 1;
    padding: 150px 80px 100px;
    background: #ffffff;
}

.catalog-header h1 {
    font-size: 48px;
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

.mb-10 {
    margin-bottom: 2.5rem;
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

.flex {
    display: flex;
}

.flex-wrap {
    flex-wrap: wrap;
}

.justify-center {
    justify-content: center;
}

.gap-4 {
    gap: 1rem;
}

.categories {
    flex-wrap: wrap;
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
}

.btn-category.active,
.btn-category:hover {
    background: #000;
    color: #fff;
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

.empty-state {
    padding: 80px 20px;
}

/* Медиазапросы для адаптивности */

/* Планшеты и небольшие ноутбуки */
@media (max-width: 1024px) {
    .catalog-page {
        padding: 150px 40px 80px;
    }

    .catalog-header h1 {
        font-size: 40px;
        letter-spacing: 2px;
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
    .catalog-page {
        padding: 150px 25px 60px;
    }

    .catalog-header h1 {
        font-size: 32px;
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

    .categories {
        gap: 0.75rem;
    }

    .gap-4 {
        gap: 0.75rem;
    }

    .btn-category {
        padding: 10px 20px;
        font-size: 14px;
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

    .mb-12 {
        margin-bottom: 2rem;
    }

    .mb-10 {
        margin-bottom: 2rem;
    }

    .mt-20 {
        margin-top: 3rem;
    }

    .empty-state {
        padding: 60px 20px;
    }
}

/* Мобильные устройства */
@media (max-width: 480px) {
    .catalog-page {
        padding: 150px 15px 50px;
    }

    .catalog-header h1 {
        font-size: 26px;
        letter-spacing: 1.5px;
    }

    .text-4xl {
        font-size: 1.5rem;
        line-height: 2rem;
    }

    .text-lg {
        font-size: 0.95rem;
        line-height: 1.4rem;
    }

    .categories {
        gap: 0.5rem;
    }

    .gap-4 {
        gap: 0.5rem;
    }

    .btn-category {
        padding: 8px 16px;
        font-size: 13px;
        border-radius: 8px;
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

    .mb-12 {
        margin-bottom: 1.5rem;
    }

    .mb-10 {
        margin-bottom: 1.5rem;
    }

    .mt-20 {
        margin-top: 2rem;
    }

    .empty-state {
        padding: 40px 15px;
    }
}

/* Очень маленькие экраны */
@media (max-width: 360px) {
    .catalog-page {
        padding: 150px 10px 40px;
    }

    .catalog-header h1 {
        font-size: 22px;
        letter-spacing: 1px;
    }

    .text-lg {
        font-size: 0.9rem;
    }

    .btn-category {
        padding: 7px 14px;
        font-size: 12px;
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
}
</style>
