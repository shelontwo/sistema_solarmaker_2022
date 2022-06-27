<template>
  <a class="log-out" v-on:click="Logout()"> Sair </a>
</template>
<style lang="scss">
.log-out {
  color: #231f20 !important;
  &:hover {
    color: #6f39ff !important;
		cursor: pointer;
  }
}
</style>
<script>
import axios from "axios";
export default {
  data() {
    return {};
  },
  methods: {
    Logout() {
      this.$swal({
        title: "<span style='color:white;'>Realmente deseja sair?</span>",
        showCancelButton: true,
        confirmButtonText: `Sair`,
        cancelButtonText: `Cancelar`,
        background: "#231F20",
      }).then((result) => {
        if (result.value) {
          localStorage.clear();
          location.href = "/";
        }
      });
    },
  },
};
</script>