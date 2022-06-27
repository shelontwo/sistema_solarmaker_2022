<template>
  <div class="cart row">
    <!-- Defines the tabs -->
    <div
      class="
        col-12
        cart__wrapper
        d-flex
        align-items-center
        justify-content-between
      "
    >
      <p class="cart__step" v-if="cart_step == 0">
        <i class="fa fa-shopping-cart"></i>
        Carrinho
      </p>
      <p class="cart__step" v-if="cart_step == 1">
        <i class="fa fa-user"></i>
        Identificação
      </p>
      <p class="cart__step" v-if="cart_step == 2">
        <i class="fa fa-credit-card-alt"></i>
        Pagamento
      </p>
      <p class="cart__step" v-if="cart_step == 3">
        <i class="fa fa-paper-plane-o"></i>
        Ao Infinito
      </p>
      <ul class="nav nav-tabs cart__tabs" id="myTab" role="tablist">
        <li
          class="nav-item"
          role="presentation"
          @click="changeCartStepName($event)"
        >
          <a
            :class="
              'nav-link ' +
              (step == 0
                ? 'active'
                : step == 3 && pix_show
                ? 'disabled finished'
                : step > 0 && pix_show
                ? 'disabled finished'
                : step > 0
                ? 'finished'
                : '')
            "
            id="home-tab"
            data-toggle="tab"
            href="#home"
            role="tab"
            aria-controls="home"
            aria-selected="true"
          >
            <i class="fa fa-shopping-cart mr-3"></i>
            <span> Carrinho </span>
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a
            :class="
              'nav-link ' +
              (is_logged || step > 1
                ? 'disabled finished'
                : step == 1
                ? 'active'
                : step < 1 || pix_show
                ? 'disabled'
                : '')
            "
            id="profile-tab"
            data-toggle="tab"
            href="#profile"
            role="tab"
            aria-controls="profile"
            aria-selected="false"
            @click="changeCartStepName($event)"
          >
            <i class="fa fa-user mr-3"></i>
            <span>
              {{ is_logged ? "Você está logado" : "Identificação" }}
            </span>
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a
            :class="
              'nav-link ' +
              (step == 2
                ? 'active'
                : step == 3 && pix_show
                ? 'disabled finished'
                : step > 2
                ? 'finished'
                : step < 2 || pix_show
                ? 'disabled'
                : '')
            "
            id="contact-tab"
            data-toggle="tab"
            href="#contact"
            role="tab"
            aria-controls="contact"
            aria-selected="false"
            @click="changeCartStepName($event)"
          >
            <i class="fa fa-credit-card-alt mr-3"></i>
            <span> Pagamento </span>
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a
            :class="
              'nav-link ' + (step == 3 ? 'active' : step < 3 ? 'disabled' : '')
            "
            id="eventohj-tab"
            data-toggle="tab"
            href="#eventohj"
            role="tab"
            aria-controls="contact"
            aria-selected="false"
            @click="changeCartStepName($event)"
          >
            <i class="fa fa-paper-plane-o mr-3"></i>
            <span> Ao Infinito </span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Content of the tabs -->
    <div class="tab-content w-100" id="myTabContent">
      <div
        class="row no-gutters w-100 tab-pane fade show active"
        id="home"
        role="tabpanel"
        aria-labelledby="home-tab"
      >
        <div class="col-xl-9 col-lg-8 col-md-7 col-12">
          <div class="cart__items-wrapper">
            <the-buy
              @discount="setDiscount"
              v-if="products.length > 0"
              :products="products"
              :sum="sum"
              :sub="sub"
              :cancel="cancel"
              :step="step"
            ></the-buy>
            <div class="cart__no-products" v-if="products.length == 0">
              Sem passaportes ainda :(
            </div>
          </div>
					<div class="cart__cashback">
						<img src="/img/cashback.png" alt="Selo de Cashback">
						<div>
							<p>
								Ao finalizar sua compra, o time Evento HOJE entrará em contato para explicar como utilizar o seu Cashback em produtos A9 Performance.
							</p>
							<p>
								<strong>
									Cada Passaporte adquirido, retornará para você:
								</strong>
							</p>
							<p>
								GO Pass - R$ 200 em Cashback
							</p>
							<p>
								VIP Pass - R$ 300 em Cashback
							</p>
							<p>
								Platinum Pass - R$400 em Cashback
							</p>
							<p>
								Member Black - R$500 em CashBack (ESGOTADO)
							</p>
							<p>
								*Esta promoção possui quantidades limitadas de passaportes e pode ser desativada sem aviso prévio.
							</p>
						</div>
					</div>
          <a href="/#passports-home" class="cart__buy-one-more">
            <span>
              <i class="fa fa-plus"></i>
            </span>
            <p>Adicionar novo passaporte</p>
          </a>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-5 col-12">
          <aside>
            <div class="container">
              <div class="row">
                <h2 class="col-12 cart__bold-text">Resumo do Pedido</h2>
              </div>
            </div>
            <ul class="container" v-if="products.length > 0">
              <li
                class="row no-gutters cart__items"
                v-for="(item, index) in products"
                :key="index"
              >
                <p class="col-sm-5 col-6">
                  {{ item.quantity }} {{ item.title }}
                </p>
                <span class="col-sm-1 col-1 cart__price-sign"> R$ </span>
                <span class="col-sm-6 col-5 cart__bold-text">
                  {{
                    (item.quantity * item.total_price).toLocaleString("pt-br", {
                      maximumFractionDigits: 2,
                      minimumFractionDigits: 2,
                    })
                  }}
                </span>
              </li>
            </ul>
            <ul class="container" v-if="products.length == 0">
              <li class="row no-gutters cart__items">
                Sem passaportes ainda :(
              </li>
            </ul>
            <form action="#" class="ui-form my-3">
              <div class="col-12">
                <div
                  class="signup__input-wrapper cupon_input"
                  :class="cupon_disabled ? cupon_disabled_class : ''"
                >
                  <input
                    type="text"
                    class="cupon"
                    minlength="6"
                    maxlength="20"
                    v-model="cupon_code"
                    @blur="setCupon()"
                    :disabled="!!cupon_disabled"
                    required
                  />
                  <button type="button" @click="removeCupon($event)">
                    <i class="fa fa-close"></i>
                  </button>
                  <label for="cupon"> Cupom de Desconto </label>
                </div>
              </div>
            </form>
            <ul class="container">
              <li class="row no-gutters cart__items">
                <p class="col-sm-5 col-6">Desconto do Cupom</p>
                <span class="col-1 cart__price-sign"> R$ </span>
                <span class="col-sm-6 col-5 cart__bold-text">
                  {{
                    discount.toLocaleString("pt-br", {
                      maximumFractionDigits: 2,
                      minimumFractionDigits: 2,
                    })
                  }}
                </span>
              </li>
              <li class="cart__items no-gutters">
                <hr />
              </li>
              <li class="row no-gutters">
                <p class="col-sm-5 col-6">Total</p>
                <span class="col-1 cart__price-sign"> R$ </span>
                <span class="col-sm-6 col-5 cart__bold-text">
                  {{
                    (total - discount).toLocaleString("pt-br", {
                      maximumFractionDigits: 2,
                      minimumFractionDigits: 2,
                    })
                  }}
                </span>
              </li>
            </ul>
            <button
              class="ui-button--green cart__submit mt-3"
              v-on:click="changeStep()"
              :disabled="products.length == 0"
            >
              Avançar
            </button>
          </aside>
        </div>
      </div>

      <div
        class="tab-pane fade row"
        id="profile"
        role="tabpanel"
        aria-labelledby="profile-tab"
      >
        <div class="col-lg-6 col-md-7 col-12 login__form--twice">
          <div v-if="login_not_signup">
            <h1 class="ui-section-title--black">Entrar</h1>
            <p>Faça login na sua conta do HJ21.</p>
            <login
              @loginStep="makeStepWalkOnLogin"
              :endpoint="login_route"
              :login_endpoint="login_endpoint"
              :home_endpoint="home_endpoint"
            ></login>
            <p class="ui-text--black mt-md-4 mt-0">
              <strong>
                Ainda não tem uma conta?
                <button
                  v-on:click="changeToLoginSignup()"
                  class="change_login login__login"
                >
                  Cadastre-se agora!
                  <input value="false" type="hidden" />
                </button>
              </strong>
            </p>
          </div>
          <div v-if="!login_not_signup">
            <h1 class="ui-section-title--black">Cadastre-se</h1>
            <p id="signup-change">
              Faça seu cadastro no HJ21. Precisaremos de seus dados e endereço.
            </p>
            <signup
              @signupStep="makeStepWalkOnSignuUp"
              :check_email_endpoint="check_email_endpoint"
              :register_endpoint="register_endpoint"
              :login_endpoint="login_endpoint"
              :home_endpoint="home_endpoint"
            ></signup>
            <p class="ui-text--black mt-md-4 mt-0">
              <strong>
                Já tem uma conta?
                <button
                  v-on:click="changeToLoginSignup()"
                  class="change_login login__login"
                >
                  Entre agora!
                </button>
              </strong>
            </p>
          </div>
        </div>
        <div
          v-if="!login_not_signup"
          class="
            col-lg-6 col-md-5 col-12
            d-flex
            flex-column
            justify-content-end
          "
        >
          <img src="/img/girl-full.webp" alt="Personagem do Evento Hoje" />
        </div>
        <div
          v-if="login_not_signup"
          class="
            col-lg-6 col-md-5 col-12
            d-flex
            flex-column
            justify-content-end
          "
        >
          <img src="/img/guy.webp" alt="Personagem do Evento Hoje" />
        </div>
      </div>
      <div
        class="tab-pane row fade"
        id="contact"
        role="tabpanel"
        aria-labelledby="contact-tab"
      >
        <div
          class="
            col-xl-9 col-lg-8 col-md-7 col-12
            d-flex
            justify-content-center
          "
        >
          <form class="ui-form w-100" method="POST">
            <div class="row signup__columns mt-md-4 mt-0">
              <div class="col-md-4 col-sm-6 col-12">
                <label
                  for="paper"
                  :class="
                    'signup__radio' +
                    (form.payment_method == 2 ? ' selected-label' : '')
                  "
                >
                  <input
                    type="radio"
                    id="paper"
                    name="payment_method"
                    value="2"
                    v-model="form.payment_method"
                    :disabled="pix_show ? true : false"
                    required
                  />
                  <span></span>
                  Boleto
                </label>
              </div>
              <div class="col-md-4 col-sm-6 col-12">
                <label
                  for="card"
                  :class="
                    'signup__radio' +
                    (form.payment_method == 1 ? ' selected-label' : '')
                  "
                >
                  <input
                    type="radio"
                    id="card"
                    name="payment_method"
                    value="1"
                    v-model="form.payment_method"
                    :disabled="pix_show ? true : false"
                    required
                  />
                  <span></span>
                  Cartão de Crédito
                </label>
              </div>
              <div class="col-md-4 col-sm-6 col-12">
                <label
                  for="pix"
                  :class="
                    'signup__radio' +
                    (form.payment_method == 3 ? ' selected-label' : '')
                  "
                >
                  <input
                    type="radio"
                    id="pix"
                    name="payment_method"
                    value="3"
                    v-model="form.payment_method"
                    :disabled="pix_show ? true : false"
                    required
                  />
                  <span></span>
                  PIX
                </label>
              </div>
            </div>
          </form>
        </div>
        <div
          class="
            col-xl-9 col-lg-8 col-md-7 col-12
            d-flex
            justify-content-center
          "
        >
          <div class="payment--no-gutters" v-if="form.payment_method == 3">
            <strong>Importante:</strong>
            <ul>
              <li>
                Acesse seu internet banking e efetue a leitura do
                <strong>QR Code</strong> ou copie o código;
              </li>
              <li>Pix válido até: <strong>meia noite de hoje</strong>;</li>
              <li>
                Após efetuar o pagamento, clique em: <strong>avançar</strong>;
              </li>
              <li>
                Precisaremos que você digite abaixo o seu
                <strong>CPF</strong> ou <strong>CNPJ</strong> para gerar o
                <strong>QR Code</strong>.
              </li>
            </ul>
            <form
              @submit.prevent="makePayment"
              class="ui-form d-flex flex-column align-items-center"
              method="POST"
              v-if="!pix_show"
            >
              <div class="row login__columns mt-md-4 mt-0">
                <div class="col-12" data-wronged="">
                  <div class="login__input-wrapper">
                    <the-mask
                      aria-label="CPF ou CPNJ"
                      name="cpf_cnpj"
                      id="cpf_cnpj"
                      :masked="true"
                      :mask="['###.###.###-##', '##.###.###/####-##']"
                      v-model="form.cpfCnpj"
                      required
                    />
                    <label for="cpf_cnpj"> CPF ou CPNJ*</label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-12">
                  <button
                    type="submit"
                    id="pay-button"
                    class="ui-button--green"
                    :disabled="disabled"
                  >
                    GERAR PIX
                  </button>
                </div>
              </div>
            </form>
            <img
              v-if="pix_show"
              :src="'data:image/png;base64,' + pix_base64"
              alt="QR Code do PIX"
            />
            <div class="d-flex cart_pix">
              <button
                @click="copyPIX()"
                v-if="pix_show"
                type="button"
                class="ui-button--green"
              >
                COPIAR PIX
              </button>
              <button
                @click="changeStepPaymentPIX()"
                v-if="pix_show"
                type="button"
                class="ui-button--green"
              >
                Avançar
              </button>
            </div>
          </div>
          <div class="payment--no-gutters" v-if="form.payment_method == 2">
            <strong>Importante:</strong>
            <ul>
              <li>
                Ao clicar em <strong>"gerar boleto"</strong>, você poderá
                imprimir o boleto para pagamento;
              </li>
              <li>
                Depois de gerar o o boleto, você terá ele aberto em uma nova aba
                e irá para a última etapa do carrinho;
              </li>
              <li>
                O boleto estará disponível em
                <strong>Meus passaportes</strong> até ser pago;
              </li>
              <li>O boleto tem validade de <strong>3 dias úteis</strong>;</li>
              <li>
                A liberação do seu passaporte acontecerá em até
                <strong>3 dias úteis</strong> após o pagamento do boleto.
              </li>
              <li>
                Precisaremos que você digite abaixo o seu
                <strong>CPF</strong> ou <strong>CNPJ</strong> para gerar o
                <strong>boleto</strong>.
              </li>
            </ul>
            <form
              @submit.prevent="makePayment"
              class="ui-form d-flex flex-column align-items-center"
              method="POST"
            >
              <div class="row login__columns mt-md-4 mt-0">
                <div class="col-12" data-wronged="">
                  <div class="login__input-wrapper">
                    <the-mask
                      aria-label="CPF ou CPNJ"
                      name="cpf_cnpj"
                      id="cpf_cnpj"
                      :masked="true"
                      :mask="['###.###.###-##', '##.###.###/####-##']"
                      v-model="cpf_cnpj"
                      required
                    />
                    <label for="cpf_cnpj"> CPF ou CPNJ* </label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <button
                    type="submit"
                    id="generate-ticket"
                    class="ui-button--green"
                    :disabled="disabled"
                  >
                    GERAR BOLETO
                  </button>
                </div>
              </div>
            </form>
          </div>
          <div class="payment" v-if="form.payment_method == 1">
            <div
              class="
                payment__card payment__card-input
                d-flex
                flex-column
                justify-content-between
              "
            >
              <div></div>
              <div class="d-flex justify-content-between">
                <img src="/img/chip.png" width="60" height="50" alt="Chip" />
                <img
                  src="/img/visa.png"
                  width="75"
                  height="25"
                  alt="Chip"
                  v-if="card_flag == 1"
                />
                <img
                  src="/img/mastercard.svg"
                  width="74"
                  height="50"
                  alt="Chip"
                  v-if="card_flag == 2"
                />
              </div>
              <the-mask
                aria-label="CEP"
                id="text"
                :masked="true"
                placeholder="#### #### #### ####"
                :mask="['#### #### #### ####']"
                v-model="form.number"
                disabled
                required
              />
              <div class="d-flex justify-content-between">
                <div>
                  <span> Proprietário do cartão </span>
                  <input
                    type="text"
                    placeholder="Nome completo"
                    :value="form.name"
                    disabled
                  />
                </div>
                <div
                  class="
                    d-flex
                    flex-column
                    align-items-end
                    justify-content-end
                    payment__input
                  "
                >
                  <span> Validade </span>
                  <input
                    type="text"
                    placeholder="Mês/Ano"
                    :value="form.expiryMonth + '/' + form.expiryYear"
                    disabled
                  />
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
                      id="number"
                      :masked="true"
                      :mask="['#### #### #### ####']"
                      v-model="form.number"
                      required
                    />
                    <label for="number"> Número do cartão* </label>
                  </div>
                </div>
                <div class="col-12" data-wronged="">
                  <div class="payment__input-wrapper">
                    <input
                      type="text"
                      id="owner"
                      v-model="form.name"
                      required
                    />
                    <label for="owner"> Proprietário do cartão* </label>
                  </div>
                </div>
                <div class="col-12" data-wronged="">
                  <div class="payment__input-wrapper">
                    <the-mask
                      aria-label="CPF"
                      id="cpf"
                      :masked="true"
                      :mask="['###.###.###-##', '##.###.###/####-##']"
                      v-model="form.cpfCnpj"
                      required
                    />
                    <label for="cpf"> CPF ou CNPJ* </label>
                  </div>
                </div>
                <div class="col-md-6 col-12" data-wronged="">
                  <div class="payment__input-wrapper">
                    <select id="month" v-model="form.expiryMonth" required>
                      <option disabled selected>Mês</option>
                      <option v-for="index in 12" :key="index" :value="index">
                        {{ index }}
                      </option>
                    </select>
                    <label for="month"> Mês* </label>
                  </div>
                </div>
                <div class="col-md-6 col-12" data-wronged="">
                  <div class="payment__input-wrapper">
                    <select id="year" v-model="form.expiryYear" required>
                      <option disabled selected>Ano</option>
                      <option
                        v-for="index in 12"
                        :key="index + 2020"
                        :value="index + 2020"
                      >
                        {{ index + 2020 }}
                      </option>
                    </select>
                    <label for="year"> Ano* </label>
                  </div>
                </div>
                <div class="col-md-6 col-12" data-wronged="">
                  <div class="payment__input-wrapper">
                    <the-mask
                      aria-label="CVV"
                      id="ccv"
                      :masked="true"
                      :mask="['####']"
                      v-model="form.ccv"
                      required
                    />
                    <label for="ccv"> CVV* </label>
                  </div>
                </div>
                <div class="col-md-6 col-12" data-wronged="">
                  <div class="payment__input-wrapper">
                    <select id="installment" v-model="form.parcels" required>
                      <option disabled selected>Parcelas</option>
                      <option
                        v-for="index in +max_installments"
                        :key="index"
                        :value="index"
                      >
                        {{ index }} - R$
                        {{
                          ((total - discount) / index).toLocaleString("pt-br", {
                            maximumFractionDigits: 2,
                            minimumFractionDigits: 2,
                          })
                        }}
                        (sem juros)
                      </option>
                    </select>
                    <label for="installment"> Número de Parcelas* </label>
                  </div>
                </div>
                <div class="col-12 col-sm-6 mx-auto">
                  <button
                    class="ui-button--green cart__submit"
                    id="pay-button"
                    v-on:click="makePayment()"
                    :disabled="products.length == 0"
                  >
                    Avançar
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-5 col-12">
          <aside>
            <div class="container">
              <div class="row">
                <h2 class="col-12 cart__bold-text">Resumo do Pedido</h2>
              </div>
            </div>
            <ul class="container" v-if="products.length > 0">
              <li
                class="row no-gutters cart__items"
                v-for="(item, index) in products"
                :key="index"
              >
                <p class="col-sm-5 col-6">
                  {{ item.quantity }} {{ item.title }}
                </p>
                <span class="col-sm-1 col-1 cart__price-sign"> R$ </span>
                <span class="col-sm-6 col-5 cart__bold-text">
                  {{
                    (item.quantity * item.total_price).toLocaleString("pt-br", {
                      maximumFractionDigits: 2,
                      minimumFractionDigits: 2,
                    })
                  }}
                </span>
              </li>
            </ul>
            <ul class="container" v-if="products.length == 0">
              <li class="row no-gutters cart__items">
                Sem passaportes ainda :(
              </li>
            </ul>
            <form action="#" class="ui-form my-3">
              <div class="col-12">
                <div
                  class="signup__input-wrapper cupon_input"
                  :class="cupon_disabled ? cupon_disabled_class : ''"
                >
                  <input
                    type="text"
                    class="cupon2"
                    v-model="cupon_code"
                    @blur="setCupon()"
                    :disabled="!!cupon_disabled"
                    required
                  />
                  <button type="button" @click="removeCupon($event)">
                    <i class="fa fa-close"></i>
                  </button>
                  <label for="cupon"> Cupom de Desconto </label>
                </div>
              </div>
            </form>
            <ul class="container">
              <li class="row no-gutters cart__items">
                <p class="col-sm-5 col-6">Desconto do Cupom</p>
                <span class="col-1 cart__price-sign"> R$ </span>
                <span class="col-sm-6 col-5 cart__bold-text">
                  {{
                    discount.toLocaleString("pt-br", {
                      maximumFractionDigits: 2,
                      minimumFractionDigits: 2,
                    })
                  }}
                </span>
              </li>
              <li class="cart__items no-gutters">
                <hr />
              </li>
              <li class="row no-gutters">
                <p class="col-sm-5 col-6">Total</p>
                <span class="col-1 cart__price-sign"> R$ </span>
                <span class="col-sm-6 col-5 cart__bold-text">
                  {{
                    (total - discount).toLocaleString("pt-br", {
                      maximumFractionDigits: 2,
                      minimumFractionDigits: 2,
                    })
                  }}
                </span>
              </li>
            </ul>
          </aside>
        </div>
      </div>
      <div
        class="tab-pane row fade"
        id="eventohj"
        role="tabpanel"
        aria-labelledby="eventohj-tab"
      >
        <div class="container">
          <div
            class="row flex-column align-items-center justify-content-center"
          >
            <div class="col-12 d-flex flex-column align-items-center">
              <p class="ui-section-title--black">
                Você já está pronto para fazer parte do HJ21
              </p>
              <p class="ui-text--black mb-3">
                Muito obrigado por embarcar conosco no HJ21!
              </p>
              <p class="ui-text--black mb-3">
                Um e-mail com detalhes da sua compra foi enviado para
                <strong>{{ email }}</strong
                >.
              </p>
              <a
                :href="url"
                target="_blank"
                class="ui-button--green eventohj-button"
                v-if="this.form.payment_method == 2"
              >
                Visualizar boleto
              </a>
              <p class="ui-text--black">Nos vemos no</p>
              <img src="/img/HJ21.svg" alt="HJ21" />
              <a :href="passports" class="ui-button--big-red m-3">
                Ver meus passaportes
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import TheBuy from "./TheBuyPassport.vue";
import Login from "./TheLoginForm.vue";
import SignUp from "./TheSignUpForm.vue";
import Payment from "./ThePayment.vue";

export default {
  props: [
    "retrieve_open_cart",
    "sum",
    "sub",
    "cancel",
    "payment",
    "passports",
    "login_route",
    "login_endpoint",
    "home_endpoint",
    "check_email_endpoint",
    "register_endpoint",
    "cupon",
    "max_parcels",
  ],
  components: {
    the_buy: TheBuy,
    login: Login,
    signup: SignUp,
    payment: Payment,
  },
  data() {
    return {
      login_not_signup: false,
      is_logged: false,
      products: "",
      total: 0,
      step: 0,
      cupon_code: "",
      cupon_disabled: "",
      email: "",
      form: {
        name: "",
        parcels: "",
        cpfCnpj: "",
        number: "",
        expiryMonth: "Mês",
        expiryYear: "Ano",
        ccv: "",
        temp: "",
        payment_method: 1,
        client_id: "",
      },
      button: "Pagar",
      disabled: false,
      card_flag: 0,
      response: "",
      cart_step: 0,
      page: true,
      cpf_cnpj: "",
      discount: 0,
      cupon_disabled_class: " cupon_active",
      url: "",
      pix_show: false,
      pix_base64: "",
      pix_link: "",
    };
  },
  created() {
    if ("__AUTH" in localStorage) {
      this.is_logged = true;
      var client_token = JSON.parse(localStorage.getItem("__AUTH")).value;
    } else {
      var client_token = JSON.parse(localStorage.getItem("__TMP")).key;
    }
    this.email = JSON.parse(localStorage.getItem("__TMP")).email;

    this.max_installments = this.max_parcels;

    axios
      .post(this.retrieve_open_cart, {
        is_logged: this.is_logged,
        client_token: client_token,
      })
      .then((response) => {
        this.products = response.data;
        for (var i = 0; i < this.products.length; i++) {
          var disalbled_set = {
            minus_disabled: false,
            disabled: false,
          };
          if (
            this.products[i].quantity == this.products[i].max_sell ||
            this.products[i].t_quantity == 0
          ) {
            Object.assign(disalbled_set, { plus_disabled: true });
          } else {
            Object.assign(disalbled_set, { plus_disabled: false });
          }
          Object.assign(this.products[i], disalbled_set);
        }
        try {
          this.cupon_code = this.products[0].coupon_code
            ? this.products[0].coupon_code
            : "";
          this.cupon_disabled = !!this.cupon_code;
          this.discount = this.products[0].coupon_value
            ? this.products[0].coupon_value
            : 0;
        } catch (e) {
          this.cupon_code = "";
          this.cupon_disabled = !!this.cupon_code;
          this.discount = 0;
        }
      });
  },
  beforeUpdate() {
    this.total = 0;
    for (var i = 0; i < this.products.length; i++) {
      this.total += this.products[i].total_price * this.products[i].quantity;
    }

    var el = document.getElementsByClassName("active")[0];
    var id = el.id.split("-")[0];
    document.getElementById(id).classList.remove("active");
    document.getElementById(id).classList.remove("show");
  },
  updated() {
    var el = document.getElementsByClassName("active")[0];
    var id = el.id.split("-")[0];
    document.getElementById(id).classList.add("active");
    document.getElementById(id).classList.add("show");
    if ("__AUTH" in localStorage) {
      this.is_logged = true;
    }

    for (var k = 1; k <= this.max_installments; k++) {
      if ((this.total - this.discount) / k <= 5) {
        this.max_installments = (k == 1 ? 1 : k - 1);
        break;
      }
    }
  },
  watch: {
    "form.number": function () {
      var number = this.form.number.slice(0, 2);
      if (
        number == "51" ||
        number == "52" ||
        number == "53" ||
        number == "54" ||
        number == "55"
      ) {
        this.card_flag = 2; // It's Mastercad
      } else if (number.slice(0, 1) == "4") {
        this.card_flag = 1; // It's Visa
      } else {
        this.card_flag = 0; // It's none
      }
    },
    step: function () {
      this.cart_step = this.step;
    },
  },
  methods: {
    setCupon() {
      if (this.cupon_code != "" && !this.cupon_disabled) {
        var cupon = document.getElementsByClassName("cupon")[0];
        var cupon2 = document.getElementsByClassName("cupon2")[0];
        cupon.disabled = true;
        cupon2.disabled = true;
        this.$swal({
          title: "🎟️ Adicionando cupom... 🎟️",
          text: "Aguarde enquanto adicionamos um cupom.",
          allowOutsideClick: false,
          showConfirmButton: false,
        });
        axios
          .post(this.cupon, {
            temp: localStorage.getItem("__TMP"),
            promo_code: this.cupon_code,
          })
          .then((response) => {
            this.$swal.close();
            if (typeof response.data == "string") {
              this.cupon_disabled_class += " ui-wronged";
              cupon.parentElement.classList.remove("ui-correct");
              this.assignError(cupon, response.data);
              cupon.removeAttribute("disabled");
              cupon2.parentElement.classList.remove("ui-correct");
              this.assignError(cupon2, response.data);
              cupon2.removeAttribute("disabled");
            } else {
              this.$swal.close();
              this.discount = response.data.Desconto;
              cupon.parentElement.classList.remove("ui-wronged");
              cupon2.parentElement.classList.remove("ui-wronged");
              this.cupon_disabled_class += " ui-correct";
              this.assignSuccess(cupon, "Este cupom é válido :)");
              this.assignSuccess(cupon2, "Este cupom é válido :)");
              this.cupon_disabled = this.cupon_code;
            }
          })
          .catch((error) => {
            this.$swal.close();
          });
      } else {
        var cupon = document.getElementsByClassName("cupon")[0];
        var cupon2 = document.getElementsByClassName("cupon2")[0];
        cupon.parentElement.classList.remove("ui-wronged");
        cupon2.parentElement.classList.remove("ui-wronged");
        cupon.parentElement.classList.remove("ui-correct");
        cupon2.parentElement.classList.remove("ui-correct");
      }
    },
    removeCupon(event) {
      this.$swal({
        title: "Exclusão de cupom",
        text: "Você tem certeza que deseja excluir este cupom do seu carrinho?",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Excluir cupom",
        cancelButtonColor: "#DF1949",
        confirmButtonColor: "#8B8B8B",
      }).then((confirm) => {
        if (confirm.value) {
          event.path[1].disabled = true;
          event.path[0].classList.remove("fa-close");
          event.path[0].classList.add("fa-spinner", "fa-pulse");
          axios
            .post(this.cupon, {
              temp: localStorage.getItem("__TMP"),
              promo_code: this.cupon_code,
            })
            .then((response) => {
              this.cupon_code = "";
              this.cupon_disabled = "";
              var cupon = document.getElementsByClassName("cupon")[0];
              var cupon2 = document.getElementsByClassName("cupon2")[0];
              this.assignError(cupon, "Cupom Removido");
              this.assignError(cupon2, "Cupom Removido");
              this.discount = 0;
            })
            .catch((error) => {
              this.$swal({
                title: "Oops :|",
                text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
              });
            })
            .finally(() => {
              event.path[0].classList.remove("fa-spinner", "fa-pulse");
              event.path[0].classList.add("fa-close");
              event.path[1].removeAttribute("disabled");
            });
        }
      });
    },
    setDiscount(discount) {
      this.discount = discount;
    },
    changeCartStepName(event) {
      // Changes the name of the page in which the user is currently on
      if (event.path[1].classList.contains("nav-link")) {
        var cart_step_name = event.path[1].children[1].innerText;
      } else if (event.path[1].classList.contains("nav-item")) {
        var cart_step_name = event.path[1].children[0].children[1].innerText;
      } else if (event.path[0].classList.contains("nav-link")) {
        var cart_step_name = event.path[0].children[1];
        innerText;
      }

      switch (cart_step_name.replace(/\s/g, "")) {
        case "Carrinho":
          this.cart_step = 0;
          this.step = 0;
          break;
        case "Identificação":
          this.cart_step = 1;
          this.step = 1;
          break;
        case "Pagamento":
          this.cart_step = 2;
          this.step = 2;
          break;
        case "Ao Infinito":
          this.cart_step = 3;
          this.step = 3;
          break;
      }
    },
    makeStepWalkOnLogin(email) {
      this.step += 1;
      let tmp = JSON.parse(localStorage.getItem("__TMP"));
      tmp.email = email;
      localStorage.setItem("__TMP", JSON.stringify(tmp));
      var submenus = document.getElementsByClassName("header__account");
      submenus[0].classList.add("menu-active");
      submenus[1].classList.add("menu-active");
      submenus[2].classList.add("menu-active");
    },
    makeStepWalkOnSignuUp() {
      this.step += 1;
      var submenus = document.getElementsByClassName("header__account");
      submenus[0].classList.add("menu-active");
      submenus[1].classList.add("menu-active");
      submenus[2].classList.add("menu-active");
    },
    changeToLoginSignup() {
      if (!this.login_not_signup) {
        this.page = this.login_not_signup ? "cadastro" : "login";
        this.$swal({
          title: "Ir para " + this.page,
          text:
            "Você tem certeza que deseja ir para o " +
            this.page +
            "? " +
            (this.page != "login"
              ? "Isso apagará seus dados dos campos e-mail e senha."
              : "Isso apagará todos os dados que você preencheu até agora."),
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          confirmButtonText: "Ir para " + this.page,
          cancelButtonColor: "#DF1949",
          confirmButtonColor: "#8B8B8B",
        }).then((confirm) => {
          if (confirm.value) {
            this.login_not_signup = !this.login_not_signup;
          }
        });
      } else {
        this.login_not_signup = !this.login_not_signup;
      }
    },
    changeStep() {
      if (this.products.length > 0) {
        if (this.step == 0) {
          this.step = 1;
          if (this.is_logged) {
            this.step = 2;
          }
        } else if (this.step == 1) {
          this.step = 2;
        } else if (this.step == 2) {
          if (this.cart_step == 0) {
            this.step = 2;
          } else {
            this.step = 0;
          }
        }
        window.scrollTo(0, 0);
      }
      this.cart_step = this.step;
    },
    copyPIX() {
      try {
        const el = document.createElement("textarea");
        el.value = this.pix_link;
        document.body.appendChild(el);
        el.select();
        document.execCommand("copy");
        document.body.removeChild(el);

        //mudar isso
        this.$swal({
          title: "Copiado",
          confirmButtonText: "Ok",
          confirmButtonColor: "#6fe335",
        });
      } catch (exception) {
        this.$swal({
          title: "Oops :|",
          html: "Desculpe, mas tivemos um problema ao copiar o pix. Por favor, tente novamente mais tarde.",
          confirmButtonText: "Ok",
          confirmButtonColor: "#DF1949",
        });
      }
    },
    changeStepPaymentPIX() {
      this.$swal({
        title: "Avançar",
        text: "Você tem certeza que deseja avançar para o final do carrinho? Você poderá ver o QR Code do PIX de novo em Meus Passportes.",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Avançar",
        cancelButtonColor: "#DF1949",
        confirmButtonColor: "#6fe335",
      }).then((confirm) => {
        if (confirm.value) {
          this.changeStepPayment();
        }
      });
    },
    changeStepPayment() {
      this.step = 3;
    },
    checkCPF(cpf) {
      if (cpf.length <= 14) {
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
      } else {
        return true;
      }
    },
    assignSuccess(element, message) {
      //Assign a success message
      element.parentElement.dataset.wronged = message;
      element.parentElement.classList.add("ui-correct");
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
    changeColour(event) {
      event.path[0].style.backgroundColor = "#6f39ff";
      event.path[0].style.color = "white;";
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
      if (!this.checkCPF(this.form.cpfCnpj)) {
        this.assignError(cpf, "Digite um CPF válido");
        is_ok = false;
      }

      // Check if the month is on the list
      var month = document.getElementById("month");
      if (![1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].includes(+month.value)) {
        this.assignError(month, "Escolha uma opção válida");
        is_ok = false;
      }

      // Check if the year is on the list
      var year = document.getElementById("year");
      if (
        ![
          2021, 2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030, 2031,
          2032,
        ].includes(+year.value)
      ) {
        this.assignError(year, "Escolha uma opção válida");
        is_ok = false;
      }

      // Check if the fields are filled
      var items = ["number", "owner", "ccv", "installment", "month", "year"];
      for (var i = 0; i < items.length; i++) {
        var element = document.getElementById(items[i]);
        if (element.value.length < 1) {
          this.assignError(element, "Este campo é obrigatório");
          is_ok = false;
        }
      }

      if (this.form.cpfCnpj.length < 1) {
        this.assignError(cpf, "Este campo é obrigatório");
        is_ok = false;
      }

      return is_ok;
    },
    makePayment() {
      if (this.form.payment_method == 2) {
        var generate_ticket = document.getElementById("generate-ticket");
        generate_ticket.disabled = true;
        var cpf_cpnj = document.getElementById("cpf_cnpj");
        var cupon = document.getElementsByClassName("cupon")[0];
        var cupon2 = document.getElementsByClassName("cupon2")[0];
        var pay_button = document.getElementById("generate-ticket");
        if (cpf_cpnj.value.length <= 14) {
          if (this.checkCPF(cpf_cpnj.value)) {
            this.$swal({
              title: "💳 Criando pagamento... 💳",
              text: "Aguarde enquanto verificamos seus dados.",
              allowOutsideClick: false,
              showConfirmButton: false,
            });
            axios
              .post(this.payment, {
                client_id: JSON.parse(localStorage.getItem("__AUTH")).value,
                temp: JSON.parse(localStorage.getItem("__TMP")).email,
                cpfCnpj: this.cpf_cnpj,
                payment_method: +this.form.payment_method,
              })
              .then((response) => {
                this.$swal.close();
                if (response.data.error) {
                  this.cupon_code = "";
                  this.discount = 0;
                  var pay_button = document.getElementById("generate-ticket");
                  pay_button.removeAttribute("disabled");
                  pay_button.innerHTML = "GERAR BOLETO";
                  this.cupon_disabled_class += "";
                  cupon.parentElement.classList.remove("ui-correct");
                  cupon.removeAttribute("disabled");
                  cupon2.parentElement.classList.remove("ui-correct");
                  cupon2.removeAttribute("disabled");
                  this.$swal({
                    title: "Oops :|",
                    text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#6fe335",
                  });
                } else {
                  this.cupon_disabled = true;
                  var cupon = document.getElementsByClassName("cupon")[0];
                  var cupon2 = document.getElementsByClassName("cupon2")[0];
                  cupon.disabled = true;
                  cupon2.disabled = true;
                  this.url = response.data.bankSlipUrl;
                  this.changeStepPayment();
                  window.open(response.data.bankSlipUrl, "_blank").focus();
                }
              })
              .catch((error) => {
                this.$swal.close();
                this.cupon_code = "";
                this.discount = 0;
                var pay_button = document.getElementById("generate-ticket");
                pay_button.removeAttribute("disabled");
                pay_button.innerHTML = "GERAR BOLETO";
                this.cupon_disabled_class += "";
                cupon.parentElement.classList.remove("ui-correct");
                cupon.removeAttribute("disabled");
                cupon2.parentElement.classList.remove("ui-correct");
                cupon2.removeAttribute("disabled");
                if (
                  error.response.data.msg ==
                  "CUPOM JÁ UTILIZADO EM COMPRAS ANTERIORES!"
                ) {
                  this.$swal({
                    title: "Oops :|",
                    text: "Cupom já utilizado em compras anteriores!",
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#6fe335",
                  });
                } else {
                  this.$swal({
                    title: "Oops :|",
                    text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#6fe335",
                  });
                }
                generate_ticket.removeAttribute("disabled");
              });
          } else {
            this.assignError(cpf_cpnj, "Digite um CPF ou CPNJ válido");
          }
        } else {
          this.$swal({
            title: "💳 Criando pagamento... 💳",
            text: "Aguarde enquanto verificamos seus dados.",
            allowOutsideClick: false,
            showConfirmButton: false,
          });
          axios
            .post(this.payment, {
              client_id: JSON.parse(localStorage.getItem("__AUTH")).value,
              temp: JSON.parse(localStorage.getItem("__TMP")).email,
              cpfCnpj: this.cpf_cnpj,
              payment_method: +this.form.payment_method,
            })
            .then((response) => {
              if (response.data.error) {
                this.cupon_code = "";
                this.discount = 0;
                var pay_button = document.getElementById("generate-ticket");
                pay_button.removeAttribute("disabled");
                pay_button.innerHTML = "GERAR BOLETO";
                this.cupon_disabled_class += "";
                cupon.parentElement.classList.remove("ui-correct");
                cupon.removeAttribute("disabled");
                cupon2.parentElement.classList.remove("ui-correct");
                cupon2.removeAttribute("disabled");
                this.$swal({
                  title: "Oops :|",
                  text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                  showCancelButton: false,
                  confirmButtonText: "Ok",
                  confirmButtonColor: "#6fe335",
                });
              } else {
                this.cupon_disabled = true;
                var cupon = document.getElementsByClassName("cupon")[0];
                var cupon2 = document.getElementsByClassName("cupon2")[0];
                cupon.disabled = true;
                cupon2.disabled = true;
                this.url = response.data.bankSlipUrl;
                this.changeStepPayment();
                window.open(response.data.bankSlipUrl, "_blank").focus();
              }
            })
            .catch((error) => {
              this.cupon_code = "";
              this.discount = 0;
              var pay_button = document.getElementById("generate-ticket");
              pay_button.removeAttribute("disabled");
              pay_button.innerHTML = "GERAR BOLETO";
              this.cupon_disabled_class += "";
              cupon.parentElement.classList.remove("ui-correct");
              cupon.removeAttribute("disabled");
              cupon2.parentElement.classList.remove("ui-correct");
              cupon2.removeAttribute("disabled");
              if (
                error.response.data.msg ==
                "CUPOM JÁ UTILIZADO EM COMPRAS ANTERIORES!"
              ) {
                this.$swal({
                  title: "Oops :|",
                  text: "Cupom já utilizado em compras anteriores!",
                  showCancelButton: false,
                  confirmButtonText: "Ok",
                  confirmButtonColor: "#6fe335",
                });
              } else {
                this.$swal({
                  title: "Oops :|",
                  text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                  showCancelButton: false,
                  confirmButtonText: "Ok",
                  confirmButtonColor: "#6fe335",
                });
              }
              generate_ticket.removeAttribute("disabled");
            });
        }
      }
      if (this.form.payment_method == 1 && this.isFormOk()) {
        var cupon = document.getElementsByClassName("cupon")[0];
        var cupon2 = document.getElementsByClassName("cupon2")[0];
        this.form.temp = JSON.parse(localStorage.getItem("__TMP")).email;
        this.form.client_id = JSON.parse(localStorage.getItem("__AUTH")).value;
        var pay_button = document.getElementById("pay-button");
        pay_button.disabled = "disabled";
        pay_button.innerHTML = "Criando pagamento...";
        this.$swal({
          title: "💳 Criando pagamento... 💳",
          text: "Aguarde enquanto verificamos seus dados.",
          allowOutsideClick: false,
          showConfirmButton: false,
        });
        axios
          .post(this.payment, this.form)
          .then((response) => {
            this.$swal.close();
            if (response.data.status == "CONFIRMED") {
              pay_button.removeAttribute("disabled");
              pay_button.innerHTML = "Avançar";
              this.cupon_disabled = true;
              var cupon = document.getElementsByClassName("cupon")[0];
              var cupon2 = document.getElementsByClassName("cupon2")[0];
              cupon.disabled = true;
              cupon2.disabled = true;
              this.changeStepPayment();
            }
            if (response.data.error == "Informe o código de segurança do seu cartão.") {
              var ccv = document.getElementById("ccv");
              this.assignError(
                ccv,
                "Informe o código de segurança do seu cartão"
              );
              pay_button.removeAttribute("disabled");
              pay_button.innerHTML = "Avançar";
            } else if (response.data.error == "O CPF ou CNPJ informado é inválido.") {
              var cpf_cnpj = document.getElementById("cpf");
              this.assignError(cpf_cnpj, "O CPF ou CNPJ informado é inválido");
              pay_button.removeAttribute("disabled");
              pay_button.innerHTML = "Avançar";
            } else if (response.data.error) {
              this.$swal({
                title: "Oops :|",
                text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
              });
              var number = document.getElementById("number");
              this.assignError(number, response.data.error);
              pay_button.removeAttribute("disabled");
              pay_button.innerHTML = "Avançar";
            } else {
              var number = document.getElementById("number");
              this.assignError(number, response.data.error);
              pay_button.removeAttribute("disabled");
              pay_button.innerHTML = "Avançar";
            }
          })
          .catch((error) => {
            if (
              error.response.data.msg ==
              "CUPOM JÁ UTILIZADO EM COMPRAS ANTERIORES!"
            ) {
              this.cupon_code = "";
              this.discount = 0;
              pay_button.removeAttribute("disabled");
              pay_button.innerHTML = "Avançar";
              this.cupon_disabled_class += "";
              cupon.parentElement.classList.remove("ui-correct");
              cupon.removeAttribute("disabled");
              cupon2.parentElement.classList.remove("ui-correct");
              cupon2.removeAttribute("disabled");
              this.$swal.close();
              this.$swal({
                title: "Oops :|",
                text: "Cupom já utilizado em compras anteriores!",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
              });
            } else {
              this.$swal.close();
              this.$swal({
                title: "Oops :|",
                text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
              });
              pay_button.removeAttribute("disabled");
              pay_button.innerHTML = "Avançar";
            }
          });
      }
      if (this.form.payment_method == 3) {
        var cupon = document.getElementsByClassName("cupon")[0];
        var cupon2 = document.getElementsByClassName("cupon2")[0];
        this.form.temp = JSON.parse(localStorage.getItem("__TMP")).email;
        this.form.client_id = JSON.parse(localStorage.getItem("__AUTH")).value;
        var pay_button = document.getElementById("pay-button");
        pay_button.disabled = "disabled";
        pay_button.innerHTML = "Criando pagamento...";
        this.$swal({
          title: "💳 Criando pagamento... 💳",
          text: "Aguarde enquanto verificamos seus dados.",
          allowOutsideClick: false,
          showConfirmButton: false,
        });
        axios
          .post(this.payment, this.form)
          .then((response) => {
            this.pix_show = true;
            this.pix_base64 = response.data.encodedImage;
            this.pix_link = response.data.payload;
            var cupon = document.getElementsByClassName("cupon")[0];
            var cupon2 = document.getElementsByClassName("cupon2")[0];
            cupon.disabled = true;
            cupon2.disabled = true;
            this.cupon_disabled = true;
            this.$swal.close();
          })
          .catch((error) => {
            this.$swal.close();
            this.cupon_code = "";
            this.discount = 0;
            pay_button.removeAttribute("disabled");
            pay_button.innerHTML = "GERAR PIX";
            this.cupon_disabled_class = "";
            cupon.parentElement.classList.remove("ui-correct");
            cupon.removeAttribute("disabled");
            cupon2.parentElement.classList.remove("ui-correct");
            cupon2.removeAttribute("disabled");
            if (
              error.response.data.msg ==
              "CUPOM JÁ UTILIZADO EM COMPRAS ANTERIORES!"
            ) {
              this.$swal({
                title: "Oops :|",
                text: "Cupom já utilizado em compras anteriores!",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
              });
            } else {
              this.$swal({
                title: "Oops :|",
                text: "Tivemos um pequeno problema. Por favor, tente novamente mais tarde ou nos chame no chat que aparece na home do site.",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#6fe335",
              });
            }
          });
      }
    },
  },
};
</script>
