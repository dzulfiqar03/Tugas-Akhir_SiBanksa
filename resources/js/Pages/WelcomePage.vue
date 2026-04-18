<template>
    <div>

        <Head title="Home Page" />
        <div
            class="relative min-h-screen transition-colors duration-700 bg-[#faf7f2] dark:bg-gray-900 text-[#1a1a1a] dark:text-white overflow-x-hidden font-sans">

            <div class="fixed inset-0 opacity-[0.03] dark:opacity-[0.07] pointer-events-none z-[60] mix-blend-overlay"
                style="background-image: url('data:image/svg+xml,%3Csvg viewBox=\'0 0 200 200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'noiseFilter\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.65\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23noiseFilter)\'/%3E%3C/svg%3E');">
            </div>

            <div class="fixed top-0 left-0 right-0 z-[100] px-4 py-6 md:py-8 pointer-events-none reveal">
                <nav class="mx-auto max-w-[95%] md:max-w-[900px] pointer-events-auto
                    flex items-center justify-between p-1.5 md:p-2
                    bg-white/40 dark:bg-black/40 backdrop-blur-2xl
                    border border-white/40 dark:border-white/10
                    rounded-full shadow-[0_20px_50px_rgba(0,0,0,0.1)]
                    dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)]
                    scale-95 md:scale-100 transition-all duration-500 hover:shadow-emerald-500/10">

                    <div
                        class="flex items-center pl-4 pr-6 border-r border-slate-200/50 dark:border-white/10 group cursor-pointer">
                        <div class="relative">
                            <span
                                class="text-white dark:text-emerald-500 font-black italic tracking-tighter text-xl md:text-2xl transition-transform group-hover:-rotate-3 inline-block">
                                SiBanksa.
                            </span>
                            <span class="absolute -top-1 -right-2 flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center bg-black/5 dark:bg-white/5 rounded-full p-1 gap-1">
                        <button v-for="item in navItems" :key="item.id" @click="scrollTo(item.id)"
                            class="relative px-6 py-2 text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 rounded-full group overflow-hidden"
                            :class="activeSection === item.id ? 'text-emerald-600 dark:text-emerald-400' : 'text-white dark:text-slate-400 hover:text-emerald-500'">

                            <span class="relative z-10">{{ item.name }}</span>

                            <div v-if="activeSection === item.id"
                                class="absolute inset-0 bg-white dark:bg-emerald-500/10 shadow-sm rounded-full transition-all duration-500">
                            </div>
                        </button>
                    </div>

                    <div class="flex items-center">
                        <button @click="goToLogin()"
                            class="relative group flex items-center gap-3 pl-6 pr-2 py-2 bg-[#1a3a2a] dark:bg-emerald-600 rounded-full overflow-hidden transition-all duration-500 hover:pr-6 hover:bg-emerald-500 shadow-lg">
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-white">
                                Portal Login
                            </span>

                            <div
                                class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center transition-all duration-500 group-hover:bg-white group-hover:scale-110">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4 text-white group-hover:text-emerald-600 transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </div>

                            <div
                                class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shine_1.5s_ease-in-out_infinite]">
                            </div>
                        </button>
                    </div>
                </nav>
            </div>

            <section id="hero"
                class="relative reveal h-[90vh] md:h-screen flex flex-col items-center justify-center overflow-hidden animate-in">
                <div class="w-full h-full relative transition-all duration-700 ease-in-out bg-emerald-900 z-20">
                    <div v-for="(slide, index) in slides" :key="index"
                        class="absolute inset-0 transition-opacity duration-1000"
                        :class="currentSlide === index ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                        <img :src="slide.image" class="h-full w-full object-cover opacity-50" alt="Warga">
                        <div class="absolute inset-0 bg-black/60"></div>

                        <div
                            class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-emerald-950/30 to-transparent">
                        </div>



                        <div class="absolute bottom-16 md:bottom-24 left-6 md:left-10 right-6 md:right-10 z-20">
                            <div class="max-w-4xl">
                                <div class="flex items-center gap-4 mb-4">
                                    <span class="h-[1px] w-8 md:w-12 bg-emerald-400"></span>
                                    <p
                                        class="text-[9px] md:text-[10px] font-bold uppercase tracking-[0.4em] text-emerald-400">
                                        Smart Community</p>
                                </div>
                                <h2
                                    class="text-5xl md:text-8xl font-black text-white mb-4 leading-none uppercase tracking-tighter">
                                    {{ slide.title }}
                                </h2>
                                <p class="text-emerald-100/70 text-sm md:text-lg font-light max-w-xl">{{ slide.desc
                                    }}</p>
                            </div>
                        </div>
                    </div>



                    <div class="absolute bottom-6 left-10 z-20 flex gap-1.5">
                        <div v-for="(_, i) in slides" :key="i"
                            :class="['h-1 rounded-full transition-all duration-500', currentSlide === i ? 'w-8 bg-emerald-400' : 'w-2 bg-white/30']">
                        </div>
                    </div>
                </div>
            </section>

            <section id="profile"
                class="relative py-20 md:py-32 px-4 md:px-20 bg-white dark:bg-gray-900 z-30 space-y-10">
                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 md:gap-20 items-start">

                    <div class="relative space-y-6 reveal">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3
                                    class="font-black text-2xl text-gray-800 dark:text-white uppercase tracking-tighter italic">
                                    Ecosystem Map</h3>
                                <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest mt-1">Live
                                    Resident & Unit Distribution</p>
                            </div>
                            <div class="hidden md:flex gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 animate-pulse">
                                    <i class="fas fa-satellite"></i>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col h-[500px] md:h-[650px] overflow-hidden rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-2xl bg-white dark:bg-gray-900 relative">
                            <div class="flex-1 relative">
                                <div id="map" class="h-full w-full z-0"></div>

                                <div
                                    class="absolute bottom-4 left-4 z-[400] bg-white/90 dark:bg-gray-800/90 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 min-w-[120px]">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                            <span
                                                class="text-[9px] font-bold dark:text-gray-400 uppercase">Petugas</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                            <span
                                                class="text-[9px] font-bold dark:text-gray-400 uppercase">Nasabah</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div :class="isPreviewOpen ? 'h-[60%] lg:h-full lg:w-[350px]' : 'h-0 lg:w-0 overflow-hidden'"
                                class="absolute lg:relative bottom-0 left-0 right-0 lg:right-auto transition-all duration-700 ease-in-out bg-white dark:bg-gray-800 z-[500] lg:z-10 border-t lg:border-t-0 lg:border-l border-gray-100 dark:border-white/5 shadow-2xl overflow-y-auto">

                                <div v-if="selectedData" class="p-6 md:p-8 space-y-6">
                                    <div class="flex justify-between items-start">
                                        <div
                                            class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-3xl font-black text-emerald-500 border border-emerald-100 dark:border-emerald-800">
                                            {{ selectedData.fullName.charAt(0) }}
                                        </div>
                                        <button @click="isPreviewOpen = false"
                                            class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                            <i class="fas fa-times text-xl"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-1">
                                        <h4
                                            class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                                            {{ selectedData.fullName }}</h4>
                                        <div class="flex gap-2">
                                            <span
                                                class="px-2 py-0.5 rounded bg-emerald-600 text-white text-[8px] font-black uppercase">{{
                                                    selectedData.roles?.role || 'User' }}</span>
                                            <span
                                                class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[8px] font-black uppercase">RT
                                                0{{ selectedData.rt?.RT }}</span>
                                        </div>
                                    </div>

                                    <div
                                        class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border-l-4 border-emerald-500">
                                        <p
                                            class="text-[9px] text-emerald-600 font-black mb-1 uppercase tracking-widest italic">
                                            Precision Address</p>
                                        <p
                                            class="text-[11px] text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                                            {{ selectedData.address }}</p>
                                    </div>

                                    <a :href="`https://www.google.com/maps/dir/?api=1&destination=${selectedData.location?.open_street?.latitude},${selectedData.location?.open_street?.longitude}`"
                                        target="_blank"
                                        class="flex items-center justify-center gap-3 w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-xl font-black text-[10px] transition-all uppercase tracking-widest shadow-xl shadow-emerald-600/20">
                                        Buka Navigasi <i class="fas fa-location-arrow"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8 md:pt-12 reveal">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <span class="w-8 h-[1px] bg-emerald-600"></span>
                                <h3 class="text-emerald-600 font-black text-xs uppercase tracking-[0.5em]">Vision &
                                    Mission
                                </h3>
                            </div>
                            <h2
                                class="text-5xl md:text-7xl font-black italic uppercase tracking-tighter leading-none mb-6">
                                Smart Grid,<br /> <span class="text-transparent stroke-text opacity-30">Clean
                                    Earth.</span>
                            </h2>
                            <p
                                class="text-slate-600 dark:text-slate-400 text-base md:text-lg leading-relaxed font-light italic">
                                Sistem informasi SiBanksa memungkinkan monitoring real-time terhadap {{
                                    nasabahAll.length }}
                                nasabah aktif. Kami membangun kemandirian ekonomi melalui digitalisasi sampah anorganik.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="feat in features" :key="feat.title"
                                class="flex items-start gap-4 p-5 bg-white dark:bg-white/5 rounded-3xl border border-slate-100 dark:border-white/5 hover:border-emerald-500/30 transition-all group">
                                <span
                                    class="text-2xl p-3 bg-emerald-50 dark:bg-emerald-950/20 rounded-2xl group-hover:scale-110 transition-transform">{{
                                        feat.icon }}</span>
                                <div>
                                    <h4 class="font-bold text-[11px] uppercase tracking-tighter mb-1">{{ feat.title }}
                                    </h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 italic leading-relaxed">{{
                                        feat.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto px-6">

                    <div class="lg:col-span-1 text-center reveal">
                        <div class="flex items-center justify-center gap-3 mb-4">
                            <span class="w-8 h-[1px] bg-emerald-500"></span>
                            <p class="text-emerald-500 text-lg font-black uppercase tracking-[0.5em]">Aktivitas
                            </p>
                        </div>

                        <p class="text-slate-500 text-sm leading-relaxed mb-8 italic">
                            Aktivitas Bank Sampah SiBanksa Perumahan Sidorukun Indah Gresik
                        </p>

                    </div>
                    <div v-for="(item, index) in activities" :key="item.id"
                        class="grid grid-cols-1 lg:grid-cols-3 items-center gap-12 mb-32 last:mb-0">

                        <div :class="[
                            'lg:col-span-2 space-y-6',
                            index % 2 !== 0 ? 'lg:order-last lg:pl-20' : 'lg:pr-20'
                        ]">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="w-8 h-[1px] bg-emerald-500"></span>
                                <p class="text-emerald-500 text-[10px] font-black uppercase tracking-[0.5em]">
                                    {{ item.category || 'Activity' }}
                                </p>
                            </div>

                            <h2
                                class="text-5xl md:text-7xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none">
                                {{ item.title }}
                                <span class="block text-transparent stroke-text opacity-20 text-4xl md:text-6xl">{{
                                    item.subtitle }}</span>
                            </h2>

                            <p class="text-slate-500 dark:text-slate-400 text-lg leading-relaxed italic max-w-2xl">
                                {{ item.desc }}
                            </p>
                        </div>

                        <div class="lg:col-span-1 reveal group">
                            <div
                                class="relative overflow-hidden bg-white dark:bg-[#0a1a12]/50 p-3 rounded-[2.5rem] border border-gray-100 dark:border-emerald-500/10 shadow-2xl transition-all duration-700 group-hover:scale-[1.05] group-hover:border-emerald-500/30">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                                </div>

                                <img :src="item.image" :alt="item.title"
                                    class="w-full h-[400px] object-cover rounded-[2rem] grayscale-[50%] group-hover:grayscale-0 transition-all duration-700">

                                <div
                                    class="absolute bottom-6 right-6 bg-emerald-600/90 backdrop-blur-md px-4 py-2 rounded-xl shadow-lg">
                                    <p class="text-[8px] font-black text-white uppercase tracking-widest">SiBanksa
                                        Archive
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="achievements" class="py-20 md:py-32 bg-[#0a1a12] relative overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 opacity-[0.02] select-none pointer-events-none">
                    <h2 class="text-[25vw] font-black italic leading-none text-white uppercase">Bank Sampah Kami</h2>
                </div>

                <div class="px-6 md:px-20 relative z-10">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-12 mb-16">
                        <div class="max-w-xl">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="w-8 h-[1px] bg-emerald-500"></span>
                                <p class="text-emerald-400 text-[9px] font-black uppercase tracking-[0.5em]">Live
                                    Monitoring
                                </p>
                            </div>
                            <h4
                                class="text-4xl md:text-5xl font-black italic uppercase tracking-tighter text-white leading-none">
                                Active <span class="text-transparent stroke-text opacity-40">Bank Sampah</span>
                            </h4>
                        </div>

                        <div class="flex gap-12 border-l border-white/10 pl-8">
                            <div class="text-center">
                                <p
                                    class="text-4xl md:text-5xl font-black leading-none mb-2 tracking-tighter text-emerald-400">
                                    <span class="counter" :data-target="totalOnline">0</span>
                                </p>
                                <p class="text-[8px] uppercase tracking-[0.3em] text-white/30 font-bold">Online</p>
                            </div>
                            <div class="text-center">
                                <p
                                    class="text-4xl md:text-5xl font-black leading-none mb-2 tracking-tighter text-white">
                                    <span class="counter" :data-target="nasabahAll.length">0</span>
                                </p>
                                <p class="text-[8px] uppercase tracking-[0.3em] text-white/30 font-bold">Unit
                                    Terverifikasi</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative group">
                        <swiper :modules="[Autoplay, FreeMode]" :slides-per-view="1.2" :space-between="16" :loop="true"
                            :free-mode="true" :autoplay="{
                                delay: 3000,
                                disableOnInteraction: false,
                                pauseOnMouseEnter: true
                            }" :breakpoints="{
                                '640': { slidesPerView: 2.2, spaceBetween: 20 },
                                '1024': { slidesPerView: 3, spaceBetween: 24 },
                                '1280': { slidesPerView: 4, spaceBetween: 24 }
                            }" class="!overflow-visible">
                            <swiper-slide v-for="unit in nasabahAll" :key="unit.id" class="h-auto">
                                <div
                                    class="bg-white/[0.03] reveal backdrop-blur-md border border-white/5 p-8 rounded-[2.5rem] hover:bg-white/[0.08] hover:border-emerald-500/30 transition-all duration-500 group/card relative overflow-hidden h-full flex flex-col justify-between">

                                    <div
                                        class="absolute -top-10 -right-10 w-24 h-24 bg-emerald-500/5 rounded-full blur-3xl group-hover/card:bg-emerald-500/20 transition-all">
                                    </div>

                                    <div>
                                        <div class="flex justify-between items-start mb-10">
                                            <p
                                                class="font-mono text-[10px] text-emerald-400/60 tracking-widest uppercase">
                                                RT.0{{ unit.rt?.RT || '0' }}
                                            </p>

                                            <div class="flex items-center  gap-2">
                                                <div
                                                    class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_#10b981]">
                                                </div>

                                                <h1 class="text-white items-center">{{ unit.is_online ? 'Online' :
                                                    'Offline'
                                                    }}</h1>

                                            </div>

                                        </div>

                                        <h5
                                            class="text-xl font-bold text-white leading-tight uppercase tracking-tight group-hover/card:text-emerald-400 transition-colors mb-2 italic">
                                            {{ unit.fullName.replace('Bank Sampah', '').replace('Petugas', '') }}
                                        </h5>
                                        <p class="text-[9px] text-white/20 uppercase tracking-[0.2em] mb-8 font-bold">
                                            Official Unit RT.0{{ unit.rt?.RT }}</p>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="flex items-end justify-between">
                                            <div>
                                                <p class="text-[8px] text-white/30 uppercase tracking-widest mb-1">
                                                    Cakupan
                                                    Nasabah</p>

                                                <span class="counter text-2xl font-black text-white leading-none italic"
                                                    :data-target="getNasabahCountByRT(unit.id_rt)">0</span>

                                                <span class="text-xl font-bold text-white"> KK</span>


                                            </div>
                                            <div class="text-right">
                                                <p class="text-[8px] text-white/30 uppercase tracking-widest mb-1">ID
                                                    UNIT
                                                </p>
                                                <p class="text-[10px] font-black text-emerald-500">{{ unit.id_rt }}</p>
                                            </div>
                                        </div>

                                        <div class="w-full bg-white/5 h-[3px] rounded-full overflow-hidden">
                                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-[2000ms]"
                                                :style="{ width: Math.min((unit.pencatatan?.length || 0) * 10, 100) + '%' }">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </swiper-slide>
                        </swiper>
                    </div>
                </div>
            </section>


            <section id="analytics" class="py-20 bg-white dark:bg-gray-900">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-center">

                        <div class="lg:col-span-1">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="w-8 h-[1px] bg-emerald-500"></span>
                                <p class="text-emerald-500 text-[9px] font-black uppercase tracking-[0.5em]">Tumbuh
                                    Bersama
                                </p>
                            </div>
                            <h2
                                class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-6">
                                Statistik <br><span class="text-emerald-500">Ekosistem.</span>
                            </h2>
                            <p class="text-slate-500 text-sm leading-relaxed mb-8 italic">
                                Visualisasi volume sampah yang berhasil dikelola oleh seluruh unit SiBanksa setiap
                                bulannya.
                            </p>

                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/10">
                                    <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Total</p>
                                    <span class="counter uppercase text-xl font-bold"
                                        :data-target="props.beratSampah">{{ props.beratSampah }}</span>
                                    <span class="text-md font-bold text-emerald-500"> Kg Sampah</span>
                                </div>
                                <div
                                    class="p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/10">
                                    <p class="text-[10px] text-emerald-500 uppercase font-bold mb-1">Growth</p>
                                    <p class="text-xl font-black"
                                        :class="growthSampah.isUp ? 'text-emerald-500' : 'text-red-500'">

                                        <span class="text-md font-bold text-black dark:text-white/50">
                                            {{ growthSampah.value > 100 ? 'Lonjakan Pesat' : 'Pertumbuhan' }}
                                        </span>
                                    <h2 v-if="growthSampah.value <= 100" :data-target="growthSampah.value"
                                        class="counter {{ growthSampah.value > 0 ? 'text-green-500' : 'text-red-500' }}">
                                        0%
                                    </h2>


                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="lg:col-span-2 bg-white dark:bg-[#0a1a12]/50 p-6 md:p-8 rounded-[2.5rem] border border-gray-100 dark:border-emerald-500/10 shadow-2xl">
                            <div class="flex items-center justify-between mb-8">
                                <h4 class="text-xs font-black uppercase tracking-widest text-gray-400">Monthly Volume
                                    (Kg)
                                </h4>
                                <div class="flex gap-2">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-500/30"></span>
                                </div>
                            </div>
                            <VueApexCharts class="reveal" type="area" height="350" :options="chartOptions"
                                :series="series" />
                        </div>

                    </div>
                </div>
            </section>


            <section id="waste-list" class="py-24 bg-white dark:bg-gray-900">
                <div class="max-w-7xl mx-auto px-6">

                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
                        <div>
                            <p class="text-emerald-500 text-[10px] font-black uppercase tracking-[0.5em] mb-4">Material
                                Directory</p>
                            <h2
                                class="text-5xl font-black italic uppercase tracking-tighter dark:text-white leading-none">
                                Katalog <span class="text-transparent stroke-text opacity-30">Sampah.</span>
                            </h2>
                        </div>

                        <div
                            class="flex p-1.5 bg-gray-100 dark:bg-white/5 rounded-full border border-slate-200 dark:border-white/10 w-fit">
                            <button @click="setTab('Daur Ulang')"
                                :class="activeTab === 'Daur Ulang' ? 'bg-emerald-500 text-white shadow-lg' : 'text-slate-500 hover:text-emerald-500'"
                                class="px-8 py-3 rounded-full text-[10px] font-black uppercase tracking-widest transition-all duration-500">
                                Daur Ulang
                            </button>
                            <button @click="setTab('Non Daur Ulang')"
                                :class="activeTab === 'Non Daur Ulang' ? 'bg-orange-500 text-white shadow-lg' : 'text-slate-500 hover:text-orange-500'"
                                class="px-8 py-3 rounded-full text-[10px] font-black uppercase tracking-widest transition-all duration-500">
                                Non Daur Ulang
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <div v-for="value in displayData" :key="value.id"
                                class="group reveal p-10 bg-gray-50 dark:bg-white/5 rounded-[3rem] border border-transparent hover:border-emerald-500/30 transition-all duration-500 animate-in fade-in zoom-in">

                                <div class="flex justify-between items-start mb-8">
                                    <div :class="activeTab === 'Daur Ulang' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-orange-500/10 text-orange-500'"
                                        class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                                        <i :class="value.icon || 'fas fa-box'"></i>
                                    </div>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">ID-{{
                                        value.id }}</span>
                                </div>

                                <h4
                                    class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight group-hover:text-emerald-400 transition-colors mb-2 italic">
                                    {{ value.nama_sampah }}
                                </h4>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 italic leading-relaxed">
                                    Kategori: {{ value.kategori }}
                                </p>
                            </div>
                        </div>

                        <div v-if="filteredSampah.length > itemsPerPage" class="mt-16 flex justify-center">
                            <button @click="nextSlide"
                                class="group flex items-center gap-4 px-10 py-4 bg-[#1a3a2a] dark:bg-white/5 rounded-full border border-white/10 hover:bg-emerald-600 transition-all duration-500 shadow-xl">
                                <span class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Muat
                                    Lainnya</span>
                                <div
                                    class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center group-hover:translate-x-2 transition-transform">
                                    <i class="fas fa-chevron-right text-[10px] text-white"></i>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>
            </section>

            <section id="faq" class="py-24 md:py-40 bg-[#0a1a12] relative overflow-hidden">
                <div class="absolute top-0 right-1/2 translate-x-1/2 opacity-[0.02] select-none pointer-events-none">
                    <h2 class="text-[25vw] font-black italic leading-none text-white uppercase">Sidorukun Indah Gresik
                    </h2>
                </div>

                <div class="max-w-4xl mx-auto px-6 relative z-10">
                    <div class="text-center mb-20">
                        <p class="text-emerald-500 text-[10px] font-black uppercase tracking-[0.6em] mb-4">Inquiry
                            Center
                        </p>
                        <h2 class="text-5xl md:text-6xl font-black italic uppercase tracking-tighter text-white">
                            Common <span class="text-transparent stroke-text opacity-30">Questions.</span>
                        </h2>
                    </div>

                    <div class="space-y-6">
                        <div v-for="(item, index) in faqs" :key="index" @click="toggleFaq(index)"
                            class="group reveal relative overflow-hidden bg-white/[0.02] hover:bg-white/[0.04] backdrop-blur-md rounded-[2rem] border border-white/5 hover:border-emerald-500/30 transition-all duration-500 cursor-pointer p-1">

                            <div class="p-6 md:p-10">
                                <div class="flex items-center justify-between gap-6">
                                    <div class="flex items-center gap-6">
                                        <span
                                            class="text-xs font-black italic text-emerald-500/40 group-hover:text-emerald-500 transition-colors">
                                            0{{ index + 1 }}
                                        </span>
                                        <h5
                                            class="font-bold text-sm md:text-lg uppercase tracking-tight text-white group-hover:translate-x-1 transition-transform duration-500">
                                            {{ item.question }}
                                        </h5>
                                    </div>

                                    <div :class="activeIndex === index ? 'rotate-180 bg-emerald-500 text-white' : 'bg-white/5 text-emerald-500'"
                                        class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500 border border-white/10">
                                        <i class="fas fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>

                                <div :class="activeIndex === index ? 'max-h-[300px] opacity-100 mt-8' : 'max-h-0 opacity-0'"
                                    class="overflow-hidden transition-all duration-700 ease-in-out">
                                    <div class="pl-12 pr-6 border-l-2 border-emerald-500/30">
                                        <p class="text-xs md:text-sm text-slate-400 leading-relaxed italic">
                                            {{ item.answer }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="absolute inset-0 bg-gradient-to-r from-emerald-500/0 via-emerald-500/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer
                class="pt-32 pb-10 bg-white dark:bg-gray-900 border-t border-slate-100 dark:border-white/5 text-center relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[1em] text-emerald-600 mb-8">Ready to join the
                        movement?</p>
                    <div class="flex justify-center gap-4 mb-20 px-6 reveal">
                        <button
                            onclick="window.open('https://wa.me/6281252218959?text=Halo%20Ketua%20RW%2C%20saya%20ingin%20bertanya%20terkait%20Sistem%20Bank%20Sampah%20SiBanksa.', '_blank')"
                            class="px-10 md:px-14 py-4 md:py-5 bg-[#1a3a2a] text-white font-black uppercase tracking-widest text-[10px] rounded-full hover:bg-emerald-600 transition-all transform hover:-translate-y-1 shadow-2xl">
                            Hubungi Ketua RW <i class="fab fa-whatsapp ml-2"></i>
                        </button>
                    </div>
                    <div class="opacity-5 dark:opacity-10 pointer-events-none select-none">
                        <h2 class="text-[15vw] font-black italic uppercase tracking-tighter leading-none">SiBanksa.</h2>
                    </div>
                    <div class="mt-16 px-6 flex flex-col md:flex-row justify-between items-center gap-6 text-slate-400">
                        <p class="text-[10px] uppercase tracking-[0.3em] font-medium italic">© {{ new
                            Date().getFullYear() }} Sidorukun Indah •
                            Ecosystem Digitized</p>
                        <div class="flex gap-8 text-[10px] font-black uppercase tracking-widest">
                            <a href="#" class="hover:text-emerald-600 transition-colors">Documentation</a>
                            <a href="#" class="hover:text-emerald-600 transition-colors">Privacy</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <button @click="toggleTheme"
        class="fixed z-50 bottom-6 right-6 w-14 h-14 bg-emerald-500 rounded-full text-white shadow-lg flex items-center justify-center hover:scale-110 transition-transform">
        {{ isDark ? '☀️' : '🌙' }}
    </button>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import 'swiper/css';
import 'swiper/css/free-mode';
import { Autoplay, FreeMode } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    nasabahAll: {
        type: Array,
        default: () => []
    },
    nasabah: {
        type: Object,
        default: () => ({})
    },
    chartData: Object,
    sampah: {
        type: Object,
        default: () => ({})
    },
    beratSampah: Number
});


