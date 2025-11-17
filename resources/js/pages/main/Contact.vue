<template>
    <section id="contact" class="contact bg-black text-white !py-30 !px-20 text-center relative">
        <h2 class="text-3xl sm:text-5xl font-extrabold mb-8 tracking-normal sm:tracking-widest uppercase relative
           after:content-[''] after:block after:w-24 after:h-0.5 after:bg-white after:mx-auto after:mt-2.5 after:opacity-50">
            СВЯЖИТЕСЬ С НАМИ
        </h2>

        <p class="text-lg mb-12 text-gray-400 max-w-2xl mx-auto">
            Посетите наши салоны и убедитесь в качестве нашей продукции
        </p>

        <div class="cta-buttons flex flex-col sm:flex-row justify-center gap-4 sm:gap-6 mb-16">
            <a
                href="tel:+79025655141"
                class="btn btn-primary px-6 sm:px-9 py-2.5 sm:py-3.5 rounded-lg text-sm sm:text-base font-semibold transition-transform transition-shadow hover:-translate-y-1 hover:shadow-lg hover:shadow-white/15 bg-white text-black border-none no-underline flex items-center justify-center gap-2"
                aria-label="Позвонить по номеру +7 (902) 565-51-41"
                role="button"
            >
                Позвонить
                <svg class="icon w-4 sm:w-5 h-4 sm:h-5 stroke-black transition-stroke hover:stroke-gray-400" viewBox="0 0 24 24" fill="black" stroke-width="2" aria-hidden="true">
                    <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                +7 (902) 565-51-41
            </a>
            <button
                class="btn btn-secondary px-6 sm:px-9 py-2.5 sm:py-3.5 rounded-lg text-sm sm:text-base font-semibold transition-transform transition-shadow hover:-translate-y-1 hover:shadow-lg hover:shadow-white/15 bg-transparent border-2 border-white text-white cursor-pointer"
                @click="openModal"
                aria-label="Открыть форму для сообщения"
                role="button"
            >
                Написать
            </button>
        </div>


        <!-- Модальное окно для формы -->
        <div v-if="isModalOpen" class="modal fixed z-50 inset-0 w-full h-full overflow-auto bg-black/85 transition-opacity">
            <div class="modal-content bg-black my-[15%] mx-auto p-10 border border-white/20 w-4/5 max-w-md rounded-2xl text-left shadow-2xl shadow-black/50 animate-modal-fade">
                <span class="close text-white float-right text-3xl font-bold cursor-pointer transition-colors hover:text-gray-400" @click="closeModal">&times;</span>
                <h3 class="text-2xl mb-6 text-center font-bold">Напишите нам</h3>
                <form id="contactForm" @submit.prevent="handleSubmit">
                    <label for="name" class="block mb-2 text-white font-medium">Имя:</label>
                    <input
                        type="text"
                        id="name"
                        v-model="form.name"
                        required
                        aria-required="true"
                        class="w-full p-3 mb-5 border border-gray-400 rounded-lg bg-white/5 text-white transition-border focus:border-white focus:outline-none"
                    />

                    <label for="email" class="block mb-2 text-white font-medium">Email:</label>
                    <input
                        type="email"
                        id="email"
                        v-model="form.email"
                        required
                        aria-required="true"
                        class="w-full p-3 mb-5 border border-gray-400 rounded-lg bg-white/5 text-white transition-border focus:border-white focus:outline-none"
                    />

                    <label for="message" class="block mb-2 text-white font-medium">Сообщение:</label>
                    <textarea
                        id="message"
                        v-model="form.message"
                        required
                        aria-required="true"
                        class="w-full p-3 mb-5 border border-gray-400 rounded-lg bg-white/5 text-white transition-border focus:border-white focus:outline-none"
                    ></textarea>

                    <button type="submit" class="btn btn-primary w-full px-9 py-3.5 rounded-lg text-base font-semibold transition-transform transition-shadow hover:-translate-y-1 hover:shadow-lg hover:shadow-white/15 bg-white text-black border-none cursor-pointer">
                        Отправить
                    </button>
                </form>
            </div>
        </div>

        <div class="contact-info flex flex-col md:flex-row md:justify-center md:gap-8 mt-12" itemscope itemtype="https://schema.org/Organization">
            <!-- Филиал 1 -->
            <div class="contact-item text-center max-w-sm bg-white/5 border border-white/10 rounded-2xl p-8 shadow-xl shadow-black/30 transition-transform transition-shadow hover:scale-105 hover:shadow-2xl hover:shadow-black/40" itemscope itemtype="https://schema.org/Place">
                <h3 class="text-xl mb-5 tracking-wider uppercase font-bold">ФИЛИАЛ 1</h3>
                <p class="text-base text-white flex items-center justify-center gap-3 mb-4" itemprop="address">
                    <svg class="icon w-5 h-5 stroke-white transition-stroke hover:stroke-gray-400" viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    ул. Балтахинова, 36, Остановка: Центральный рынок
                </p>
                <p class="text-base mb-4">Режим работы:</p>
                <ul class="schedule list-none text-base text-gray-400 grid grid-cols-[auto_1fr] gap-x-5 gap-y-2.5 text-left max-w-xs mx-auto">
                    <template v-for="day in branch1Schedule" :key="day.day">
                        <li class="contents">
                            <span class="font-semibold text-white">{{ day.day }}</span> {{ day.hours }}
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Филиал 2 -->
            <div class="contact-item text-center max-w-sm bg-white/5 border border-white/10 rounded-2xl p-8 shadow-xl shadow-black/30 transition-transform transition-shadow hover:scale-105 hover:shadow-2xl hover:shadow-black/40" itemscope itemtype="https://schema.org/Place">
                <h3 class="text-xl mb-5 tracking-wider uppercase font-bold">ФИЛИАЛ 2</h3>
                <p class="text-base text-white flex items-center justify-center gap-3 mb-4" itemprop="address">
                    <svg class="icon w-5 h-5 stroke-white transition-stroke hover:stroke-gray-400" viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    ул. Балтахинова, 15, ТЦ Galaxy, 3 этаж, бутик 330
                </p>
                <p class="text-base mb-4">Режим работы:</p>
                <ul class="schedule list-none text-base text-gray-400 grid grid-cols-[auto_1fr] gap-x-5 gap-y-2.5 text-left max-w-xs mx-auto">
                    <template v-for="day in branch2Schedule" :key="day.day">
                        <li class="contents">
                            <span class="font-semibold text-white">{{ day.day }}</span> {{ day.hours }}
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </section>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';

