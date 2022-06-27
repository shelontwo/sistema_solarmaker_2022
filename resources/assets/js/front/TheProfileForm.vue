<template>
  <div>
    <form @submit.prevent="submitForm" class="ui-form row" method="POST">
      <div class="col-12">
        <div class="row signup__columns mt-md-4 mt-0">
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <input
                type="text"
                id="name"
                v-model="form.name"
                maxlength="255"
                required
              />
              <label for="name"> Nome Completo* </label>
            </div>
          </div>
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <the-mask
                aria-label="Telefone"
                name="phone"
                id="phone"
                :masked="true"
                :mask="['(##) ####-####', '(##) #####-####']"
                v-model="form.phone"
                required
              />
              <label for="phone"> Telefone* </label>
            </div>
          </div>
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <input
                type="text"
                id="email"
                v-model="form.email"
                minlength="6"
                maxlength="20"
                disabled
              />
              <label for="email"> E-mail </label>
            </div>
          </div>
          <div class="col-xl-6 d-xl-block d-none"></div>
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <input
                type="password"
                id="password"
                autocomplete="off"
                v-model="form.password"
                placeholder="Não preencha se não quiser mudar a senha"
              />
              <label for="password"> Nova senha </label>
            </div>
          </div>
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <input
                type="password"
                id="confirms"
                v-model="form.confirm_password"
              />
              <label for="confirms"> Confirmar Senha </label>
            </div>
          </div>
          <div
            class="col-xl-6 col-md-12 col-sm-6 col-12"
            data-wronged=""
            v-if="form.person_type == 'pf'"
          >
            <div class="signup__input-wrapper">
              <the-mask
                aria-label="CPF"
                name="cpf"
                id="cpf"
                :masked="true"
                :mask="['###.###.###-##']"
                v-model="form.cpf"
                required
              />
              <label for="cpf"> CPF* </label>
            </div>
          </div>
          <div
            class="col-xl-6 col-md-12 col-sm-6 col-12"
            data-wronged=""
            v-if="form.person_type == 'pf'"
          >
            <div class="signup__input-wrapper">
              <the-mask
                aria-label="CPF"
                name="birthday"
                id="birthday"
                :masked="true"
                :mask="['##/##/####']"
                v-model="form.birthday"
                required
              />
              <label for="birthday"> Data de nascimento* </label>
            </div>
          </div>
          <div
            class="col-xl-6 col-12"
            data-wronged=""
            v-if="form.person_type == 'pj'"
          >
            <div class="signup__input-wrapper">
              <input
                type="text"
                id="social-name"
                v-model="form.social_name"
                required
              />
              <label for="social-name"> Razão Social* </label>
            </div>
          </div>
          <div
            class="col-xl-6 col-md-12 col-sm-6 col-12"
            data-wronged=""
            v-if="form.person_type == 'pj'"
          >
            <div class="signup__input-wrapper">
              <input
                type="text"
                id="state-registration"
                v-model="form.state_registration"
                required
              />
              <label for="state-registration"> Inscrição Estadual* </label>
            </div>
          </div>
          <div
            class="col-xl-6 col-md-12 col-sm-6 col-12"
            data-wronged=""
            v-if="form.person_type == 'pj'"
          >
            <div class="signup__input-wrapper">
              <the-mask
                aria-label="CNPJ"
                name="cnpj"
                id="cnpj"
                :masked="true"
                :mask="['##.###.###/####-##']"
                v-model="form.cnpj"
                required
              />
              <label for="cnpj"> CNPJ* </label>
            </div>
          </div>
          <div
            class="col-xl-6 col-12"
            data-wronged=""
            v-if="form.person_type == 'pj'"
          >
            <div class="signup__input-wrapper">
              <input
                type="text"
                id="responsible"
                v-model="form.responsible"
                required
              />
              <label for="responsible"> Responsável* </label>
            </div>
          </div>
          <div
            class="col-xl-6 col-12"
            data-wronged=""
            v-if="form.person_type == 'pj'"
          >
            <div class="signup__input-wrapper">
              <input
                type="text"
                id="position"
                v-model="form.position"
                required
              />
              <label for="position"> Cargo* </label>
            </div>
          </div>
        </div>
      </div>
      <hr />
      <div class="col-12">
        <div class="row signup__columns mt-md-4 mt-0">
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <the-mask
                aria-label="CEP"
                id="cep"
                :masked="true"
                :mask="['#####-###']"
                v-model="form.cep"
                v-on:change="viaCep()"
                required
              />
              <label for="cep"> CEP* </label>
            </div>
          </div>
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <input
                type="text"
                id="street"
                v-model="form.street"
                maxlength="500"
                required
              />
              <label for="street"> Rua* </label>
            </div>
          </div>
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <input
                type="text"
                id="neibourhood"
                v-model="form.neibourhood"
                maxlength="255"
                required
              />
              <label for="neibourhood"> Bairro* </label>
            </div>
          </div>
          <div class="col-xl-6 d-xl-block d-none"></div>
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <input
                type="number"
                id="number"
                v-model="form.number"
                maxlength="20"
                required
              />
              <label for="number"> Número* </label>
            </div>
          </div>
          <div class="col-xl-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <input
                type="text"
                id="complement"
                v-model="form.complement"
                maxlength="255"
              />
              <label for="complement"> Complemento </label>
            </div>
          </div>
          <div class="col-xl-6 col-md-12 col-sm-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <select
                id="state"
                v-model="form.state"
                v-on:change="changesState()"
                required
              >
                <option disabled selected>
                  Por favor, selecione um estado
                </option>
                <option
                  v-for="(item, index) in locations.estados"
                  :key="index"
                  :value="item.nome"
                >
                  {{ item.nome }}
                </option>
              </select>
              <label for="state"> Estado* </label>
            </div>
          </div>
          <div class="col-xl-6 col-md-12 col-sm-6 col-12" data-wronged="">
            <div class="signup__input-wrapper">
              <select id="city" v-model="form.city" required>
                <option disabled selected>
                  Por favor, selecione uma cidade
                </option>
                <option
                  v-for="(item, index) in cities"
                  :key="index"
                  :value="item"
                >
                  {{ item }}
                </option>
              </select>
              <label for="city"> Cidade* </label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-12">
        <button
          type="submit"
          class="ui-button--blue signup__submit"
          id="submit-button"
          :disabled="block == true"
        >
          {{ button }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import locations from "./locations/city.json";

export default {
  props: ["check_email_endpoint", "profile", "change_profile"],
  data() {
    return {
      form: {
        name: "",
        email: "",
        phone: "",
        password: "",
        confirm_password: "",
        person_type: "pf",
        // For PF
        cpf: "",
        birthday: "",
        // For PJ
        social_name: "",
        state_registration: "",
        cnpj: "",
        responsible: "",
        position: "",
        cep: "",
        street: "",
        neibourhood: "",
        number: "",
        complement: "",
        state: "",
        city: "",
      },
      button: "Atualizar cadastro",
      disabled: false,
      address_turn: false,
      locations: locations,
      cities: "",
      block: false,
      first_time: true,
    };
  },
  watch: {
    "form.cep": function () {
      this.viaCep();
    },
    "form.person_type": function () {
      this.removeErrorMessages();
    },
  },
  beforeMount() {
    var auth = localStorage.getItem("__AUTH");
    axios
      .post(this.profile, {
        auth: JSON.parse(auth).value,
      })
      .then((response) => {
        this.form = response.data;
        this.changesState();
      });
  },
  methods: {
    resetForm() {
      this.form = {
        name: "",
        email: "",
        phone: "",
        password: "",
        confirm_password: "",
        // For PF
        cpf: "",
        person_type: this.form.person_type,
        birthday: "",
        // For PJ
        social_name: "",
        state_registration: "",
        cnpj: "",
        responsible: "",
        position: "",
        cep: "",
        street: "",
        neibourhood: "",
        number: "",
        complement: "",
        state: "",
        city: "",
      };
    },
    removeErrorMessages() {
      //Removes all error messages from the inputs
      var items = document.getElementsByClassName("ui-wronged");
      for (var i = items.length - 1; i >= 0; i--) {
        items[items.length - 1].classList.remove("ui-wronged");
      }
    },
    submitForm() {
      var submit_button = document.getElementById("submit-button");
      submit_button.disabled = true;
      this.button = "Atualizando seu cadastro...";
      let is_form_ok = this.isFormOk();
      if (is_form_ok) {
        this.block = true;
        this.removeErrorMessages();
        const sleepingPromise = (delay) =>
          new Promise((resolve) => setTimeout(resolve, delay));
        const sleep = async () => {
          await sleepingPromise(1000);
          axios
            .post(this.change_profile, {
              client_token: JSON.parse(localStorage.getItem("__AUTH")).value,
              ...this.form,
            })
            .then((response) => {
              this.button = "Cadastro atualizado :)";
              this.$swal({
                title: "Dados alterados :)",
                text: "Seus dados foram alterados com sucesso. Vamos recarregar a página para mostrar suas informações alteradas.",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
              }).then((confirm) => {
                if (confirm.value) {
                  location.reload();
                }
              });
            })
            .catch((e) => {
              if (e.response.data.errors.birthday) {
                if (
                  e.response.data.errors.birthday[0] == "validation.date_format"
                ) {
                  let item = document.getElementById("birthday");
                  this.assignError(item, "Preencha uma data válida!");
                }
              } else if (e.response.data.errors.cep) {
                if (e.response.data.errors.cep[0] == "validation.min.string") {
                  let item = document.getElementById("cep");
                  this.assignError(item, "Preencha um cep válido!");
                  item.parentNode.classList.remove("ui-correct");
                }
              } else {
								this.$swal({
									title: "Oops :|",
									text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
									showCancelButton: false,
									confirmButtonText: "Ok",
									confirmButtonColor: "#6fe335",
								});
							}

              submit_button.disabled = false;
              this.button = "ATUALIZAR CADASTRO";
            });
        };
        sleep();
      } else {
        submit_button.disabled = false;
        this.button = "ATUALIZAR CADASTRO";
      }
    },

    isFormOk() {
      this.removeErrorMessages();
      var is_ok = true;
      // Check if the email is unique
      axios
        .post(this.check_email_endpoint, { email: this.form.email })
        .then((response) => {})
        .catch((e) => {
          if (e.response.data.errors.email[0] == "validation.unique") {
            let item = document.getElementById("email");
            this.assignError(item, "Já há uma conta com este e-mail!");
          } else if (e.response.data.errors.email[0] == "validation.required") {
            let item = document.getElementById("email");
            this.assignError(item, "É necessário preencher um e-mail!");
          } else if (e.response.data.errors.email[0] == "validation.email") {
            let item = document.getElementById("email");
            this.assignError(item, "Preencha um e-mail válido!");
          }
          is_ok = false;
        });

      //Checks if the name has more than two words if it's a PF
      var name = document.getElementById("name");
      if (this.form.person_type == "pf" && !(name.value.indexOf(" ") >= 0)) {
        this.assignError(name, "Digite nome e sobrenome, por favor");
        is_ok = false;
      }

      //Checks if both the password and its confirmation are the same
      var password = document.getElementById("password");
      var confirms = document.getElementById("confirms");
      if (password.value != "") {
        if (password.value != "" && password.value != confirms.value) {
          this.assignError(password, "Digite senhas iguais, por favor");
          this.assignError(confirms, "Digite senhas iguais, por favor");
          is_ok = false;
        }

        //Checks if the password has at least 6 digits
        if (password.value != "" && password.value.length < 6) {
          this.assignError(
            password,
            "A senha precisa ter 6 caracteres, no mínimo"
          );
          is_ok = false;
        }
      }

      // Checks if all basic fields are fielled
      var items = ["name", "email", "phone"];
      for (var i = 0; i < items.length; i++) {
        var element = document.getElementById(items[i]);
        if (element.value.length < 1) {
          this.assignError(element, "Este campo é obrigatório");
          is_ok = false;
        }
      }

      if (this.form.person_type == "pf") {
        //Checks if the CPF is a valid one
        var cpf = document.getElementById("cpf");
        if (!this.checkCPF(cpf.value)) {
          this.assignError(cpf, "Digite um CPF válido, por favor");
          is_ok = false;
        }

        //Checks if the birthday date is complete
        var birthday_date = document.getElementById("birthday");
        if (birthday_date.value.length < 10) {
          this.assignError(birthday_date, "Digite uma data válida, por favor");
          is_ok = false;
        }
        if (birthday_date.value.split("/")[2] == "2021") {
          this.assignError(birthday_date, "Digite um ano válido, por favor");
          is_ok = false;
        }

        //Checks if all the fields are filled
        var items = ["cpf", "birthday"];
        for (var i = 0; i < items.length; i++) {
          var element = document.getElementById(items[i]);
          if (element.value.length < 1) {
            this.assignError(element, "Este campo é obrigatório");
            is_ok = false;
          }
        }
      }

      if (this.form.person_type == "pj") {
        //Checks if all the fields are filled
        var items = [
          "social-name",
          "state-registration",
          "cnpj",
          "responsible",
          "position",
        ];
        for (var i = 0; i < items.length; i++) {
          var element = document.getElementById(items[i]);
          if (element.value.length < 1) {
            this.assignError(element, "Este campo é obrigatório");
            is_ok = false;
          }
        }
      }

      // Checks if the address is filled too
      var items = ["cep", "street", "neibourhood", "number", "state", "city"];
      for (var i = 0; i < items.length; i++) {
        var element = document.getElementById(items[i]);
        if (element.value.length < 1) {
          this.assignError(element, "Este campo é obrigatório");
          is_ok = false;
        }
      }

      return is_ok;
    },
    assignError(element, message) {
      //Assign an error message
      element.parentElement.dataset.wronged = message;
      element.parentElement.classList.add("ui-wronged");
    },
    changesState() {
      //Changes the cities available to correspond with the selceted state
      for (var i = 0; i < this.locations.estados.length; i++) {
        if (this.locations.estados[i].nome == this.form.state) {
          this.cities = this.locations.estados[i].cidades;
        }
      }
    },
    viaCep() {
      //Changes the inputs related to the address based on the typed CEP
      if (this.form.cep.length == 9) {
        axios
          .get("https://viacep.com.br/ws/" + this.form.cep + "/json")
          .then((response) => {
            if (response.data.erro == true) {
              document
                .getElementById("cep")
                .parentElement.classList.remove("ui-correct");
              this.assignError(
                document.getElementById("cep"),
                "Digite um CEP válido"
              );
            } else {
              document
                .getElementById("cep")
                .parentElement.classList.remove("ui-wronged");
              this.assignSuccess(
                document.getElementById("cep"),
                "Este é um CEP válido :)"
              );
              this.form.cep = response.data.cep;
              this.form.street = response.data.logradouro;
              this.form.complement = response.data.complemento;
              this.form.neibourhood = response.data.bairro;
              this.form.city = response.data.localidade;
              for (var i = 0; i < this.locations.estados.length; i++) {
                if (this.locations.estados[i].sigla == response.data.uf) {
                  this.form.state = this.locations.estados[i].nome;
                  break;
                }
              }
              this.changesState();
            }
          });
      }
    },
    assignSuccess(element, message) {
      //Assign a success message
      element.parentElement.dataset.wronged = message;
      element.parentElement.classList.add("ui-correct");
    },
    checkCPF(cpf) {
      //Separates the numbers before and after the '-'
      var cpf = cpf.replaceAll(".", "");
      var cpf = cpf.replaceAll("-", "");
      var cpf_validation = cpf.slice(9, 12);
      var cpf_operation = cpf.slice(0, 9);

      //Sums each number before the '-' multiplied by (10..2)
      var sum = 0;
      for (var i = 10; i >= 2; i--) {
        sum += +cpf_operation[10 - i] * i;
      }

      //Get the rest of the sum, if it's 11 or 10, it'll be zero
      var rest = (sum * 10) % 11;
      if (rest == 10 || rest == 11) {
        rest = 0;
      }

      //If the rest is different from the first digit, it's already invalid
      if (rest != +cpf_validation[0]) {
        return false;
      }

      //Repeat for the next digit of validation
      var cpf_validation = cpf.slice(10, 12);
      var cpf_operation = cpf.slice(0, 11);

      //Sums each number before the last digit multiplied by (11..2)
      var sum = 0;
      for (var i = 11; i >= 2; i--) {
        sum += +cpf_operation[11 - i] * i;
      }

      //Get the rest of the sum, if it's 11 or 10, it'll be zero
      var rest = (sum * 10) % 11;
      if (rest == 10 || rest == 11) {
        rest = 0;
      }

      //If the rest is different from the first digit, it's already invalid
      if (rest != +cpf_validation[0]) {
        return false;
      } else {
        return true;
      }
    },
  },
};
</script>

<style>
hr {
  background-color: #bababa;
  border: 0;
  height: 1px;
  margin-bottom: calc(1rem + 10px);
  margin-top: 0;
  width: 100%;
}
#email[disabled] {
  cursor: not-allowed;
}
</style>