const chartOptions = {
    chart: {
        type: 'area',
        toolbar: { show: false },
        zoom: { enabled: false },
        fontFamily: 'Inter, sans-serif'
    },
    colors: ['#10b981'], // Emerald-500
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 3 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.05,
            stops: [0, 90, 100]
        }
    },
    grid: {
        borderColor: 'rgba(255, 255, 255, 0.05)',
        strokeDashArray: 4,
    },
    xaxis: {
        categories: props.chartData?.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        labels: { style: { colors: '#64748b', fontWeight: 600 } }
    },
    yaxis: {
        labels: { style: { colors: '#64748b' } }
    },
    tooltip: { theme: 'dark' }
};

const series = [{
    name: 'Total Sampah (Kg)',
    data: props.chartData?.series || [30, 40, 35, 50, 49, 60]
}];

// MAP STATE
let map = null;
let markerLayer = null;
const isPreviewOpen = ref(false);
const selectedData = ref(null);
const detailMapContainer = ref(null);



const getNasabahCountByRT = (idRt) => {
    return props.nasabah.filter(n => n.id_rt === idRt).length;
};

// Total aktivitas dari seluruh unit yang terdaftar di slider
const totalAllNasabah = computed(() => {
    return props.nasabahAll.reduce((acc, unit) => {
        return acc + (unit.pencatatan?.length || 0);
    }, 0);
});

