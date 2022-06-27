<template>
    <div>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Novo Prêmio </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form form-class="form-horizontal col-12" title="Adicionar Recompensa">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="participante">Participante*</label>
                                <div class="col-sm-10">
                                    <vue-multiselect
                                        v-model="selecCoins"
                                        :options="nomes"
                                        selectLabel="Precione Enter para selecionar"
                                        deselectLabel="Precione Enter para remover"
                                        placeholder="Busque por um participante"
                                        @input="buscaHjCoins(selecCoins)"
                                    >
                                    </vue-multiselect>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="hjcoins">HJ Coins Disponíveis</label>
                                <div class="col-sm-2">
                                    <input v-if="!loadingCoin" type="text" class="form-control" aria-label="HjCoins" name="participante" id="hjcoins" :value="(coins.hj_coins == null || coins.hj_coins == '') ? 0 : coins.hj_coins" disabled> 
                                    <i v-if="loadingCoin" class="fa fa-spinner fa-pulse fa-2x fa-fw" aria-hidden="true"></i>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="recompensa">Recompensa*</label>
                                <div class="col-sm-10">
                                    <vue-multiselect
                                        v-model="selecRecompensaCoins"
                                        :options="recompensa"
                                        selectLabel="Pressione enter para selecionar"
                                        placeholder="Busque por uma recompensa"
                                        @input="buscaValorRecompensa(selecRecompensaCoins)"
                                    >
                                    </vue-multiselect>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="recompensa">Valor Recompensa</label>
                                <div class="col-sm-2">
                                    <input v-if="!loadingRecompensa" type="numeric" class="form-control" name="quant" id="quant" :value="(coinsRecompensa.hjcoins_quant == null || coinsRecompensa.hjcoins_quant == '') ? 0 : coinsRecompensa.hjcoins_quant" disabled>
                                    <i v-if="loadingRecompensa" class="fa fa-spinner fa-pulse fa-2x fa-fw" aria-hidden="true"></i>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <span class="text-danger">{{mensagem}}</span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button v-if="loadingEnviar" type="button" class="btn btn-success" :disabled="disabled" ><i class="fa fa-spinner fa-pulse fa-fw"></i> </button>
                        <button v-else type="button" class="btn btn-success" :disabled="disabled" @click.prevent="cadastraPremio()">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<script>
import axios from 'axios';
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead';
import Multiselect from 'vue-multiselect';

    export default {
        props: ['ingressos', 'recompensas'],

        created(){
            this.converteNomes();
            this.converteRecompensas();
        },

        data(){
            return {
                enviar:'Enviar',
                selecCoins:'',
                selecRecompensaCoins: '',
                quantSelec:'',
                mensagem:'',
                nomes:'',
                nomeSelecionado:'',
                recompensa:'',
                disabled: false,
                coins:[],
                coinsRecompensa:[],
                loadingCoin: false,
                loadingRecompensa: false,
                loadingEnviar: false
            }
        },

        components:{
            'vue-bootstrap-typeahead':VueBootstrapTypeahead,
            'vue-multiselect':Multiselect
        },

        methods:{

            buscaHjCoins(nome) {

                if(nome != null) {
                    this.loadingCoin = true;
                    axios.get('busca-coins/'+nome)
                    .then(res => {
                        this.coins = res.data.data;
                        this.loadingCoin = false;
                        this.verificaValor();
                    })
                } else {
                    this.coins.hj_coins= '';                
                }
            },

            buscaValorRecompensa(nomeRecompensa) {

                 if(nomeRecompensa != null) {
                    this.loadingRecompensa = true;
                    axios.get('busca-valor-recompensa/'+nomeRecompensa)
                    .then(res => {
                        this.coinsRecompensa = res.data.data;
                        this.loadingRecompensa = false;
                        this.verificaValor();
                    })
                 } else {
                     this.coinsRecompensa.hjcoins_quant = '';      
                 }
            },

            converteNomes() {
                const array = [];
                
                this.ingressos.forEach(ing => {
                    if(ing.ingresso_nome != null && ing.ingresso_nome != '' && ing.ingresso_nome != '-') {
                        array.push(ing.ingresso_nome);
                    }
                })

                this.nomes = array;
            },

            converteRecompensas() {
                const array = [];

                this.recompensas.forEach(rec => {
                    if(rec.nome != null) {
                        array.push(rec.nome);
                    }
                })

                this.recompensa = array;
            },

            verificaValor() {
                (this.coins.hj_coins < this.coinsRecompensa.hjcoins_quant) ? this.disabled = true : this.disabled = false;
            },

            verificaQuantSelec() {
                
            },

            cadastraPremio() {
                var data = {
                    'fk_ingresso_id': this.coins.ingresso_id,
                    'fk_recompensas_id': this.coinsRecompensa.id,
                    'quant': this.coinsRecompensa.hjcoins_quant,
                    'tipo': 2,
                    'coinsRestantes': this.coins.hj_coins - this.coinsRecompensa.hjcoins_quant
                }
                this.loadingEnviar = true;
                this.disabled = true;
                axios.post('cadastra-premiacao', data)
                .then(res => {
                    if(res) {
                        this.loadingEnviar = false;
                        this.disabled = false;
                        this.$swal("Ok!", "Prêmio cadastrado com sucesso!", "success");
                    }
                    setTimeout(function(){
                        location.reload();
                    }, 1000)
                })
                .catch(err => {
                    this.loadingEnviar = false;
                    this.disabled = false;
                    this.$swal("Oops!", "Não foi possivel realizar o cadastro!", "error");
                })
                
            }
        }
    }
</script>
