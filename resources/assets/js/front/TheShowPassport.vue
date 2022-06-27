<template>
  <div class="d-flex flex-column align-items-center">
    <div
      class="cart__no-products mt-5 d-flex flex-column"
      v-if="products.length == 0"
    >
      Sem passaportes ainda :(
      <a href="/" class="ui-button--big-red mt-4"> Ir para a loja </a>
    </div>
    <div
      v-for="(cart, index) in carts"
      :key="index"
      class="
        cart-wrapper
        w-100
        d-flex
        flex-column
        align-items-center
        justify-content-center
      "
    >
      <div class="w-100 d-flex align-items-center justify-content-between my-3">
        <h1 class="ui-text--black">
          Pedido nº {{ cart.carrinho }}
          <i :class="cart.cart_step_id == 5 ? 'fa fa-check' : 'd-none'"></i>
        </h1>
        <p
          class="ui-button--not-button"
          v-if="cart.cart_step_id == 4 && cart.payment_method_id == 1"
        >
          Aguardando confirmação de pagamento
        </p>
        <a
          :href="cart.payment_link"
          target="_blank"
          class="ui-button--green"
          aria-label="Ver boleto deste pedido"
          v-if="cart.cart_step_id == 4 && cart.payment_method_id == 2"
        >
          Ver boleto
        </a>
        <button
          target="_blank"
          aria-label="Ver PIX deste pedido"
          class="pix-qr-code ui-button--green"
          v-if="cart.cart_step_id == 4 && cart.payment_method_id == 3"
          @click="mountModal(cart.pix_qr_code, cart.pix_link)"
        >
          Ver QR Code do PIX
        </button>
        <p
          aria-label="Fazer check-in"
          class="ui-button--not-button"
          v-if="cart.cart_step_id == 5 && !cart.ingresso_nome"
        >
          Pagamento cofirmado
        </p>
        <button
          aria-label="Fazer check-in"
          v-if="cart.cart_step_id == 5 && cart.ingresso_nome && !limit_date"
          @click="removeCheckIn(cart.ingresso_id, cart.ingresso_nome, $event)"
        >
          Remover o check-in de {{ cart.ingresso_nome }}
          <i class="loading"></i>
        </button>
        <p
          v-else-if="cart.cart_step_id == 5 && cart.ingresso_nome && limit_date"
        >
          Check-in feito
        </p>
        <p
          v-if="
            cart.cart_step_id == 7 ||
            cart.cart_step_id == 8 ||
            cart.cart_step_id == 9
          "
          class="ui-button--not-button--red"
        >
          Pedido cancelado
        </p>
      </div>
      <div
        class="cart__item--show"
        v-for="(item, index) in products"
        v-if="cart.carrinho == item.carrinho"
        :key="index"
      >
        <div
          class="cart__info"
          :style="
            'background-color:' +
            item.color +
            (width < 992 ? ' !important' : '')
          "
        >
          <div :style="'background-color:' + item.color">
            {{ item.title }}
          </div>
          <div class="row justify-content-center">
            <div class="col-md-6 col-12 d-flex flex-column align-items-center">
              <p v-if="item.cart_step_id != 5">Método de pagamento</p>
              <p v-if="item.cart_step_id == 5">
                {{
                  item.ingresso_nome
                    ? "Está em nome de"
                    : "Não esqueça do check-in"
                }}
              </p>
              <p class="cart-checkin" v-if="item.cart_step_id == 5">
                {{ item.ingresso_nome ? item.ingresso_nome : ":)" }}
              </p>
              <p class="cart-checkin" v-if="item.cart_step_id != 5">
                {{
                  item.payment_method_id == 1
                    ? "Cartão de Crédito"
                    : item.payment_method_id == 2
                    ? "Boleto"
                    : "PIX"
                }}
              </p>
            </div>
            <div class="col-md-6 col-12 d-flex flex-column align-items-center">
              <p>Código</p>
              <p class="cart-checkin">
                {{ item.ingresso_codigo ? item.ingresso_codigo : "--" }}
              </p>
            </div>
            <div class="cart__buy-more">
              <p v-if="item.cart_step_id == 4">
                Aguardando confirmação de pagamento
              </p>
              <a
                :href="checkin + `?p=${item.ingresso_codigo}`"
                aria-label="Fazer check-in"
                v-if="item.cart_step_id == 5 && !item.ingresso_nome"
              >
                Fazer check-in
              </a>
              <button
                aria-label="Fazer check-in"
                class="remove"
                v-if="
                  item.cart_step_id == 5 && item.ingresso_nome && !limit_date
                "
                @click="
                  removeCheckIn(item.ingresso_id, item.ingresso_nome, $event)
                "
              >
                Remover o check-in de {{ item.ingresso_nome }}
                <i class="loading"></i>
              </button>
              <p
                v-else-if="
                  item.cart_step_id == 5 && item.ingresso_nome && limit_date
                "
              >
                Check-in feito
              </p>
              <p
                v-if="
                  item.cart_step_id == 7 ||
                  item.cart_step_id == 8 ||
                  item.cart_step_id == 9
                "
              >
                Pedido cancelado
              </p>
            </div>
          </div>
        </div>
        <div class="cart__buy-more">
          <p
            :class="
              'cart__item-badge' +
              (item.cart_step_id == 4 ? ' badge-warning' : '')
            "
            v-if="item.cart_step_id == 4"
          >
            Aguardando confirmação de pagamento
          </p>
          <a
            :href="checkin + `?p=${item.ingresso_codigo}`"
            aria-label="Fazer check-in"
            :class="
              'cart__item-badge' +
              (item.cart_step_id == 5 ? ' badge-success' : '')
            "
            v-if="item.cart_step_id == 5 && !item.ingresso_nome"
          >
            Fazer check-in
          </a>
          <button
            aria-label="Fazer check-in"
            :class="
              'cart__item-badge' +
              (item.cart_step_id == 5 ? ' badge-success' : '')
            "
            v-if="item.cart_step_id == 5 && item.ingresso_nome && !limit_date"
            @click="removeCheckIn(item.ingresso_id, item.ingresso_nome, $event)"
          >
            Remover o check-in de {{ item.ingresso_nome }}
            <i class="loading"></i>
          </button>
          <p
            class="cart__item-badge badge-success"
            v-else-if="
              item.cart_step_id == 5 && item.ingresso_nome && limit_date
            "
          >
            Check-in feito
          </p>
          <p
            :class="
              'cart__item-badge' +
              (item.cart_step_id == 7 ||
              item.cart_step_id == 8 ||
              item.cart_step_id == 9
                ? ' badge-danger'
                : '')
            "
            v-if="
              item.cart_step_id == 7 ||
              item.cart_step_id == 8 ||
              item.cart_step_id == 9
            "
          >
            Pedido cancelado
          </p>
        </div>
        <div class="row no-gutters">
          <div
            class="
              col-md-6 col-7
              text-center
              d-flex
              flex-column
              align-items-center
            "
          >
            <p v-if="item.cart_step_id != 5">Método de pagamento</p>
            <p v-if="item.cart_step_id == 5">
              {{
                item.ingresso_nome
                  ? "Está em nome de"
                  : "Não esqueça do check-in"
              }}
            </p>
            <p class="cart-checkin" v-if="item.cart_step_id == 5">
              {{ item.ingresso_nome ? item.ingresso_nome : ":)" }}
            </p>
            <p class="cart-checkin" v-if="item.cart_step_id != 5">
              {{
                item.payment_method_id == 1
                  ? "Cartão de Crédito"
                  : item.payment_method_id == 2
                  ? "Boleto"
                  : "PIX"
              }}
            </p>
          </div>
          <div
            class="
              col-md-6 col-5
              text-center
              d-flex
              flex-column
              align-items-center
            "
          >
            <p>Código</p>
            <p class="cart-checkin">
              {{ item.ingresso_codigo ? item.ingresso_codigo : "--" }}
            </p>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="exampleModal"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body d-flex flex-column align-items-center">
            <strong>Importante:</strong>
            <ul>
              <li>
                Acesse seu internet banking e efetue a leitura do
                <strong>QR Code</strong> ou copie o código;
              </li>
              <li>Pix válido até: <strong>meia noite de hoje</strong>;</li>
            </ul>
            <img
              :src="'data:image/png;base64,' + qr_code"
              alt="QR Code do PIX"
            />
            <button type="button" class="ui-button--green" @click="copyPIX()">
              COPIAR PIX
            </button>
          </div>
        </div>
      </div>
    </div>
    <h1 v-if="checkins_tickets.length > 0" class="ui-section-title--black my-4">
      Meus Check-ins
    </h1>
    <div
      class="cart__item--show cart-view"
      v-for="(item, index) in checkins_tickets"
      :key="index"
    >
      <div
        v-if="checkins_tickets.length > 0"
        class="cart__info"
        :style="
          'background-color:' +
          item.color +
          (width < 992 ? ' !important' : '') +
          ';'
        "
      >
        <div :style="'background-color:' + item.color">
          {{ item.title }}
        </div>
        <div class="row justify-content-center">
          <div class="col-md-6 col-12 d-flex flex-column align-items-center">
            <p>Nome do comprador</p>
            <p class="cart-checkin">
              {{ item.name }}
            </p>
          </div>
          <div class="col-md-6 col-12 d-flex flex-column align-items-center">
            <p>Código</p>
            <p class="cart-checkin">
              {{ item.ingresso_codigo }}
            </p>
          </div>
        </div>
      </div>
      <div class="cart__buy-more"></div>
      <div class="row no-gutters">
        <div
          class="
            col-md-6 col-7
            text-center
            d-flex
            flex-column
            align-items-center
          "
        >
          <p>Nome do comprador</p>
          <p class="cart-checkin">
            {{ item.name }}
          </p>
        </div>
        <div
          class="
            col-md-6 col-5
            text-center
            d-flex
            flex-column
            align-items-center
          "
        >
          <p>Código</p>
          <p class="cart-checkin">
            {{ item.ingresso_codigo ? item.ingresso_codigo : "--" }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: [
    "retrieve_closed_cart",
    "checkin",
    "alter_checkin",
    "remove",
    "checkins",
  ],
  data() {
    return {
      is_logged: false,
      products: "",
      carts: "",
      checkins_tickets: "",
      width: window.innerWidth,
      qr_code: "",
      pix_link: "",
      limit_date: false,
    };
  },
  created() {
    var client_token = localStorage.getItem("__AUTH");
    axios
      .post(this.retrieve_closed_cart, {
        client_token: JSON.parse(client_token).value,
      })
      .then((response) => {
        this.products = response.data.client;
        this.carts = response.data.cart;
      })
      .catch((error) => {});

    axios
      .post(this.checkins, {
        email: JSON.parse(localStorage.getItem("__TMP")).email,
        authentication: client_token,
      })
      .then((response) => {
        this.checkins_tickets = response.data;
      })
      .catch((error) => {});

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
    var yyyy = today.getFullYear();
    if (+yyyy == 2021 && +dd >= 22 && +mm >= 9) {
      this.limit_date = true;
    }
  },
  methods: {
    removeCheckIn(id, name, event) {
      event.path[0].disabled = true;
      var auth_data = localStorage.getItem("__AUTH");
      this.$swal({
        title: "Remover check-in",
        text:
          "Você tem certeza que deseja remover o check-in feito por " +
          name +
          " deste ingresso?",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Remover check-in",
        cancelButtonColor: "#DF1949",
        confirmButtonColor: "#8B8B8B",
      }).then((confirm) => {
        if (confirm.value) {
          event.path[0].classList.add("no-text");
          event.path[0].children[0].classList.add(
            "fa",
            "fa-spinner",
            "fa-pulse"
          );
          axios
            .post(this.remove, {
              id: id,
              authentication: auth_data,
            })
            .then((response) => {
              this.$swal({
                title: "Remoção de check-in",
                text: response.data.msg,
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
                closeOnClickOutside: false,
                allowOutsideClick: false,
                closeOnClickOutside: false,
              }).then((confirm) => {
                if (confirm.value) {
                  location.reload();
                }
              });
            })
            .catch((error) => {
              this.$swal({
                title: "Oops :|",
                text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
              });
              event.path[0].removeAttribute("disabled");
              event.path[0].classList.remove("no-text");
              event.path[0].children[0].classList.remove(
                "fa",
                "fa-spinner",
                "fa-pulse"
              );
            });
        } else {
          event.path[0].removeAttribute("disabled");
          event.path[0].classList.remove("no-text");
          event.path[0].children[0].classList.remove(
            "fa",
            "fa-spinner",
            "fa-pulse"
          );
        }
      });
    },
    mountModal(qr_code, pix_link) {
      this.qr_code = qr_code;
      this.pix_link = pix_link;
      $("#exampleModal").modal("show");
    },
    copyPIX() {
      try {
        const el = document.createElement("textarea");
        el.value = this.pix_link;
        document.body.appendChild(el);
        el.select(0, 99999);
        document.execCommand("copy");
        document.body.removeChild(el);
        this.$swal({
          title: "Copiado",
          confirmButtonText: "Ok",
          confirmButtonColor: "#6fe335",
        }).then((confirms) => {
          if (confirms.value) {
            $("#exampleModal").modal("toggle");
          }
        });
      } catch (exception) {
        this.$swal({
          title: "Oops :|",
          html: "Desculpe, mas tivemos um problema ao copiar o pix. Por favor, tente novamente mais tarde.",
          confirmButtonText: "Ok",
          confirmButtonColor: "#DF1949",
        });
      }
    },
  },
};
</script>
