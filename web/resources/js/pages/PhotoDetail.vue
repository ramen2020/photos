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
      <button class="button button--like" title="Like photo">
        <i class="icon ion-md-heart"></i>12
      </button>
      <h2 class="photo-detail__title">
        <i class="icon ion-md-chatboxes"></i>Comments
      </h2>
      <ul class="photo-detail__comments">
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
      <form @submit.prevent="addComment" class="form">
        <textarea class="form__item" v-model="commentContent"></textarea>
        <div class="form__button">
          <button type="submit" class="button button--inverse">submit comment</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { OK } from '../util'
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
      commentContent: ''
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
      })

      this.commentContent = ''

      this.photo.comments = [
        response.data,
        ...this.photo.comments
      ]
    }
  },
  filters: {
    moment: function (date) {
        return moment(date).format('YYYY/MM/DD');
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