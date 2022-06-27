<template>
  <div>
    <carousel ref="carouselSelector"   
      :navigationEnabled="true"
      :pagination-enabled="false"
      :loop="true"
      :autoplay="true"
      navigationNextLabel='<img src="/img/next.webp" alt="Próxima imagem"/>'
      navigationPrevLabel='<img src="/img/previous.webp" alt="Imagem anterior"/>'
     	:perPageCustom="[[0, 1], [768, 2]]"
			:autoplayTimeout="2000"
			:scrollPerPage="false"
      >
      <slide v-for="(item, index) in items" :key="index">
				<div :style="'background-image:url(' + item.image + ');'" :id="index" class="ui-image--cover previous-editions__card"></div>
      </slide>
    </carousel>
  </div>
</template>

<script>
import { Carousel, Slide } from "vue-carousel"
export default {
  props:["items"],
  data() {
    return {
      width: screen.width
    }
  },
  components: {
    Carousel,
    Slide
  },
  methods: {
    navigateTo(index) {
      this.slider = index
      this.$refs.carouselSelector.goToPage(index);
    },
  },
  mounted() {
    setTimeout(() => {
      this.$refs['carouselSelector'].onResize();
      this.$refs['carouselSelector'].goToPage(0);
    }, 200);
  }
}
</script>

<style lang="scss" scoped>
@import "resources/assets/sass/website/_variables.scss";

.VueCarousel-slide, .VueCarousel-slider-active {
	padding-right: 15px;
	padding-left: 15px;
	width: 1000px;
}

.previous-editions {
	&__card {
    background-size: cover;
    background-position: center;
		border-radius: 10px;
		height: 400px;
		width: 100%;
	}
}
</style>
