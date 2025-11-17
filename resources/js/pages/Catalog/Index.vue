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
.catalog-page {
    padding: 120px 80px;
    background: #ffffff;
}

.catalog-header h1 {
    font-size: 48px;
    font-weight: 700;
    letter-spacing: 3px;
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
}

.product-description {
    font-size: 15px;
    color: #cccccc;
}
</style>
