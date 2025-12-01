<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DefaultLayout from '@/pages/main/DefaultLayout.vue';
import MainHeader from '@/pages/main/MainHeader.vue';
import { useCartStore } from '@/stores/cart';

const cartStore = useCartStore();

// Модалка оформления
const showCheckoutModal = ref(false);
const phone = ref('');
const isSubmitting = ref(false);
const submitError = ref('');
const isOrderSuccess = ref(false);

// Маска телефона +7 (___) ___-__-__
const formatPhone = () => {
    let val = phone.value.replace(/\D/g, '');

    // Если начинается с 8 → заменяем на 7
    if (val.startsWith('8') && val.length > 1) val = '7' + val.slice(1);
    if (val.startsWith('7')) val = '7' + val.slice(1);

    let formatted = '';
    if (val.length > 0) formatted += '+7';
    if (val.length > 1) formatted += ' (' + val.slice(1, 4);
    if (val.length >= 5) formatted += ') ' + val.slice(4, 7);
    if (val.length >= 8) formatted += '-' + val.slice(7, 9);
    if (val.length >= 10) formatted += '-' + val.slice(9, 11);

    phone.value = formatted;
};

onMounted(() => {
    cartStore.load();
});

const updateQuantity = (id: number, delta: number) => {
    const item = cartStore.items.find(i => i.id === id);
    if (item) {
        cartStore.updateQuantity(id, item.quantity + delta);
    }
};

