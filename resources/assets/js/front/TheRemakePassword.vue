<template>
  <div>
    <form @submit.prevent="submitForm" class="ui-form" method="POST">
      <div class="row login__columns mt-md-4 mt-0">
        <div class="col-12" data-wronged="">
          <div class="login__input-wrapper">
            <input
              type="password"
              id="password"
              minlength="6"
              maxlength="20"
              v-model="form.password"
              required
            />
            <label for="password"> Senha </label>
          </div>
        </div>
        <div class="col-12" data-wronged="">
          <div class="login__input-wrapper">
            <input
              type="password"
              id="confirm"
              minlength="6"
              maxlength="20"
              v-model="form.confirm"
              required
            />
            <label for="confirm"> Confirmar senha </label>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 col-12 login__submit">
          <button type="submit" class="ui-button--blue" id="submit-button">
            {{ button }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import ForgotPassword from "./TheForgotPassword";

export default {
  props: ["change_endpoint", "login_endpoint", "verify_token"],
  components: {
    password: ForgotPassword,
  },
  data() {
    return {
      form: {
        password: "",
        confirm: "",
      },
      button: "Redefinir",
      account: "",
      token: "",
    };
  },

  created() {
    const url_token = window.location.search;
    if (!url_token) {
      location.href = "";
    } else {
      const urlParams = new URLSearchParams(url_token);
      this.token = urlParams.get("p");
      if (!this.token) {
        location.href = "";
      } else {
        this.verifyToken(this.token);
      }
    }
  },

  methods: {
    verifyToken(token) {
      axios
        .post(this.verify_token, { token: token })
        .then((response) => {
          this.account = response.data;
        })
        .catch((error) => {
          if (error.response.status == 404) {
            this.$swal({
              title: "Oops!",
              text: "Código ínvalido ou expirado!",
              type: "error",
              timer: 2500,
              // showConfirmButton: false,
              allowOutsideClick: false,
            }).then(() => {
              location.href = "";
            });
          }
        });
    },

    submitForm() {
      if (this.isFormOk()) {
        this.button = "Aguarde, redefinindo sua senha...";
        var submit_button = document.getElementById("submit-button");
        submit_button.disabled = true;
        axios
          .post(this.change_endpoint, {
            password: this.form.password,
            authentication: this.account.authentication,
            email: this.account.email,
            token: this.token,
          })
          .then((response) => {
            this.$swal({
              title: "Redefinida",
              text: "Senha redefinida com sucesso :)",
              type: "success",
              timer: 1500,
              allowOutsideClick: false,
              showConfirmButton: false,
            }).then(() => {
              this.login();
            });
          })
          .catch((e) => {
          });
      }
    },

    login() {
      axios
        .post(this.login_endpoint, {
          email: this.account.email,
          password: this.form.password,
        })
        .then((response) => {
          this.setWithExpiry(
            "__AUTH",
            response.data,
            1296000000,
            this.account.email
          );
        })
        .catch((error) => {});
    },

    setWithExpiry(key, value, ttl, email) {
      const now = new Date();
      const item = {
        value: value,
        expiry: now.getTime() + ttl,
      };
      localStorage.setItem(key, JSON.stringify(item));
      window.location.href = "";
    },

    isFormOk() {
      this.removeErrorMessages();

      var is_ok = true;
      var confirm = document.getElementById("confirm");
      var password = document.getElementById("password");

      //Checks if the confirm is not empty
      if (confirm.value.length < 1) {
        this.assignError(confirm, "É obrigatório confirmar a senha");
        is_ok = false;
      }

      //Cheks if the password is not empty
      if (password.value.length < 1) {
        this.assignError(password, "É obrigatório uma senha");
        is_ok = false;
      }

      //Checks if the confirm is not empty
      if (confirm.value.length < 6) {
        this.assignError(confirm, "O tamanho mínimo é seis caracteres");
        is_ok = false;
      }

      //Cheks if the password is not empty
      if (password.value.length < 6) {
        this.assignError(password, "O tamanho mínimo é seis caracteres");
        is_ok = false;
      }

      //Checks if the confirm is not empty
      if (confirm.value.length > 20) {
        this.assignError(confirm, "O tamanho máximo é vinte caracteres");
        is_ok = false;
      }

      //Cheks if the password is not empty
      if (password.value.length > 20) {
        this.assignError(password, "O tamanho máximo é vinte caracteres");
        is_ok = false;
      }

      if (password.value != confirm.value) {
        this.assignError(password, "As senhas devem ser iguais");
        this.assignError(confirm, "As senhas devem ser iguais");
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
