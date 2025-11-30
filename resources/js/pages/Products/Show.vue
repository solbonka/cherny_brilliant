<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import productsRoutes from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
    created_at: string;
    updated_at: string;
}

interface Props {
    product: Product;
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
];

const currentImageIndex = ref(0);

const deleteProduct = () => {
    if (confirm(`Вы уверены, что хотите удалить товар "${props.product.title}"?`)) {
        router.delete(productsRoutes.destroy(props.product.id).url);
    }
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
    }).format(price);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const selectImage = (index: number) => {
    currentImageIndex.value = index;
};

const nextImage = () => {
    if (props.product.images.length > 0) {
        currentImageIndex.value = (currentImageIndex.value + 1) % props.product.images.length;
    }
};

const prevImage = () => {
    if (props.product.images.length > 0) {
        currentImageIndex.value =
            (currentImageIndex.value - 1 + props.product.images.length) %
            props.product.images.length;
    }
};
</script>

<template>
    <Head :title="product.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold">{{ product.title }}</h1>
                    <p class="text-sm text-muted-foreground">
                        Подробная информация о товаре
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="productsRoutes.edit(product.id).url"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                    >
                        Изменить
                    </Link>
                    <button
                        @click="deleteProduct"
                        class="inline-flex items-center justify-center rounded-md border border-destructive/50 px-4 py-2 text-sm font-medium text-destructive hover:bg-destructive hover:text-destructive-foreground"
                    >
                        Удалить
                    </button>
                    <Link
                        :href="productsRoutes.index().url"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                    >
                        Назад
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Images Section -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h2 class="mb-4 text-lg font-semibold">Изображения</h2>

                    <div v-if="product.images.length > 0" class="space-y-4">
                        <!-- Main Image -->
                        <div class="relative aspect-square overflow-hidden rounded-lg bg-muted">
                            <img
                                :src="product.images[currentImageIndex].url"
                                :alt="product.title"
                                class="h-full w-full object-cover"
                            />

                            <!-- Navigation Buttons -->
                            <button
                                v-if="product.images.length > 1"
                                @click="prevImage"
                                class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-black/50 p-2 text-white hover:bg-black/70"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </button>
                            <button
                                v-if="product.images.length > 1"
                                @click="nextImage"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-black/50 p-2 text-white hover:bg-black/70"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>

                            <!-- Image Counter -->
                            <div
                                v-if="product.images.length > 1"
                                class="absolute bottom-2 right-2 rounded-md bg-black/50 px-2 py-1 text-xs text-white"
                            >
                                {{ currentImageIndex + 1 }} / {{ product.images.length }}
                            </div>
                        </div>

                        <!-- Thumbnails -->
                        <div
                            v-if="product.images.length > 1"
                            class="grid grid-cols-6 gap-2"
                        >
                            <button
                                v-for="(image, index) in product.images"
                                :key="image.id"
                                @click="selectImage(index)"
                                :class="[
                                    'aspect-square overflow-hidden rounded-md border-2 transition-all',
                                    currentImageIndex === index
                                        ? 'border-primary'
                                        : 'border-transparent hover:border-muted-foreground/50',
                                ]"
                            >
                                <img
                                    :src="image.url"
                                    :alt="`${product.title} - ${index + 1}`"
                                    class="h-full w-full object-cover"
                                />
                            </button>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex aspect-square items-center justify-center rounded-lg bg-muted"
                    >
                        <p class="text-muted-foreground">Нет изображений</p>
                    </div>
                </div>

                <!-- Product Info Section -->
                <div class="space-y-6">
                    <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                        <h2 class="mb-4 text-lg font-semibold">Информация о товаре</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Название</label>
                                <p class="mt-1 text-base">{{ product.title }}</p>
                            </div>

                            <div v-if="product.description">
                                <label class="text-sm font-medium text-muted-foreground">Описание</label>
                                <p class="mt-1 text-base whitespace-pre-wrap">{{ product.description }}</p>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-muted-foreground">Цена</label>
                                    <p class="mt-1 text-2xl font-bold">{{ formatPrice(product.price) }}</p>
                                </div>

                                <div v-if="product.old_price">
                                    <label class="text-sm font-medium text-muted-foreground">Старая цена</label>
                                    <div class="mt-1 flex items-center gap-2">
                                        <p class="text-lg text-muted-foreground line-through">
                                            {{ formatPrice(product.old_price) }}
                                        </p>
                                        <span class="rounded-md bg-destructive px-2 py-1 text-xs font-semibold text-destructive-foreground">
                                            -{{ Math.round((1 - product.price / product.old_price) * 100) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="product.size || product.color || product.material" class="grid gap-4 sm:grid-cols-3">
                                <div v-if="product.size">
                                    <label class="text-sm font-medium text-muted-foreground">Размер</label>
                                    <p class="mt-1 text-base">{{ product.size }}</p>
                                </div>

                                <div v-if="product.color">
                                    <label class="text-sm font-medium text-muted-foreground">Цвет</label>
                                    <p class="mt-1 text-base">{{ product.color }}</p>
                                </div>

                                <div v-if="product.material">
                                    <label class="text-sm font-medium text-muted-foreground">Состав</label>
                                    <p class="mt-1 text-base">{{ product.material }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                        <h2 class="mb-4 text-lg font-semibold">Метаинформация</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-muted-foreground">ID товара:</span>
                                <span class="text-sm font-medium">{{ product.id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-muted-foreground">Создан:</span>
                                <span class="text-sm font-medium">{{ formatDate(product.created_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-muted-foreground">Обновлён:</span>
                                <span class="text-sm font-medium">{{ formatDate(product.updated_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-muted-foreground">Изображений:</span>
                                <span class="text-sm font-medium">{{ product.images.length }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
