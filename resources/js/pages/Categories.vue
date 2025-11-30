<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import categoriesRoutes from '@/routes/categories';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Category {
    id: number;
    name: string;
    parent_id: number | null;
    created_at: string;
    children?: Category[];
    children_recursive?: Category[];
}

const props = defineProps<{
    categories: Category[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Категории',
        href: categoriesRoutes.index().url,
    },
];

const showModal = ref(false);
const editingCategory = ref<Category | null>(null);
const searchQuery = ref('');
const expandedCategories = ref<Set<number>>(new Set());

const form = useForm({
    name: '',
    parent_id: null as number | null,
});

// Рекурсивно собираем все категории
const getAllCategories = (cats: Category[]): Category[] => {
    let result: Category[] = [];
    for (const cat of cats) {
        result.push(cat);
        const childrenArray = cat.children_recursive || cat.children || [];
        if (childrenArray.length > 0) {
            result = result.concat(getAllCategories(childrenArray));
        }
    }
    return result;
};

const allCategories = computed(() => getAllCategories(props.categories));

// Рекурсивно рендерим строки таблицы
const renderCategoryRows = (cats: Category[], level: number = 0): any[] => {
    const rows: any[] = [];

    for (const category of cats) {
        const childrenArray = category.children_recursive || category.children || [];
        const hasChildren = childrenArray.length > 0;
        const isExpanded = expandedCategories.value.has(category.id);
        const matchesSearch = searchQuery.value
            ? category.name.toLowerCase().includes(searchQuery.value.toLowerCase())
            : false;

        rows.push({
            category,
            level,
            hasChildren,
            isExpanded,
            matchesSearch,
            childrenCount: childrenArray.length,
        });

        if (hasChildren && isExpanded) {
            rows.push(...renderCategoryRows(childrenArray, level + 1));
        }
    }

    return rows;
};

const categoryRows = computed(() => renderCategoryRows(props.categories));

const toggleExpand = (categoryId: number) => {
    if (expandedCategories.value.has(categoryId)) {
        expandedCategories.value.delete(categoryId);
    } else {
        expandedCategories.value.add(categoryId);
    }
};

const openCreateModal = (parentId: number | null = null) => {
    editingCategory.value = null;
    form.reset();
    form.parent_id = parentId;
    showModal.value = true;
};

const openEditModal = (category: Category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.parent_id = category.parent_id;
    showModal.value = true;
};

const submitForm = () => {
    if (editingCategory.value) {
        form.put(categoriesRoutes.update(editingCategory.value.id).url, {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(categoriesRoutes.store().url, {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteCategory = (id: number) => {
    if (confirm('Вы уверены, что хотите удалить эту категорию? Все подкатегории также будут удалены.')) {
        router.delete(categoriesRoutes.destroy(id).url);
    }
};

const getCategoryPath = (categoryId: number | null): string => {
    if (!categoryId) return 'Корень';
    const category = allCategories.value.find(c => c.id === categoryId);
    if (!category) return 'Корень';

    const path: string[] = [category.name];
    let current = category;

    while (current.parent_id) {
        const parent = allCategories.value.find(c => c.id === current.parent_id);
        if (!parent) break;
        path.unshift(parent.name);
        current = parent;
    }

    return path.join(' > ');
};

const countTotalCategories = (cats: Category[]): number => {
    let count = cats.length;
    for (const cat of cats) {
        const childrenArray = cat.children_recursive || cat.children || [];
        if (childrenArray.length > 0) {
            count += countTotalCategories(childrenArray);
        }
    }
    return count;
};

const totalCategories = computed(() => countTotalCategories(props.categories));
const rootCategories = computed(() => props.categories.length);
</script>

<template>
    <Head title="Категории" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header with Stats -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="flex flex-col justify-between rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border"
                >
                    <div class="text-sm text-muted-foreground">Всего категорий</div>
                    <div class="text-3xl font-bold">{{ totalCategories }}</div>
                </div>
                <div
                    class="flex flex-col justify-between rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border"
                >
                    <div class="text-sm text-muted-foreground">Корневые категории</div>
                    <div class="text-3xl font-bold">{{ rootCategories }}</div>
                </div>
                <div
                    class="flex flex-col justify-between rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border"
                >
                    <div class="text-sm text-muted-foreground">Вложенные уровни</div>
                    <div class="text-3xl font-bold">∞</div>
                </div>
            </div>

            <!-- Main Content -->
            <div
                class="flex flex-1 flex-col rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border"
            >
                <!-- Toolbar -->
                <div class="flex items-center justify-between border-b p-4">
                    <div class="flex items-center gap-2">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Поиск категорий..."
                            class="rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                    </div>
                    <button
                        @click="openCreateModal()"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                    >
                        + Новая корневая категория
                    </button>
                </div>

                <!-- Tree Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium">Название</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Родитель</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Подкатегории</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Создано</th>
                            <th class="px-4 py-3 text-right text-sm font-medium">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="(row, index) in categoryRows"
                            :key="row.category.id"
                            :class="[
                                    'border-b hover:bg-muted/30',
                                    { 'bg-yellow-50/50 dark:bg-yellow-950/20': searchQuery && row.matchesSearch }
                                ]"
                        >
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center gap-2" :style="{ paddingLeft: row.level * 24 + 'px' }">
                                    <button v-if="row.hasChildren" @click="toggleExpand(row.category.id)" class="hover:text-foreground">
                                        <svg v-if="row.isExpanded" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 5L6 9L10 5" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        <svg v-else width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 2L9 6L5 10" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </button>
                                    <span v-else class="inline-block w-4"></span>
                                    <span class="font-medium">{{ row.category.name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ row.category.parent_id ? '—' : 'Корень' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ row.childrenCount }}
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ new Date(row.category.created_at).toLocaleDateString('ru-RU') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <button
                                        @click="openCreateModal(row.category.id)"
                                        class="rounded px-3 py-1 text-sm text-primary hover:bg-primary/10"
                                    >
                                        Добавить подкатегорию
                                    </button>
                                    <button
                                        @click="openEditModal(row.category)"
                                        class="rounded px-3 py-1 text-sm hover:bg-muted"
                                    >
                                        Редактировать
                                    </button>
                                    <button
                                        @click="deleteCategory(row.category.id)"
                                        class="rounded px-3 py-1 text-sm text-destructive hover:bg-destructive/10"
                                    >
                                        Удалить
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="categoryRows.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-foreground">
                                Категории не найдены
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @click.self="showModal = false"
        >
            <div class="w-full max-w-md rounded-xl bg-card p-6 shadow-lg">
                <h2 class="mb-4 text-xl font-semibold">
                    {{ editingCategory ? 'Редактировать категорию' : 'Создать категорию' }}
                </h2>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Название</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <div v-if="form.errors.name" class="mt-1 text-sm text-destructive">
                            {{ form.errors.name }}
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Родительская категория</label>
                        <select
                            v-model="form.parent_id"
                            class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            <option :value="null">Корень (Без родителя)</option>
                            <option
                                v-for="cat in allCategories"
                                :key="cat.id"
                                :value="cat.id"
                                :disabled="editingCategory?.id === cat.id"
                            >
                                {{ getCategoryPath(cat.parent_id) }} > {{ cat.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.parent_id" class="mt-1 text-sm text-destructive">
                            {{ form.errors.parent_id }}
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="rounded-lg border px-4 py-2 text-sm hover:bg-muted"
                        >
                            Отмена
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Сохранение...' : 'Сохранить' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