// Отправка заказа
const submitOrder = () => {
    const cleanPhone = phone.value.replace(/\D/g, '');

    // Российский номер — 11 цифр
    if (cleanPhone.length !== 11) {
        submitError.value = 'Введите корректный номер телефона';
        return;
    }

    isSubmitting.value = true;
    submitError.value = '';

    router.post('/orders', {
        phone: cleanPhone,
        items: cartStore.items.map(item => ({
            id: item.id,
            title: item.title,
            price: item.price,
            old_price: item.old_price || null,
            quantity: item.quantity,
            image: item.image,
        })),
        total_price: cartStore.totalPrice,
        total_items: cartStore.totalItems,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            cartStore.clear();
            showCheckoutModal.value = false;
            phone.value = '';
            isOrderSuccess.value = true;

            // ✅ ДОБАВИТЬ: Скролл вверх после успеха
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        onError: (errors: any) => {
            console.log(errors);
            submitError.value = errors.phone || 'Произошла ошибка, попробуйте позже';
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
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
                    <!-- ✅ УЛУЧШЕНО: Состояние успеха -->
                    <div v-if="isOrderSuccess" class="success-state">
                        <div class="success-icon">✓</div>
                        <h2 class="success-title">Заказ успешно оформлен!</h2>
                        <p class="success-text">
                            Спасибо за ваш заказ!<br>
                            Мы перезвоним вам в течение 15–30 минут<br>
                            для подтверждения и уточнения деталей доставки.
                        </p>
                        <Link href="/catalog" class="btn-primary">Вернуться в каталог</Link>
                    </div>

                    <div v-else class="empty-state">
                        <div class="empty-icon">🛒</div>
                        <p class="empty-text">Ваша корзина пуста</p>
                        <Link href="/catalog" class="btn-primary">Перейти в каталог</Link>
                    </div>
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

                        <button @click="showCheckoutModal = true" class="checkout-btn">
                            Оформить заказ
                        </button>

                        <Link href="/catalog" class="continue-shopping">
                            Продолжить покупки
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модалка оформления -->
        <teleport to="body">
            <transition name="modal">
                <div
                    v-if="showCheckoutModal"
                    class="checkout-modal-overlay"
                    @click.self="showCheckoutModal = false"
                >
                    <div class="checkout-modal" @click.stop>
                        <button @click="showCheckoutModal = false" class="modal-close">×</button>

                        <h2 class="modal-title">Оформление заказа</h2>

                        <div class="modal-summary">
                            <div class="summary-row total">
                                <span>Итого к оплате:</span>
                                <span class="total-price">{{ cartStore.totalPrice.toLocaleString() }} ₽</span>
                            </div>
                        </div>

                        <form @submit.prevent="submitOrder" class="checkout-form">
                            <div class="form-group">
                                <label>Ваш номер телефона</label>
                                <input
                                    v-model="phone"
                                    @input="formatPhone"
                                    type="tel"
                                    placeholder="+7 (___) ___-__-__"
                                    class="phone-input"
                                    :disabled="isSubmitting"
                                    required
                                    autocomplete="tel"
                                />
                                <p class="form-hint">
                                    Мы перезвоним вам в течение 15–30 минут для подтверждения заказа
                                </p>
                            </div>

                            <p v-if="submitError" class="error-text">{{ submitError }}</p>

                            <button
                                type="submit"
                                class="submit-btn"
                                :disabled="isSubmitting"
                            >
                                <span v-if="isSubmitting">Оформляем заказ...</span>
                                <span v-else>Заказать за {{ cartStore.totalPrice.toLocaleString() }} ₽</span>
                            </button>
                        </form>
                    </div>
                </div>
            </transition>
        </teleport>
    </DefaultLayout>
</template>

<style scoped>
/* Основные стили */
.cart-page { padding: 140px 0 120px; background: #f8f8f8; min-height: 100vh; }
.container { max-width: 1400px; margin: 0 auto; padding: 0 40px; }
.page-title { font-size: 42px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 50px; text-align: center; }

/* Empty cart */
.empty-cart { text-align: center; padding: 100px 0; }
.empty-state { text-align: center; }
.empty-icon { font-size: 100px; margin-bottom: 30px; opacity: 0.3; }
.empty-text { font-size: 24px; color: #666; margin-bottom: 40px; }

/* Success state */
.success-state {
    text-align: center;
    padding: 80px 0;
    animation: fadeIn 0.6s ease;
}

.success-icon {
    width: 140px;
    height: 140px;
    margin: 0 auto 40px;
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    color: #fff;
    border-radius: 50%;
    font-size: 70px;
    font-weight: 300;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: successPop 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes successPop {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

.success-title {
    font-size: 42px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 30px;
    animation: slideDown 0.6s ease 0.2s both;
}

.success-text {
    font-size: 20px;
    color: #444;
    line-height: 1.8;
    margin-bottom: 50px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    animation: slideDown 0.6s ease 0.4s both;
}

@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.btn-primary {
    padding: 18px 48px;
    background: #000;
    color: #fff;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    font-size: 18px;
    transition: .3s;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    animation: slideDown 0.6s ease 0.6s both;
}

.btn-primary:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

/* Cart content */
.cart-content { display: grid; grid-template-columns: 1fr 400px; gap: 60px; }
.cart-items { display: flex; flex-direction: column; gap: 20px; }
.cart-item {
    background: #fff;
    border-radius: 20px;
    padding: 24px;
    display: grid;
    grid-template-columns: 120px 1fr auto;
    gap: 24px;
    align-items: center;
    box-shadow: 0 4px 20px rgba(0,0,0,.08);
    transition: all 0.3s ease;
}

.cart-item:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,.12);
    transform: translateY(-2px);
}

.item-image img { width: 100%; height: 150px; object-fit: cover; border-radius: 12px; }
.item-title { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
.item-price { font-size: 24px; font-weight: 700; }
.old-price { font-size: 16px; color: #999; text-decoration: line-through; margin-left: 12px; }
.item-controls { display: flex; flex-direction: column; align-items: flex-end; gap: 16px; }
.quantity { display: flex; align-items: center; gap: 16px; font-size: 20px; font-weight: 600; }
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
    transition: all 0.2s;
}

.quantity button:hover:not(:disabled) {
    background: #000;
    color: #fff;
}

.quantity button:disabled { opacity: 0.3; cursor: not-allowed; }
.remove-btn {
    color: #999;
    background: none;
    border: none;
    font-size: 14px;
    cursor: pointer;
    text-decoration: underline;
    transition: color 0.2s;
}

.remove-btn:hover {
    color: #d00;
}

.cart-summary { background: #fff; border-radius: 20px; padding: 32px; height: fit-content; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
.summary-row { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 18px; }
.summary-row.total { font-size: 28px; font-weight: 700; padding-top: 20px; border-top: 2px solid #eee; margin-bottom: 40px; }
.total-price { color: #000; }
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
    text-transform: uppercase;
    letter-spacing: 1px;
}

.checkout-btn:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

.continue-shopping {
    display: block;
    width: 100%;
    padding: 20px;
    margin-top: 24px;
    border: 2px solid #000;
    border-radius: 16px;
    background: transparent;
    color: #000;
    text-align: center;
    font-size: 18px;
    font-weight: 600;
    text-decoration: none;
    transition: all .35s ease;
    position: relative;
}

.continue-shopping::before {
    content: '←';
    margin-right: 12px;
    font-size: 20px;
    transition: .35s;
}

.continue-shopping:hover {
    background: #000;
    color: #fff;
    padding-left: 36px;
}

.continue-shopping:hover::before {
    margin-right: 16px;
    color: #fff;
}

/* Modal */
.checkout-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.checkout-modal {
    background: #fff;
    border-radius: 24px;
    padding: 40px;
    width: 520px;
    max-width: 92vw;
    position: relative;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 24px;
    width: 44px;
    height: 44px;
    border: none;
    background: #f0f0f0;
    border-radius: 50%;
    font-size: 28px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: .2s;
}

.modal-close:hover { background: #ddd; }

.modal-title {
    font-size: 32px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 32px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

.modal-summary {
    background: #f8f8f8;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
}

.phone-input {
    width: 100%;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 16px;
    font-size: 20px;
    margin-top: 12px;
    background: #fff;
    transition: border 0.2s;
}

.phone-input:focus {
    outline: none;
    border-color: #000;
}

.form-hint {
    font-size: 14px;
    color: #666;
    margin-top: 10px;
    text-align: center;
}

.submit-btn {
    width: 100%;
    padding: 24px;
    background: #000;
    color: #fff;
    border: none;
    border-radius: 16px;
    font-size: 20px;
    font-weight: 600;
    margin-top: 32px;
    cursor: pointer;
    transition: .3s;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.submit-btn:hover:not(:disabled) {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.error-text {
    color: #d00;
    text-align: center;
    margin-top: 16px;
    font-weight: 500;
}

/* Transitions */
.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
    opacity: 0;
}

.modal-enter-active .checkout-modal {
    animation: slideUp 0.4s ease;
}

.modal-leave-active .checkout-modal {
    animation: slideDown 0.3s ease;
}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { transform: translateY(60px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

@media (max-width: 1024px) {
    .cart-content { grid-template-columns: 1fr; }
    .cart-summary { order: -1; }
}

@media (max-width: 640px) {
    .container { padding: 0 20px; }
    .page-title { font-size: 32px; }
    .cart-item { grid-template-columns: 100px 1fr; }
    .item-controls { grid-column: 1 / -1; align-items: center; justify-content: space-between; flex-direction: row; }
    .checkout-modal { padding: 32px 24px; }
    .modal-title { font-size: 24px; }
    .success-title { font-size: 32px; }
    .success-text { font-size: 18px; }
}
</style>
