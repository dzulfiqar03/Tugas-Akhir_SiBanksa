<template>

    <Head title="Cakap Sampah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="flex h-full bg-gray-100 dark:bg-gray-950 overflow-hidden relative">

            <div v-if="idBtn" @click="idBtn = null" class="fixed inset-0 z-30 bg-transparent"></div>

            <div :class="[
                'w-full md:w-1/3 bg-white dark:bg-gray-900 border-r border-gray-300 dark:border-gray-800 flex flex-col transition-all duration-300 z-30',
                isMobileChatOpen ? '-translate-x-full md:translate-x-0 hidden md:flex' : 'translate-x-0 flex'
            ]">
                <div
                    class="p-4 bg-white dark:bg-gray-900 border-b border-white  dark:border-gray-800 flex justify-between items-center sticky top-0">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold ring-2 ring-emerald-100 dark:ring-emerald-900">
                            {{ $page.props.auth.user.user_detail.fullName?.charAt(0) }}
                        </div>
                        <h1 class="font-bold text-gray-800 dark:text-gray-100 text-lg">Pesan</h1>
                    </div>
                    <button @click="tambahOrang"
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors">
                        <i class="fas fa-user-plus text-gray-500 dark:text-gray-400 hover:text-emerald-600"></i>
                    </button>
                </div>

                <div class="p-4 border-b border-white dark:border-gray-800">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 dark:text-gray-500 text-xs"></i>
                        <input v-model="searchQuery" placeholder="Cari percakapan..."
                            class="w-full bg-gray-50 dark:bg-gray-800 text-black  border-none p-2 pl-9 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 dark:text-gray-200 dark:placeholder-gray-500" />
                    </div>
                </div>

                <div v-if="filteredChatList.length > 0" class="flex-1 overflow-y-auto custom-scrollbar">
                    <div v-for="chat in filteredChatList" :key="chat.id" @contextmenu.prevent="deleteRoom(chat)"
                        @click.stop="pilihChat(chat)"
                        :class="['p-4 flex gap-3 cursor-pointer border-b border-white dark:border-gray-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all relative',
                            activeChat?.id === chat.id ? 'bg-emerald-50 dark:bg-emerald-900/30 border-r-4 border-r-emerald-500' : '']">


                        <div class="relative">
                            <div
                                class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 rounded-full flex items-center justify-center font-bold uppercase shadow-sm">
                                {{ chat.fullName.charAt(0) }}
                            </div>
                            <div v-if="chat.online === 'Online'"
                                class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-gray-900 rounded-full">
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="font-bold text-gray-900 dark:text-gray-100 truncate">{{ chat.fullName
                                    }}</span>
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">{{
                                    chat.user_chat.at(-1)?.time }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate pr-4">
                                    {{ chat.user_chat.at(-1)?.message || 'Belum ada pesan' }}
                                </p>
                                <span v-if="chat.countUnreadMessage > 0"
                                    class="bg-emerald-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-black">
                                    {{ chat.countUnreadMessage }}
                                </span>
                            </div>
                        </div>

                        <div v-if="idBtn === chat.id"
                            class="absolute right-4 top-12 w-48 bg-white dark:bg-gray-800 shadow-2xl rounded-xl border dark:border-gray-700 z-50 overflow-hidden py-2">
                            <button @click.stop="deleteMessage(chat.id)"
                                class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 font-bold">
                                <i class="fas fa-trash mr-2"></i> Hapus Percakapan
                            </button>
                            <div @click.stop="idBtn = null"
                                class="w-full text-left px-4 py-2 text-xs text-black hover:bg-red-50 dark:hover:bg-red-900/20 font-bold">
                                Batal
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="hidden md:flex flex-1 items-center justify-center bg-gray-50 dark:bg-gray-950">
                    <div class="text-center">
                        <div
                            class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 p-2  rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-500">
                            <div class="bg-white w-20 h-20 flex items-center justify-center  rounded-full"> <i
                                    class="fas fa-user text-3xl animate-bounce"></i></div>
                        </div>
                        <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200">Data Tidak Ditemukan</h2>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Klik pada icon <i
                                class="fas fa-user-plus text-black"></i> untuk memulai obrolan</p>
                    </div>
                </div>


            </div>

            <div v-if="activeChat?.id" :class="[
                'flex-1 flex flex-col bg-[#efeae2] dark:bg-gray-950 transition-all duration-300',
                isMobileChatOpen ? 'flex' : isProfileChatOpen === false ? 'hidden md:flex' : 'hidden'
            ]">
                <div
                    class="p-3 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-white dark:border-gray-800 flex items-center gap-3 sticky top-0 z-20 shadow-sm">
                    <p class=" md:hidden dark:text-gray-400">{{ countChat }}</p>
                    <button @click="isMobileChatOpen = false" class="md:hidden p-2 text-emerald-600">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div @click="isProfileChatOpen = true"
                        class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold cursor-pointer">
                        {{ activeChat.fullName.charAt(0) }}
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-gray-800 dark:text-gray-100 leading-tight">{{ activeChat.fullName }}
                        </div>
                        <div class="text-[10px]"
                            :class="activeChat.online === 'Online' ? 'text-emerald-500 font-bold' : 'text-gray-400 dark:text-gray-500'">
                            {{ activeChat.online === 'Online' ? '• Online' : 'Offline' }}
                        </div>
                    </div>
                </div>

                <div ref="chatBody" class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                    <template v-if="detectChat === 'AI Banksa'" v-for="(msg, i) in activeChat.user_chat" :key="i">

                        <template v-if="activeChat.id === 'AI_BOT'">
                            <div class="flex w-full justify-end">
                                <div
                                    class="max-w-[75%] hover:-translate-x-3 duration-75 p-3 rounded-2xl bg-[#dcf8c6] dark:bg-emerald-800 rounded-tr-none shadow-sm">
                                    <p class="text-sm text-black dark:text-white">{{ msg.user_msg }}</p>
                                    <div class="text-[9px] text-right opacity-50 mt-1 uppercase">{{ msg.time }}</div>
                                </div>
                            </div>
                            <div class="flex w-full justify-start">
                                <div
                                    class="max-w-[75%] p-3 hover:translate-x-3 duration-75 rounded-2xl bg-white dark:bg-gray-800 rounded-tl-none border border-gray-100 dark:border-gray-700 shadow-sm">
                                    <p class="text-[10px] font-bold text-emerald-600 mb-1">AI Banksa</p>
                                    <p class="text-sm text-black dark:text-white">{{ msg.message }}</p>
                                    <div
                                        class="text-[9px] text-right opacity-50 text-black dark:text-white mt-1 uppercase">
                                        {{ msg.time }}</div>
                                </div>
                            </div>
                        </template>

                        <div v-else
                            :class="['flex w-full', msg.sender_id === currentUserId ? 'justify-end' : 'justify-start']">
                        </div>

                    </template>

                    <template v-else>
                        <div v-for="(msg, i) in activeChat.user_chat" :key="i"
                            :class="['flex w-full', msg.sender_id === currentUserId ? 'justify-end' : 'justify-start']">

                            <div @contextmenu.prevent="idBtn = msg.id" :class="[
                                'max-w-[75%] p-3 rounded-2xl  shadow-sm cursor-pointer relative',
                                msg.sender_id === currentUserId
                                    ? 'bg-[#dcf8c6] dark:bg-emerald-800  text-black dark:text-emerald-50 rounded-tr-none'
                                    : 'bg-white dark:bg-gray-800  text-black dark:text-gray-100  dark:border-gray-700 rounded-tl-none border border-gray-100',
                                idBtn === msg.id ? 'ring-4 ring-emerald-200 dark:ring-emerald-900 shadow-md' : ''
                            ]">
                                <p class="text-sm leading-relaxed">{{ msg.message }}</p>
                                <div class="text-[9px] text-right text-gray-500 dark:text-gray-400 mt-1 uppercase">{{
                                    msg.time }}</div>

                                <div v-if="idBtn === msg.id" :class="[
                                    'absolute top-full mt-2 w-40 bg-white dark:bg-gray-800 shadow-2xl rounded-xl border dark:border-gray-700 z-50 overflow-hidden',
                                    msg.sender_id === currentUserId ? 'right-0' : 'left-0'
                                ]">
                                    <button v-if="msg.sender_id === currentUserId" @click.stop="updateMessage(msg)"
                                        class="w-full text-left px-4 py-2 text-xs hover:bg-gray-50 dark:hover:bg-gray-700 text-emerald-600 font-bold border-b border-white dark:border-gray-700">
                                        <i class="fas fa-edit mr-2"></i> Edit
                                    </button>
                                    <button v-if="msg.sender_id === currentUserId" @click.stop="deleteMessage(msg.id)"
                                        class="w-full text-left px-4 py-2 text-xs hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 font-bold">
                                        <i class="fas fa-trash mr-2"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>



                <div
                    class="p-4 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-t border-gray-300 dark:border-gray-800">
                    <div class="flex gap-2 items-center bg-gray-100 dark:bg-gray-800 rounded-2xl px-4 py-1">
                        <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Ketik pesan..."
                            class="flex-1 bg-transparent text-black  border-none text-sm py-2 dark:text-gray-100 dark:placeholder-gray-500" />
                        <button @click="sendMessage" :disabled="!newMessage.trim()"
                            class="w-10 h-10 flex items-center justify-center text-emerald-600 disabled:text-gray-300 dark:disabled:text-gray-600 transition-colors">
                            <i class="fas fa-paper-plane text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="hidden md:flex flex-1 items-center justify-center bg-gray-50 dark:bg-gray-950">
                <div class="text-center">
                    <div
                        class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-500">
                        <i class="fas fa-comments text-3xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200">Pilih Percakapan</h2>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Klik pada salah satu chat untuk memulai pesan
                    </p>
                </div>
            </div>

            <div v-if="isProfileChatOpen" :class="[
                'flex-1 flex flex-col bg-[#efeae2] dark:bg-gray-950 transition-all duration-300',
            ]">
                <div
                    class="p-3 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-white dark:border-gray-800 flex items-center gap-3 sticky top-0 z-20 shadow-sm">
                    <button @click="isProfileChatOpen = false" class=" p-2 text-emerald-600">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="flex-1">
                        <div class="font-bold text-gray-800 dark:text-gray-100 leading-tight">Profile Info</div>
                    </div>
                </div>

                <div class="text-center py-5 flex flex-col space-y-3">
                    <div
                        class="w-24 h-24 m-auto bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                        {{ activeChat.fullName.charAt(0) }}
                    </div>
                    <div class="flex-1 flex-col text-black dark:text-gray-100 space-y-4">
                        <div class="font-bold text-gray-800 dark:text-gray-100 leading-tight text-xl">{{
                            activeChat.fullName }}
                        </div>

                        <div
                            class="flex bg-white dark:bg-gray-900 w-max m-auto p-5 rounded-xl shadow dark:shadow-gray-950 items-center justify-center space-x-3">
                            <div class="text-md"
                                :class="activeChat.online === 'Online' ? 'text-emerald-500 font-bold' : 'text-gray-400'">
                                {{ activeChat.online === 'Online' ? '• Online' : 'Offline' }}
                            </div>
                            <span class="text-gray-300 dark:text-gray-700">|</span>
                            <div class="text-md text-gray-800 dark:text-gray-300">{{ activeChat.email }}</div>
                            <span class="text-gray-300 dark:text-gray-700">|</span>
                            <div class="text-md text-gray-800 dark:text-gray-300">RT0{{ activeChat.rt }}</div>
                        </div>

                        <div class="flex flex-col m-auto p-7 justify-between items-start space-y-3">
                            <div
                                class="text-md text-gray-800 dark:text-gray-200 flex p-5 w-full bg-white dark:bg-gray-900 rounded-xl shadow dark:shadow-gray-950 justify-between">
                                <span><i class="fas fa-image mr-2"></i> Media and Docs</span>
                                <span> {{ activeChat.documentCount + activeChat.imageCount }}</span>
                            </div>

                            <div class="w-full">
                                <div
                                    class="text-md flex p-5 w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-t-xl shadow dark:shadow-gray-950 justify-between border-b border-white dark:border-gray-800">
                                    <h1>Mute Sound</h1>
                                    <button type="button" @click="toggleSound" :class="['relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                        notifEnable == '1' ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700']">
                                        <span aria-hidden="true" :class="['inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                            notifEnable == '1' ? 'translate-x-5' : 'translate-x-0']" />
                                    </button>
                                </div>

                                <div
                                    class="text-md flex p-5 w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-b-xl shadow dark:shadow-gray-950 justify-between">
                                    <h1>Dark Mode</h1>
                                    <button type="button" @click="toggleTheme" :class="['relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                        isDark ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700']">
                                        <span aria-hidden="true" :class="['inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                            isDark ? 'translate-x-5' : 'translate-x-0']" />
                                    </button>
                                </div>
                            </div>

                            <div class="w-full">
                                <div
                                    class="bg-white dark:bg-gray-900 rounded-t-xl shadow dark:shadow-gray-950 py-2 border-b border-white dark:border-gray-800">
                                    <button @click.stop="deleteMessage(chat.id)"
                                        class="w-full text-left px-4 py-2 text-md text-red-600 hover:text-black dark:hover:text-white font-bold">
                                        Hapus Percakapan
                                    </button>
                                </div>
                                <div class="bg-white dark:bg-gray-900 rounded-b-xl shadow dark:shadow-gray-950 py-2">
                                    <button type="button" @click="sendLogout"
                                        class="w-full flex items-center gap-3 px-4 py-2 text-md bg-white dark:bg-gray-900 font-bold text-red-600 hover:text-black dark:hover:text-white">
                                        <span class="truncate">Log Out</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import Swal from 'sweetalert2';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
