<script setup lang="ts">
import { ref } from 'vue';
import type { Category } from '@/types/categories';

interface Props {
    category: Category;
}

const props = defineProps<Props>();
const isOpen = ref(false);

const toggle = () => {
    isOpen.value = !isOpen.value;
};
</script>

<template>
    <div class="category-item">
        <div class="category-label" @click="toggle">
            <span>{{ category.name }}</span>

            <!-- SVG стрелка -->
            <svg
                v-if="category.children && category.children.length > 0"
                class="arrow"
                :class="{ open: isOpen }"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill="currentColor"
                    d="M7 10l5 5 5-5H7z"
                />
            </svg>
        </div>

        <div v-if="isOpen && category.children?.length" class="category-children">
            <CategoryItem
                v-for="child in category.children"
                :key="child.id"
                :category="child"
            />
        </div>
    </div>
</template>

<style scoped>
.category-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 20px;
    cursor: pointer;
    color: #fff;
}

/* стрелка */
.arrow {
    transition: transform 0.25s ease;
}

/* стрелка вверх при open */
.arrow.open {
    transform: rotate(180deg);
}

.category-children {
    padding-left: 15px;
    margin-top: 3px;
}

@media (max-width: 768px) {
    .category-label {
        font-size: 15px;
        padding: 18px 30px;
    }
}
</style>
