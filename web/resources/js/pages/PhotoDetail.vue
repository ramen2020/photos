<template>
  <div v-if="photo"
    class="photo-detail"
    :class="{ 'photo-detail--column': fullWidth }"
  >
    <figure
      class="photo-detail__pane photo-detail__image"
      @click="fullWidth = ! fullWidth"
    >
      <img :src="'/img/photo_images/'+photo.filename" alt="">
      <figcaption>Posted by {{ photo.user.name }}</figcaption>
    </figure>
    <div class="photo-detail__pane">
      <button
        class="button button--like"
        :class="{ 'button--liked': photo.liked_by_user }"
        title="Like photo"
        @click="onLikeClick"
      >
        <i class="icon ion-md-heart"></i>{{ photo.likes_count }}
      </button>
      <h2 class="photo-detail__title">
        <i class="icon ion-md-chatboxes"></i>Comments
      </h2>
      <ul v-if="photo.comments.length > 0" class="photo-detail__comments">
        <li
          v-for="comment in photo.comments"
          :key="comment.content"
          class="photo-detail__commentItem"
        >
          <p class="photo-detail__commentInfo">
            {{ comment.author.name }} - {{ comment.created_at | moment }}
          </p>
          <p class="photo-detail__commentBody">
            {{ comment.content }}
          </p>
        </li>
      </ul>
      <div v-else>
        <p v-if="isLogin">Let's comment on this photo.</p>
        <p v-else>Let's Log in and comment on this picture!</p>
      </div>
      <form v-if="isLogin" @submit.prevent="addComment" class="form">
        <div v-if="commentErrors" class="errors">
          <ul v-if="commentErrors.content">
            <li v-for="msg in commentErrors.content" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <textarea class="form__item" v-model="commentContent"></textarea>
        <div class="form__button">
          <button type="submit" class="button button--inverse">submit comment</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util'
import moment from 'moment';

export default {
  props: {
    id: {
      type: String,
      required: true
    }
  },
  data () {
    return {
      photo: null,
      fullWidth: false,
      commentContent: '',
      commentErrors: null
    }
  },
  methods: {
    async fetchPhoto () {
      const response = await axios.get(`/api/photos/${this.id}`)

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.photo = response.data
    },
    async addComment () {
      const response = await axios.post(`/api/photos/${this.id}/comments`, {
        content: this.commentContent
      }).catch(err => err.response || err)

      if (response.status === UNPROCESSABLE_ENTITY) {
        this.commentErrors = response.data.errors
        return false
      }

      this.commentContent = ''
      this.commentErrors = null

      if (response.status !== CREATED) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.photo.comments = [
        response.data,
        ...this.photo.comments
      ]
    },
    onLikeClick () {
      if (! this.isLogin) {
        alert('ログインしてください。')
        return false
      }

      if (this.photo.liked_by_user) {
        this.unlike()
      } else {
        this.like()
      }
    },
    async like () {
      const response = await axios.put(`/api/photos/${this.id}/like`)

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.photo.likes_count = this.photo.likes_count + 1
      this.photo.liked_by_user = true
    },
    async unlike () {
      const response = await axios.delete(`/api/photos/${this.id}/like`)

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.photo.likes_count = this.photo.likes_count - 1
      this.photo.liked_by_user = false
    }
  },
  filters: {
    moment: function (date) {
        return moment(date).format('YYYY/MM/DD');
    }
  },
  computed: {
    isLogin () {
      return this.$store.getters['auth/check']
    }
  },
  watch: {
    $route: {
      async handler () {
        await this.fetchPhoto()
      },
      immediate: true
    }
  }
}
</script>