<template>
    <div>
        <div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Novo QRCode </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="custom-control custom-switch">
                                                <input 
                                                type="checkbox" 
                                                class="custom-control-input" 
                                                id="customSwitches" 
                                                v-model="unico"
                                                >
                                                <label 
                                                class="custom-control-label" 
                                                for="customSwitches"
                                                >
                                                    Único
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <input 
                                    type="text" 
                                    class="form-control" 
                                    v-model="qr"
                                    >
                                </div>
                                
                                <!--<div class="form-group" v-if="!unico">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Data inicial</label>
                                            <flat-pickr v-model="date" :config="config" class="form-control"></flat-pickr>
                                        </div>

                                        <div class="col-sm-6">
                                            <label>Data final</label>
                                            <flat-pickr v-model="date2" :config="config" class="form-control"></flat-pickr>
                                        </div>
                                    </div>
                                </div>-->

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <label>Tempo
                                                <i  
                                                class="fa fa-info-circle"
                                                data-toggle="tooltip"
                                                title="Tempo minimo de releitura."
                                                ></i>
                                            </label>
                                            <input 
                                            type="number" 
                                            class="form-control" 
                                            v-model="tempo"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <label>Qtd. HJCoins</label>
                                            <input 
                                            type="number" 
                                            class="form-control" 
                                            v-model="hjcoins"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button 
                            type="reset" 
                            class="btn btn-secondary" 
                            data-dismiss="modal"
                        >
                            Cancelar
                        </button>

                        <button 
                            v-if="loadingEnviar" 
                            type="button" 
                            class="btn btn-success" 
                            :disabled="disabled" >
                            <i class="fa fa-circle-o-notch fa-spin fa-fw"></i> 
                        </button>

                        <button 
                            v-else 
                            type="button" 
                            class="btn btn-success" 
                            :disabled="disabled" 
                            @click.prevent="cadastraQr()"
                        >
                            Enviar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';
import flatPickr from 'vue-flatpickr-component';
import { Portuguese } from "flatpickr/dist/l10n/pt.js";
import 'flatpickr/dist/flatpickr.css';

export default {
    name: "QRCode",
  
    components: {
        'flat-pickr':flatPickr
    },

    data() {
        return {
            qr:'',
            hjcoins:'',
            unico: false,
            tempo: '',
            disabled: false,
            loadingEnviar: false,
            date: null,
            date2:null,
            config: {
                enableTime: true,
                locale: Portuguese
            },
        }
    },
    methods: {

        cadastraQr() {
            const data = {
                'qr':this.qr,
                'hjcoins':this.hjcoins,
                'tipo': (this.unico) ? 1 : 2,
                'tempo': (!this.unico) ? this.tempo : ''
            }

            this.loadingEnviar = true;
            this.disabled = true;
            axios.post('cadastra-qrcode', data)
            .then(res => {
                this.$swal("Ok!", "Saldo cadastrado com sucesso!", "success");

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

    },
};
</script>