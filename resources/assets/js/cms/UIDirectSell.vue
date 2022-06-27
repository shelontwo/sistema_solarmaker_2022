<template>
  <div>
    <div class="form-group">
      <label
        for="event_id"
        style="text-align-last: justify"
        class="col-sm-2 control-label"
        >Evento*</label
      >
      <div class="col-sm-6 col-12" style="padding-right: 0">
        <!-- <ui-select
          :options="event_list"
          id="event_id"
          required="true"
          name="event_id"
        >
        </ui-select> -->

        <select
          class="form-control"
          name="event_id"
          id="event_id"
          v-on:change="selectChanges()"
          required
        >
          <option value="" :selected="!selected || selected == 0">
            Selecione
          </option>
          <option
            v-for="(option, index) in options"
            :key="index"
            :value="option.value"
            :selected="selected == option.value || false"
          >
            {{ option.label }}
          </option>
        </select>
      </div>
    </div>

    <div class="row" style="padding-left: 15px">
      <div
        class="col-md-6 col-12 form-group"
        style="text-align: start; white-space: nowrap"
      >
        <label>E-mail*</label>
        <div>
          <input
            class="form-control"
            aria-label="Nome"
            type="email"
            name="email"
            id="email"
            v-model="email"
            v-on:blur="SearchEmail()"
            v-on:keydown.enter.prevent
            required
          />
        </div>
      </div>
      <div
        v-if="loading"
        class="col-md-6 col-12 form-group d-flex align-self-end"
        style="padding-bottom: 10px"
      >
        <span class="help-block"
          ><i class="fa fa-spinner fa-pulse fa-fw"></i> Verificando e-mail</span
        >
      </div>
    </div>
    <div v-if="exists" class="col-12 help-block" style="padding-bottom: 15px">
      <span
        ><b
          >Este e-mail já está cadastrado, verifique se os dados abaixo estão
          corretos!</b
        ></span
      >
    </div>
    <div v-if="searched == true" class="row" style="padding-left: 15px">
      <div
        class="col-md-6 col-12 form-group"
        style="text-align: start; white-space: nowrap"
      >
        <label>Nome ou Razão Social*</label>
        <div>
          <input
            class="form-control"
            aria-label="name"
            type="text"
            name="name"
            id="name"
            :value="
              form.person_type == 'pf'
                ? form.name
                : form.social_name
                ? form.social_name
                : form.name
            "
            :readonly="form.name || form.social_name"
            v-on:keydown.enter.prevent
            required
          />
        </div>
      </div>
      <div
        class="col-md-6 col-12 form-group"
        style="text-align: start; white-space: nowrap"
      >
        <label>{{ random_pass ? "Senha automática:" : "CPF ou CNPJ" }}</label>
        <div>
          <input
            class="form-control"
            :aria-label="random_pass ? 'Senha automática' : 'CPF ou CNPJ'"
            v-on:keydown.enter.prevent
            type="text"
            :name="
              random_pass
                ? 'password'
                : form.person_type == 'pf'
                ? 'CPF'
                : 'CNPJ'
            "
            :id="
              random_pass
                ? 'password'
                : form.person_type == 'pf'
                ? 'CPF'
                : 'CNPJ'
            "
            :value="
              random_pass
                ? random_pass
                : form.person_type == 'pf'
                ? form.cpf
                : form.cnpj
            "
            readonly
          />
        </div>
      </div>
    </div>
    <div class="row" style="padding-left: 15px">
      <div
        class="col-md-6 col-12 form-group"
        style="text-align: start; white-space: nowrap"
      >
        <label>Observações de pagamento</label>
        <div>
          <input
            class="form-control"
            aria-label="observations"
            type="text"
            name="observations"
            id="observations"
            value=""
            v-on:keydown.enter.prevent
          />
        </div>
      </div>
      <div
        class="col-md-6 col-12 form-group"
        style="text-align: start; white-space: nowrap"
      >
        <label>Valor do pagamento</label>
        <div>
          <money
            class="form-control"
            v-model="payment_value"
            v-bind="money"
            :value="payment_value"
            name="payment_value"
            id="payment_value"
            v-on:keydown.enter.prevent
            required
          ></money>
        </div>
      </div>
    </div>

    <!-- voltar -->
    <ul class="list-group" v-if="searched == true" style="padding-left: 15px">
      <li v-if="year_selected" class="list-group-item" style="background: #eee">
        <div class="d-flex justify-content-between">
          <span class="list-items">Nome</span
          ><span class="list-items">Lote</span
          ><span style="width: 10rem" class="list-items">Quantidade</span
          ><span class="list-items">Dísponiveis</span
          ><span class="list-items">Preço</span>
        </div>
      </li>
      <li
        class="list-group-item"
        v-for="(ticket, index) in tickets"
        :key="index"
      >
        <div class="d-flex justify-content-between">
          <span style="width:20px;" class="list-items">{{ ticket.title }}</span>
          <span style="width:20px;" class="list-items">{{ ticket.lot }}</span>
          <div style="width:10rem;" class="icon-change">
            <input
              style="max-width: 10rem"
              class="form-control"
              type="number"
              min="0"
              v-model="ticket.quantity_to_sell"
              id="quantity_to_buy"
              name="quantity_to_buy"
              v-on:change="Verify(ticket)"
              v-on:keydown.enter.prevent
              :disabled="
                !ticket.quantity_to_save && ticket.quantity_to_sell <= 0
              "
            /><i
              :class="{
                'fa fa-check': ticket.quantity != ticket.quantity_to_save,
              }"
              :id="'icon' + ticket.id"
            ></i>
          </div>
          <span
          style="width:20px;"
            class="list-items"
            :class="{ 'item-font-change': ticket.quantity <= 0 }"
          >
            {{
              ticket.quantity <= 0 ? "Esgotado" : ticket.quantity + " Unidades"
            }}</span
          >
          <span style="width:20px;" class="list-items"
            >R$
            {{
              ticket.price.toLocaleString("pt-br", {
                maximumFractionDigits: 2,
                minimumFractionDigits: 2,
              })
            }}</span
          >
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import axios from "axios";
export default {
  props: [
    "search_endpoint",
    "tickets_endpoint",
    "save_endpoint",
    "ongoing_sell",
    "sell_endpoint",
    "event_list",
  ],
  data() {
    return {
      email: "",
      searched: false,
      loading: false,
      exists: false,
      random_pass: false,
      tickets: "",
      save_list: [],
      sell: "",
      year_selected: "",
      payment_value: 0,
      options: {},
      selected: "",
      form: {
        cnpj: "",
        cpf: "",
        name: "",
        person_type: "",
        social_name: "",
      },
      money: {
        decimal: ",",
        thousands: ".",
        precision: 2,
        masked: false,
      },
    };
  },
  created() {
    this.seller = this.ongoing_sell;
  },

  mounted() {
    this.options = JSON.parse(this.event_list);
  },

  methods: {
    selectChanges() {
      this.year_selected = document.getElementById("event_id").value;
      this.GetSell(true);
    },

    Save(item) {
      // return false;
      axios
        .post(this.save_endpoint, { item: item })
        .then((response) => {
          this.GetSell(false);
        })
        .catch((error) => {
          if (error.response.data.error) {
            if (error.response.data.error == 1) {
              this.$swal("Oops!", "Estoque do produto insuficiente!", "error");
            }
          }
        })
        .finally(() => {
          var a = (document.getElementById("icon" + item.id).className =
            "fa fa-check");
        });
    },

    Verify(item) {
      var a = (document.getElementById("icon" + item.id).className =
        "fa fa-spinner fa-pulse fa-fw icon-change-loading");
      if (!item.quantity_to_save && item.quantity_to_sell) {
        this.$swal("Oops!", "Este produto está esgotado!", "error");
        var a = (document.getElementById("icon" + item.id).className =
          "fa fa-times");
      } else if (item.quantity_to_sell > item.quantity_to_save) {
        this.$swal("Oops!", "Estoque do produto insuficiente!", "error");
        var a = (document.getElementById("icon" + item.id).className =
          "fa fa-times");
      } else {
        item.quantity = item.quantity_to_save - item.quantity_to_sell;
        this.Save(item);
      }
    },

    SearchEmail() {
      this.Reset();
      this.loading = true;
      axios
        .post(this.search_endpoint, { email: this.email })
        .then((response) => {
          if (response.data.name) {
            this.exists = true;
            this.form = response.data;
            if (this.form.cpf) {
              this.form.cpf =
                this.form.cpf.slice(0, 3) +
                "." +
                this.form.cpf.slice(3, 6) +
                "." +
                this.form.cpf.slice(6, 9) +
                "-" +
                this.form.cpf.slice(9, 11);
            } else if (this.form.cnpj) {
              this.form.cnpj =
                this.form.cnpj.slice(0, 2) +
                "." +
                this.form.cnpj.slice(2, 5) +
                "." +
                this.form.cnpj.slice(5, 8) +
                "/" +
                this.form.cnpj.slice(8, 12) +
                "-" +
                this.form.cnpj.slice(12, 14);
            } else {
              this.form.person_type = "pf";
              this.form.cpf = "Cliente de Venda Direta";
            }
          } else {
            this.random_pass = this.PassGenerator();
          }
          this.searched = true;
        })
        .catch((error) => {
          if (error.response.data.errors.email) {
            this.$swal("Oops!", "Insira um e-mail válido!", "error");
            this.searched = false;
            this.Reset();
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },

    PassGenerator() {
      return Math.random().toString(36).substr(2, 8);
    },

    GetSell(bolean_v) {
      axios
        .post(this.sell_endpoint, { user: this.seller })
        .then((response) => {
          this.sell = response.data;
        })
        .catch((error) => {})
        .finally(() => {
          if (bolean_v) {
            this.GetTickets();
          }
        });
    },

    GetTickets() {
      // year_selected = year_selected.options[year_selected.selectedIndex].text;
      axios
        .post(this.tickets_endpoint, { year: this.year_selected })
        .then((response) => {
          this.tickets = response.data;
          for (let i = 0; i < this.tickets.length; i++) {
            this.tickets[i].quantity_to_sell = 0;
            this.tickets[i].quantity_to_save = response.data[i].quantity;
          }
        })
        .catch((error) => {})
        .finally(() => {
          if (this.sell.length > 0) {
            for (let i = 0; i < this.sell.length; i++) {
              for (let j = 0; j < this.tickets.length; j++) {
                if (this.sell[i].ticket_id == this.tickets[j].id) {
                  this.tickets[j].quantity_to_sell = this.sell[i].quantity;
                  this.tickets[j].quantity_to_save =
                    this.sell[i].quantity + this.tickets[j].quantity_old;
                  this.tickets[j].quantity =
                    this.tickets[j].quantity_to_save -
                    this.tickets[j].quantity_to_sell;
                  this.tickets[j].sell = this.sell[i];
                }
                this.tickets[j].seller_id = this.sell[i].seller_id;
              }
            }
          } else {
            for (let val of this.tickets) {
              val.seller_id = this.ongoing_sell;
            }
          }
        });
    },

    Reset() {
      this.form = "";
      this.random_pass = false;
      this.exists = false;
    },
  },
};
</script> 
 <style lang="scss">
input:read-only:focus {
  border-color: #d2d6de !important;
  box-shadow: none !important;
}

input:read-only:hover {
  cursor: default;
}

.list-items {
  min-width: 95px;
  align-self: center;
  text-align: center;
  font-family: "PrivaOnePro", "Titillium Web";
}

.item-font-change {
  font-weight: bold !important;
}

.icon-change {
  position: relative;
  i {
    position: absolute;
    top: 50%;
    right: 0;
    transform: translate(-50%, -50%);
  }
}

.icon-change-loading {
  top: 30% !important;
  right: 7% !important;
}
</style>