const props = defineProps({
    sidebardata: Object,
    allNasabah: Array,
    nasabahList: Array
})

const page = usePage()
const user = computed(() => page.props.auth?.user);
const userDetail = computed(() => user.value?.user_detail || {});
const statusVerifikasi = computed(() => userDetail.value?.status);

const countChat = computed(() => {
    if (!userDetail.value.user_chat) return 0;

    return userDetail.value.user_chat.filter(msg =>
        msg.is_read === false || msg.is_read === 0
    ).length;
});

const currentUserId = computed(() => page.props.auth.user.id)

const chatTersedia = computed(() => props.allNasabah);
const nasabah = ref([...props.nasabahList])
const activeChat = ref(null)
const newMessage = ref('')
const searchQuery = ref('')

const chatBody = ref(null)
const notifContainer = ref(null);
const showNotif = ref(false);
const idBtn = ref(null);
const isEdit = ref(false);
const isDelete = ref(false);
const chatID = ref(null);
const isMobileChatOpen = ref(false);
const isProfileChatOpen = ref(false);
const detectChat = ref('');



watch(
    () => props.allNasabah,
    val => (chatTersedia.value = [...val]),
    { deep: true }
)

const filteredChatList = computed(() => {
    if (!searchQuery.value) return chatTersedia.value
    return chatTersedia.value.filter(c =>
        c.fullName.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})


const scrollToBottom = async () => {
    await nextTick()
    chatBody.value && (chatBody.value.scrollTop = chatBody.value.scrollHeight)
}

onMounted(scrollToBottom)
watch(() => activeChat.value?.user_chat?.length, scrollToBottom)

const pilihChat = chat => (
    activeChat.value = chat,
    detectChat.value = chat.fullName,
    isMobileChatOpen.value = true,
    router.put(
        route('warga.read-chat', activeChat.value.id),
        { message: newMessage.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                newMessage.value = ''
                scrollToBottom()
            },
            onError: (errors) => {
                console.error("Gagal mengirim pesan:", errors)
                Swal.fire('Error', 'Gagal mengirim pesan ke database', 'error')
            }
        }
    )

)


