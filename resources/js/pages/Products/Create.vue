<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import productsRoutes from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Category {
    id: number;
    name: string;
    parent_id: number | null;
    children_recursive?: Category[];
}

interface Props {
    categories: Category[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Товары',
        href: productsRoutes.index().url,
    },
    {
        title: 'Создание',
        href: productsRoutes.create().url,
    },
];

const form = useForm({
    category_id: null as number | null,
    title: '',
    description: '',
    price: 0,
    old_price: null as number | null,
    size: '',
    color: '',
    material: '',
    images: [] as File[],
});

const imagePreviews = ref<string[]>([]);

// Функция для создания плоского списка категорий с отступами
const flattenCategories = (categories: Category[], level = 0): Array<{ id: number; name: string; level: number }> => {
    let result: Array<{ id: number; name: string; level: number }> = [];

    categories.forEach(category => {
        result.push({
            id: category.id,
            name: category.name,
            level: level,
        });

        if (category.children_recursive && category.children_recursive.length > 0) {
            result = result.concat(flattenCategories(category.children_recursive, level + 1));
        }
    });

    return result;
};

const flatCategories = computed(() => flattenCategories(props.categories));

const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        form.images.push(...files);

        files.forEach((file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (e.target?.result) {
                    imagePreviews.value.push(e.target.result as string);
                }
            };
            reader.readAsDataURL(file);
        });
    }
};

const removeImage = (index: number) => {
    form.images.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

const submit = () => {
    form.post(productsRoutes.store().url, {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Создание товара" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold">Создание товара</h1>
                    <p class="text-sm text-muted-foreground">
                        Добавьте новый товар в каталог
                    </p>
                </div>
                <Link
                    :href="productsRoutes.index().url"
                    class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                >
                    Назад
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h2 class="mb-4 text-lg font-semibold">Основная информация</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Категория</label>
                            <select
                                v-model="form.category_id"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option :value="null">Без категории</option>
                                <option
                                    v-for="category in flatCategories"
                                    :key="category.id"
                                    :value="category.id"
                                    :style="{ paddingLeft: `${category.level * 20 + 8}px` }"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_id" class="mt-1 text-sm text-destructive">
                                {{ form.errors.category_id }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Название <span class="text-destructive">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                placeholder="Введите название товара"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-destructive">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Описание</label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                placeholder="Введите описание товара"
                            />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-destructive">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    Цена <span class="text-destructive">*</span>
                                </label>
                                <input
                                    v-model.number="form.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    placeholder="0.00"
                                />
                                <p v-if="form.errors.price" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.price }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium">Старая цена</label>
                                <input
                                    v-model.number="form.old_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    placeholder="0.00"
                                />
                                <p v-if="form.errors.old_price" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.old_price }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="mb-2 block text-sm font-medium">Размер</label>
                                <input
                                    v-model="form.size"
                                    type="text"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    placeholder="M, L, XL"
                                />
                                <p v-if="form.errors.size" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.size }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium">Цвет</label>
                                <input
                                    v-model="form.color"
                                    type="text"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    placeholder="Синий, Красный"
                                />
                                <p v-if="form.errors.color" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.color }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium">Состав</label>
                                <input
                                    v-model="form.material"
                                    type="text"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    placeholder="Хлопок, Полиэстер"
                                />
                                <p v-if="form.errors.material" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.material }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h2 class="mb-4 text-lg font-semibold">Изображения</h2>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="flex h-32 cursor-pointer items-center justify-center rounded-md border-2 border-dashed border-input bg-background hover:bg-accent"
                            >
                                <input
                                    type="file"
                                    multiple
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleImageUpload"
                                />
                                <div class="text-center">
                                    <p class="text-sm font-medium">Нажмите для загрузки</p>
                                    <p class="text-xs text-muted-foreground">PNG, JPG, GIF до 2MB</p>
                                </div>
                            </label>
                            <p v-if="form.errors.images" class="mt-1 text-sm text-destructive">
                                {{ form.errors.images }}
                            </p>
                        </div>

                        <div v-if="imagePreviews.length > 0" class="grid gap-4 sm:grid-cols-4">
                            <div
                                v-for="(preview, index) in imagePreviews"
                                :key="index"
                                class="group relative aspect-square overflow-hidden rounded-md border border-sidebar-border/70"
                            >
                                <img
                                    :src="preview"
                                    alt="Preview"
                                    class="h-full w-full object-cover"
                                />
                                <button
                                    type="button"
                                    @click="removeImage(index)"
                                    class="absolute right-2 top-2 rounded-md bg-destructive p-1 text-destructive-foreground opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <Link
                        :href="productsRoutes.index().url"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                    >
                        Отмена
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Сохранение...' : 'Создать товар' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