const totalOnline = computed(() => {
    return props.nasabahAll.filter(unit => unit.is_online).length;
});



// SLIDESHOW
const currentSlide = ref(0);
const slides = [
    {
        image: 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?q=80&w=2070',
        title: 'Gotong Royong',
        desc: 'Membangun lingkungan yang bersih dan nyaman bersama.'
    },
    {
        image: 'https://images.unsplash.com/photo-1577495508048-b635879837f1?q=80&w=1974',
        title: 'Kegiatan Warga',
        desc: 'Transparansi dana RT untuk kesejahteraan kita semua.'
    },
    {
        image: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=2070',
        title: 'Musyawarah Digital',
        desc: 'Sistem Informasi Bank Sampah & Keuangan Terpadu.'
    }
];

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % slides.length;
};
const prevSlide = () => {
    currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length;
};


const activities = [
    {
        id: 1,
        category: 'Core Process',
        title: 'Setor Sampah',
        subtitle: 'Digitalized Collection',
        image: 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?q=80&w=2070',
        desc: 'Proses penimbangan sampah anorganik yang terintegrasi langsung ke sistem. Petugas menginput berat sampah, dan saldo nasabah otomatis bertambah secara real-time.'
    },
    {
        id: 2,
        category: 'Financial',
        title: 'Pencairan Tabungan',
        subtitle: 'Transparent Economy',
        image: 'https://images.unsplash.com/photo-1577495508048-b635879837f1?q=80&w=1974',
        desc: 'Warga dapat mencairkan hasil tabungan sampah mereka untuk kebutuhan iuran bulanan atau tunai. Seluruh histori transaksi terekam transparan untuk menjaga kepercayaan komunitas.'
    },
    {
        id: 3,
        category: 'Community',
        title: 'Musyawarah Digital',
        subtitle: 'Smart Governance',
        image: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=2070',
        desc: 'Laporan volume sampah dan statistik lingkungan disajikan dalam dashboard interaktif, memudahkan pengurus RT dalam mengambil keputusan berbasis data untuk lingkungan.'
    }
];

