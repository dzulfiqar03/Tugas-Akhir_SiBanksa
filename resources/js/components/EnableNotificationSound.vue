<script setup>
import { ref, onMounted } from 'vue'

const showBanner = ref(false)

onMounted(() => {
  const enabled = localStorage.getItem('notif_sound_enabled')
  if (!enabled) {
    showBanner.value = true
  }
})

const enableSound = () => {
  const audio = new Audio('/sounds/notification.mp3')
  audio.muted = true

  audio.play().then(() => {
    audio.pause()
    audio.currentTime = 0
    audio.muted = false

    // 🔥 GLOBAL (biar bisa dipakai di mana saja)
    window.notificationAudio = audio
    window.audioUnlocked = true

    localStorage.setItem('notif_sound_enabled', '1')
    showBanner.value = false

    console.log('🔓 Notification sound enabled')
  }).catch(err => {
    console.log('Enable sound failed:', err)
  })
}

const skip = () => {
  localStorage.setItem('notif_sound_enabled', '0')
  showBanner.value = false
}
</script>

<template>

</template>