const sendMessage = () => {
    if (!newMessage.value.trim() || !activeChat.value?.id) return

    isEdit.value === false ?

        router.post(route('warga.add-chat', activeChat.value.id),
            { message: newMessage.value, name: activeChat.value.fullName },
            {
                preserveScroll: true,
                onSuccess: (page) => {
                    newMessage.value = '';

                    const updatedChat = props.allNasabah.find(c => c.id === activeChat.value.id);
                    if (updatedChat) {
                        activeChat.value = updatedChat;
                    }
                    scrollToBottom();
                }
            }
        ) : router.put(
            route('warga.update-chat', activeChat.value.id),
            { message: newMessage.value, id: chatID.value },
            {
                preserveScroll: true,
                onSuccess: () => {
                    newMessage.value = ''
                    isEdit.value = false
                    chatID.value = ''

                    scrollToBottom()
                },
                onError: (errors) => {
                    console.error("Gagal mengirim pesan:", errors)
                    Swal.fire('Error', 'Gagal mengirim pesan ke database', 'error')
                }
            }
        )

}

onClickOutside(chatBody, () => idBtn.value = null);

const updateMessage = (item) => {
    isEdit.value = true;
    newMessage.value = item.message;
    chatID.value = item.id;
};

const deleteRoom = (item) => {
    idBtn.value = idBtn.value === item.id ? null : item.id
    isDelete.value = true;
    chatID.value = item.id;
};

