<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import MainHeader from '@/pages/main/MainHeader.vue';
import DefaultLayout from "@/pages/main/DefaultLayout.vue";
import { debounce } from 'lodash';
import { useFavoritesStore } from '@/stores/favorites';
import {useCartStore} from "@/stores/cart";

interface BackendCategory {
    id: number;
    name: string;
    children_recursive?: BackendCategory[];
}

interface Category {
    id: number;
    name: string;
    children?: Category[];
}

interface Product {
    id: number;
    title: string;
    description: string | null;
    price: number;
    old_price: number | null;
    images: { url: string; is_main: boolean }[];
}

const props = withDefaults(defineProps<{
    products?: {
        data: Product[];
        current_page: number;
        last_page: number;
    };
    categories?: BackendCategory[];
    filters?: {
        search?: string;
        category_id?: string;
    };
}>(), {
    products: () => ({
        data: [],
        current_page: 1,
        last_page: 1
    }),
    categories: () => [],
    filters: () => ({})
});

// Нормализация children_recursive → children
const normalizeCategory = (cat: BackendCategory): Category => ({
    id: cat.id,
    name: cat.name,
    children: cat.children_recursive?.length
        ? cat.children_recursive.map(normalizeCategory)
        : undefined
});

const categories = ref<Category[]>((props.categories || []).map(normalizeCategory));

const selectedCategory = ref<number | null>(
    props.filters?.category_id ? Number(props.filters.category_id) : null
);

const searchQuery = ref(props.filters?.search ?? '');

const expandedCategories = ref<Set<number>>(new Set());

// Store для избранного и корзины
const favoritesStore = useFavoritesStore();
const addedToCart = ref(false);

// Вспомогательная функция для проверки избранного
const isFavorite = (productId: number) => {
    return favoritesStore.items.includes(productId);
};

onMounted(() => {
    favoritesStore.load();
    cartStore.load();
});

