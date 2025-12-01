<!-- resources/js/Layouts/DefaultLayout.vue -->
<template>
    <div class="black-diamond-site">
        <MainHeader />

        <!-- Основной контент страницы -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 Салон "Черный Бриллиант". Все права защищены.</p>
            <p>27 лет на рынке роскошной верхней одежды</p>
        </footer>
    </div>
</template>

<script setup lang="ts">
import MainHeader from '@/pages/main/MainHeader.vue';
</script>

<style scoped>
.black-diamond-site {
    font-family: 'Montserrat', 'Segoe UI', sans-serif;
    background: #ffffff;
    color: #000000;
    overflow-x: hidden;
    line-height: 1.6;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

main {
    flex: 1; /* чтобы футер всегда был внизу */
}

/* Footer */
footer {
    background: #0a0a0a;
    color: #888888;
    padding: 40px 80px;
    text-align: center;
    font-size: 14px;
    margin-top: auto;
}
</style>
