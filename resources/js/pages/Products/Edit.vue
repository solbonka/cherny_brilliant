<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import productsRoutes from '@/routes/products';
import productImagesRoutes from '@/routes/product-images';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

interface ProductImage {
    id: number;
    path: string;
    url: string;
    is_main: boolean;
    sort_order: number;
}

interface Product {
    id: number;
    category_id: number | null;
    title: string;
    description: string | null;
    price: number;
    old_price: number | null;
    size: string | null;
    color: string | null;
    material: string | null;
    images: ProductImage[];
}

interface Category {
    id: number;
    name: string;
    parent_id: number | null;
    children_recursive?: Category[];
}

interface Props {
    product: Product;
    categories: Category[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Товары',
        href: productsRoutes.index().url,
    },
    {
        title: props.product.title,
        href: productsRoutes.show(props.product.id).url,
    },
    {
        title: 'Редактирование',
        href: productsRoutes.edit(props.product.id).url,
    },
];

const form = useForm({
    category_id: props.product.category_id || null,
    title: props.product.title,
    description: props.product.description || '',
    price: props.product.price,
    old_price: props.product.old_price,
    size: props.product.size || '',
    color: props.product.color || '',
    material: props.product.material || '',
    images: [] as File[],
    existing_images: props.product.images.map((img) => ({
        id: img.id,
        sort_order: img.sort_order,
        is_main: img.is_main,
    })),
});

const existingImages = ref<ProductImage[]>([...props.product.images]);
const newImagePreviews = ref<string[]>([]);

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
                    newImagePreviews.value.push(e.target.result as string);
                }
            };
            reader.readAsDataURL(file);
        });
    }
};

const removeNewImage = (index: number) => {
    form.images.splice(index, 1);
    newImagePreviews.value.splice(index, 1);
};

const deleteExistingImage = async (image: ProductImage, index: number) => {
    if (confirm('Удалить это изображение?')) {
        try {
            await axios.delete(productImagesRoutes.destroy(image.id).url);
            existingImages.value.splice(index, 1);
            form.existing_images = form.existing_images.filter((img) => img.id !== image.id);
        } catch (error) {
            console.error('Ошибка при удалении изображения:', error);
        }
    }
};

const moveImage = (index: number, direction: 'up' | 'down') => {
    const newIndex = direction === 'up' ? index - 1 : index + 1;
    if (newIndex < 0 || newIndex >= existingImages.value.length) return;

    [existingImages.value[index], existingImages.value[newIndex]] = [
        existingImages.value[newIndex],
        existingImages.value[index],
    ];

    existingImages.value.forEach((img, idx) => {
        const formImage = form.existing_images.find((fi) => fi.id === img.id);
        if (formImage) {
            formImage.sort_order = idx;
        }
    });
};

const setMainImage = (imageId: number) => {
    // Снять флаг is_main со всех изображений
    form.existing_images.forEach((img) => {
        img.is_main = img.id === imageId;
    });
};

const submit = () => {
    const url = productsRoutes.update(props.product.id).url;

    if (form.images.length > 0) {
        // Есть новые изображения → отправляем POST + multipart
        form.post(url, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                newImagePreviews.value = [];
                form.images = []; // опционально, чтобы очистить форму
            },
        });
    } else {
        // Нет новых изображений → обычный Inertia PUT (JSON)
        form.put(url, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="`Редактирование: ${product.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold">Редактирование товара</h1>
                    <p class="text-sm text-muted-foreground">
                        Измените информацию о товаре
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="productsRoutes.show(product.id).url"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                    >
                        Просмотр
                    </Link>
                    <Link
                        :href="productsRoutes.index().url"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                    >
                        Назад
                    </Link>
                </div>
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
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium">Цвет</label>
                                <input
                                    v-model="form.color"
                                    type="text"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    placeholder="Синий, Красный"
                                />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium">Состав</label>
                                <input
                                    v-model="form.material"
                                    type="text"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    placeholder="Хлопок, Полиэстер"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h2 class="mb-4 text-lg font-semibold">Изображения</h2>

                    <div class="space-y-4">
                        <div v-if="existingImages.length > 0">
                            <p class="mb-2 text-sm font-medium">Текущие изображения</p>
                            <div class="grid gap-4 sm:grid-cols-4">
                                <div
                                    v-for="(image, index) in existingImages"
                                    :key="image.id"
                                    class="group relative aspect-square overflow-hidden rounded-md border border-sidebar-border/70"
                                >
                                    <img
                                        :src="image.url"
                                        alt="Product image"
                                        class="h-full w-full object-cover"
                                    />
                                    <!-- Main Badge -->
                                    <div
                                        v-if="image.is_main"
                                        class="absolute left-2 top-2 rounded-md bg-primary px-2 py-1 text-xs font-semibold text-primary-foreground"
                                    >
                                        Главное
                                    </div>
                                    <div class="absolute inset-0 flex items-center justify-center gap-1 bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                                        <button
                                            v-if="!image.is_main"
                                            type="button"
                                            @click="setMainImage(image.id)"
                                            class="rounded-md bg-primary p-1 text-primary-foreground text-xs px-2"
                                            title="Сделать главным"
                                        >
                                            Главное
                                        </button>
                                        <button
                                            v-if="index > 0"
                                            type="button"
                                            @click="moveImage(index, 'up')"
                                            class="rounded-md bg-white p-1 text-black"
                                            title="Переместить влево"
                                        >
                                            ←
                                        </button>
                                        <button
                                            v-if="index < existingImages.length - 1"
                                            type="button"
                                            @click="moveImage(index, 'down')"
                                            class="rounded-md bg-white p-1 text-black"
                                            title="Переместить вправо"
                                        >
                                            →
                                        </button>
                                        <button
                                            type="button"
                                            @click="deleteExistingImage(image, index)"
                                            class="rounded-md bg-destructive p-1 text-destructive-foreground"
                                            title="Удалить"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="mb-2 text-sm font-medium">Добавить новые изображения</p>
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
                        </div>

                        <div v-if="newImagePreviews.length > 0" class="grid gap-4 sm:grid-cols-4">
                            <div
                                v-for="(preview, index) in newImagePreviews"
                                :key="`new-${index}`"
                                class="group relative aspect-square overflow-hidden rounded-md border border-sidebar-border/70"
                            >
                                <img
                                    :src="preview"
                                    alt="New image preview"
                                    class="h-full w-full object-cover"
                                />
                                <button
                                    type="button"
                                    @click="removeNewImage(index)"
                                    class="absolute right-2 top-2 rounded-md bg-destructive p-1 text-destructive-foreground opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    ✕
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
                        {{ form.processing ? 'Сохранение...' : 'Сохранить изменения' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
