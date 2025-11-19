<template>
    <div class="logo-wrap" role="img" aria-label="Логотип Черный Бриллиант">
        <img
            src="/app/logo_diamond.png"
            alt="Черный Бриллиант лого"
            class="logo-icon"
        />

        <img
            src="/app/logo_text.png"
            alt="Черный Бриллиант"
            class="logo-text"
        />
    </div>
</template>

<script>
export default {
    name: 'AppHeadLogo'
}
</script>

<style scoped>
.logo-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 0.5rem;
}

.logo-icon {
    height: 30px;
    display: block;
}

.logo-text {
    height: 60px;
}
</style>
