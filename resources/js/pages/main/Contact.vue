<template>
    <section id="contact" class="contact bg-black text-white py-24 px-6 sm:px-20 !pt-20 relative overflow-hidden">
        <!-- Декоративные элементы -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto">
            <!-- Заголовок -->
            <div class="text-center mb-20">
                <h2 class="text-5xl sm:text-7xl font-black mb-6 tracking-tight uppercase">
                    СВЯЖИТЕСЬ С НАМИ
                </h2>
                <div class="w-24 h-1 bg-white mx-auto mb-8"></div>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Посетите наш салон и убедитесь в качестве нашей продукции
                </p>
            </div>

            <!-- Кнопки связи -->
            <div class="flex flex-col sm:flex-row justify-center gap-6 mb-24">
                <a
                href="tel:+79025655141"
                class="group relative px-10 py-5 bg-white text-black rounded-none text-lg font-bold uppercase tracking-wide overflow-hidden transition-all duration-300 hover:bg-gray-100 no-underline flex items-center justify-center gap-4"
                >
                <svg class="w-6 h-6 fill-black" viewBox="0 0 24 24">
                    <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 00-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/>
                </svg>
                +7 (902) 565-51-41
                </a>

                <button
                    @click="openModal"
                    class="group relative px-10 py-5 bg-black text-white border-2 border-white rounded-none text-lg font-bold uppercase tracking-wide overflow-hidden transition-all duration-300 hover:bg-white hover:text-black cursor-pointer flex items-center justify-center gap-4"
                >
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                    </svg>
                    Написать
                </button>
            </div>

            <!-- Модальное окно -->
            <div v-if="isModalOpen" class="modal fixed z-50 inset-0 w-full h-full overflow-auto bg-black/95 backdrop-blur-sm" @click.self="closeModal">
                <div class="modal-content bg-black border-2 border-white my-[10%] mx-auto p-12 w-[90%] max-w-lg shadow-2xl animate-modal-fade relative">
                    <button
                        class="absolute top-6 right-6 text-white hover:text-gray-400 text-4xl font-light cursor-pointer transition-colors"
                        @click="closeModal"
                    >
                        ×
                    </button>

                    <h3 class="text-3xl font-black uppercase mb-10 text-center border-b-2 border-white pb-4">
                        Выберите мессенджер
                    </h3>

                    <div class="messenger-buttons flex flex-col gap-5">
                        <!-- Telegram -->
                        <a
                        href="https://t.me/mikhaylova_evgenia"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex items-center gap-4 px-6 py-5 bg-[#0088cc] hover:bg-[#0077b5] text-white rounded-lg font-semibold transition-all duration-300 no-underline"
                        >
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="text-lg font-bold">Telegram</div>
                            <div class="text-sm text-blue-100 opacity-90">@mikhaylova_evgenia</div>
                        </div>
                        <svg class="w-6 h-6 opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                        </svg>
                        </a>

                        <!-- WhatsApp -->
                        <a
                        href="https://wa.me/79025647339"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex items-center gap-4 px-6 py-5 bg-[#25D366] hover:bg-[#20BA5A] text-white rounded-lg font-semibold transition-all duration-300 no-underline"
                        >
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="text-lg font-bold">WhatsApp</div>
                            <div class="text-sm text-green-100 opacity-90">+7 (902) 564-73-39</div>
                        </div>
                        <svg class="w-6 h-6 opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                        </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Карточка салона и карта в две колонки -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
                <!-- Информация о салоне -->
                <div class="bg-white text-black p-12 border-4 border-white transition-all duration-300 hover:border-gray-300" itemscope itemtype="https://schema.org/Place">
                    <div class="flex items-center justify-center mb-8">
                        <div class="w-20 h-20 bg-black flex items-center justify-center">
                            <svg class="w-10 h-10 stroke-white" viewBox="0 0 24 24" fill="none" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-3xl font-black uppercase mb-8 text-center tracking-wider border-b-2 border-black pb-4">
                        НАШ САЛОН
                    </h3>

                    <div class="space-y-6 text-center">
                        <div itemprop="address">
                            <p class="text-2xl font-bold mb-2">ул. Балтахинова, 36</p>
                            <p class="text-lg text-gray-600">Остановка: Центральный рынок</p>
                        </div>

                        <div class="border-t-2 border-black pt-6 mt-6">
                            <p class="text-sm uppercase tracking-widest mb-3 text-gray-600">Режим работы</p>
                            <p class="text-2xl font-black">ЕЖЕДНЕВНО</p>
                            <p class="text-3xl font-black">10:00 - 20:00</p>
                        </div>
                    </div>
                </div>

                <!-- Карта -->
                <div class="border-4 border-white overflow-hidden transition-all duration-300 hover:border-gray-300 h-full min-h-[500px]">
                    <div id="yandex-map" class="w-full h-full min-h-[500px]"></div>
                </div>
            </div>
        </div>
    </section>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';

declare global {
    interface Window {
        ymaps: any;
    }
}

export default defineComponent({
    setup() {
        const isModalOpen = ref<boolean>(false);

        const openModal = (): void => {
            isModalOpen.value = true;
        };

        const closeModal = (): void => {
            isModalOpen.value = false;
        };

        const initYandexMap = (): void => {
            if (typeof window.ymaps !== 'undefined') {
                window.ymaps.ready(() => {
                    const map = new window.ymaps.Map('yandex-map', {
                        center: [51.824735, 107.592533],
                        zoom: 17,
                        controls: ['zoomControl', 'fullscreenControl']
                    });

                    const placemark = new window.ymaps.Placemark([51.824735, 107.592533], {
                        balloonContent: '<strong>Наш салон</strong><br>ул. Балтахинова, 36<br>Ежедневно: 10:00 - 20:00',
                        hintContent: 'ул. Балтахинова, 36'
                    }, {
                        preset: 'islands#blackDotIcon'
                    });

                    map.geoObjects.add(placemark);
                });
            }
        };

        onMounted(() => {
            const script = document.createElement('script');
            script.src = 'https://api-maps.yandex.ru/2.1/?apikey=f404cf4f-ae3f-43bf-a1ac-122a15993619&lang=ru_RU';
            script.async = true;
            script.onload = initYandexMap;
            document.head.appendChild(script);
        });

        return { isModalOpen, openModal, closeModal };
    },
});
</script>

<style scoped>
@keyframes modalFade {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-modal-fade {
    animation: modalFade 0.3s ease-out;
}
</style>
