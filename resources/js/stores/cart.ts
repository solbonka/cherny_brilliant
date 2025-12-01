import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export interface CartItem {
    id: number;
    title: string;
    price: number;
    old_price?: number | null;
    image: string;
    quantity: number;
}

export const useCartStore = defineStore('cart', () => {
    const items = ref<CartItem[]>([]);

    const load = () => {
        const saved = localStorage.getItem('cart');
        if (saved) {
            items.value = JSON.parse(saved);
        }
    };

    const save = () => {
        localStorage.setItem('cart', JSON.stringify(items.value));
    };

    const add = (product: {
        id: number;
        title: string;
        price: number;
        old_price?: number | null;
        images: { url: string; is_main: boolean }[];
    }) => {
        const mainImage = product.images.find(img => img.is_main)?.url || product.images[0]?.url || '/placeholder.jpg';

        const existing = items.value.find(item => item.id === product.id);
        if (existing) {
            existing.quantity += 1;
        } else {
            items.value.push({
                id: product.id,
                title: product.title,
                price: product.price,
                old_price: product.old_price,
                image: mainImage,
                quantity: 1,
            });
        }
        save();
    };

    const remove = (id: number) => {
        items.value = items.value.filter(item => item.id !== id);
        save();
    };

    const updateQuantity = (id: number, quantity: number) => {
        if (quantity <= 0) {
            remove(id);
            return;
        }
        const item = items.value.find(item => item.id === id);
        if (item) {
            item.quantity = quantity;
            save();
        }
    };

    const clear = () => {
        items.value = [];
        save();
    };

    // ✅ НОВЫЙ МЕТОД - проверка наличия товара в корзине
    const has = (productId: number) => {
        return items.value.some(item => item.id === productId);
    };

    const totalPrice = computed(() => {
        return items.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
    });

    const totalItems = computed(() => {
        return items.value.reduce((sum, item) => sum + item.quantity, 0);
    });

    return {
        items,
        add,
        remove,
        updateQuantity,
        clear,
        has,  // ✅ Экспортируем новый метод
        totalPrice,
        totalItems,
        load,
    };
});
