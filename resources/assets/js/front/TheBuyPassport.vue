<template>
  <div>
    <div class="cart__item" v-for="(item, index) in products" :key="index">
      <div
        class="cart__info"
        :style="
          'background-color:' +
          item.color +
          (width <= 880 ? ' !important' : '') +
          ';'
        "
      >
        <div :style="'background-color:' + item.color + ';'">
          {{ item.title }}
          <button v-on:click="removeProduct(index)" :id="'exclude' + item.p_id">
            <i class="fa fa-close"></i>
          </button>
        </div>
        <div class="d-flex flex-column justify-content-center">
          <span>
            {{
              (item.total_price * item.quantity).toLocaleString("pt-br", {
                minimumFractionDigits: 2,
              })
            }}
          </span>
          <span>
            {{ item.quantity }} <span>passaportes de</span> <span>X</span> R${{
              item.total_price.toLocaleString("pt-br", {
                minimumFractionDigits: 2,
              })
            }}
          </span>
        </div>
      </div>
      <div class="cart__buy-more">
        <button
          v-on:click="subtract(index)"
          :id="'minus' + item.p_id"
          :disabled="item.minus_disabled"
        >
          <i class="fa fa-minus"></i>
        </button>
        <input type="text" :value="item.quantity" disabled />
        <div :id="'spin' + item.p_id">
          <i class="fa fa-spinner fa-pulse"></i>
        </div>
        <button
          v-on:click="add(index)"
          :id="'plus' + item.p_id"
          :disabled="item.plus_disabled"
        >
          <i class="fa fa-plus"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: ["products", "sum", "sub", "cancel", "step"],
  data() {
    return {
      response: "",
      width: window.innerWidth,
    };
  },
  methods: {
    add(index) {
      var item_plus = document.getElementById(
        "plus" + this.products[index].p_id
      );
      var item_minus = document.getElementById(
        "minus" + this.products[index].p_id
      );
      var item_exclude = document.getElementById(
        "exclude" + this.products[index].p_id
      );
      var pay_button = document.getElementById("contact-tab");
      var advance_button = document.getElementsByClassName("cart__submit")[0];
      var spin = document.getElementById("spin" + this.products[index].p_id);

      spin.style.display = "flex";
      item_plus.disabled = "disabled";
      item_minus.disabled = "disabled";
      item_exclude.disabled = "disabled";
      pay_button.classList.add("disabled");
      advance_button.disabled = "disabled";

      if (
        this.products[index].quantity < this.products[index].max_sell &&
        this.products[index].t_quantity > 0
      ) {
        axios
          .post(this.sum, {
            temp: localStorage.getItem("__TMP"),
            ticket_id: this.products[index].ticket_id,
          })
          .then((response) => {
            if (response.data.error == "Estoque insuficiente deste item!") {
              this.$swal({
                title: "Passaporte esgotado",
                text:
                  "Todos os passaportes " +
                  this.products[index].title +
                  " estão com algum participante do HJ21. Você pode tentar novamente mais tarde e ver se temos mais 👀",
                confirmButtonText: "Ok",
                confirmButtonColor: "#28a745",
                closeOnClickOutside: true,
                allowOutsideClick: false,
                closeOnClickOutside: false,
                allowEscapeKey: false,
              });
              item_minus.removeAttribute("disabled");
              item_exclude.removeAttribute("disabled");
              if (this.step === 2) {
                pay_button.classList.remove("disabled");
              }
              spin.style.display = "none";
            } else if (!response.data.error) {
              this.discount = response.data.Desconto;
              this.$emit("discount", this.discount);
              this.products[index].quantity += 1;
              advance_button.removeAttribute("disabled");
              item_minus.removeAttribute("disabled");
              item_exclude.removeAttribute("disabled");
              if (this.step === 2) {
                pay_button.classList.remove("disabled");
              }
              spin.style.display = "none";
              if (
                this.products[index].quantity < this.products[index].max_sell
              ) {
                item_plus.removeAttribute("disabled");
              }
            }
          })
          .catch((error) => {
            this.$swal({
              title: "Oops :|",
              text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
              showCancelButton: false,
              confirmButtonText: "Ok",
              confirmButtonColor: "#6fe335",
            }).then((confirm) => {
              if (confirm.value) {
                spin.style.display = "none";
                if (this.step === 2) {
                  pay_button.classList.remove("disabled");
                }
                item_plus.removeAttribute("disabled");
                item_minus.removeAttribute("disabled");
                item_exclude.removeAttribute("disabled");
                advance_button.removeAttribute("disabled");
              }
            });
          });
      } else {
        item_minus.removeAttribute("disabled");
        item_exclude.removeAttribute("disabled");
        advance_button.removeAttribute("disabled");
        if (this.step === 2) {
          pay_button.classList.remove("disabled");
        }
        spin.style.display = "none";
      }
    },
    subtract(index) {
      var item_plus = document.getElementById(
        "plus" + this.products[index].p_id
      );
      var item_minus = document.getElementById(
        "minus" + this.products[index].p_id
      );
      var item_exclude = document.getElementById(
        "exclude" + this.products[index].p_id
      );
      var pay_button = document.getElementById("contact-tab");
      var spin = document.getElementById("spin" + this.products[index].p_id);

      spin.style.display = "flex";
      item_plus.disabled = "disabled";
      item_minus.disabled = "disabled";
      item_exclude.disabled = "disabled";
      pay_button.classList.add("disabled");
      axios
        .post(this.sub, {
          temp: localStorage.getItem("__TMP"),
          ticket_id: this.products[index].ticket_id,
        })
        .then((response) => {
          this.discount = response.data.Desconto;
          this.$emit("discount", this.discount);
          this.products[index].quantity -= 1;
          this.products[index].t_quantity += 1;
          if (this.products[index].quantity == 0) {
            location.reload();
          } else {
            spin.style.display = "none";
            item_minus.removeAttribute("disabled");
            item_exclude.removeAttribute("disabled");
            item_plus.removeAttribute("disabled");
            if (this.step === 2) {
              pay_button.classList.remove("disabled");
            }
          }
        })
        .catch((error) => {
          this.$swal({
            title: "Oops :|",
            text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
            showCancelButton: false,
            confirmButtonText: "Ok",
            confirmButtonColor: "#6fe335",
          }).then((confirm) => {
            if (confirm.value) {
              spin.style.display = "none";
              if (this.step === 2) {
                pay_button.classList.remove("disabled");
              }
              item_plus.removeAttribute("disabled");
              item_minus.removeAttribute("disabled");
              item_exclude.removeAttribute("disabled");
            }
          });
        });
    },
    removeProduct(index) {
      this.$swal({
        title: "Exclusão de passaporte",
        text: "Você tem certeza que deseja excluir este passaporte do seu carrinho?",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Excluir passaporte",
        cancelButtonColor: "#DF1949",
        confirmButtonColor: "#8B8B8B",
      }).then((confirm) => {
        if (confirm.value) {
          var item_plus = document.getElementById(
            "plus" + this.products[index].p_id
          );
          var item_minus = document.getElementById(
            "minus" + this.products[index].p_id
          );
          var item_exclude = document.getElementById(
            "exclude" + this.products[index].p_id
          );
          var pay_button = document.getElementById("contact-tab");
          var spin = document.getElementById(
            "spin" + this.products[index].p_id
          );

          spin.style.display = "flex";
          pay_button.classList.add("disabled");
          item_plus.disabled = "disabled";
          item_minus.disabled = "disabled";
          item_exclude.disabled = "disabled";

          axios
            .post(this.cancel, {
              temp: localStorage.getItem("__TMP"),
              ticket_id: this.products[index].ticket_id,
            })
            .then((response) => {
              this.discount = 0;
              if (response.status == 200) {
                location.reload();
              }
            })
            .catch((error) => {
              this.$swal({
                title: "Oops :|",
                text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
              }).then((confirm) => {
                if (confirm.value) {
                  spin.style.display = "none";
                  if (this.step === 2) {
                    pay_button.classList.remove("disabled");
                  }
                  item_plus.removeAttribute("disabled");
                  item_minus.removeAttribute("disabled");
                  item_exclude.removeAttribute("disabled");
                }
              });
            });
        }
      });
    },
  },
};
</script>
