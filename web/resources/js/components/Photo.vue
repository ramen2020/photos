<template>
  <div class="photo">
    <figure class="photo__wrapper">
      <img
        class="photo__image"
        :src="'/img/photo_images/'+item.filename"
        :alt="`Photo by ${item.user.name}`"
      >
    </figure>
    <RouterLink
      class="photo__overlay"
      :to="`/photos/${item.id}`"
      :title="`View the photo by ${item.user.name}`"
    >
    </RouterLink>
    <div class="photo__controls">
      <button
        class="photo__action photo__action--like"
        :class="{ 'photo__action--liked': item.liked_by_user }"
        title="Like photo"
        @click.prevent="like"
      >
        <i class="icon ion-md-heart"></i>{{ item.likes_count }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      required: true
    },
  },
  methods: {
    like () {
      this.$emit('like', {
        id: this.item.id,
        liked: this.item.liked_by_user
      })
    }
  }
}
</script>