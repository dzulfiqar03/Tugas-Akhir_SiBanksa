<div class="col-span-full mb-6">
                    <div x-data="{ isHovered: false }" class="relative group">
                        <div class="flex items-center justify-between mb-2 px-1">
                            <label class="text-[10px] font-bold text-emerald-600/60 uppercase tracking-[0.2em]">Smart
                                Recognition</label>
                            <span x-show="isUploading"
                                class="text-[10px] text-emerald-500 animate-pulse font-medium italic">Processing...</span>
                        </div>

                        <div @mouseenter="isHovered = true" @mouseleave="isHovered = false"
                            :class="isUploading ? 'bg-emerald-50/50 border-emerald-100' : (isHovered ?
                                'bg-white border-emerald-300 shadow-xl shadow-emerald-500/5' :
                                'bg-gray-50/50 border-gray-100')"
                            class="relative h-24 rounded-2xl border-2 transition-all duration-500 ease-out overflow-hidden flex items-center justify-center group">
                            <input type="file" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                onchange="handleScanDoc(event)">

                            <div x-show="!isUploading" class="flex flex-col items-center gap-1">
                                <div
                                    class="p-2 rounded-full bg-emerald-100/50 text-emerald-600 group-hover:scale-110 transition-transform duration-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-[11px] font-medium text-gray-400 group-hover:text-emerald-700 transition-colors">Tarik
                                    foto atau klik untuk scan</span>
                            </div>

                            <div x-show="isUploading" x-cloak class="flex flex-col items-center gap-2">
                                <div class="relative w-8 h-8">
                                    <div class="absolute inset-0 border-2 border-emerald-100 rounded-full"></div>
                                    <div
                                        class="absolute inset-0 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin">
                                    </div>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-emerald-600 uppercase tracking-tighter">Menganalisa...</span>
                            </div>

                            <div class="absolute -bottom-4 -right-4 w-12 h-12 bg-emerald-200/20 blur-2xl rounded-full">
                            </div>
                        </div>
                    </div>
                </div>