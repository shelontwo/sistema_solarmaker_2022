<template>
  <div class="box box-widget">
    <div class="box-header">
      <div class="row">
        <div class="col-md-8 col-sm-6">
          <h3 class="box-title">{{title}}</h3>
        </div>
        <div v-if="busca != 'false'" class="col-md-4 col-sm-6">
          <form method="GET" class="form-horizontal" :action="url">
            <div class="input-group">
              <input
                type="text"
                name="busca"
                class="form-control"
                placeholder="Buscar por"
                :value="busca"
              />
              <span class="input-group-btn">
                <button type="submit" class="btn btn-primary btn-flat">
                  <i class="fa fa-search"></i>
                </button>
                <a
                  :href="url"
                  class="btn btn-default btn-flat"
                  data-toggle="tooltip"
                  title="Limpar Busca"
                >
                  <i class="fa fa-list"></i>
                </a>
              </span>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="box-body">
      <slot></slot>

      <form
        v-if="items.data.length > 0"
        :class="formClass"
        v-on:submit.prevent="confirmDelete"
        :action="url + '/destroy'"
        method="POST"
      >
        <input type="hidden" name="_token" :value="token" />
        <input type="hidden" name="_method" value="DELETE" />
        <table id="example1" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th v-if="!notDeletable" align="center" width="50px">
                <input
                  class="checkbox-delete"
                  id="checkbox-delete-origin"
                  type="checkbox"
                  name="registro[]"
                  data-toggle="tooltip"
                  title="Marcar tudo"
                  v-on:click="checkAll"
                />
              </th>
              <th v-for="(title, index) in titles" :key="index">{{title}}</th>
              <th v-if="actions != 'false'">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in items.data" :key="index">
              <th v-if="!notDeletable" align="center" class="v-middle">
                <input class="checkbox-delete" type="checkbox" name="registro[]" :value="item.id" />
              </th> 
              <td v-for="(field, index) in item" :key="index" class="v-middle">
                <span v-if="field === null">{{field}}</span>
                <span v-else-if="!field.type && !Array.isArray(field)">{{field}}</span>
                 <span
                  class="mr-2 px-2 py-0 rounded-pill badge"
                  v-else-if="isMultiBadge(field)"
                  v-for="(campo, index) in field"
                  :key="index"
                  :class="'badge-' + campo.status"
                >
                  <span>{{campo.text}}</span>
                </span>
                <span v-else-if="field.type == 'img'">
                  <img :src="field.src" style="max-width:150px; max-height:150px;" />
                </span>

                <span
                  v-else-if="field.type == 'badge'"
                  class="badge"
                  :class="'badge-' + field.status"
                >{{ field.text }}</span>

                <span
                  v-else-if="field.type == 'action'"
                  v-for="(action, index) in item.actions"
                  :key="index"
                >
                  <a
                    data-toggle="tooltip"
                    class="btn btn-flat ml-10"
                    :class="'btn-' + action.color"
                    :href="action.path"
                    :title="action.label"
                  >
                    <i :class="action.icon"></i>
                  </a>&nbsp;
                </span>
              </td>
              <td v-if="!item.actions && actions != 'false' && !comment && !qrcode" class="v-middle">
                <a
                  class="btn btn-flat btn-primary"
                  :href="url + '/' + item.id + '/edit'"
                  data-toggle="tooltip"
                  title="Editar"
                >
                  <i class="fa fa-pencil"></i>
                </a>
              </td>
              <td v-if="comment" class="v-middle">
                <a
                  class="btn btn-flat btn-success"
                  @click="actionComment(url + '/approve/' + item.id)" 
                  data-toggle="tooltip"
                  title="Aprovar"
                  v-if="item.aprovado.text == 'Reprovado'"
                >
                  <i class="fa fa-check"></i>
                </a>
                <a
                  class="btn btn-flat btn-danger"
                  @click="actionComment(url + '/disapprove/' + item.id)" 
                  data-toggle="tooltip"
                  title="Reprovar"
                  v-else
                >
                  <i class="fa fa-ban"></i>
                </a>
              </td>

              <td v-if="qrcode" class="v-middle">
                <a
                  class="btn btn-flat btn-dark"
                  @click="geraQrCode(item.codigo_qrcode)" 
                  data-toggle="tooltip"
                  title="Gerar QRCode"
                >
                  <i :id="item.codigo_qrcode" class="fa fa-qrcode"></i>
                </a>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td :colspan="(titles.length + 2)" align="left" vertical-align="center">
                <button
									v-if="!notDeletable"
                  type="submit"
                  class="mv-22 btn btn-flat btn-danger"
                  :disabled="btnDelete.disabled"
                >
                  <i :class="btnDelete.icon"></i>
                  {{ btnDelete.text }}
                </button>
                <slot name="pagination"></slot>
              </td>
            </tr>
          </tfoot>
        </table>
      </form>

      <div v-if="items.data.length == 0" class="callout callout-default">
        <h4>Nenhum registro encontrado!</h4>
      </div>
    </div>
  </div>