const deleteMessage = (id) => {
    console.log(chatID.value)
    isDelete.value === false ?
        router.delete(
            route('warga.delete-chat', id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    newMessage.value = ''
                    scrollToBottom()
                },
                onError: (errors) => {
                    console.error("Gagal mengirim pesan:", errors)
                    Swal.fire('Error', 'Gagal mengirim pesan ke database', 'error')
                }
            }
        ) : router.delete(
            route('warga.delete-roomChat', chatID.value),
            {
                preserveScroll: true,
                onSuccess: () => {
                    newMessage.value = ''
                    scrollToBottom()
                },
                onError: (errors) => {
                    console.error("Gagal mengirim pesan:", errors)
                    Swal.fire('Error', 'Gagal mengirim pesan ke database', 'error')
                }
            }
        )
}


const tambahOrang = () => {

    const generateListHtml = (searchTerm = '') => {
        const filtered = props.nasabahList.filter(u => {
            const sudahAdaDiChat = chatTersedia.value.some(chat => String(chat.id) === String(u.id));

            const cocokPencarian = u.fullName.toLowerCase().includes(searchTerm.toLowerCase());

            return !sudahAdaDiChat && cocokPencarian;
        }
        )

        if (filtered.length === 0) return '<p class="text-center py-4 text-gray-400 text-xs">Nasabah tidak ditemukan</p>'

        return filtered.map(u => `
      <div onclick="window.selectUser('${u.id}','${u.fullName}')"
           class="p-3 flex items-center gap-3 border-b border-gray-100 dark:border-gray-800 cursor-pointer hover:bg-emerald-50 dark:hover:bg-gray-800 transition-all">
        <div class="relative">
          <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center font-bold uppercase shadow-sm text-sm">
            ${u.fullName.charAt(0)}
          </div>
          <div class="${u.online === 'Online' ? 'absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white dark:border-gray-900 rounded-full' : ''}"></div>
        </div>
        <div class="text-left">
          <p class="text-sm font-bold text-black dark:text-white capitalize leading-none">${u.fullName}</p>
          <p class="text-[10px] text-gray-400 mt-1">${u.online === 'Online' ? 'Online' : 'Offline'}</p>
        </div>
      </div>
    `).join('')
    }

    Swal.fire({
        title: '<span class="text-sm dark:text-gray-100 text-black font-bold">Mulai Chat</span>',
        html: `
      <div class="mb-4 relative">
        <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-xs"></i>
        <input id="swal-search" type="text" placeholder="Cari nasabah..."
               class="w-full bg-gray-50 dark:bg-gray-800 text-black dark:text-white border-none p-2 pl-9 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500">
      </div>
      <div id="nasabah-container" class="max-h-[400px] overflow-y-auto pr-1 custom-scrollbar">
        <div onclick="window.selectUser('0','AI Banksa')"
           class="p-3 flex items-center gap-3 border-b border-gray-100 dark:border-gray-800 cursor-pointer hover:bg-emerald-50 dark:hover:bg-gray-800 transition-all">
        <div class="relative">
          <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center font-bold uppercase shadow-sm text-sm">
           <i class="fas fa-robot"></i>
          </div>
        </div>
 <div class="text-left">
          <p class="text-sm font-bold capitalize  dark:text-white text-black leading-none">AI Banksa</p>
          <p class="text-[10px] text-gray-400 mt-1">Online</p>
        </div>
      </div>
        ${generateListHtml()}
      </div>`,
        showConfirmButton: false,
        width: '400px',
        padding: '1.5em',
        customClass: {
            popup: 'rounded-3xl dark:bg-gray-900 bg-white border dark:border-gray-800 scroll-smooth'
        },
        didRender: () => {
            const searchInput = document.getElementById('swal-search')
            const container = document.getElementById('nasabah-container')

            // Listener pencarian manual
            searchInput.addEventListener('input', (e) => {
                container.innerHTML = generateListHtml(e.target.value)
            })
        }
    })
}
window.selectUser = (id, name) => {
    Swal.close()

    // Pastikan membandingkan UUID sebagai String agar tidak terduplikasi
    let chat = chatTersedia.value.find(c => String(c.id) === String(id))
    if (!chat) {
        chat = {
            id: String(id), // Gunakan UUID di sini
            fullName: name,
            user_chat: []
        }
        chatTersedia.value.unshift(chat)
    }
    activeChat.value = chat
}