// Функция для обновления URL и данных
const updateCatalog = () => {
    router.get(
        '/catalog',
        {
            search: searchQuery.value || undefined,
            category_id: selectedCategory.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

// Debounced версия для поиска
const debouncedUpdate = debounce(updateCatalog, 300);

// Watch только для поиска
watch(searchQuery, () => {
    debouncedUpdate();
});

// === Пагинация ===
const loadPage = (page: number) => {
    router.get('/catalog', {
        ...(props.filters || {}),
        page,
    }, { preserveScroll: true });
};

const getCurrentCategoryName = (): string => {
    if (selectedCategory.value === null) return 'Все товары';

    const find = (id: number, cats: Category[]): string | null => {
        for (const cat of cats) {
            if (cat.id === id) return cat.name;
            if (cat.children) {
                const found = find(id, cat.children);
                if (found) return found;
            }
        }
        return null;
    };

    return find(selectedCategory.value, categories.value) || 'Каталог';
};

const selectCategory = (id: number | null) => {
    selectedCategory.value = id;
    updateCatalog();
};

const toggleCategory = (id: number) => {
    if (expandedCategories.value.has(id)) {
        expandedCategories.value.delete(id);
    } else {
        expandedCategories.value.add(id);
    }
};

const handleCategoryClick = (id: number) => {
    const cat = categories.value.find(c => c.id === id);

    // Всегда выбираем категорию
    selectCategory(id);

    // Если есть подкатегории, также переключаем раскрытие
    if (cat?.children && cat.children.length > 0) {
        toggleCategory(id);
    }
};

const toggleFavorite = (productId: number, event?: Event) => {
    if (event) {
        event.stopPropagation();
    }

    favoritesStore.toggle(productId);
};

const cartStore = useCartStore();

const quantities = ref<Map<number, number>>(new Map());

const getQuantity = (productId: number) => {
    return quantities.value.get(productId) || 1;
};

const increaseQuantity = (productId: number) => {
    const current = getQuantity(productId);
    quantities.value.set(productId, current + 1);
};

const decreaseQuantity = (productId: number) => {
    const current = getQuantity(productId);
    if (current > 1) {
        quantities.value.set(productId, current - 1);
    }
};

const setQuantity = (productId: number, event: Event) => {
    const target = event.target as HTMLInputElement;
    const value = parseInt(target.value) || 1;
    if (value >= 1) {
        quantities.value.set(productId, value);
    }
};

const addToCart = (product: Product, event: Event) => {
    event.stopPropagation();
    const quantity = getQuantity(product.id);

    // Добавляем товар нужное количество раз
    for (let i = 0; i < quantity; i++) {
        cartStore.add(product);
    }

    // Сбрасываем количество после добавления
    quantities.value.set(product.id, 1);

    addedToCart.value = true;
    setTimeout(() => addedToCart.value = false, 2000);
};
</script>

<template>
    <DefaultLayout>
        <Head title="Каталог - ЧЕРНЫЙ БРИЛЛИАНТ" />

        <div class="black-diamond-site">
            <MainHeader />

            <section class="catalog-page">
                <div class="catalog-wrapper">
                    <!-- Sidebar -->
                    <aside class="catalog-sidebar">
                        <div class="sidebar-header">
                            <h2>Категории</h2>
                        </div>

                        <div class="category-list">
                            <!-- Все товары -->
                            <div
                                class="category-item"
                                :class="{ active: selectedCategory === null }"
                                @click="selectCategory(null)"
                            >
                                <div class="category-main">
                                    <span class="category-icon">📦</span>
                                    <span class="category-name">Все товары</span>
                                </div>
                            </div>

                            <!-- Категории -->
                            <div v-for="cat in categories" :key="cat.id" class="category-group">
                                <div
                                    class="category-item"
                                    :class="{
                                        active: selectedCategory === cat.id,
                                        'has-children': cat.children && cat.children.length > 0,
                                        expanded: expandedCategories.has(cat.id)
                                    }"
                                    @click="handleCategoryClick(cat.id)"
                                >
                                    <div class="category-main">
                                        <span class="category-icon">📁</span>
                                        <span class="category-name">{{ cat.name }}</span>
                                    </div>
                                    <span v-if="cat.children && cat.children.length > 0" class="expand-icon">
                                        {{ expandedCategories.has(cat.id) ? '▼' : '▶' }}
                                    </span>
                                </div>

                                <transition name="slide">
                                    <div
                                        v-if="cat.children && cat.children.length > 0 && expandedCategories.has(cat.id)"
                                        class="subcategory-list"
                                    >
                                        <div
                                            v-for="sub in cat.children"
                                            :key="sub.id"
                                            class="subcategory-item"
                                            :class="{ active: selectedCategory === sub.id }"
                                            @click.stop="selectCategory(sub.id)"
                                        >
                                            <span class="subcategory-dot">•</span>
                                            <span class="subcategory-name">{{ sub.name }}</span>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>
                    </aside>

                    <!-- Main Content -->
                    <main class="catalog-content">
                        <div class="catalog-header">
                            <h1>{{ getCurrentCategoryName() }}</h1>
                            <p class="category-description">
                                {{ props.products?.data.length || 0 }} товаров
                            </p>

                            <!-- Поиск -->
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Поиск по названию или описанию..."
                                class="search-input"
                            />
                        </div>

                        <!-- Товары -->
                        <div v-if="props.products && props.products.data.length" class="product-grid">
                            <div v-for="product in props.products.data" :key="product.id" class="product-card">
                                <div class="product-card">
                                    <Link
                                        :href="`/catalog/${product.id}`"
                                        class="product-card-link"
                                    >
                                        <div class="product-image">
                                            <img
                                                :src="product.images.find(i => i.is_main)?.url || product.images[0]?.url || '/placeholder.jpg'"
                                                :alt="product.title"
                                            />
                                            <button
                                                class="favorite-btn"
                                                :class="{ active: isFavorite(product.id) }"
                                                @click="toggleFavorite(product.id, $event)"
                                                :title="isFavorite(product.id) ? 'Удалить из избранного' : 'Добавить в избранное'"
                                            >
                                                {{ isFavorite(product.id) ? '❤️' : '🤍' }}
                                            </button>
                                        </div>
                                        <div class="product-info">
                                            <h3 class="product-name">{{ product.title }}</h3>
                                            <p v-if="product.description" class="product-description">
                                                {{ product.description }}
                                            </p>
                                            <div class="product-footer">
                                                <div class="product-price">
                                                    <span class="current-price">{{ product.price.toLocaleString() }} ₽</span>
                                                    <span v-if="product.old_price" class="old-price">
                                                        {{ product.old_price.toLocaleString() }} ₽
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </Link>
                                    <div class="cart-actions">
                                        <!-- Селектор количества (показываем только если товара нет в корзине) -->
                                        <div v-if="!cartStore.has(product.id)" class="quantity-selector" @click.stop>
                                            <button
                                                class="quantity-btn"
                                                @click="decreaseQuantity(product.id)"
                                                :disabled="getQuantity(product.id) <= 1"
                                            >
                                                −
                                            </button>
                                            <input
                                                type="number"
                                                class="quantity-input"
                                                :value="getQuantity(product.id)"
                                                @input="setQuantity(product.id, $event)"
                                                min="1"
                                                @click.stop
                                            />
                                            <button
                                                class="quantity-btn"
                                                @click="increaseQuantity(product.id)"
                                            >
                                                +
                                            </button>
                                        </div>

                                        <!-- Кнопка добавления в корзину -->
                                        <button
                                            class="add-to-cart-btn"
                                            @click="addToCart(product, $event)"
                                            :class="{ 'in-cart': cartStore.has(product.id) }"
                                        >
                                            {{ cartStore.has(product.id) ? '✓ В корзине' : '🛒 В корзину' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="empty-state">
                            <div class="empty-icon">🔍</div>
                            <p>Товары не найдены</p>
                        </div>

                        <!-- Пагинация -->
                        <div class="pagination" v-if="props.products && props.products.last_page > 1">
                            <button
                                v-for="page in props.products.last_page"
                                :key="page"
                                @click="loadPage(page)"
                                :class="{ active: page === props.products.current_page }"
                                class="page-btn"
                            >
                                {{ page }}
                            </button>
                        </div>
                    </main>
                </div>
            </section>
        </div>
    </DefaultLayout>
</template>

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
    background: #f8f8f8;
}

.catalog-wrapper {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 40px;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 40px;
}

/* Sidebar */
.catalog-sidebar {
    background: #ffffff;
    border-radius: 12px;
    padding: 30px;
    height: fit-content;
    position: sticky;
    top: 140px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.sidebar-header {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.sidebar-header h2 {
    font-size: 24px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.category-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.category-group {
    display: flex;
    flex-direction: column;
}

.category-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    background: #ffffff;
}

.category-item:hover {
    background: #f5f5f5;
}

.category-item.active {
    background: #000000;
    color: #ffffff;
}

.category-main {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.category-icon {
    font-size: 22px;
    width: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-name {
    font-size: 15px;
    font-weight: 600;
}

.expand-icon {
    font-size: 12px;
    color: #999;
    transition: transform 0.3s;
}

.category-item.expanded .expand-icon {
    transform: rotate(0deg);
}

/* Subcategories */
.subcategory-list {
    margin-left: 42px;
    margin-top: 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.subcategory-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
}

.subcategory-item:hover {
    background: #f5f5f5;
}

.subcategory-item.active {
    background: #000000;
    color: #ffffff;
}

.subcategory-dot {
    font-size: 20px;
    color: #999;
}

.subcategory-item.active .subcategory-dot {
    color: #ffffff;
}

/* Main Content */
.catalog-content {
    min-height: 600px;
}

.catalog-header {
    margin-bottom: 40px;
}

.catalog-header h1 {
    font-size: 42px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 10px;
}

.category-description {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
}

.search-input {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s;
    outline: none;
}

.search-input:focus {
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
}

.product-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.4s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    position: relative; /* ✅ Для абсолютного позиционирования кнопки */
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    border-color: #e0e0e0;
}

.product-image {
    width: 100%;
    aspect-ratio: 4 / 5;
    background: #fafafa;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.product-card-link {
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    flex: 1;
    cursor: pointer;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center; /* Центрирует изображение */
    transition: transform 0.4s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.favorite-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.favorite-btn:hover {
    transform: scale(1.1);
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.favorite-btn.active {
    animation: heartbeat 0.4s ease;
}

@keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.2); }
    50% { transform: scale(1.1); }
    75% { transform: scale(1.15); }
}

.product-info {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex: 1;
}

.product-name {
    font-size: 18px;
    font-weight: 600;
    color: #000000;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 50px;
}

.product-description {
    font-size: 14px;
    color: #666666;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: auto;
    padding-top: 16px;
    border-top: 1px solid #f0f0f0;
}

.product-price {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.current-price {
    font-size: 22px;
    font-weight: 700;
    color: #000000;
}

.old-price {
    font-size: 14px;
    color: #999999;
    text-decoration: line-through;
}

.add-to-cart-btn.in-cart:hover {
    background: #45a049;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 100px 20px;
    text-align: center;
}

.empty-icon {
    font-size: 80px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-state p {
    font-size: 18px;
    color: #999;
}

/* Pagination */
.pagination {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 50px;
    flex-wrap: wrap;
}

.page-btn {
    padding: 12px 20px;
    background: #ffffff;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    min-width: 50px;
}

.page-btn:hover {
    background: #f5f5f5;
    border-color: #000000;
}

.page-btn.active {
    background: #000000;
    color: #ffffff;
    border-color: #000000;
}

/* Responsive */
@media (max-width: 1024px) {
    .catalog-wrapper {
        grid-template-columns: 280px 1fr;
        gap: 30px;
        padding: 0 30px;
    }

    .catalog-sidebar {
        padding: 25px;
    }

    .catalog-header h1 {
        font-size: 36px;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
    }
}

@media (max-width: 768px) {
    .catalog-page {
        padding: 100px 0 40px;
    }

    .catalog-wrapper {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 0 20px;
    }

    .catalog-sidebar {
        position: static;
        padding: 20px;
    }

    .sidebar-header h2 {
        font-size: 20px;
    }

    .category-item {
        padding: 12px 15px;
    }

    .category-icon {
        font-size: 20px;
    }

    .category-name {
        font-size: 14px;
    }

    .catalog-header h1 {
        font-size: 28px;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }

    .product-footer {
        flex-direction: column;
        align-items: stretch;
    }

    .add-to-cart-btn {
        width: calc(100% - 30px);
        margin: 0 15px 15px 15px;
    }
}

@media (max-width: 480px) {
    .catalog-wrapper {
        padding: 0 15px;
    }

    .catalog-sidebar {
        padding: 15px;
    }

    .catalog-header h1 {
        font-size: 24px;
        letter-spacing: 1px;
    }

    .category-description {
        font-size: 14px;
    }

    .product-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .product-info {
        padding: 15px;
    }

    .add-to-cart-btn {
        width: calc(100% - 30px);
        margin: 0 15px 15px 15px;
    }

    .product-name {
        font-size: 16px;
        min-height: 44px;
    }

    .product-description {
        font-size: 13px;
    }

    .current-price {
        font-size: 20px;
    }

    .subcategory-list {
        margin-left: 30px;
    }
}

.cart-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 0 20px 20px 20px;
}

.quantity-selector {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2px;
    background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
    border: none;
    border-radius: 14px;
    padding: 4px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08),
    inset 0 -2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.quantity-selector:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12),
    inset 0 -2px 4px rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}

.quantity-btn {
    width: 38px;
    height: 38px;
    border: none;
    background: linear-gradient(135deg, #ffffff 0%, #f8f8f8 100%);
    color: #000000;
    font-size: 18px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.quantity-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #000000 0%, #2a2a2a 100%);
    color: #ffffff;
    transform: scale(1.08);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.quantity-btn:active:not(:disabled) {
    transform: scale(0.96);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

.quantity-btn:disabled {
    opacity: 0.25;
    cursor: not-allowed;
    background: linear-gradient(135deg, #e0e0e0 0%, #d0d0d0 100%);
}

.quantity-input {
    width: 54px;
    height: 38px;
    border: none;
    background: transparent;
    text-align: center;
    font-size: 16px;
    font-weight: 800;
    color: #000000;
    outline: none;
    padding: 0;
    letter-spacing: 1px;
}

.quantity-input::-webkit-inner-spin-button,
.quantity-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.quantity-input[type="number"] {
    -moz-appearance: textfield;
}

/* Кнопка добавления в корзину */
.add-to-cart-btn {
    width: 100%;
    padding: 13px 20px;
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    color: #ffffff;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
}

.add-to-cart-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.add-to-cart-btn:hover::before {
    width: 300px;
    height: 300px;
}

.add-to-cart-btn:hover {
    background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.add-to-cart-btn:active {
    transform: translateY(0);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.add-to-cart-btn.in-cart {
    background: linear-gradient(135deg, #4CAF50 0%, #2e7d32 100%);
    box-shadow: 0 4px 16px rgba(76, 175, 80, 0.4);
    animation: successGlow 1.5s ease infinite;
}

@keyframes successGlow {
    0%, 100% {
        box-shadow: 0 4px 16px rgba(76, 175, 80, 0.4);
    }
    50% {
        box-shadow: 0 6px 24px rgba(76, 175, 80, 0.6);
    }
}
</style>