export default defineComponent({
    setup() {
        const isModalOpen = ref<boolean>(false);
        const form = ref<{ name: string; email: string; message: string }>({
            name: '',
            email: '',
            message: '',
        });

        const openModal = (): void => {
            isModalOpen.value = true;
        };

        const closeModal = (): void => {
            isModalOpen.value = false;
        };

        const handleSubmit = (): void => {
            // Здесь можно добавить код для отправки формы (например, via Fetch API)
            alert('Сообщение отправлено!'); // Placeholder
            closeModal();
            form.value = { name: '', email: '', message: '' }; // Сброс формы
        };

        const branch1Schedule = [
            { day: 'Пн', hours: '10:00 - 20:00' },
            { day: 'Вт', hours: '10:00 - 20:00' },
            { day: 'Ср', hours: '10:00 - 20:00' },
            { day: 'Чт', hours: '10:00 - 20:00' },
            { day: 'Пт', hours: '10:00 - 20:00' },
            { day: 'Сб', hours: '10:00 - 20:00' },
            { day: 'Вс', hours: '10:00 - 20:00' },
        ];

        const branch2Schedule = [
            { day: 'Пн', hours: '11:00 - 20:00' },
            { day: 'Вт', hours: '11:00 - 20:00' },
            { day: 'Ср', hours: '11:00 - 20:00' },
            { day: 'Чт', hours: '11:00 - 20:00' },
            { day: 'Пт', hours: '11:00 - 20:00' },
            { day: 'Сб', hours: '11:00 - 20:00' },
            { day: 'Вс', hours: '11:00 - 20:00' },
        ];

        return { isModalOpen, form, openModal, closeModal, handleSubmit, branch1Schedule, branch2Schedule };
    },
});
</script>

<style scoped>
/* Кастомные анимации и переходы с Tailwind */
@keyframes modalFade {
    from { opacity: 0; transform: translateY(50px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-modal-fade {
    animation: modalFade 0.5s;
}

/* Для dotted lines в расписании, если нужно, но в Vue мы используем grid без after */
.schedule li:not(:last-child)::after {
    content: '';
    grid-column: 1 / -1;
    border-bottom: 1px dotted #cccccc;
    opacity: 0.5;
    margin: 5px 0;
}
</style>
