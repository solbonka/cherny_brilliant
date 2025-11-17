<template>
    <div class="logo-wrap" role="img" aria-label="Логотип Черный Бриллиант">
        <!-- SVG логотип, полностью восстановленный -->
        <svg
            class="logo-icon"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 134 127"
            preserveAspectRatio="xMidYMid meet"
            aria-hidden="true"
        >
            <g
                transform="translate(0,127) scale(0.1,-0.1)"
                fill="none"
                stroke="#ffffff"
                stroke-width="12"
                stroke-linejoin="round"
                stroke-linecap="round"
            >
                <!-- внутренняя геометрия -->
                <path d="M425 988 c38 -124 69 -215 75 -221 6 -7 286 187 325 224 5 5 -72 9 -198 9 -160 0 -206 -3 -202 -12z"/>
                <path d="M765 894 c-71 -52 -158 -114 -192 -138 l-61 -44 78 -242 c42 -132 80 -241 84 -241 4 0 106 127 227 283 l219 283 -101 98 c-55 53 -106 97 -112 96 -7 0 -70 -43 -142 -95z"/>
                <path d="M278 892 c-53 -48 -97 -91 -97 -97 0 -5 83 -111 185 -235 103 -124 197 -238 211 -255 24 -30 25 -30 20 -5 -6 36 -208 664 -216 672 -3 4 -50 -32 -103 -80z"/>
            </g>
        </svg>

        <h1>ЧЕРНЫЙ БРИЛЛИАНТ</h1>
        <p class="subtitle">ШУБЫ • ВЕРХНЯЯ ОДЕЖДА</p>
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
    width: 60px;
    height: 60px;
    display: block;
}

h1 {
    margin: 0;
    font-family: 'Montserrat', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-weight: 300;
    font-size: 40px;
    letter-spacing: 0.14em;
    line-height: 0.95;
    color: #fff;
    white-space: nowrap;
    text-transform: uppercase;
}

p.subtitle {
    margin: 0;
    font-family: 'Montserrat', system-ui, sans-serif;
    font-weight: 400;
    font-size: clamp(12px, 1.6vw, 20px);
    letter-spacing: 0.25em;
    opacity: 0.95;
    text-transform: uppercase;
    color: #fff;
}

@media (max-width: 520px) {
    .logo-icon {
        width: 34vw;
        max-width: 160px;
    }

    h1 {
        letter-spacing: 0.12em;
        font-size: clamp(20px, 9vw, 34px);
        white-space: normal;
    }

    p.subtitle {
        letter-spacing: 0.18em;
    }
}
</style>
