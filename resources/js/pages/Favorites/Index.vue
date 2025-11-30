<template>
    <DefaultLayout>
        <Head title="Избранное - ЧЕРНЫЙ БРИЛЛИАНТ"></Head>

        <div class="black-diamond-site">
            <MainHeader></MainHeader>

            <!-- Favorites Section -->
            <section class="favorite-page">
                <div class="favorite-header">
                    <h1>ИЗБРАННОЕ</h1>
                    <p v-if="favoriteProducts.length === 0" class="description">
                        У вас пока нет избранных товаров
                    </p>
                    <p v-else class="description">
                        Товаров в избранном: {{ favoriteProducts.length }}
                    </p>
                </div>

                <div v-if="favoriteProducts.length > 0" class="product-grid">
                    <div v-for="product in favoriteProducts" :key="product.id" class="product-card">
                        <div class="product-image">
                            <img
                                :src="product.images.find(i => i.is_main)?.url || product.images[0]?.url || '/placeholder.jpg'"
                                :alt="product.title"
                            />
                            <button
                                class="favorite-btn active"
                                @click.stop="removeFavorite(product.id)"
                                title="Удалить из избранного"
                            >
                                ❤️
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
                                <button
                                    class="add-to-cart-btn"
                                    @click.stop="addToCart(product.id)"
                                    :class="{ 'in-cart': cart.has(product.id) }"
                                >
                                    {{ cart.has(product.id) ? '✓ В корзине' : '🛒 В корзину' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="empty-state">
                    <div class="empty-icon">🤍</div>
                    <p class="empty-text">Добавьте товары в избранное в каталоге</p>
                    <Link href="/catalog" class="btn-catalog">Перейти в каталог</Link>
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
import { useFavoritesStore } from '@/stores/favorites';

interface Product {
    id: number;
    title: string;
    description: string | null;
    price: number;
    old_price: number | null;
    images: { url: string; is_main: boolean }[];
}

const props = withDefaults(defineProps<{
    products?: Product[];
}>(), {
    products: () => []
});

const favoritesStore = useFavoritesStore();
const cart = ref<Set<number>>(new Set());

// Вычисляемое свойство для фильтрации избранных товаров
const favoriteProducts = computed(() => {
    return (props.products || []).filter(product =>
        favoritesStore.items.includes(product.id)
    );
});

const removeFavorite = (productId: number): void => {
    favoritesStore.toggle(productId);
};

const addToCart = (productId: number): void => {
    cart.value.add(productId);
    cart.value = new Set(cart.value);
};

onMounted(() => {
    favoritesStore.load();
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
    background: #f8f8f8;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
}

.favorite-header {
    text-align: center;
    margin-bottom: 60px;
}

.favorite-header h1 {
    font-size: 42px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 15px;
}

.description {
    font-size: 18px;
    color: #666;
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
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
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

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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
    margin-top: 8px;
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

.add-to-cart-btn {
    padding: 12px 20px;
    background: #000000;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
}

.add-to-cart-btn:hover {
    background: #333333;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.add-to-cart-btn.in-cart {
    background: #4CAF50;
}

.add-to-cart-btn.in-cart:hover {
    background: #45a049;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 100px 20px;
    text-align: center;
}

.empty-icon {
    font-size: 120px;
    margin-bottom: 30px;
    opacity: 0.3;
}

.empty-text {
    font-size: 18px;
    color: #999;
    margin-bottom: 30px;
}

.btn-catalog {
    padding: 14px 32px;
    background: #000000;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-catalog:hover {
    background: #333333;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Responsive */
@media (max-width: 1024px) {
    .favorite-page {
        padding: 150px 40px 80px;
    }

    .favorite-header h1 {
        font-size: 36px;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
    }
}

@media (max-width: 768px) {
    .favorite-page {
        padding: 120px 25px 60px;
    }

    .favorite-header {
        margin-bottom: 40px;
    }

    .favorite-header h1 {
        font-size: 28px;
    }

    .description {
        font-size: 16px;
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
        width: 100%;
        justify-content: center;
    }

    .empty-icon {
        font-size: 100px;
    }

    .empty-state {
        padding: 80px 20px;
    }
}

@media (max-width: 480px) {
    .favorite-page {
        padding: 120px 15px 50px;
    }

    .favorite-header h1 {
        font-size: 24px;
        letter-spacing: 1px;
    }

    .description {
        font-size: 14px;
    }

    .product-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .product-info {
        padding: 15px;
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

    .empty-icon {
        font-size: 80px;
    }

    .empty-state {
        padding: 60px 15px;
    }

    .btn-catalog {
        padding: 12px 28px;
        font-size: 14px;
    }
}
</style>
