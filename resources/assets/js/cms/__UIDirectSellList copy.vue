<template>
  <div>
    <select
      type="text"
      name="seller_selected"
      v-model="seller_selected"
      class="form-control col-sm-5 col-12"
      id="seller_selected"
      v-on:change="CheckSeller()"
    >
      <option value="0">Todas as Vendas</option>
      <option
        v-for="(seller, index) in sellers_list"
        :key="index"
        :value="seller.seller_id"
      >
        {{ seller.seller_name }}
      </option>
    </select>
    <div style="padding: 10px 15px 10px 15px">
      <div
        v-for="(item, index) in sells"
        :key="index"
        class="d-flex row list-box"
      >
        <span class="col-12"
          ><b>#</b>{{ item[0].cart_id }} - {{ item[0].client_email }}</span
        >
        <span class="col-12 mt-3">{{ item[0].client_name }}</span>
        <span class="col-12">{{ item[0].created }}</span>
        <span class="col-12 mt-3"
          ><b>Vendedor:</b> {{ item[0].seller_name }}</span
        >
        <div class="col-12 sell-data">
          <span
            ><b>{{ item[0].total_quantity }}</b>
            {{ item[0].total_quantity == 1 ? "Item" : "Itens" }} no valor de:
            <b>{{
              item[0].total_price.toLocaleString("pt-br", {
                minimumFractionDigits: 2,
								maximumFractionDigits: 2,
              })
            }}</b
            >, pelo valor cobrado de: <b>{{ item[0].price }}</b></span
          >
          <span class="sell-details" v-on:click="Show()">Ver detalhes</span>
        </div>
      </div>
    </div>
    <pagination
      :records="records"
      v-model="page"
      :per-page="10"
      @paginate="callback"
    >
    </pagination>
  </div>
</template>


<script>
import axios from "axios";
export default {
  props: ["direct_sells", "sellers"],
  data() {
    return {
      sells: "",
      sellers_list: "",
      seller_selected: "",
      all: "",
      page: 1,
      firstPage: "",
      endPage: "",
      records: 0,
    };
  },

  created() {
    this.sells = JSON.parse(this.direct_sells);
    this.all = Object.keys(this.sells).map((key) => this.sells[key]);
    this.sells = this.all;
    this.sellers_list = JSON.parse(this.sellers);
    this.records = this.sells.length;
    this.callback();
  },

  methods: {
    callback() {
      if (this.seller_selected <= 0) {
        this.endPage = this.page * 10;
        this.firstPage = this.endPage - 10;
        this.sells = this.all.slice(this.firstPage, this.endPage);
      } else {
        this.CheckSeller();
        this.callback();
      }
    },

    Show() {
      this.$swal();
    },

    CheckSeller() {
      if (this.seller_selected == 0) {
        this.sells = this.all;
      } else {
        var items = this.all;
        var result = Object.keys(items)
          .map((key) => items[key])
          .filter((item) =>
            item.some((item) => item.seller_id === this.seller_selected)
          );

        if (result.length > 0) {
          this.sells = result;
        } else {
          this.$swal("Oops!", "Este vendedor não possui vendas", "error");
          this.sells = this.all;
          this.seller_selected = 0;
        }
      }
    },
  },
};
</script> 
<style lang="scss">
.list-box {
  border: 1px solid transparent;
  background-color: #eee;
  box-shadow: 1px;
  padding: 10px 15px;
  background-image: linear-gradient(to bottom, #f5f5f5 0, #e8e8e8 100%);
  background-repeat: repeat-x;
  border-color: #ddd;
  border-bottom: 0;
  border-top-left-radius: 3px;
  border-top-right-radius: 3px;
  margin-bottom: 15px;
  &:last-child {
    margin-bottom: 0;
  }
}

.sell-details {
  cursor: pointer;
  color: #6f39ff;
  &:hover {
    transition: 0.3s;
    color: black;
  }
}

.sell-data {
  display: flex;
  justify-content: space-between;
}
@media (max-width: 860px) {
  .sell-data {
    flex-direction: column;
  }
  .sell-details {
    text-align: center;
    margin-top: 15px;
  }
}
</style>