// FEATURES
const features = [
    { icon: '🏡', title: 'Smart Territory', desc: 'Pemetaan digital seluruh hunian untuk pelayanan presisi.' },
    { icon: '🤝', title: 'Gotong Royong 2.0', desc: 'Sistem iuran dan tabungan sampah yang transparan & otomatis.' },
    { icon: '♻️', title: 'Circular Economy', desc: 'Konversi sampah menjadi saldo tabungan yang bernilai ekonomi.' },
    { icon: '📊', title: 'Real-time Data', desc: 'Pantau volume sampah dan nasabah aktif secara langsung dari dashboard.' }
];


const activeTab = ref('Daur Ulang');

const currentPage = ref(0);
const itemsPerPage = 6;

const filteredSampah = computed(() => {
    return props.sampah.filter(s =>
        (s.kategori || '').toLowerCase() === activeTab.value.toLowerCase()
    );
});

const displayData = computed(() => {
    const start = currentPage.value * itemsPerPage;
    return filteredSampah.value.slice(start, start + itemsPerPage);
});

const setTab = (tabName) => {
    activeTab.value = tabName;
    currentPage.value = 0;
};

const nextSlides = () => {
    const maxPage = Math.ceil(filteredSampah.value.length / itemsPerPage) - 1;
    if (currentPage.value < maxPage) {
        currentPage.value++;
    } else {
        currentPage.value = 0;
    }
};

