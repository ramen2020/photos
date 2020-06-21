<template>
  <div class="photo">
    <figure class="photo__wrapper">
      <img
        class="photo__image"
        :src="'/img/photo_images/'+item.filename"
        :alt="`Photo by ${item.user.name}`"
      >
    </figure>
  </div>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      required: true
    }
  }
}
</script>