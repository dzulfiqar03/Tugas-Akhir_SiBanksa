<!-- <script setup>
import { onMounted, ref } from 'vue';

const show = ref(true);

onMounted(() => {
    // Simulasi loading atau menunggu hingga komponen mounted
    setTimeout(() => {
        show.value = false;
    }, 1000); // Sesuaikan durasi transisi
});
</script>

<template>
    <Transition leave-active-class="transition duration-1000 ease-in-out" leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <div v-if="show"
            class="absolute inset-0 w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 z-[50]">

            <div class="house-spin-container">
                <div class="loader-content">
                    <svg class="house-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path class="house-solid-fill"
                            d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />

                        <path class="house-outline-bg"
                            d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />

                        <path class="house-outline-active"
                            d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                    </svg>

                    <div class="banksa-logo">$</div>
                </div>
            </div>
            <h1 class="pt-5 text-2xl font-semibold text-gray-800 dark:text-gray-100 font-[Poppins] text-center">
                <span class="font-light">SI</span>
                Banksa
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">Memuat halaman...</p>
        </div>
    </Transition>
</template>

<style scoped>
.loader-content {
    position: relative;
    width: 50px;
    height: 50px;
    background: transparent;
}

.house-svg {
    width: 100%;
    height: 100%;
    fill: none;
}

.house-outline-bg {
    fill: none;
    stroke: #e5e7eb;
    stroke-width: 0.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.house-outline-active {
    fill: none;
    stroke: #059669;
    stroke-width: 1.2;
    stroke-linecap: round;
    stroke-linejoin: round;

    stroke-dasharray: 12 50;
    animation: house-spin 2s linear infinite;
}

.house-solid-fill {
    fill: #059669;
    stroke: none;
    opacity: 0.1;
}

@keyframes house-spin {
    from {
        stroke-dashoffset: 62;
    }

    to {
        stroke-dashoffset: 0;
    }
}

.banksa-logo {
    position: absolute;
    top: 52%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.6);
    font-weight: 800;
    color: #059669;
    z-index: 10;
}
</style> -->

<script setup>
import { onMounted, ref } from 'vue';

const show = ref(true);

onMounted(() => {
    setTimeout(() => {
        show.value = false;
    }, 2000);
});
</script>

