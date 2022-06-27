<template>
  <div>
    <form @submit.prevent="submitForm" class="ui-form" method="POST">
      <div class="row login__columns mt-md-4 mt-0">
        <div class="col-12" data-wronged="">
          <div class="login__input-wrapper">
            <input type="email" id="email" v-model="form.email"/>
            <label for="email"> E-mail </label>
          </div>
        </div>
        <div class="col-12" data-wronged="">
          <div class="login__input-wrapper">
            <input
              type="password"
              id="password"
              v-model="form.password"
            />
            <label for="password"> Senha </label>
          </div>
        </div>
      </div>
			<div class="row">
				<div class="col-md-6 col-12 login__submit">
					<button type="submit" class="ui-button--blue" id="submit-button">
						{{ button }}
					</button>
				</div>
				<div class="col-12 d-md-block d-flex justify-content-center">
					<password :endpoint="endpoint" :check_email="check_email" :send_recover="send_recover"></password>
				</div>
			</div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import ForgotPassword from "./TheForgotPassword";

export default {
  props: ["login_endpoint", "home_endpoint", "endpoint", "check_email","send_recover"],
	components: {
		password: ForgotPassword
	},
  data() {
    return {
      form: {
        email: "",
        password: "",
      },
      button: "Entrar"
    };
  },

  methods: {
    submitForm() {
			if (this.isFormOk()) {
				this.button = "Aguarde, estamos logando você..."
				var submit_button = document.getElementById("submit-button");
				submit_button.disabled = true;
				axios
					.post(this.login_endpoint, {
						email: this.form.email,
						password: this.form.password,
					})
					.then((response) => {
						this.$swal({
							title: "Você está logado(a) :)",
							text: "Você foi oficialmente logado no HJ21.",
							type: "success",
							allowOutsideClick: false,
							showConfirmButton: false
						})
						this.button = "Você está logado(a) :)";
						if (location.pathname.split('/')[2] == "carrinho") {
							this.$emit("loginStep", this.form.email);
							this.$swal.close();
						}
						this.setWithExpiry("__AUTH", response.data, 1296000000);
					})
					.catch((e) => {
						if (e.response.data.email) {
							var item = document.getElementById("email");
							this.assignError(item, e.response.data.msg);
							this.button = "Entrar";
							submit_button.removeAttribute("disabled");
						} else if (e.response.data.password) {
							var item = document.getElementById("password");
							this.assignError(item, e.response.data.msg);
							this.button = "Entrar";
							submit_button.removeAttribute("disabled");
						}
					})
					.finally(() => {
						this.button = "Entrar";
					});
			}
    },
    setWithExpiry(key, value, ttl) {
      const now = new Date();
      const item = {
        value: value,
        expiry: now.getTime() + ttl,
      };
      localStorage.setItem(key, JSON.stringify(item));
			if (location.pathname.split('/')[2] != "carrinho") {
      	window.location.href = this.home_endpoint;
			} else {
				axios.post("api/cart/attribution", {
          temp: localStorage.getItem("__TMP"),
          client_id: JSON.parse(localStorage.getItem("__AUTH")).value,
					post_attribution: true
        });
			}
    },
		isFormOk() {
			this.removeErrorMessages();

			var is_ok = true;
			var email = document.getElementById("email");
			var password = document.getElementById("password");

			//Checks if the email is not empty
			if (email.value.length < 1) {
				this.assignError(email, "É obrigatório um e-mail para logar");
				is_ok = false;
			}

			//Cheks if the password is not empty
			if (password.value.length < 1) {
				this.assignError(password, "É obrigatório uma senha para logar");
				is_ok = false;
			}

			return is_ok;
		},
		removeErrorMessages() {
      //Removes all error messages from the inputs
      var items = document.getElementsByClassName("ui-wronged");
      for (var i = items.length - 1; i >= 0; i--) {
        items[items.length - 1].classList.remove("ui-wronged");
      }
    },
    assignError(element, message) {
      //Assign an error message
      element.parentElement.dataset.wronged = message;
      element.parentElement.classList.add("ui-wronged");
    },
  },
};
</script>