const growthSampah = computed(() => {
    const val = props.chartData?.growth || 0;
    return {
        value: Math.abs(val), // Untuk counter
        isUp: val >= 0,       // Untuk warna & panah
        raw: val
    };
});

const activeIndex = ref(null);

const toggleFaq = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index;
};
const faqs = [
    {
        question: "Bagaimana cara menjadi nasabah SiBanksa?",
        answer: "Warga cukup mendatangi unit Bank Sampah di RT masing-masing dengan membawa sampah yang sudah dipilah dan KTP untuk pendaftaran akun oleh petugas."
    },
    {
        question: "Kapan saldo tabungan bisa dicairkan?",
        answer: "Saldo tabungan dapat dicairkan kapan saja sesuai jadwal operasional masing-masing unit RT atau dikonversi menjadi iuran warga bulanan."
    },
    {
        question: "Apakah sampah harus dibersihkan terlebih dahulu?",
        answer: "Ya, disarankan botol plastik atau kaleng dibilas sedikit agar tidak meninggalkan bau dan mempermudah proses penimbangan di unit."
    }
];

const navItems = [
    { name: 'Beranda', id: 'hero' },
    { name: 'Tentang', id: 'profile' },
    { name: 'Statistik', id: 'achievements' },
    { name: 'Analytics', id: 'analytics' }
];



