<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/pages/main/DefaultLayout.vue';
import MainHeader from '@/pages/main/MainHeader.vue';
import { useFavoritesStore } from '@/stores/favorites';
import { useCartStore } from '@/stores/cart';

const props = defineProps<{
    product: {
        id: number;
        title: string;
        description: string | null;
        price: number;
        old_price?: number | null;
        size?: string | null;
        color?: string | null;
        material?: string | null;
        images: {
            id: number;
            url: string;
            is_main: boolean;
            sort_order: number;
        }[];
    };
}>();

const favoritesStore = useFavoritesStore();
const cartStore = useCartStore();
const currentImage = ref<string>('');
const addedToCart = ref(false);
const quantity = ref(1);

const increaseQuantity = () => {
    quantity.value++;
};

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

// ✅ ИСПРАВИТЬ: Функцию setQuantity с правильной типизацией
const handleQuantityInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const value = parseInt(target.value) || 1;
    if (value >= 1) {
        quantity.value = value;
    } else {
        quantity.value = 1;
    }
};

onMounted(() => {
    favoritesStore.load();
    cartStore.load();

    const mainImg = props.product.images.find(img => img.is_main) || props.product.images[0];
    currentImage.value = mainImg?.url || '/placeholder.jpg';
});

const changeImage = (url: string) => {
    currentImage.value = url;
};

const toggleFavorite = () => favoritesStore.toggle(props.product.id);
const isFavorite = () => favoritesStore.items.includes(props.product.id);

const addToCart = () => {
    for (let i = 0; i < quantity.value; i++) {
        cartStore.add(props.product);
    }

    addedToCart.value = true;
    setTimeout(() => addedToCart.value = false, 2000);

    quantity.value = 1;
};
</script>

