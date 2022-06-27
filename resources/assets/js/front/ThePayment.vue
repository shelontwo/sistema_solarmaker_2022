<template>
  <div class="payment">
    <div
      class="payment__card payment__card-input d-flex flex-column justify-content-between"
    >
			<div></div>
      <div class="d-flex justify-content-between">
        <img src="/img/chip.png" width="60" height="50" alt="Chip" />
        <img src="/img/visa.png" width="75" height="50" alt="Chip" v-if="is_visa"/>
				<img src="/img/mastercard.png" width="74" height="50" alt="Chip" v-if="!is_visa"/>
      </div>
      <the-mask
        aria-label="CEP"
        id="text"
        :masked="true"
				placeholder="#### #### #### ####"
        :mask="['#### #### #### ####']"
        v-model="form.number"
				v-on:change="cardNumberChange()"
        required
      />
      <div class="d-flex justify-content-between">
				<div>
					<span>
						Proprietário do cartão
					</span>
        	<input type="text" placeholder="Nome completo" :value="form.name" />
				</div>
				<div class="d-flex flex-column align-items-end justify-content-end payment__input">
					<span>
						Validade
					</span>
        	<input type="text" placeholder="Mês/Ano" :value="form.expiryMonth + '/' + form.expiryYear" />
				</div>
      </div>
    </div>
    <form
      @submit.prevent="makePayment"
      class="ui-form payment__form"
      method="POST"
    >
      <div class="row payment__columns mt-md-4 mt-0">
        <div class="col-12" data-wronged="">
          <div class="payment__input-wrapper">
            <the-mask
              aria-label="Número do cartão"
              id="text"
              :masked="true"
              :mask="['#### #### #### ####']"
              v-model="form.number"
							v-on:change="cardNumberChange()"
              required
            />
            <label for="password"> Número do cartão* </label>
          </div>
        </div>
        <div class="col-12" data-wronged="">
          <div class="payment__input-wrapper">
            <input type="text" id="owner" v-model="form.name" required />
            <label for="owner"> Proprietário do cartão* </label>
          </div>
        </div>
				<div class="col-12" data-wronged="">
          <div class="payment__input-wrapper">
            <the-mask
              aria-label="CPF"
              id="cpf"
              :masked="true"
              :mask="['###.###.###-##']"
              v-model="form.cpf"
							v-on:change="cardNumberChange()"
              required
            />
            <label for="cpf"> CPF* </label>
          </div>
        </div>
        <div class="col-md-6 col-12" data-wronged="">
          <div class="payment__input-wrapper">
            <select id="month" v-model="form.expiryMonth" required>
              <option disabled selected>Mês</option>
              <option v-for="index in 12" :key="index" :value="index">
                {{ index }}
              </option>
							<option>123123123123</option>
            </select>
            <label for="month"> Mês* </label>
          </div>
        </div>
        <div class="col-md-6 col-12" data-wronged="">
          <div class="payment__input-wrapper">
            <select id="year" v-model="form.expiryYear" required>
              <option disabled selected>Ano</option>
              <option v-for="index in 12" :key="index + 2020" :value="index + 2020">
                {{ index + 2020 }}
              </option>
							<option>123123123123</option>
            </select>
            <label for="year"> Ano* </label>
          </div>
        </div>
        <div class="col-md-6 col-12" data-wronged="">
          <div class="payment__input-wrapper">
            <the-mask
              aria-label="CVV"
              id="cvv"
              :masked="true"
              :mask="['####']"
              v-model="form.cvv"
              required
            />
            <label for="cvv"> CVV* </label>
          </div>
        </div>
				<div class="col-md-6 col-12" data-wronged="">
          <div class="payment__input-wrapper">
            <select id="installment" v-model="form.parcels" required>
              <option disabled selected>Parcelas</option>
              <option v-for="index in 12" :key="index" :value="index">
                {{ index }}
              </option>
							<option>123123123123</option>
            </select>
            <label for="installment"> Número de Parcelas* </label>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 payment__submit">
          <button type="submit" class="ui-button--blue" :disabled="disabled">
            {{ button }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: ["step", "cupon", "payment", "go_pay"],
  data() {
    return {
      form: {
        name: "",
				parcels: "",
				cpfCnpj: "",
        number: "",
        expiryMonth: "Mês",
        expiryYear: "Ano",
        cvv: "",
				temp: "",
				payment_method: 1
      },
      button: "Pagar",
      disabled: false,
			is_visa: true
    };
  },
	methods: {
		cardNumberChange() {
			if (this.form.number[0] == "4") {
				this.is_visa = true;
			} else {
				this.is_visa = false;
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
		assignError(element, message) {
      //Assign an error message
      element.parentElement.dataset.wronged = message;
      element.parentElement.classList.add("ui-wronged");
    },
		removeErrorMessages() {
      //Removes all error messages from the inputs
      var items = document.getElementsByClassName("ui-wronged");
      for (var i = items.length - 1; i >= 0; i--) {
        items[items.length - 1].classList.remove("ui-wronged");
      }
    },
		isFormOk() {
			this.removeErrorMessages();
			var is_ok = true;

			// Checks if the name is complete
			var name = document.getElementById("owner");
			if (!(name.value.indexOf(" ") >= 0)) {
				this.assignError(name, "Digite seu nome completo");
				is_ok = false;
			}
			
			// Checks if the CPF is valid
			var cpf = document.getElementById("cpf");
			if (!this.checkCPF(cpf.value)) {
				this.assignError(cpf, "Digite um cpf válido");
				is_ok = false
			}

			// Check if the installment number is on the list
			var parcels = document.getElementById("installment");
			if (![1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].includes(+parcels.value)) {
				this.assignError(parcels, "Escolha uma opção válida");
				is_ok = false
			}

			// Check if the month is on the list
			var month = document.getElementById("month");
			if (![1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].includes(+month.value)) {
				this.assignError(month, "Escolha uma opção válida");
				is_ok = false
			}

			// Check if the year is on the list
			var year = document.getElementById("year");
			if (![2021, 2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030, 2031, 2032].includes(+year.value)) {
				this.assignError(year, "Escolha uma opção válida");
				is_ok = false
			}
		
			// 	"name":"PAULO",
			// "payment_method": ,1
			// "cpfCnpj": "08846169913",
			// "number": "5162306219378829",
			// "expiryMonth": "07",
			// "expiryYear": "2021",
			// "ccv" : "318",
			// "temp":"arthur@teste.com"

			return is_ok;
		},
		makePayment() {
			if (this.isFormOk()) {
				this.form.temp = JSON.parse(localStorage.getItem("__TMP")).email;
				this.form
				axios
					.post(this.payment, this.form)
					.then((response) => {
					})
					.catch((error) => {
						if (response.data.error == "Informe o código de segurança do seu cartão.") {
							var cvv = document.getElementById("cvv");
							this.assignError(cvv, "Informe o código de segurança do seu cartão");
						}
					})
			}
		}
	}
};
</script>
