<template>
  <div>
    <div class="d-flex d-row justify-content-between sell-options">
      <select
        type="text"
        name="seller_selected"
        v-model="seller_selected"
        class="form-control mr-3"
        id="seller_selected"
        v-on:change="intermediateSelect()"
      >
        <option value="" selected>Todas as Vendas</option>
        <option
          v-for="(seller, index) in sellers_list"
          :key="index"
          :value="seller.seller_id"
        >
          {{ seller.seller_name }}
        </option>
      </select>
      <input
        type="text"
        placeholder="E-mail, Ano do Evento, Código ou Nome"
        class="form-control"
        v-model="search"
        v-on:blur="callback()"
      />
    </div>
    <div style="padding: 10px 15px 10px 15px">
      <div
        v-for="(item, index) in sells"
        :key="index"
        class="d-flex row list-box"
      >
        <div>
          <span class="col-12"
            ><b>#{{ item[0].cart_id }}</b> - {{ item[0].client_email }}</span
          ><span
            :class="
              item[0].step == 4
                ? 'badge badge-primary'
                : item[0].step == 5
                ? 'badge badge-success'
                : 'badge badge-danger'
            "
            >{{
              item[0].step == 4
                ? "Aguardando Pagamento"
                : item[0].step == 5
                ? "Pagamento Confirmado"
                : "Cancelado"
            }}</span
          >
        </div>
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
            }}</b>
            pelo valor cobrado de: <b>{{ item[0].price }}</b></span
          >
          <span class="sell-details" v-on:click="Show(item[0])"
            >Ver detalhes</span
          >
        </div>
      </div>
      <!-- Modal -->
      <div
        class="modal fade"
        id="modalteste"
        tabindex="-1"
        aria-labelledby="modaltesteLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title w-100" id="modaltesteLabel">
                <div class="d-flex d-row justify-content-between">
                  <div style="width: 300px">
                    <span><b>Detalhes dos Passaportes</b></span>
                  </div>
                  <div style="text-align: start">
                    <span style="white-space: nowrap; margin-left: 3em"
                      ><b>Valor Unitário</b></span
                    >
                  </div>
                  <div style="width: 300px; text-align: end">
                    <span><b>Status do Check-in</b></span>
                  </div>
                </div>
              </h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" style="padding: 0">
              <div
                v-for="(ticket, index) in ticket_list"
                :key="index"
                :style="
                  index % 2 == 0
                    ? 'background-color:#eee'
                    : 'background-color:white'
                "
                style="padding: 15px; height: 96px"
                class="d-flex d-row justify-content-between modal-body-item"
              >
                <div class="modal-body-details" style="width: 300px">
                  <div class="mb-1">
                    <span>{{ ticket.ticket_name }}</span>
                  </div>
                  <div class="mb-1">
                    <span
                      ><b>{{
                        !ticket.codigo
                          ? "Código ainda não gerado"
                          : ticket.codigo.toUpperCase()
                      }}</b></span
                    >
                  </div>
                  <div>
                    <span>{{
                      !ticket.email
                        ? "Check-in não realizado"
                        : ticket.name + " - " + ticket.email
                    }}</span>
                  </div>
                </div>

                <div
                  style="
                    align-self: center;
                    text-align: start;
                    padding-left: 15px;
                  "
                >
                  {{
                    "R$ " +
                    ticket.price.toLocaleString("pt-br", {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2,
                    })
                  }}
                </div>
                <div style="align-self: center; width: 300px; text-align: end">
                  <span
                    class="badge"
                    @click="GoToCheckin(ticket)"
                    :class="
                      ticket.codigo == ''
                        ? 'badge-warning'
                        : !ticket.name
                        ? 'badge-danger alter-mouse'
                        : ticket.name == '-'
                        ? 'badge-danger'
                        : 'badge-success'
                    "
                    style="font-size: medium"
                    >{{
                      ticket.codigo == ""
                        ? "Aguardando Pagamento"
                        : !ticket.name
                        ? "Check-in Pendente"
                        : ticket.name == "-"
                        ? "Passaporte Cancelado!"
                        : "Check-in Feito"
                    }}</span
                  >
                </div>
              </div>
              <div
                class="d-flex d-row justify-content-between"
                style="padding: 15px; height: 96px; align-items: center"
                :style="
                  ticket_list.length % 2 == 0
                    ? 'background-color:#eee'
                    : 'background-color:white'
                "
              >
                <span style="width: 300px" v-if="ticket_list[0]">
                  <b>Observações: </b>
                  {{
                    ticket_list[0].observations
                      ? ticket_list[0].observations
                      : "Não foram informadas!"
                  }}
                </span>
                <span style="white-space: nowrap"
                  ><b>Total: </b> {{ "R$ " + ticket_list.full_price }}</span
                >
                <span style="width: 300px"></span>
              </div>
            </div>

            <div class="modal-footer justify-content-center">
              <div v-if="ticket_list[0]">
                <button
                  v-if="ticket_list[0].step == 4 || ticket_list[0].step == 5"
                  type="button"
                  class="btn btn-danger"
                  @click="AreYouSure(false)"
                >
                  Cancelar Pedido
                </button>
              </div>
              <div v-if="ticket_list[0]">
                <button
                  v-if="ticket_list[0].step == 4"
                  type="button"
                  @click="AreYouSure(true)"
                  class="btn btn-success"
                >
                  Confirmar Pedido
                </button>
              </div>
            </div>
          </div>
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
  props: [
    "sellers",
    "ingressos_endpoint",
    "confirm_endpoint",
    "cancel_endpoint",
  ],
  data() {
    return {
      sells: "",
      seller_selected: "",
      page: 1,
      records: 0,
      sellers_list: [],
      search: "",
      ticket_list: [],
    };
  },

  created() {
    this.sellers_list = JSON.parse(this.sellers);
    this.callback();
  },

  methods: {
    GoToCheckin(ticket) {
      if (ticket.name || !ticket.codigo) {
        return false;
      } else if (!ticket.name) {
        window.open("/cliente/checkin" + `?p=${ticket.codigo}`, "_blank");
      }
    },

    AreYouSure(choose) {
      this.$swal({
        title: choose
          ? "Deseja confirmar o pedido?"
          : "Deseja cancelar o pedido?",
        showCancelButton: true,
        confirmButtonText: `Sim`,
        confirmButtonColor: "#28a745",
        cancelButtonText: `Não`,
        cancelButtonColor: "#dc3545",
      }).then((result) => {
        if (result.value) {
          if (choose == true) {
            this.$swal({
              title: "Carregando dados, aguarde...",
              allowEscapeKey: false,
              allowOutsideClick: false,
              onOpen: () => {
                this.$swal.showLoading();
              },
            });
            axios
              .post(this.confirm_endpoint, {
                itens: this.ticket_list,
                cart_id: this.ticket_list.cart_id,
              })
              .then((response) => {
                // location.reload();
              })
              .catch((error) => {})
              .finally(() => {
                let item = { cart_id: this.ticket_list.cart_id, confirm: true };
                this.Show(item);
              });
          } else if (choose == false) {
            this.$swal({
              title: "Carregando dados, aguarde...",
              allowEscapeKey: false,
              allowOutsideClick: false,
              onOpen: () => {
                this.$swal.showLoading();
              },
            });
            axios
              .post(this.cancel_endpoint, {
                itens: this.ticket_list,
                cart_id: this.ticket_list.cart_id,
              })
              .then((response) => {})
              .catch((error) => {})
              .finally(() => {
                let item = { cart_id: this.ticket_list.cart_id, cancel: true };
                this.Show(item);
              });
          }
        }
      });
    },

    intermediateSelect() {
      this.page = 1;
      this.callback();
    },

    getSells() {
      axios
        .post("/api/getSells", {
          seller: this.seller_selected,
          search: this.search,
        })
        .then((response) => {
          this.records = +response.data;
        })
        .catch((err) => {});
    },

    callback(again) {
      if (again == "error") {
        return false;
      } else {
        this.$swal({
          title: "Carregando dados, aguarde...",
          allowEscapeKey: false,
          allowOutsideClick: false,
          onOpen: () => {
            this.$swal.showLoading();
          },
        });
        axios
          .post("/api/pagination", {
            page: this.page,
            seller: this.seller_selected,
            search: this.search,
          })
          .then((response) => {
            if (response.data.length == 0) {
              this.$swal(
                "Oops!",
                "Não há resultados para essa pesquisa",
                "error"
              );
              this.seller_selected = "";
              this.search = "";
              this.callback("error");
            } else {
              this.sells = response.data;
              this.$swal({
                title: "Dados Carregados com Sucesso",
                timer: 500,
                type: "success",
                showConfirmButton: false,
              });
            }
          })
          .catch((err) => {})
          .finally(() => {
            this.getSells();
          });
      }
    },

    Show(item) {
      if (!item.confirm && !item.cancel) {
        this.$swal({
          title: "Carregando dados, aguarde...",
          allowEscapeKey: false,
          allowOutsideClick: false,
          onOpen: () => {
            this.$swal.showLoading();
          },
        });
      }
      axios
        .post(this.ingressos_endpoint, { cart_id: item.cart_id })
        .then((response) => {
          this.ticket_list = response.data;
          this.ticket_list.cart_id = item.cart_id;
          this.ticket_list.full_price = 0;
          for (let i = 0; i < this.ticket_list.length; i++) {
            this.ticket_list.full_price += this.ticket_list[i].price;
          }
          this.ticket_list.full_price =
            this.ticket_list.full_price.toLocaleString("pt-br", {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            });
        })
        .catch((error) => {
          console.error(error);
        })
        .finally(() => {
          if (item.cancel || item.confirm) {
            this.callback();
          }
          this.$swal({
            title: "Dados Carregados com Sucesso",
            timer: 500,
            type: "success",
            showConfirmButton: false,
          });
          $("#modalteste").modal("show");
        });
    },
  },
};
</script> 
<style lang="scss">
@media (max-width: 1200px) {
  .modal-dialog {
    padding-left: 13.9945px;
    max-width: 100%;
  }
}

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

@media (max-width: 600px) {
  .modal-body-item {
    flex-direction: column;
  }

  .modal-body-details {
    text-align: center;
    margin-bottom: 10px;
  }
}

@media (max-width: 990px) {
  .sell-data {
    flex-direction: column;
  }
  .sell-details {
    text-align: center;
    margin-top: 15px;
  }

  .sell-options {
    flex-direction: column !important;
    input {
      margin-top: 10px;
    }
  }
}
</style>