<template>
    <Transition
        leave-active-class="transition duration-1000 ease-in-out"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="absolute inset-0 w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 z-[50]"
        >

            <!-- SVG LOADER -->
            <div class="loader-content">

                <svg
                    class="sig-svg"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 500 500"
                >

                    <!-- BASE -->
                    <g class="sig-bg">
                        <polygon points="152.82 142.5 129.22 117.82 243.32 5.58 266.92 30.27 152.82 142.5"/>
                        <polygon points="298.24 452.85 321.84 428.16 200.96 309.25 177.36 333.94 298.24 452.85"/>
                        <polygon points="315.32 176.39 338.92 151.71 248.47 62.72 224.87 87.41 315.32 176.39"/>
                        <polygon points="217.99 209.77 194.39 185.09 239.04 141.16 262.64 165.85 217.99 209.77"/>
                        <polygon points="183.94 174.25 160.35 149.57 249.2 62.16 272.8 86.85 183.94 174.25"/>
                        <polygon points="281.07 208.17 304.67 183.49 249.92 129.63 226.32 154.31 281.07 208.17"/>
                        <polygon points="347.35 145.49 370.95 120.81 248.14 0 224.54 24.69 347.35 145.49"/>
                        <polygon points="281.87 274.56 258.27 249.88 372.37 137.64 395.97 162.33 281.87 274.56"/>
                        <polygon points="444.37 308.46 467.97 283.77 377.52 194.79 353.92 219.47 444.37 308.46"/>
                        <polygon points="347.04 341.83 323.44 317.15 368.09 273.23 391.69 297.91 347.04 341.83"/>
                        <polygon points="313 306.32 289.4 281.63 378.25 194.23 401.85 218.91 313 306.32"/>
                        <polygon points="410.12 340.23 433.72 315.55 378.97 261.69 355.37 286.37 410.12 340.23"/>
                        <polygon points="476.4 277.56 500 252.87 377.19 132.06 353.59 156.75 476.4 277.56"/>
                        <polygon points="378.32 373.43 354.72 348.75 475.14 230.29 498.74 254.98 378.32 373.43"/>
                        <polygon points="187.45 306.19 211.05 281.51 120.59 192.52 96.99 217.21 187.45 306.19"/>
                        <polygon points="90.12 339.57 66.52 314.88 111.17 270.96 134.77 295.65 90.12 339.57"/>
                        <polygon points="56.07 304.05 32.47 279.37 121.32 191.96 144.92 216.65 56.07 304.05"/>
                        <polygon points="153.2 337.97 176.8 313.28 122.04 259.42 98.45 284.11 153.2 337.97"/>
                        <polygon points="219.48 275.29 243.08 250.6 120.27 129.8 96.67 154.49 219.48 275.29"/>
                        <polygon points="203.68 451.72 180.08 427.03 299.71 309.35 323.31 334.04 203.68 451.72"/>
                        <polygon points="272.3 283.97 248.33 259.65 129.95 379.74 153.92 404.06 272.3 283.97"/>
                        <polygon points="226.57 283.97 250.54 259.65 368.92 379.74 344.95 404.06 226.57 283.97"/>
                        <polygon points="249.59 499.36 273.19 474.68 152.77 356.21 129.17 380.9 249.59 499.36"/>
                        <polygon points="122.45 370.13 146.05 345.45 23.6 224.99 0 249.68 122.45 370.13"/>
                        <polygon points="24.95 272.3 1.35 247.61 121.06 129.86 144.66 154.54 24.95 272.3"/>
                        <polygon points="249.66 500 226.06 475.31 346.51 356.83 370.11 381.51 249.66 500"/>
                    </g>

                    <!-- ACTIVE -->
                    <g class="sig-active">
                        <polygon points="152.82 142.5 129.22 117.82 243.32 5.58 266.92 30.27 152.82 142.5"/>
                        <polygon points="298.24 452.85 321.84 428.16 200.96 309.25 177.36 333.94 298.24 452.85"/>
                        <polygon points="315.32 176.39 338.92 151.71 248.47 62.72 224.87 87.41 315.32 176.39"/>
                        <polygon points="217.99 209.77 194.39 185.09 239.04 141.16 262.64 165.85 217.99 209.77"/>
                        <polygon points="183.94 174.25 160.35 149.57 249.2 62.16 272.8 86.85 183.94 174.25"/>
                        <polygon points="281.07 208.17 304.67 183.49 249.92 129.63 226.32 154.31 281.07 208.17"/>
                        <polygon points="347.35 145.49 370.95 120.81 248.14 0 224.54 24.69 347.35 145.49"/>
                        <polygon points="281.87 274.56 258.27 249.88 372.37 137.64 395.97 162.33 281.87 274.56"/>
                        <polygon points="444.37 308.46 467.97 283.77 377.52 194.79 353.92 219.47 444.37 308.46"/>
                        <polygon points="347.04 341.83 323.44 317.15 368.09 273.23 391.69 297.91 347.04 341.83"/>
                        <polygon points="313 306.32 289.4 281.63 378.25 194.23 401.85 218.91 313 306.32"/>
                        <polygon points="410.12 340.23 433.72 315.55 378.97 261.69 355.37 286.37 410.12 340.23"/>
                        <polygon points="476.4 277.56 500 252.87 377.19 132.06 353.59 156.75 476.4 277.56"/>
                        <polygon points="378.32 373.43 354.72 348.75 475.14 230.29 498.74 254.98 378.32 373.43"/>
                        <polygon points="187.45 306.19 211.05 281.51 120.59 192.52 96.99 217.21 187.45 306.19"/>
                        <polygon points="90.12 339.57 66.52 314.88 111.17 270.96 134.77 295.65 90.12 339.57"/>
                        <polygon points="56.07 304.05 32.47 279.37 121.32 191.96 144.92 216.65 56.07 304.05"/>
                        <polygon points="153.2 337.97 176.8 313.28 122.04 259.42 98.45 284.11 153.2 337.97"/>
                        <polygon points="219.48 275.29 243.08 250.6 120.27 129.8 96.67 154.49 219.48 275.29"/>
                        <polygon points="203.68 451.72 180.08 427.03 299.71 309.35 323.31 334.04 203.68 451.72"/>
                        <polygon points="272.3 283.97 248.33 259.65 129.95 379.74 153.92 404.06 272.3 283.97"/>
                        <polygon points="226.57 283.97 250.54 259.65 368.92 379.74 344.95 404.06 226.57 283.97"/>
                        <polygon points="249.59 499.36 273.19 474.68 152.77 356.21 129.17 380.9 249.59 499.36"/>
                        <polygon points="122.45 370.13 146.05 345.45 23.6 224.99 0 249.68 122.45 370.13"/>
                        <polygon points="24.95 272.3 1.35 247.61 121.06 129.86 144.66 154.54 24.95 272.3"/>
                        <polygon points="249.66 500 226.06 475.31 346.51 356.83 370.11 381.51 249.66 500"/>
                    </g>

                </svg>
            </div>

            <h1
                class="pt-4 text-xl font-semibold text-gray-800 dark:text-gray-100 font-[Poppins] text-center"
            >
                <span class="font-light">SI</span>
                Banksa
            </h1>

            <p class="mt-1 text-gray-600 dark:text-gray-400 text-sm">
                Memuat halaman...
            </p>

        </div>
    </Transition>
