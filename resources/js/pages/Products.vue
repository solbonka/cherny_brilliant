<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import productsRoutes from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

interface ProductImage {
    id: number;
    path: string;
    url: string;
    is_main: boolean;
    sort_order: number;
}

interface Product {
    id: number;
    title: string;
    description: string | null;
    price: number;
    old_price: number | null;
    size: string | null;
    color: string | null;
    material: string | null;
    images: ProductImage[];
}

interface Props {
    products: {
        data: Product[];
        links: any[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Товары',
        href: productsRoutes.index().url,
    },
];

const search = ref(props.filters.search || '');

const searchProducts = debounce(() => {
    router.get(
        productsRoutes.index().url,
        { search: search.value },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

watch(search, () => {
    searchProducts();
});

const deleteProduct = (product: Product) => {
    if (confirm(`Вы уверены, что хотите удалить товар "${product.title}"?`)) {
        router.delete(productsRoutes.destroy(product.id).url);
    }
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
    }).format(price);
};
</script>

<template>
    <Head title="Товары" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-semibold">Товары</h1>
                    <p class="text-sm text-muted-foreground">
                        Управление товарами в каталоге
                    </p>
                </div>
                <Link
                    :href="productsRoutes.create().url"
                    class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    Добавить товар
                </Link>
            </div>

            <!-- Search -->
            <div class="flex items-center gap-4">
                <div class="relative flex-1">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Поиск товаров..."
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    />
                </div>
            </div>

            <!-- Products Grid -->
            <div
                v-if="products.data.length > 0"
                class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <div
                    v-for="product in products.data"
                    :key="product.id"
                    class="group overflow-hidden rounded-xl border border-sidebar-border/70 bg-card transition-shadow hover:shadow-md dark:border-sidebar-border"
                >
                    <!-- Image -->
                    <div class="relative aspect-square overflow-hidden bg-muted">
                        <img
                            v-if="product.images.length > 0"
                            :src="product.images[0].url"
                            :alt="product.title"
                            class="h-full w-full object-cover transition-transform group-hover:scale-105"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center bg-muted"
                        >
                            <span class="text-muted-foreground">Нет фото</span>
                        </div>

                        <!-- Discount Badge -->
                        <div
                            v-if="product.old_price && product.old_price > product.price"
                            class="absolute left-2 top-2 rounded-md bg-destructive px-2 py-1 text-xs font-semibold text-destructive-foreground"
                        >
                            -{{ Math.round((1 - product.price / product.old_price) * 100) }}%
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex flex-col gap-2 p-4">
                        <h3 class="line-clamp-2 text-base font-semibold">
                            {{ product.title }}
                        </h3>

                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold">
                                {{ formatPrice(product.price) }}
                            </span>
                            <span
                                v-if="product.old_price"
                                class="text-sm text-muted-foreground line-through"
                            >
                                {{ formatPrice(product.old_price) }}
                            </span>
                        </div>

                        <div
                            v-if="product.size || product.color || product.material"
                            class="flex flex-wrap gap-1 text-xs text-muted-foreground"
                        >
                            <span v-if="product.size">{{ product.size }}</span>
                            <span v-if="product.size && (product.color || product.material)">•</span>
                            <span v-if="product.color">{{ product.color }}</span>
                            <span v-if="product.color && product.material">•</span>
                            <span v-if="product.material">{{ product.material }}</span>
                        </div>

                        <!-- Actions -->
                        <div class="mt-2 flex gap-2">
                            <Link
                                :href="productsRoutes.show(product.id).url"
                                class="flex-1 rounded-md border border-input bg-background px-3 py-2 text-center text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                            >
                                Просмотр
                            </Link>
                            <Link
                                :href="productsRoutes.edit(product.id).url"
                                class="flex-1 rounded-md border border-input bg-background px-3 py-2 text-center text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                            >
                                Изменить
                            </Link>
                            <button
                                @click="deleteProduct(product)"
                                class="rounded-md border border-destructive/50 px-3 py-2 text-sm font-medium text-destructive hover:bg-destructive hover:text-destructive-foreground"
                            >
                                Удалить
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex min-h-[400px] flex-col items-center justify-center rounded-xl border border-sidebar-border/70 p-8 dark:border-sidebar-border"
            >
                <h3 class="text-lg font-semibold">Товары не найдены</h3>
                <p class="text-sm text-muted-foreground">
                    {{ search ? 'Попробуйте изменить поисковый запрос' : 'Начните с добавления первого товара' }}
                </p>
                <Link
                    v-if="!search"
                    :href="productsRoutes.create().url"
                    class="mt-4 inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    Добавить товар
                </Link>
            </div>

            <!-- Pagination -->
            <div
                v-if="products.last_page > 1"
                class="flex items-center justify-between rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border"
            >
                <div class="text-sm text-muted-foreground">
                    Показано {{ products.data.length }} из {{ products.total }} товаров
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="(link, index) in products.links"
                        :key="index"
                        :href="link.url || '#'"
                        :class="[
                            'rounded-md px-3 py-2 text-sm font-medium transition-colors',
                            link.active
                                ? 'bg-primary text-primary-foreground'
                                : link.url
                                ? 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'
                                : 'cursor-not-allowed text-muted-foreground opacity-50',
                        ]"
                        :disabled="!link.url"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