const isDark = ref(localStorage.getItem('darkMode') === 'true');
const notifEnable = ref(localStorage.getItem('notif_sound_enabled') || '0');


const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('darkMode', isDark.value);
    updateTheme();
    location.reload()
};

const updateTheme = () => {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};




onMounted(() => {
    updateTheme();
    console.log(notifEnable)

});


const toggleSound = () => {
    const audio = new Audio('/sounds/notification.mp3');

    if (notifEnable.value === '0') {


        audio.muted = true

        audio.play().then(() => {
            audio.pause()
            audio.currentTime = 0
            audio.muted = false

            window.notificationAudio = audio
            window.audioUnlocked = true

            notifEnable.value = '1';
            localStorage.setItem('notif_sound_enabled', '1');

            window.notificationAudio = audio;
            console.log('🔓 Sound Enabled');
        }).catch(err => console.log('Izin ditolak browser', err));

    } else {
        // PROSES MEMATIKAN
        audio.muted = false;
        notifEnable.value = '0';
        localStorage.setItem('notif_sound_enabled', '0');
        console.log('🔒 Sound Disabled');
    }
};



const emit = defineEmits(['update:modelValue'])

const sendLogout = () => {
    Swal.fire({
        title: 'Ingin Logout?',
        text: "Setelah ini akun anda akan logout dan status offline",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Logout!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('logout'), {
                onSuccess: () => Swal.fire('Berhasil!', 'Anda berhasil logout, selamat tinggal.', 'success')
            });
        }
    });
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Chat', url: null }
]
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #10b981;
    border-radius: 10px;
}
</style>
