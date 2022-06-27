<template>
  <div>
    <carousel ref="carouselSelector"   
      :navigationEnabled="true"
      :pagination-enabled="false"
      :loop="true"
			:autoplay="true"
      navigationNextLabel='<img src="/img/next.webp" alt="Próxima imagem"/>'
      navigationPrevLabel='<img src="/img/previous.webp" alt="Imagem anterior"/>'
			:perPageCustom="[[0, 1], [768, 2], [1024, 2], [1444, 4] ]"
			:autoplayTimeout="2000"
			:scrollPerPage="false"
      >
      <slide v-for="(item, index) in items" :key="index">
				<div class="speakers-section__container">
					<div :style="'background-image:linear-gradient(180deg, rgba(0, 0, 0, 0) 43.23%, rgba(0, 0, 0, 0.72) 100%), url(' + item.image + ');'" class="ui-image--cover speakers-section__speaker">
						<div class="speakers-section__info">
							<p class="speakers-section__name">
								{{ item.name }}
							</p>
							<p class="speakers-section__occupation">
								{{ item.occupation }}
							</p>
						</div>
					</div>
				</div>
      </slide>
			<slide>
				<div class="speakers-section__container">
					<button onclick="load_speakers()" class="speakers-section__interal-button">
						Ver todos os palestrantes
						<div>
							<i class="fa fa-plus"></i>
						</div>
					</button>
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
</script>

<style lang="scss" scoped>
@import "resources/assets/sass/website/_variables.scss";

.VueCarousel-slide, .VueCarousel-slider-active {
	width: 370px;
}

.speakers-section {
	&__container {
		background: $red-pink;
		border-radius: 17px;
		height: 400px;
		margin-left: 15px;
		margin-right: 15px;
	}
	&__speaker {
    background-size: cover;
    background-position: center;
		border-radius: 17px;
		height: 98%;
		position: relative;
		width: 100%;
	}
	&__info {
		position: absolute;
		bottom: 20px;
		padding: 0 20px;
		width: 100%;
	}
	&__name {
		color: white;
		font-family: $font-four;
		font-style: normal;
		font-weight: normal;
		font-size: 24px;
		line-height: 24px;
		word-wrap: break-word;
		margin-bottom: 15px;
	}
	&__occupation {
		color: $green-yellow;
		font-family: $font-three;
		font-style: normal;
		font-weight: normal;
		font-size: 13.6304px;
		line-height: 15px;
		letter-spacing: 0.1em;
		text-transform: uppercase;
	}
}
</style>
