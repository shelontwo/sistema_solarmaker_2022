<template>
  <div>
    <form @submit.prevent="submitForm" class="ui-form" method="POST">
      <div class="row signup__columns mt-md-4 mt-0" v-if="!address_turn">
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <label for="pf" class="signup__radio">
            <input
              type="radio"
              id="pf"
              name="pfpj"
              value="pf"
              v-model="form.person_type"
              @click="changePersonType($event)"
              required
            />
            <span></span>
            Pessoa Física
          </label>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <label for="pj" class="signup__radio">
            <input
              type="radio"
              id="pj"
              name="pfpj"
              value="pj"
              v-model="form.person_type"
              @click="changePersonType($event)"
              required
            />
            <span></span>
            Pessoa Jurídica
          </label>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
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
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
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
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <input
              type="email"
              id="email"
							@blur="checkEmail($event)"
              v-model="form.email"
              maxlength="255"
              required
            />
            <label for="email"> E-mail* </label>
          </div>
        </div>
        <div class="col-xl-6 d-xl-block d-lg-none col-md-6 d-md-block d-none"></div>
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <input
              type="password"
              id="password"
              v-model="form.password"
              minlength="6"
              maxlength="20"
              required
            />
            <label for="password"> Senha* </label>
          </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <input
              type="password"
              id="confirms"
              v-model="form.password_confirmation"
              minlength="6"
              maxlength="20"
              required
            />
            <label for="confirms"> Confirmar Senha* </label>
          </div>
        </div>
        <div
          class="col-xl-6 col-lg-12 col-md-6 col-12"
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
          class="col-xl-6 col-lg-12 col-md-6 col-12"
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
          class="col-xl-6 col-lg-12 col-md-6 col-12"
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
          class="col-xl-6 col-lg-12 col-md-6 col-12"
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
          class="col-xl-6 col-lg-12 col-md-6 col-12"
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
          class="col-xl-6 col-lg-12 col-md-6 col-12"
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
          class="col-xl-6 col-lg-12 col-md-6 col-12"
          data-wronged=""
          v-if="form.person_type == 'pj'"
        >
          <div class="signup__input-wrapper">
            <input type="text" id="position" v-model="form.position" required />
            <label for="position"> Cargo* </label>
          </div>
        </div>
      </div>
      <div class="row signup__columns mt-md-4 mt-0" v-if="address_turn">
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <the-mask
              aria-label="CEP"
              id="cep"
              :masked="true"
              :mask="['#####-###']"
              v-model="address.cep"
              v-on:change="viaCep()"
              required
            />
            <label for="cep"> CEP* </label>
          </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <input
              type="text"
              id="street"
              v-model="address.street"
              maxlength="500"
              required
            />
            <label for="street"> Rua* </label>
          </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <input
              type="text"
              id="neibourhood"
              v-model="address.neibourhood"
              maxlength="255"
              required
            />
            <label for="neibourhood"> Bairro* </label>
          </div>
        </div>
        <div class="col-xl-6 d-xl-block d-none"></div>
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <input
              type="number"
              id="number"
              v-model="address.number"
              maxlength="20"
              required
            />
            <label for="number"> Número* </label>
          </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <input
              type="text"
              id="complement"
              v-model="address.complement"
              maxlength="255"
            />
            <label for="complement"> Complemento </label>
          </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <select
              id="state"
              v-model="address.state"
              v-on:change="changesState()"
              required
            >
              <option disabled selected>Por favor, selecione um estado</option>
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
        <div class="col-xl-6 col-lg-12 col-md-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <select id="city" v-model="address.city" required>
              <option disabled selected>Por favor, selecione uma cidade</option>
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
      <div class="row">
        <div class="col-md-6 col-12 d-flex flex-column align-items-center">
          <button
            type="submit"
            class="ui-button--blue signup__submit"
            :disabled="block == true"
          >
            {{ button }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import locations from "./locations/city.json";

export default {
  props: [
    "register_endpoint",
    "check_email_endpoint",
    "login_endpoint",
    "home_endpoint",
  ],
  data() {
    return {
      form: {
        name: "",
        email: "",
        phone: "",
        password: "",
        password_confirmation: "",
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
      },
      address: {
        cep: "",
        street: "",
        neighbourhood: "",
        number: "",
        complement: "",
        state: "",
        city: "",
      },
      button: "Cadastrar-se",
      disabled: false,
      address_turn: false,
      locations: locations,
      cities: "",
      block: false,
      is_cep_ok: true,
			is_email_ok: true
    };
  },
  watch: {
    "address.cep": function() {
      this.viaCep();
    },
    "form.person_type": function() {
      this.removeErrorMessages();
    },
  },

  methods: {
    changePersonType(event) {
      event.preventDefault();
      var person =
        event.path[0].value == "pf" ? "Pessoa Física" : "Pessoa Jurídica";
      if (
        person !=
        (this.form.person_type == "pf" ? "Pessoa Física" : "Pessoa Jurídica")
      ) {
        this.$swal({
          title: "Troca de Pessoa",
          text:
            "Trocando para " +
            person +
            " você irá perder todos os dados que preencheu até agora!",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          confirmButtonText: "Trocar para " + person,
          cancelButtonColor: "#DF1949",
          confirmButtonColor: "#8B8B8B",
        }).then((confirm) => {
          if (confirm.value) {
            this.form.person_type = this.form.person_type == "pf" ? "pj" : "pf";
            this.resetForm();
          }
        });
      }
    },
    resetForm() {
      this.form = {
        name: "",
        email: "",
        phone: "",
        password: "",
        password_confirmation: "",
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
      };

      this.address = {
        cep: "",
        street: "",
        neighbourhood: "",
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
				if (items[items.length - 1].children[0].id == "email") {
					continue;
				}
        items[items.length - 1].classList.remove("ui-wronged");
      }
    },
		async checkEmail(email) {
			document.getElementsByClassName("signup__submit")[0].disabled = true;
			if (this.form.email) {
				await axios
					.post(this.check_email_endpoint, { "email": this.form.email })
					.then((response) => {
						if (response.data.name) {
							document.getElementById("email").parentElement.classList.remove("ui-correct");
							this.assignError(
								document.getElementById("email"),
								"Já há uma conta com este e-mail!"
							);
							this.is_email_ok = false;
							document.getElementsByClassName("signup__submit")[0].removeAttribute("disabled");
						} else if (response.data == "") {
							document.getElementById("email").parentElement.classList.remove("ui-wronged");
							this.assignSuccess(
								document.getElementById("email"),
								"Este e-mail é válido :)"
							);
							this.is_email_ok = true;
							document.getElementsByClassName("signup__submit")[0].removeAttribute("disabled");
						}
					})
					.catch((e) => {
						document.getElementById("email").parentElement.classList.remove("ui-correct");
						if (e.response.data.errors.email[0] == "validation.unique") {
							let item = document.getElementById("email");
							this.assignError(item, "Já há uma conta com este e-mail!");
							document.getElementsByClassName("signup__submit")[0].removeAttribute("disabled");
						} else if (
							e.response.data.errors.email[0] == "validation.required"
						) {
							let item = document.getElementById("email");
							this.assignError(item, "É necessário preencher um e-mail!");
							document.getElementsByClassName("signup__submit")[0].removeAttribute("disabled");						
						} else if (e.response.data.errors.email[0] == "validation.email") {
							let item = document.getElementById("email");
							this.assignError(item, "Preencha um e-mail válido!");
							document.getElementsByClassName("signup__submit")[0].removeAttribute("disabled");
						}
						this.is_email_ok = false;
					});
			}
		},
    async submitForm() {
      document.getElementsByClassName("signup__submit")[0].disabled = true;
      let is_form_ok = await this.isFormOk();
      if (is_form_ok && !this.address_turn && this.is_email_ok) {
				document.getElementById("email").parentElement.classList.remove("ui-correct");
				document.getElementsByClassName("signup__submit")[0].disabled = true;
        this.removeErrorMessages();
        const sleepingPromise = (delay) =>
          new Promise((resolve) => setTimeout(resolve, delay));
        const sleep = async () => {
          await sleepingPromise(1000);
          this.address_turn = true;
          window.scrollTo(0, 0);
          document.getElementById("signup-change").innerHTML =
            "Olá, <strong>" +
            this.form.name +
            "</strong>, somente precisamos de mais alguns dados.";
          document.getElementById("signup-change").classList.add("blink");
          document.getElementsByClassName("signup__submit")[0].removeAttribute("disabled");
        };
        sleep();
      } else if (is_form_ok && this.address_turn && this.is_cep_ok) {
        this.removeErrorMessages();
        const sleepingPromise = (delay) =>
          new Promise((resolve) => setTimeout(resolve, delay));
				this.button = "Cadastrando você...";
        document.getElementsByClassName("signup__submit")[0].disabled = true;
        const sleep = async () => {
          await sleepingPromise(1000);
          axios
            .post(this.register_endpoint, {
              client: this.form,
              address: this.address,
            })
            .then((response) => {
              if (response.status == 201) {
								this.button = "Você está cadastrado(a) :)";
                var msg = "Bem-vindo ao HJ21!";
                this.$swal({
                  title: "Conta Criada!",
                  text: msg,
                  type: "success",
                  timer: 1000,
                  allowOutsideClick: false,
                  showConfirmButton: false,
                }).then(() => {
                  axios
                    .post(this.login_endpoint, {
                      email: this.form.email,
                      password: this.form.password,
                    })
                    .then((response) => {
                      this.setWithExpiry("__AUTH", response.data, 1296000000, this.form.email);
                      this.$emit("signupStep");
                    })
                    .finally(() => {
                      this.button = "Cadastrar-se";
                      document
                        .getElementsByClassName("signup__submit")[0]
                        .removeAttribute("disabled");
                    });
                })
								.catch((error) => {
									this.$swal({
										title: "Oops :|",
										text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde.",
										showCancelButton: false,
										confirmButtonText: "Ok",
										confirmButtonColor: "#6fe335",
									});
									this.button = "Cadastrar-se";
									document
										.getElementsByClassName("signup__submit")[0]
										.removeAttribute("disabled");
								});
              }
            })
						.catch((error) => {
							this.$swal({
								title: "Oops :|",
								text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde.",
								showCancelButton: false,
								confirmButtonText: "Ok",
								confirmButtonColor: "#6fe335",
							});
							this.button = "Cadastrar-se";
							document
								.getElementsByClassName("signup__submit")[0]
								.removeAttribute("disabled");
						});
        };
        sleep();
      }
    },

    setWithExpiry(key, value, ttl, email) {
      const now = new Date();
      const item = {
        value: value,
        expiry: now.getTime() + ttl,
      };
      localStorage.setItem(key, JSON.stringify(item));
			if (location.href.split("/")[4] != "carrinho") {
      	window.location.href = this.home_endpoint;
			} else {
				let tmp = JSON.parse(localStorage.getItem("__TMP"));
				tmp.email = email;
				localStorage.setItem("__TMP", JSON.stringify(tmp));
				axios.post("api/cart/attribution", {
          temp: localStorage.getItem("__TMP"),
          client_id: JSON.parse(localStorage.getItem("__AUTH")).value,
        });
			}
    },

    async isFormOk() {
      this.removeErrorMessages();
      var is_ok = true;
      if (!this.address_turn) {

        //Checks if the name has more than two words if it's a PF
        var name = document.getElementById("name");
        if (this.form.person_type == "pf" && !(name.value.indexOf(" ") >= 0)) {
          this.assignError(name, "Digite nome e sobrenome, por favor");
          is_ok = false;
        }

        //Checks if both the password and its confirmation are the same
        var password = document.getElementById("password");
        var confirms = document.getElementById("confirms");
        if (password.value != confirms.value) {
          this.assignError(password, "Digite senhas iguais, por favor");
          this.assignError(confirms, "Digite senhas iguais, por favor");
          is_ok = false;
        }

        //Checks if the password has at least 6 digits
        if (password.value.length < 6) {
          this.assignError(
            password,
            "A senha precisa ter 6 caracteres, no mínimo"
          );
          is_ok = false;
        }

        // Checks if all basic fields are fielled
        var items = ["name", "email", "phone", "password", "confirms"];
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
            this.assignError(
              birthday_date,
              "Digite uma data válida, por favor"
            );
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
        if (this.address_turn) {
          var items = [
            "cep",
            "street",
            "neighbourhood",
            "number",
            "state",
            "city",
          ];
          for (var i = 0; i < items.length; i++) {
            var element = document.getElementById(items[i]);
            if (element.value.length < 1) {
              this.assignError(element, "Este campo é obrigatório");
              is_ok = false;
            }
          }
        }
      } else {
        var cep = document.getElementById("cep");
        this.viaCep(cep.value);
      }

			document.getElementsByClassName("signup__submit")[0].removeAttribute("disabled");
      return is_ok;
    },
    assignError(element, message) {
      //Assign an error message
      element.parentElement.dataset.wronged = message;
      element.parentElement.classList.add("ui-wronged");
    },
    assignSuccess(element, message) {
      //Assign a success message
      element.parentElement.dataset.wronged = message;
      element.parentElement.classList.add("ui-correct");
    },
    changesState() {
      //Changes the cities available to correspond with the selceted state
      for (var i = 0; i < this.locations.estados.length; i++) {
        if (this.locations.estados[i].nome == this.address.state) {
          this.cities = this.locations.estados[i].cidades;
        }
      }
    },
    viaCep() {
      //Changes the inputs related to the address based on the typed CEP
      if (this.address.cep.length == 9) {
        axios
          .get("https://viacep.com.br/ws/" + this.address.cep + "/json")
          .then((response) => {
            if (response.data.erro == true) {
              document
                .getElementById("cep")
                .parentElement.classList.remove("ui-correct");
              this.assignError(
                document.getElementById("cep"),
                "Digite um CEP válido"
              );
              this.is_cep_ok = false;
              this.address.street = "";
              this.address.complement = "";
              this.address.neighbourhood = "";
              this.address.city = "";
              this.address.state = "";
            } else {
              document
                .getElementById("cep")
                .parentElement.classList.remove("ui-wronged");
              this.assignSuccess(
                document.getElementById("cep"),
                "Este é um CEP válido :)"
              );
							this.is_cep_ok = true;
              this.address.cep = response.data.cep;
              this.address.street = response.data.logradouro;
              this.address.complement = response.data.complemento;
              this.address.neighbourhood = response.data.bairro;
              this.address.city = response.data.localidade;
              for (var i = 0; i < this.locations.estados.length; i++) {
                if (this.locations.estados[i].sigla == response.data.uf) {
                  this.address.state = this.locations.estados[i].nome;
                  break;
                }
              }
              this.changesState();
            }
          });
      }
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
