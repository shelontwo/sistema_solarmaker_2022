<template>
	<form @submit.prevent="submitForm" class="green-section__form" method="POST">
		<input aria-label="Seu nome" type="text" class="green-section__modal-input" name="name" v-model="form.name" placeholder="Seu nome" required>
		<input aria-label="Seu e-mail" type="email" class="green-section__modal-input" name="email" v-model="form.email" placeholder="Seu e-mail" required>
		<the-mask aria-label="Telefone" name="phone" :masked="true" class="green-section__modal-input" placeholder="Telefone" v-model="form.phone" :mask="['(##) ####-####', '(##) #####-####']" required />
		<input aria-label="Nome da empresa" type="text" class="green-section__modal-input" name="company" v-model="form.company" placeholder="Nome da empresa" required>
		<div class="d-flex align-items-center green-section__modal-check">
			<input aria-label="Política de Privacidade" type="checkbox" v-model="form.privacy_policy" id="modal-checkbox" class="green-section__modal-input">
			<label for="modal-checkbox">Concordo com a <a :href="this.privacy_policy" rel="noreferrer" target="_blank">Política de Privacidade</a></label>
		</div>
		<p class="green-section__modal-text">
			Ao concordar com a política de privacidade, você aceita receber contatos via, Telefone, mensagem pelo WhatsApp e E-mails com conteúdos de Negócios e informativos.
		</p>
		<button type="sbumit" class="ui-button--big green-section__form-button">
			{{ button }}
		</button>
	</form>
</template>

<script>
import {TheMask} from 'vue-the-mask'
import axios from 'axios'
  export default {
  data () {
    return {
      button: 'Tenho interesse em Patrocinar o HJ21',
      disabled: false,
      form: {
				name: '',
        email: '',
				phone: '',
				company: '',
				privacy_policy: ''
      }
    }
  },
  methods: {
    resetForm () {
      this.form = {
				name: '',
        email: '',
				phone: '',
				company: '',
				privacy_policy: ''
      }
    },
    submitForm () {
      this.button = 'Enviando...'
      this.disabled = true
      axios.post(this.endpoint, this.form)
      .then(response => {
        this.resetForm()
				document.getElementsByClassName('modal-body green-section__modal-front')[0].style.display = 'none';
				document.getElementsByClassName('modal-body green-section__modal-back')[0].style.display = 'block';
      })
      .catch(e => {
        var msg = 'Houve um erro ao enviar seus dados! Por favor, tente novamente.'
				document.getElementsByClassName('modal-body green-section__modal-front')[0].style.display = 'none';
				document.getElementsByClassName('modal-body green-section__modal-back')[0].style.display = 'none';
				document.getElementsByClassName('modal-body green-section__modal-error')[0].style.display = 'block';
				this.resetForm()
      })
      .finally(() => {
        this.button = 'Tenho interesse em Patrocinar o HJ21'
        this.disabled = false
      })
    }
  },
  props:['endpoint', 'privacy_policy']
}
</script>