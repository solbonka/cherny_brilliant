import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useFavoritesStore = defineStore('favorites', () => {
    const items = ref<number[]>([]);

    const count = computed(() => items.value.length);

    const load = () => {
        const saved = localStorage.getItem('favorites');
        items.value = saved ? JSON.parse(saved) : [];
    };

    const toggle = (id: number) => {
        if (items.value.includes(id)) {
            items.value = items.value.filter(i => i !== id);
        } else {
            items.value.push(id);
        }
        localStorage.setItem('favorites', JSON.stringify(items.value));
    };

    // Ловим изменения в localStorage из других вкладках
    window.addEventListener('storage', (e) => {
        if (e.key === 'favorites') load();
    });

    // Переопределение setItem для вызова 'storage' в той же вкладке
    const originalSetItem = localStorage.setItem;
    localStorage.setItem = function (this: Storage, key: string, value: string) {  // Явно укажите типы параметров и this
        if (key === 'favorites') {
            const oldValue = localStorage.getItem(key);
            originalSetItem.call(this, key, value);  // Используйте call вместо apply, передавая аргументы явно
            const event = new StorageEvent('storage', {
                key,
                oldValue,
                newValue: value,
                url: window.location.href,
                storageArea: localStorage
            });
            window.dispatchEvent(event);
        } else {
            originalSetItem.call(this, key, value);
        }
    };

    return { items, count, load, toggle };
});