<template>
    <DefaultLayout>
        <Head :title="product.title + ' — ЧЕРНЫЙ БРИЛЛИАНТ'" />

        <div class="product-show-page">
            <MainHeader />

            <div class="container">
                <nav class="breadcrumbs">
                    <Link href="/catalog" class="link">Каталог</Link>
                    <span class="sep">/</span>
                    <span class="current">{{ product.title }}</span>
                </nav>

                <div class="product-grid">
                    <div class="gallery">
                        <div class="main-photo">
                            <img :src="currentImage" :alt="product.title" />
                            <button @click.prevent="toggleFavorite" class="fav-big" :class="{ active: isFavorite() }">
                                {{ isFavorite() ? '❤️' : '🤍' }}
                            </button>
                        </div>

                        <div v-if="product.images.length > 1" class="thumbs">
                            <img
                                v-for="img in product.images"
                                :key="img.id"
                                :src="img.url"
                                :class="{ active: currentImage === img.url }"
                                @click="changeImage(img.url)"
                                loading="lazy"
                            />
                        </div>
                    </div>

                    <div class="info">
                        <h1 class="title">{{ product.title }}</h1>

                        <div class="price-block">
                            <div class="price">{{ product.price.toLocaleString() }} ₽</div>
                            <div v-if="product.old_price" class="old-price">
                                {{ product.old_price.toLocaleString() }} ₽
                            </div>
                        </div>

                        <div class="params">
                            <div v-if="product.size"><strong>Размер:</strong> {{ product.size }}</div>
                            <div v-if="product.color"><strong>Цвет:</strong> {{ product.color }}</div>
                            <div v-if="product.material"><strong>Материал:</strong> {{ product.material }}</div>
                        </div>

                        <!-- ✅ ИСПРАВИТЬ: Input с правильным обработчиком -->
                        <div class="quantity-block">
                            <label class="quantity-label">Количество:</label>
                            <div class="quantity-selector">
                                <button
                                    class="quantity-btn"
                                    @click="decreaseQuantity"
                                    :disabled="quantity <= 1"
                                >
                                    −
                                </button>
                                <input
                                    type="number"
                                    class="quantity-input"
                                    :value="quantity"
                                    @input="handleQuantityInput"
                                    min="1"
                                />
                                <button
                                    class="quantity-btn"
                                    @click="increaseQuantity"
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <button @click="addToCart" class="add-btn" :class="{ added: addedToCart }">
                            {{ addedToCart ? '✓ Добавлено в корзину!' : '🛒 Добавить в корзину' }}
                        </button>

                        <div v-if="product.description" class="description">
                            <h3>Описание</h3>
                            <p>{{ product.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<style scoped>
/* Тот же стиль, что я давал раньше — он идеально подходит под твой каталог */
.product-show-page { padding: 140px 0 120px; background: #f8f8f8; min-height: 100vh; }
.container { max-width: 1400px; margin: 0 auto; padding: 0 40px; }

.breadcrumbs { margin-bottom: 32px; font-size: 15px; color: #666; }
.link { color: #000; text-decoration: none; transition: .3s; }
.link:hover { text-decoration: underline; }
.sep { margin: 0 12px; color: #bbb; }
.current { font-weight: 600; color: #000; }

.product-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: start; }

.gallery { display: flex; flex-direction: column; gap: 20px; }
.product-image {
    width: 100%;
    aspect-ratio: 4 / 5; /* Фиксированное соотношение (высота больше ширины для одежды) */
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
    object-fit: cover; /* Обрезает, сохраняя пропорции */
    object-position: center; /* Центрирует изображение */
    transition: transform 0.4s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.main-photo {
       position: relative;
       aspect-ratio: 4 / 5; /* Квадратные изображения для главной (или 4/5, если хочешь как в каталоге) */
       overflow: hidden;
       box-shadow: 0 20px 60px rgba(0,0,0,.22);
       background: #fff;
       display: flex;
       align-items: center;
       justify-content: center;
   }

.main-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Заполняет квадрат, обрезая края */
    object-position: center;
    transition: transform .6s ease;
}

.main-photo:hover img {
    transform: scale(1.04);
}

.thumbs {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.thumbs img {
    width: 120px;
    height: 120px; /* Фиксированный квадрат */
    aspect-ratio: 1 / 1; /* На всякий случай */
    object-fit: cover; /* Обрезает для квадрата */
    object-position: center;
    border-radius: 16px;
    cursor: pointer;
    border: 4px solid transparent;
    transition: .4s;
    box-shadow: 0 4px 15px rgba(0,0,0,.1);
}

.fav-big { position: absolute; top: 28px; right: 28px; width: 70px; height: 70px; border-radius: 50%; background: rgba(255,255,255,.96); border: none; font-size: 32px; cursor: pointer; box-shadow: 0 6px 25px rgba(0,0,0,.25); transition: .4s; z-index: 10; }
.fav-big:hover { transform: scale(1.18); }
.fav-big.active { animation: heartbeat .7s ease; }

@keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.3); }
}
.thumbs img:hover, .thumbs img.active { border-color: #000; transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,0,0,.25); }

.info .title { font-size: 42px; font-weight: 700; line-height: 1.2; margin-bottom: 24px; }
.price-block { margin-bottom: 36px; }
.price { font-size: 48px; font-weight: 800; color: #000; }
.old-price { font-size: 28px; color: #999; text-decoration: line-through; margin-top: 8px; }

.params { margin: 32px 0 40px; line-height: 2; font-size: 18px; color: #333; }
.params div { margin-bottom: 10px; }

.quantity-block {
    margin: 40px 0;
    padding: 32px;
    background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
    border-radius: 20px;
    border: 2px solid #e0e0e0;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.quantity-block:hover {
    border-color: #000000;
    background: #ffffff;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.quantity-label {
    font-size: 20px;
    font-weight: 800;
    color: #000000;
    margin-bottom: 20px;
    display: block;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.quantity-selector {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: linear-gradient(135deg, #f0f0f0 0%, #ffffff 100%);
    border: none;
    border-radius: 18px;
    padding: 6px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12),
    inset 0 -3px 8px rgba(0, 0, 0, 0.06);
}

.quantity-btn {
    width: 60px;
    height: 60px;
    border: none;
    background: linear-gradient(135deg, #ffffff 0%, #f5f5f5 100%);
    color: #000000;
    font-size: 30px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    line-height: 1;
}

.quantity-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #000000 0%, #2a2a2a 100%);
    color: #ffffff;
    transform: scale(1.08);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
}

.quantity-btn:active:not(:disabled) {
    transform: scale(0.95);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.quantity-btn:disabled {
    opacity: 0.25;
    cursor: not-allowed;
    background: linear-gradient(135deg, #d0d0d0 0%, #c0c0c0 100%);
}

.quantity-input {
    width: 100px;
    height: 60px;
    border: none;
    background: transparent;
    text-align: center;
    font-size: 28px;
    font-weight: 900;
    color: #000000;
    outline: none;
    padding: 0 16px;
    letter-spacing: 2px;
}

.quantity-input::-webkit-inner-spin-button,
.quantity-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.quantity-input[type="number"] {
    -moz-appearance: textfield;
}

.add-btn {
    width: 100%;
    padding: 28px 40px;
    font-size: 22px;
    font-weight: 800;
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    color: #ffffff;
    border: none;
    border-radius: 18px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: uppercase;
    letter-spacing: 3px;
    box-shadow: 0 10px 32px rgba(0, 0, 0, 0.25);
    position: relative;
    overflow: hidden;
}

.add-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    transform: translate(-50%, -50%);
    transition: width 0.7s ease, height 0.7s ease;
}

.add-btn:hover::after {
    width: 400px;
    height: 400px;
}

.add-btn:hover {
    background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
    transform: translateY(-4px);
    box-shadow: 0 18px 48px rgba(0, 0, 0, 0.35);
}

.add-btn:active {
    transform: translateY(-2px);
}

.add-btn.added {
    background: linear-gradient(135deg, #4CAF50 0%, #2e7d32 100%);
    animation: buttonSuccess 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes buttonSuccess {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 20px 56px rgba(76, 175, 80, 0.5);
    }
    100% {
        transform: scale(1);
    }
}

.description h3 { font-size: 26px; margin: 40px 0 20px; font-weight: 600; }
.description p { font-size: 17px; line-height: 1.8; color: #333; }

@media (max-width: 1024px) {
    .product-grid { grid-template-columns: 1fr; gap: 60px; }
    .info .title { font-size: 36px; }
    .price { font-size: 42px; }
}

@media (max-width: 640px) {
    .container { padding: 0 20px; }
    .info .title { font-size: 30px; }
    .price { font-size: 36px; }
    .thumbs img { width: 100px; height: 100px; }
}

@media (max-width: 768px) {
    .quantity-selector {
        padding: 4px;
        gap: 2px;
    }

    .quantity-btn {
        width: 40px;
        height: 40px;
        font-size: 20px;
        border-radius: 10px;
    }

    .quantity-input {
        width: 50px;
        height: 40px;
        font-size: 16px;
    }

    /* Страница товара */
    .quantity-block {
        padding: 24px;
        margin: 32px 0;
    }

    .quantity-btn {
        width: 52px;
        height: 52px;
        font-size: 26px;
    }

    .quantity-input {
        width: 80px;
        height: 52px;
        font-size: 22px;
    }

    .add-btn {
        padding: 22px 32px;
        font-size: 18px;
        letter-spacing: 2px;
    }
}
</style>
