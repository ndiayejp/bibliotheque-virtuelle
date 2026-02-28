<template>
  <div id="app">
    <NavBar v-if="showNav" />
    <main class="main-content">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
  import { computed } from 'vue'
  import { useRoute } from 'vue-router'
  import NavBar from '@/components/layout/NavBar.vue'

  const route = useRoute()
  const showNav = computed(() => !['login', 'register'].includes(route.name))
</script>
