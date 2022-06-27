<template>
  <div>
    <div class="row form-group">
      <label
        class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left"
        >Único por cliente</label
      >
      <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
        <input
          type="checkbox"
          aria-label="Nome"
          name="unique_use"
          id="unique_use"
          value="1"
          v-model="unique_selected"
          v-on:change="verify()"
        />
      </div>
    </div>
    <div class="row form-group">
      <label
        class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left"
        >Quantidade</label
      >
      <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
        <input
          class="form-control DisableOn"
          aria-label="Nome"
          type="number"
          name="quantity"
          id="quantity"
          v-on:blur="verify()"
          v-model="quantity_use"
          :disabled="unique_selected == true"
          required
        />
      </div>
    </div>
  </div>
</template>
 
<script>
export default {
  props: ["unique_use", "quantity", "error"],
  data() {
    return {
      unique_selected: false,
      quantity_use: "",
    };
  },

  mounted() {
    if (this.unique_use || this.quantity) {
      this.quantity_use = this.quantity;
      if (this.unique_use == 1){
        this.unique_selected = true;
      } 
      if (this.unique_use == 0){
        this.unique_selected = false;
      }
    this.verify();  
    } 
  },

  methods: {
    verify() {
      if (this.unique_selected == true) {
        document.getElementById("temporary_uses").value = false;
      } else {
        document.getElementById("temporary_uses").value = this.quantity_use;
      }
    },
  },
};
</script> 