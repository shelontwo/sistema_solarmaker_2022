<template>
  <div>
    <button id="forgot-password" type="button" @click="requestNewPassword()">
      Esqueci minha senha.
      <i id="spin" class="fa fa-spinner fa-pulse"></i>
    </button>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: ["endpoint", "check_email", "send_recover"],
  data() {
    return {
      email: "",
    };
  },

  methods: {
    sendEmail() {
			this.$swal({
				title: "Validando e-mail",
				text: "Estamos validando se este e-mail existe."
			});
      axios
        .post(this.send_recover, { email: this.email })
        .then((response) => {
					this.$swal.close();
          this.$swal({
            title: "E-mail enviado",
            html:
              "Em alguns instantes você receberá um email em <strong>" +
              this.email +
              "</strong> para recuperar sua senha.",
            confirmButtonText: "Ok",
            confirmButtonColor: "#6fe335",
          });
        })
        .catch((error) => {
					this.$swal.close();
					this.requestNewPassword()
				});
    },

    async requestNewPassword() {
      const { value: email } = await this.$swal({
        title: "Recuperar senha",
        input: "email",
        inputLabel: "Seu e-mail",
        inputPlaceholder: "Preencha com o e-mail que você cadastrou no HJ21.",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
      });
      if (email) {
        document.getElementById("spin").style.display = "inline-block";
        this.email = email;
        axios
          .post(this.check_email, { email: this.email })
          .then((response) => {
            this.$swal({
              title: "Oops :|",
              html:
                "Desculpe, mas não encontramos nenhuma conta com o e-mail <strong>" +
                this.email +
                "</strong> Por favor, tente com outro e-mail.",
              confirmButtonText: "Ok",
              confirmButtonColor: "#DF1949",
            });
          })
          .catch((error) => {
			this.sendEmail();
            
          })
          .finally(() => {
            document.getElementById("spin").style.display = "none";
          });
      }
    },
  },
};
</script>

<style>
#spin {
  display: none;
}

#forgot-password {
  background-color: transparent;
  border: 0;
  color: #302c2d;
  font-size: 0.9rem;
  outline: none;
  transition: all 0.3s;
}

#forgot-password:hover {
  color: #6f39ff;
  transition: all 0.3s;
}

@media screen and (max-width: 768px) {
  #forgot-password {
    margin-bottom: 15px;
  }
}
</style>