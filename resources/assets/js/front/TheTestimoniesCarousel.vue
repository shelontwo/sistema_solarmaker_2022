<template>
  <div>
    <carousel ref="carouselSelector"   
      :navigationEnabled="true"
      :per-page="1"
      :pagination-enabled="true"
      :loop="true"
			:paginationActiveColor="'#6C6264'"
			:paginationColor="'#3D3839'"
      :autoplay="true"
      navigationNextLabel='<img src="/img/next.webp" alt="Próxima imagem" class="d-md-block d-none"/> <img src="/img/next-trace.webp" alt="Próxima imagem" class="d-md-none d-block"/>'
      navigationPrevLabel='<img src="/img/previous.webp" alt="Imagem anterior" class="d-md-block d-none"/> <img src="/img/previous-trace.webp" alt="Imagem anterior" class="d-md-none d-block"/>'
      :autoplay-timeout="5000"
			:scrollPerPage="false"
      >
      <slide v-for="(item, index) in items" :key="index">
				<div class="testimonies-section__wrapper">
					<div class="testimonies-section__image d-flex justify-content-center">
						<img :src="item.image" alt="Depoimento"/>
					</div>
					<p class="testimonies-section__testimony">
						"{{ item.testimony }}"
					</p>
					<p class="testimonies-section__name">
						{{ item.name }}
					</p>
					<p class="testimonies-section__occupation">
						{{ item.occupation }}
					</p>
				</div>
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

function load_teste() {
	$('#teste').load('/teste/');
}

</script>

<style lang="scss" scoped>
@import "resources/assets/sass/website/_variables.scss";

.VueCarousel-slide, .VueCarousel-slider-active {
	width: 100%;
}

.VueCarousel-dot-container {
	button:focus, .VueCarousel-dot, .VueCarousel-dot--active, .VueCarousel-dot:focus, .VueCarousel-dot--active:focus {
		outline: none !important;
	}
}

.VueCarousel-pagination {
	* {
		outline: none !important;
	}
}

.testimonies-section {
	&__wrapper {
		left: 50%;
		max-width: 80%;
		position: relative;
		transform: translate(-50%, 0);
		@media screen and (max-width: 768px) {
			max-width: 100%;
		}
	}
	&__image {
		bottom: 0;
		height: 250px;
		margin: 0 auto;
		position: relative;
		width: 250px;
		img {
			bottom: 0;
			overflow: visible;
			position: absolute;
			z-index: 30;
		}
	}
	&__name {
		color: $green-yellow;
		font-family: $font-four;
		font-style: normal;
		font-weight: normal;
		font-size: 17.0126px;
		line-height: 22px;
		text-align: center;
	}
	&__testimony {
		color: $grey-white;
		font-family: $font-three;
		font-style: normal;
		font-weight: normal;
		font-size: 30px;
		line-height: 32px;
		margin: 40px 0;
		text-align: center;
		@media screen and (max-width: 768px) {
			font-size: 20px;
			line-height: 20px;
			padding: 0 15px;
		}
	}
	&__occupation {
		color: $grey-white;
		font-family: $font-one;
		font-style: normal;
		font-weight: normal;
		font-size: 14.0126px;
		line-height: 18px;
		text-align: center;
	}
}
</style>
