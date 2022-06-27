<template>
	<div class="lp-section__form">
		<p class="lp-section__form-lead">
			Seja avisado assim que a compra de passaportes, palestrantes e novidades forem liberadas:
		</p>
		<form @submit.prevent="submitForm" class="green-section__form" method="POST">
			<input type="text" class="green-section__modal-input" name="name" v-model="form.name" placeholder="Seu nome" required>
			<input type="email" class="green-section__modal-input" name="email" v-model="form.email" placeholder="Seu e-mail" required>
			<the-mask aria-label="Telefone" name="phone" :masked="true" class="green-section__modal-input" placeholder="Telefone" v-model="form.phone" :mask="['(##) ####-####', '(##) #####-####']" required />
			<input type="text" class="green-section__modal-input" name="company" v-model="form.company" placeholder="Sua empresa" required>
			<div class="lp-section__container">
				<div class="d-flex align-items-center green-section__modal-check">
					<input type="checkbox" required v-model="form.privacy_policy" id="modal-checkbox" class="green-section__modal-input">
					<label for="modal-checkbox">Concordo com a <a href="https://ingresso.eventohoje.com.br/cadastro/politicas" rel="noreferrer" target="_blank">Política de Privacidade</a></label>
				</div>
				<p class="lp-section__small-text">
					Ao concordar com a política de privacidade, você aceita receber contatos via, Telefone, mensagem pelo WhatsApp e E-mails com conteúdos de Negócios e informativos do Evento HOJE.
				</p>
			</div>
			<button type="sbumit" class="ui-button--big green-section__form-button">
				{{ button }}
			</button>
		</form>
	</div>
</template>

<script>
import {TheMask} from 'vue-the-mask'
import axios from 'axios'
  export default {
  data () {
    return {
      button: 'QUERO FICAR POR DENTRO',
      disabled: false,
      form: {
				name: '',
        email: '',
				phone: '',
				privacy_policy: '',
				company: ''
      }
    }
  },
  methods: {
    resetForm () {
      this.form = {
				name: '',
        email: '',
				phone: '',
				privacy_policy: '',
				company: ''
      }
    },
    submitForm () {
      this.button = 'ENVIANDO...'
      this.disabled = true
      axios.post(this.endpoint, this.form)
      .then(response => {
        this.resetForm()
				var msg = 'Pode ficar tranquilo, manteremos você por dentro do HJ21!'
        this.$swal("Tudo pronto :)", msg, "success")
      })
      .catch(e => {
        var msg = 'Houve um erro ao enviar seus dados! Por favor, tente novamente.'
        this.$swal("Oops!", msg, "error")
        console.error(e)
      })
      .finally(() => {
        this.button = 'QUERO FICAR POR DENTRO'
        this.disabled = false
      })
    }
  },
  props:['endpoint', 'privacy_policy']
}
</script>