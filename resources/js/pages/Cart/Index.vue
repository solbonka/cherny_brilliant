<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/pages/main/DefaultLayout.vue';
import MainHeader from '@/pages/main/MainHeader.vue';
import { useCartStore } from '@/stores/cart';

const cartStore = useCartStore();

onMounted(() => {
    cartStore.load();
});

const updateQuantity = (id: number, delta: number) => {
    const item = cartStore.items.find(i => i.id === id);
    if (item) {
        cartStore.updateQuantity(id, item.quantity + delta);
    }
};
</script>

<template>
    <DefaultLayout>
        <Head title="Корзина — ЧЕРНЫЙ БРИЛЛИАНТ" />

        <div class="cart-page">
            <MainHeader />

            <div class="container">
                <h1 class="page-title">Корзина</h1>

                <div v-if="cartStore.items.length === 0" class="empty-cart">
                    <div class="empty-icon">🛒</div>
                    <p>Ваша корзина пуста</p>
                    <Link href="/catalog" class="btn-primary">Перейти в каталог</Link>
                </div>

                <div v-else class="cart-content">
                    <div class="cart-items">
                        <div v-for="item in cartStore.items" :key="item.id" class="cart-item">
                            <div class="item-image">
                                <img :src="item.image" :alt="item.title" />
                            </div>

                            <div class="item-info">
                                <h3 class="item-title">{{ item.title }}</h3>
                                <div class="item-price">
                                    {{ (item.price * item.quantity).toLocaleString() }} ₽
                                    <span v-if="item.old_price" class="old-price">
                                        {{ (item.old_price * item.quantity).toLocaleString() }} ₽
                                    </span>
                                </div>
                            </div>

                            <div class="item-controls">
                                <div class="quantity">
                                    <button @click="updateQuantity(item.id, -1)" :disabled="item.quantity <= 1">−</button>
                                    <span>{{ item.quantity }}</span>
                                    <button @click="updateQuantity(item.id, 1)">+</button>
                                </div>
                                <button @click="cartStore.remove(item.id)" class="remove-btn">Удалить</button>
                            </div>
                        </div>
                    </div>

                    <div class="cart-summary">
                        <div class="summary-row">
                            <span>Товаров:</span>
                            <span>{{ cartStore.totalItems }}</span>
                        </div>
                        <div class="summary-row total">
                            <span>Итого:</span>
                            <span class="total-price">{{ cartStore.totalPrice.toLocaleString() }} ₽</span>
                        </div>

                        <button class="checkout-btn">
                            Оформить заказ
                        </button>

                        <Link href="/catalog" class="continue-shopping">
                            ← Продолжить покупки
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<style scoped>
.cart-page {
    padding: 140px 0 120px;
    background: #f8f8f8;
    min-height: 100vh;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 40px;
}

.page-title {
    font-size: 42px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 50px;
    text-align: center;
}

.empty-cart {
    text-align: center;
    padding: 100px 0;
}

.empty-icon {
    font-size: 100px;
    margin-bottom: 30px;
    opacity: 0.3;
}

.empty-cart p {
    font-size: 24px;
    color: #666;
    margin-bottom: 40px;
}

.btn-primary {
    padding: 16px 40px;
    background: #000;
    color: #fff;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 18px;
    transition: .3s;
}

.btn-primary:hover {
    background: #333;
}

.cart-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 60px;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cart-item {
    background: #fff;
    border-radius: 20px;
    padding: 24px;
    display: grid;
    grid-template-columns: 120px 1fr auto;
    gap: 24px;
    align-items: center;
    box-shadow: 0 4px 20px rgba(0,0,0,.08);
}

.item-image img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 12px;
}

.item-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
}

.item-price {
    font-size: 24px;
    font-weight: 700;
}

.old-price {
    font-size: 16px;
    color: #999;
    text-decoration: line-through;
    margin-left: 12px;
}

.item-controls {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 16px;
}

.quantity {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 20px;
    font-weight: 600;
}

.quantity button {
    width: 40px;
    height: 40px;
    border: 2px solid #000;
    background: #fff;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity button:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.remove-btn {
    color: #999;
    background: none;
    border: none;
    font-size: 14px;
    cursor: pointer;
    text-decoration: underline;
}

.cart-summary {
    background: #fff;
    border-radius: 20px;
    padding: 32px;
    height: fit-content;
    box-shadow: 0 4px 20px rgba(0,0,0,.08);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    font-size: 18px;
}

.summary-row.total {
    font-size: 28px;
    font-weight: 700;
    padding-top: 20px;
    border-top: 2px solid #eee;
    margin-bottom: 40px;
}

.total-price {
    color: #000;
}

.checkout-btn {
    width: 100%;
    padding: 24px;
    background: #000;
    color: #fff;
    border: none;
    border-radius: 16px;
    font-size: 20px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 24px;
    transition: .3s;
}

.checkout-btn:hover {
    background: #333;
}

.continue-shopping {
    display: block;
    text-align: center;
    color: #000;
    text-decoration: none;
    font-weight: 500;
}

@media (max-width: 1024px) {
    .cart-content {
        grid-template-columns: 1fr;
    }

    .cart-summary {
        order: -1;
    }
}

@media (max-width: 640px) {
    .cart-item {
        grid-template-columns: 100px 1fr;
    }

    .item-controls {
        grid-column: 1 / -1;
        align-items: center;
        justify-content: space-between;
    }
}
</style>
