<template>
  <div>
    <header>
      <Navbar />
    </header>
    <main>
      <div class="container">
        <RouterView />
      </div>
    </main>
    <Footer />
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import { INTERNAL_SERVER_ERROR } from './util'

export default {
  components: {
    Navbar,
    Footer
  },
  computed: {
    errorCode () {
      return this.$store.state.error.code
    }
  },
  watch: {
    errorCode: {
      immediate: true,
      handler (val) {
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push('/error')
        }
      }
    },
    $route () {
      this.$store.commit('error/setCode', null)
    }
  }
}
</script>