</template>

<style scoped>
/* ========================================
   SVG SIZE
======================================== */
.loader-content {
    position: relative;
    width: 70px;
    height: 70px;
}

/* ========================================
   SVG
======================================== */
.sig-svg {
    width: 100%;
    height: 100%;

    animation:
        svg-float 3s ease-in-out infinite;

    filter:
        drop-shadow(0 0 6px rgba(34, 197, 94, 0.25))
        drop-shadow(0 0 12px rgba(34, 197, 94, 0.15));
}

/* ========================================
   BASE SVG
======================================== */
.sig-bg polygon {
    fill: rgba(34, 197, 94, 0.04);

    stroke: rgba(34, 197, 94, 0.12);
    stroke-width: 3;

    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ========================================
   ACTIVE SVG
======================================== */
.sig-active polygon {
    fill: rgba(34, 197, 94, 0.06);

    stroke: #22c55e;
    stroke-width: 4;

    stroke-linecap: round;
    stroke-linejoin: round;

    opacity: 0;

    animation:
        polygon-fade 2s ease infinite;
}

/* ========================================
   STAGGER ANIMATION
======================================== */
.sig-active polygon:nth-child(1)  { animation-delay: 0s; }
.sig-active polygon:nth-child(2)  { animation-delay: 0.05s; }
.sig-active polygon:nth-child(3)  { animation-delay: 0.1s; }
.sig-active polygon:nth-child(4)  { animation-delay: 0.15s; }
.sig-active polygon:nth-child(5)  { animation-delay: 0.2s; }
.sig-active polygon:nth-child(6)  { animation-delay: 0.25s; }
.sig-active polygon:nth-child(7)  { animation-delay: 0.3s; }
.sig-active polygon:nth-child(8)  { animation-delay: 0.35s; }
.sig-active polygon:nth-child(9)  { animation-delay: 0.4s; }
.sig-active polygon:nth-child(10) { animation-delay: 0.45s; }
.sig-active polygon:nth-child(11) { animation-delay: 0.5s; }
.sig-active polygon:nth-child(12) { animation-delay: 0.55s; }
.sig-active polygon:nth-child(13) { animation-delay: 0.6s; }
.sig-active polygon:nth-child(14) { animation-delay: 0.65s; }
.sig-active polygon:nth-child(15) { animation-delay: 0.7s; }
.sig-active polygon:nth-child(16) { animation-delay: 0.75s; }
.sig-active polygon:nth-child(17) { animation-delay: 0.8s; }
.sig-active polygon:nth-child(18) { animation-delay: 0.85s; }
.sig-active polygon:nth-child(19) { animation-delay: 0.9s; }
.sig-active polygon:nth-child(20) { animation-delay: 0.95s; }
.sig-active polygon:nth-child(21) { animation-delay: 1s; }
.sig-active polygon:nth-child(22) { animation-delay: 1.05s; }
.sig-active polygon:nth-child(23) { animation-delay: 1.1s; }
.sig-active polygon:nth-child(24) { animation-delay: 1.15s; }
.sig-active polygon:nth-child(25) { animation-delay: 1.2s; }
.sig-active polygon:nth-child(26) { animation-delay: 1.25s; }

/* ========================================
   FLOAT
======================================== */
@keyframes svg-float {
    0%, 100% {
        transform: translateY(0px) scale(1);
    }

    50% {
        transform: translateY(-4px) scale(1.03);
    }
}

/* ========================================
   POLYGON GLOW WAVE
======================================== */
@keyframes polygon-fade {
    0% {
        opacity: 0;
        transform: scale(0.92);

        filter:
            drop-shadow(0 0 0px rgba(34,197,94,0));
    }

    50% {
        opacity: 1;
        transform: scale(1);

        filter:
            drop-shadow(0 0 10px rgba(34,197,94,0.8));
    }

    100% {
        opacity: 0.15;
        transform: scale(0.96);

        filter:
            drop-shadow(0 0 4px rgba(34,197,94,0.25));
    }
}
</style>