const activeSection = ref('hero');
// SCROLL & MAP HELPERS
const scrollTo = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' });
    activeSection.value = id;
};

window.getData = (base64) => {
    const row = JSON.parse(decodeURIComponent(escape(atob(base64))));
    selectedData.value = row;
    isPreviewOpen.value = true;
};



const renderMarkers = () => {
    if (!map || !props.nasabahAll.length) return;
    if (markerLayer) markerLayer.clearLayers();

    markerLayer = L.layerGroup().addTo(map);
    const activeMarkers = [];

    props.nasabahAll.forEach(nasabah => {
        const loc = nasabah.location?.open_street;
        if (!loc?.latitude || !loc?.longitude) return;

        const lat = parseFloat(loc.latitude);
        const lng = parseFloat(loc.longitude);
        activeMarkers.push([lat, lng]);

        const base64Data = btoa(unescape(encodeURIComponent(JSON.stringify(nasabah))));
        const markerColor = nasabah.id_roles === 2 ? '#ef4444' : '#10b981';

        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div onclick="window.getData('${base64Data}')" style="background:${markerColor}" class="w-7 h-7 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-[9px] font-black cursor-pointer hover:scale-125 transition-transform">${nasabah.fullName.charAt(0)}</div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });

        L.marker([lat, lng], { icon: customIcon }).addTo(markerLayer);
    });

    if (activeMarkers.length > 0) {
        map.fitBounds(L.latLngBounds(activeMarkers), { padding: [40, 40] });
    }
};

