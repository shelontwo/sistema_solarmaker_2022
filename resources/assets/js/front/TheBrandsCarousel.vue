<template>
  <div class="brands-carousel">
    <carousel ref="carouselSelector"   
      :navigationEnabled="false"
      :pagination-enabled="false"
      :loop="true"
      :autoplay="true"
      navigationNextLabel='<img src="/img/next.webp" alt="Próxima imagem"/>'
      navigationPrevLabel='<img src="/img/previous.webp" alt="Imagem anterior"/>'
			:autoplayTimeout="2000"
			:scrollPerPage="false"
			:perPageCustom="[[375, 2], [425, 3], [768, 4], [1024, 4]]"
      >
      <slide v-for="(item, index) in items" :key="index" class="d-flex justify-content-center brands-section__image">
				<img :src="item.image" :alt="item.name">
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
	width: 370px;
}

.brands-section {
	&__image {
		display: flex !important;
		align-items: center;
		img {
			height: 70%;
    	width: initial;
		}
		@media screen and (max-width: 768px) {
			margin: 0 20px;
			img {
				height: 80%;
				width: initial;
			}
		}
	}
}

.brands-carousel {
	width: 100%;
}
</style>
