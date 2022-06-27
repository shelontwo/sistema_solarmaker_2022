<template>
  <div>
    <div class="row form-group">
      <label
        class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left"
        >Tipo de desconto*</label
      >
      <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
        <select
          type="text"
          name="discount_type"
          v-model="type_value"
          class="form-control"
          id="discount_type"
        >
          <option value="1" selected>Valor</option>
          <option value="2">Porcentagem</option>
        </select>
      </div>
    </div>
    <div class="row form-group">
      <label
        class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left"
        >Valor*</label
      >
      <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
        <span v-if="type_value == 1">
          <money
            class="form-control"
            v-model="value"
            v-bind="money"
            v-click-outside="SendHidden"
            required
          ></money
        ></span>

        <span class="input-group" v-else>
          <input
            placeholder="%"
            class="form-control"
            aria-label="Nome"
            type="text"
            v-model="percent"
            v-on:blur="SendHidden()"
            required
          />
          <div class="input-group-append">
            <span class="input-group-text">%</span>
          </div>
        </span>
      </div>
    </div>
  </div>
</template>
 
<script>
import vClickOutside from "v-click-outside";
import axios from "axios";
export default {
  props: ["edit"],
  data() {
    return {
      value: "",
      percent: "",
      type_value: 1,
      money: {
        decimal: ",",
        thousands: ".",
        prefix: "R$ ",
        precision: 2,
        masked: false,
      },
    };
  },

  mounted() {
    if (this.edit == 1) {
      this.type_value = document.getElementById("discount_type").value = 1;
      this.value = document.getElementById("temporary_coupon").value;
    }
    if (this.edit == 2) {
      this.type_value = document.getElementById("discount_type").value = 2;
      this.percent = document.getElementById("temporary_coupon").value;
    }
  },

  methods: {
    SendHidden() {
      if (this.type_value == 1) {
        document.getElementById("temporary_coupon").value = this.value;
      } else {
        document.getElementById("temporary_coupon").value = this.percent;
      }
    },
  },
};
</script> 