let slideInterval;

const isDark = ref(false)

onMounted(() => {
    slideInterval = setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % slides.length;
    }, 6000);

    setTimeout(async () => {
        await nextTick();
        const mapEl = document.getElementById('map');
        if (!mapEl) return;

        map = L.map(mapEl, { zoomControl: false }).setView([-7.168, 112.659], 18);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        renderMarkers();
    }, 2100);


    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const animateCounter = (el) => {
        const target = parseInt(el.getAttribute('data-target'));
        if (isNaN(target)) return; // Validasi agar tidak NaN

        const duration = 2000;
        const startTime = performance.now();

        const updateNumber = (currentTime) => {
            const elapsedTime = currentTime - startTime;
            if (elapsedTime < duration) {
                const progress = elapsedTime / duration;
                const easeOut = 1 - Math.pow(1 - progress, 3);
                el.innerText = Math.floor(easeOut * target);
                requestAnimationFrame(updateNumber);
            } else {
                el.innerText = target;
            }
        };
        requestAnimationFrame(updateNumber);
    };

    // 2. Observer untuk Animasi Muncul (Reveal) & Counter
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;

                // Tambahkan class active untuk semua elemen yang masuk layar
                el.classList.add('active');

                // KHUSUS untuk .counter: Jalankan animasi angka & stop observe agar tidak mengulang
                if (el.classList.contains('counter')) {
                    animateCounter(el);
                    observer.unobserve(el);
                }
            }
        });
    }, observerOptions);

    // Daftarkan semua elemen ke satu observer
    const targets = document.querySelectorAll('.counter, .reveal, .reveal-card');
    targets.forEach(t => observer.observe(t));

    const saved = localStorage.getItem('darkMode')

    if (saved !== null) {
        isDark.value = saved === 'true'
    } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }

    document.documentElement.classList.toggle('dark', isDark.value)
});

