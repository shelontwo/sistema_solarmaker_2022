<template>
  <div>
    <form @submit.prevent="submitForm" class="ui-form" method="POST">
      <div class="row signup__columns mt-md-4 mt-0">
        <div class="col-xl-6 col-12" data-wronged="">
          <div class="signup__input-wrapper">
            <the-mask
              aria-label="Telefone"
              name="code"
              id="code"
              :masked="true"
              :mask="['HJ-A#A#-##']"
              v-model="form.code"
              required
            />
            <label for="code"> Código do Passaporte* </label>
          </div>
        </div>
        <div class="col-xl-6 col-12" v-if="!disabled"></div>
        <div class="col-xl-6 col-12" data-wronged="" v-if="!disabled">
          <div class="signup__input-wrapper">
            <input
              type="text"
              name="name"
              id="name"
              v-model="form.name"
              maxlength="255"
              required
            />
            <label for="name"> Nome Completo* </label>
          </div>
        </div>
        <div class="col-xl-6 col-12" data-wronged="" v-if="!disabled">
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
        <div class="col-xl-6 col-12" data-wronged="" v-if="!disabled">
          <div class="signup__input-wrapper">
            <input
              type="email"
              id="email"
              v-model="form.email"
              maxlength="255"
              required
            />
            <label for="email"> E-mail* </label>
          </div>
        </div>
        <div class="col-xl-6 col-md-12 col-sm-6 col-12" data-wronged="" v-if="!disabled">
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
        <div class="col-xl-6 col-md-12 col-sm-6 col-12" data-wronged="" v-if="!disabled">
          <div class="signup__input-wrapper">
            <select
              id="state"
              v-model="form.state"
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
        <div class="col-xl-6 col-md-12 col-sm-6 col-12" data-wronged="" v-if="!disabled">
          <div class="signup__input-wrapper">
            <select id="city" v-model="form.city" required>
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
        <div class="col-xl-6 col-md-12 col-sm-6 col-12" data-wronged="" v-if="!disabled">
          <div class="signup__input-wrapper">
            <select id="schooling" v-model="form.schooling" required>
              <option disabled selected>Por favor, selecione uma opção</option>
              <option
                v-for="(item, index) in schooling"
                :key="index"
                :value="item"
              >
                {{ item }}
              </option>
            </select>
            <label for="schooling"> Escolaridade* </label>
          </div>
        </div>
				<div class="col-xl-6 col-md-12 col-sm-6 col-12" data-wronged="" v-if="!disabled">
          <div class="signup__input-wrapper">
            <select id="gender" v-model="form.gender" required>
              <option disabled selected>Por favor, selecione uma opção</option>
              <option value="masculino">
                Masculino
              </option>
							<option value="feminino">
                Feminino
              </option>
							<option value="outro">
                Outro
              </option>
            </select>
            <label for="gender"> Gênero* </label>
          </div>
        </div>
				<div
          class="col-xl-6 col-lg-12 col-md-6 col-12"
          data-wronged=""
					v-if="!disabled"
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
				<div class="col-xl-6 col-md-12 col-sm-6 col-12" v-if="!disabled"></div>
				<div class="col-xl-3 col-lg-4 col-md-5 col-12" data-wronged="" v-if="!disabled">
          <label for="works" class="signup__radio">
            <input
              type="checkbox"
              id="works"
              name="works"
              v-model="form.work"
							@change="changeWorks($event)"
            />
            <span></span>
            Já trabalho
          </label>
        </div>
        <div class="col-xl-6 col-md-12 col-sm-6 col-12" v-if="!disabled"></div>
        <div
          class="col-xl-6 col-12"
          data-wronged=""
          v-if="form.works && !disabled"
        >
          <div class="signup__input-wrapper">
            <the-mask
              aria-label="CNPJ"
              name="cnpj"
              id="cnpj"
              :masked="true"
              :mask="['##.###.###/####-##']"
              v-model="form.cnpj"
            />
            <label for="cnpj"> CNPJ </label>
          </div>
        </div>
        <div class="col-xl-6 col-12" data-wronged="" v-if="form.works && !disabled">
          <div class="signup__input-wrapper">
            <input type="text" id="company" v-model="form.company" required/>
            <label for="company"> Empresa* </label>
          </div>
        </div>
        <div class="col-xl-6 col-12" data-wronged="" v-if="form.works && !disabled">
          <div class="signup__input-wrapper">
            <input type="text" id="position" v-model="form.position" required/>
            <label for="position"> Cargo* </label>
          </div>
        </div>
				<div class="col-xl-6 col-12" v-if="form.works"></div>
				<div class="col-12 justify-content-start" v-if="!disabled">
          <div class="checkin-checkbox">
            <input type="checkbox" id="check1" v-model="form.check1" />
            <label for="check1"> 
							Autorizo a coleta de meus dados pessoais solicitados no momento em que realizo meu check-in no Evento. Esses dados são importantes para evitarmos eventuais fraudes e para conseguirmos entregar uma melhor experiência ao participante. Para mais informações acesse nossa política de privacidade.
						</label>
          </div>
        </div>
				<div class="col-12 justify-content-start" v-if="!disabled">
          <div class="checkin-checkbox">
            <input type="checkbox" id="check2" v-model="form.check2"/>
            <label for="check2"> 
							Autorizo o uso e tratamento de minha imagem que poderá ser divulgada em redes sociais e plataformas de streaming por um período de até 5 anos. Para mais informações acesse nossa política de privacidade .
						</label>
          </div>
        </div>
				<div class="col-12 justify-content-start" v-if="!disabled">
          <div class="checkin-checkbox">
            <input type="checkbox" id="check3" v-model="form.check3"/>
            <label for="check3"> 
							Autorizo receber conteúdos de parceiros e patrocinadores do Evento antes, durante e após o HOJE. Para mais informações acesse nossa política de privacidade.
						</label>
          </div>
        </div>
      </div>
      <div class="row" v-if="!disabled">
        <div class="col-xl-3 col-lg-4 col-md-5 col-12">
          <button
            type="submit"
            :class="'ui-button--' + ( form.check1 && form.check2 && form.check3 ? 'green' : 'grey')"
						id="submit-button"
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
  props: ["checkin", "check_passports", "p_code"],
  data() {
    return {
      form: {
        name: "",
        email: "",
        phone: "",
        cpf: "",
        state: "",
        city: "",
        schooling: "",
        works: true,
        cnpj: "",
        position: "",
        company: "",
        code: this.p_code,
				gender: "",
				birthday: "",
				check1: 0,
				check2: 0,
				check3: 0,
      },
      button: "Fazer Check-in",
      disabled: true,
      locations: locations,
      cities: "",
      block: false,
      schooling: [
        "Fundamental",
        "Ensino Médio",
        "Graduando",
        "Graduado",
        "Pós-Graduando",
        "Pós-Graduado",
      ],
    };
  },
	mounted() {
		if (this.p_code) {
			axios
				.post(this.check_passports, { code: this.form.code.toUpperCase() })
				.then((response) => {
					if (response.data.error) {
						var code = document.getElementById('code');
						code.parentElement.classList.remove("ui-correct");
						this.assignError(code, response.data.error);
					} else if (!response.data) {
						var code = document.getElementById('code');
						code.parentElement.classList.remove("ui-correct");
						this.assignError(code, "Esse passaporte não existe");
					} else {
						var code = document.getElementById('code');
						code.parentElement.classList.remove("ui-wronged");
						code.parentElement.dataset.wronged = "Você está quase lá :). Só preencher os campos abaixo.";
						code.parentElement.classList.add("ui-correct");
						this.disabled = false;
					}
				});
		}
	},
	watch: {
    "form.code": function() {
			if (this.form.code.length >= 6) {
				axios
					.post(this.check_passports, { code: this.form.code })
					.then((response) => {
						if (response.data.error) {
							var code = document.getElementById('code');
							code.parentElement.classList.remove("ui-correct");
							this.assignError(code, response.data.error);
						} else if (!response.data) {
							var code = document.getElementById('code');
							code.parentElement.classList.remove("ui-correct");
							this.assignError(code, "Esse passaporte não existe");
						} else {
							var code = document.getElementById('code');
							code.parentElement.classList.remove("ui-wronged");
							code.parentElement.dataset.wronged = "Você está quase lá :). Só preencher os campos abaixo.";
      				code.parentElement.classList.add("ui-correct");
							this.disabled = false;
						}
					});
			}
    },
  },
	updated() {
		document.getElementById("works").checked = this.form.works;
	},
  methods: {
		changeWorks(event) {
			this.form.works = !this.form.works;
		},
    resetForm() {
      this.form = {
        name: "",
        email: "",
        phone: "",
        cpf: "",
        state: "",
        city: "",
        schooling: "",
        works: true,
        cnpj: "",
        position: "",
        company: "",
        code: "",
				gender: "",
				birthday: ""
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
      let is_form_ok = this.isFormOk();
      if (is_form_ok) {
				this.button = "Fazendo checkin...";
				var submit_button = document.getElementById("submit-button");
				submit_button.disabled = true;
        this.removeErrorMessages();
        const sleepingPromise = (delay) =>
          new Promise((resolve) => setTimeout(resolve, delay));
        const sleep = async () => {
          await sleepingPromise(1000);
          axios
            .post(this.checkin, {
              client: this.form,
              address: this.address,
            })
            .then((response) => {
              this.$swal({
								title: "Check-in feito com sucesso 🚀",
								html:
									"Lembre-se de guardar o código do seu passaporte <strong>" + this.form.code + "</strong>. E daí é só esperar o evento e aproveitar 😍",
								confirmButtonText: "Ok",
								confirmButtonColor: "#28a745",
								closeOnClickOutside: true,
								allowOutsideClick: false,
								closeOnClickOutside: false,
								allowEscapeKey: false
							}).then((confirm) => {
								if (confirm.value) {
									location.href = "/cliente/checkin";
								}
							});
            })
            .catch((e) => {});
        };
        sleep();
      }
    },

    isFormOk() {
      this.removeErrorMessages();
      var is_ok = true;

      //Checks if the name has more than two words if it's a PF
      var name = document.getElementById("name");
      if (!(name.value.indexOf(" ") >= 0)) {
        this.assignError(name, "Digite nome e sobrenome, por favor");
        is_ok = false;
      }

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
      var items = [
        "name",
        "email",
        "phone",
        "cpf",
        "state",
        "city",
        "schooling",
        "code",
      ];
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