</template>

<style>
.mv-22 {
  margin: 22px 0;
}
.v-middle {
  vertical-align: middle !important;
}
</style>

<script>
import axios from 'axios';

export default {
  props: [
    "title",
    "titles",
    "items",
    "busca",
    "url",
    "token",
    "formClass",
    "notDeletable",
    "actions",
    "comment",
    "qrcode"
  ],
  data: function() {
    return {
      deleteItems: [],
      btnDelete: {
        icon: "fa fa-trash",
        text: "Excluir registros selecionados",
        disabled: false
      },
      gerarQr: false,
      loadingQr: false,
    };
  },
  created() {
    if (!this.items.data) {
      this.items.data = this.items;
    }
  },
  methods: {
    isMultiBadge(field) {
      if (Array.isArray(field) && field.length > 0) {
        if (field[0].type == "multiBadge") {
          return true;
        }
      }
      return false;
    },
    checkAll: function() {
      var checkboxOrigin = document.getElementById("checkbox-delete-origin");
      var checkboxes = document.getElementsByClassName("checkbox-delete");
      for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = checkboxOrigin.checked;
      }
    },
    confirmDelete: function(e) {
      this.$swal({
        title: "Atenção!",
        text: "Deseja realmente excluir os registros selecionados?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#008d4c",
        cancelButtonColor: "#d4d4d4",
        confirmButtonText: "Sim, deletar!",
        cancelButtonText: "Não!"
      }).then(result => {
        if (result.value) {
          e.target.submit(), (this.btnDelete.icon = "fa fa-spinner fa-pulse");
          this.btnDelete.text = "Excluindo";
          this.btnDelete.disabled = true;
        }
      });
    },
    actionComment: function(url) {
      this.$swal({
        title: "Atenção!",
        text: "Deseja realmente alterar este registro?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#008d4c",
        cancelButtonColor: "#d4d4d4",
        confirmButtonText: "Sim, alterar!",
        cancelButtonText: "Não!"
      }).then(result => {
        if (result.value) {
          window.location.href = url;
        }
      });
    },

    geraQrCode: function(codigo) {
      $('#'+codigo).removeClass("fa fa-qrcode").addClass("fa fa-spinner fa-pulse fa-fw");

      axios.get("gera-qrcode/" + codigo)
      .then(res => {

        let file = "gera-qrcode/" + codigo;
        let link = document.createElement("a");
        link.href = file;
        link.download = "QR_"+codigo+".svg";
        link.target = "_blank";
        link.click();

        $('#'+codigo).removeClass("fa fa-spinner fa-pulse fa-fw").addClass("fa fa-qrcode");
      })
      .catch(err => {
        this.loadingQr = false;
      });

    }
  }
};
</script>