watch(isDark, (val) => {
    localStorage.setItem('darkMode', val)
    document.documentElement.classList.toggle('dark', val)
})



const toggleTheme = () => {
    isDark.value = !isDark.value
}

onUnmounted(() => clearInterval(slideInterval));

const goToLogin = () => {
    // Menyimpan data
    localStorage.setItem('is_true', true);

    router.visit('/login');
};

</script>

<style scoped>
.stroke-text {
    -webkit-text-stroke: 1px currentColor;
}

.custom-marker {
    background: none !important;
    border: none !important;
}

.animate-in {
    animation: zoomOut 0.4s ease-out forwards;
}

@keyframes zoomOut {
    from {
        transform: scale(1.05);
        opacity: 0;
    }

    to {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

section {
    animation: fadeIn 1s ease-out both;
}

/* Custom Scrollbar for Map Preview */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #10b981;
    border-radius: 10px;
}

.stroke-text {
    -webkit-text-stroke: 1px rgba(255, 255, 255, 0.2);
}

/* Memastikan kartu yang terpotong di tepi tetap terlihat bagus */
.swiper {
    padding-bottom: 50px !important;
}

/* Animasi Hover Smooth */
.group\/card:hover {
    transform: translateY(-10px);
}


.reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1);
}

.reveal.active {
    opacity: 1;
    transform: translateY(0);
}

/* Efek untuk kartu dengan delay staggered */
.reveal-card {
    opacity: 0;
    transform: translateY(50px) scale(0.95);
    transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: var(--delay);
}

.reveal-card.active {
    opacity: 1;
    transform: translateY(0) scale(1);
}


.counter {
    display: inline-block;
    transition: all 0.5s ease-out;
}

/* Optional: Tambahkan efek glow pada angka emerald */
.text-emerald-400 .counter {
    text-shadow: 0 0 20px rgba(52, 211, 153, 0.3);
}
</style>
