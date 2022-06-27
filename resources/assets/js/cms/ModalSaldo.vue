<template>
    <div>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Nova Pontuação </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form form-class="form-horizontal col-12" title="Adicionar Pontuação">
                        
                            <div class="form-group row text-center">
                                <div class="col-sm-12">
                                    <div class="form-check form-check-inline ml-3">
                                        <input class="form-check-input" type="checkbox" id="todos" v-model="todos">
                                        <label class="form-check-label" for="todos">Todos participantes</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row" v-if="!todos">
                                <label class="col-sm-2 control-label" for="participante">Participante*</label>
                                <div class="col-sm-10">
                                    <vue-multiselect
                                        v-model="nomeSelecionado"
                                        :options="arrayNomes"
                                        selectLabel="Pressione Enter para selecionar"
                                        deselectLabel="Pressione Enter para remover"
                                        placeholder="Busque por um participante"
                                        @input="buscaDados()"
                                    >
                                    </vue-multiselect>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="hjCoins">Qtd. HJ Coins a Enviar*</label>
                                <div class="col-sm-2">
                                   <input type="number" class="form-control" name="hjCoins" id="hjCoins" v-model="hj_coins">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="motivo">Motivo*</label>
                                <div class="col-sm-10">
                                   <textarea type="text" class="form-control" name="motivo" id="motivo" rows="5" v-model="motivo"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button v-if="loadingEnviar" type="button" class="btn btn-success" :disabled="disabled" ><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> </button>
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
        props: ['nomes'],

        created(){
            this.converteNomes();
        },

        data(){
            return {
                nomeSelecionado:'',
                arrayNomes:'',
                nomeSelecionado:'',
                recompensa:'',
                disabled: false,
                loadingCoin: false,
                loadingRecompensa: false,
                loadingEnviar: false,
                todos:false,
                hj_coins:'',
                motivo:'',
                dados:[],
                ingressoNomes: [],

            }
        },

        components:{
            'vue-bootstrap-typeahead':VueBootstrapTypeahead,
            'vue-multiselect':Multiselect
        },

        methods:{

            converteNomes() {
                const array = [];

                this.nomes.forEach(ing => {
                    if(ing.ingresso_nome != null) {
                        array.push(ing.ingresso_nome);
                    }
                })
                this.arrayNomes = array;
            },

            buscaDados()
            {
                axios.get('busca-dados/'+this.nomeSelecionado)
                .then(res => {
                    this.dados = res.data.data;
                })
            },

            cadastraPremio() {

                if(!this.todos) {
                    this.buscaDados();
                }

                var data = {
                    'ingresso_id': (!this.todos) ? this.dados.ingresso_id : '',
                    'todos': this.todos,
                    'hj_coins': this.hj_coins,
                    'motivo': this.motivo
                }
                this.loadingEnviar = true;
                this.disabled = true;
                axios.post('cadastra-saldo', data)
                .then(res => {
                    if(res) {
                        this.loadingEnviar = false;
                        this.disabled = false;
                        this.$swal("Ok!", "Saldo cadastrado com sucesso!", "success");
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
