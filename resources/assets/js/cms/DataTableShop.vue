<template>
  <div class="box box-widget">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-4">
          <h3 class="box-title">{{ title }}</h3>
        </div>
        <div v-if="busca != 'false'" class="col-sm-8">
          <form method="GET" class="form-horizontal" :action="url">
            <div class="input-group">
              <select class="form-control" name="select_status" id="select_status">
                <option value="" :selected="!selected || selected == 0">
                  Todos os Status
                </option>
                <option
                  v-for="(option, index) in options"
                  :key="index"
                  :value="option.value"
                  :selected="selected == option.value || false"
                >
                  {{ option.label }}
                </option>
              </select>

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
                  @click="checkDelete"
                />
              </th>
              <th v-for="(title, index) in titles" :key="index">{{ title }}</th>
              <th v-if="actions != 'false'">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in items.data" :key="index">
              <th v-if="!notDeletable" align="center" class="v-middle">
                <input
                  @click="checkDelete"
                  class="checkbox-delete"
                  type="checkbox"
                  name="registro[]"
                  :value="item.id"
                />
              </th>

              <td
                v-for="(field, index) in item"
                :key="index"
                class="v-middle"
                :style="isMultiBadge(field) ? 'max-width: 300px' : ''"
              >
                <span v-if="field === null">{{ field }}</span>
                <span v-else-if="!field.type && !Array.isArray(field)">{{
                  field
                }}</span>
                <span v-else-if="field.type == 'img'">
                  <img
                    :src="field.src"
                    style="max-width: 150px; max-height: 150px"
                  />
                </span>
                <span class="colorSpan" v-else-if="field.type == 'color'">
                  <div
                    :style="field.style"
                    class="ShowColor"
                    style="height: 2em; width: 2em"
                  ></div>
                </span>
                <span
                  v-else-if="field.type == 'cart'"
                  :class="field.class"
                  style="font-size: 14px"
                  >{{ field.text }}</span
                >
                <span v-else-if="field.type == 'rate'"
                  ><span v-for="index in field.text" :key="index"
                    ><i class="star fa fa-star"></i></span
                ></span>
                <span
                  class="mr-2 px-2 py-0 rounded-pill badge"
                  v-else-if="isMultiBadge(field)"
                  v-for="(campo, index) in field"
                  :key="index"
                  :class="'badge-' + campo.status"
                >
                  <span>{{ campo.text }}</span>
                </span>
                <span
                  v-else-if="field.type == 'badge'"
                  class="badge"
                  :class="'badge-' + field.status"
                  >{{ field.text }}</span
                >

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
                    <i :class="action.icon"></i> </a
                  >&nbsp;
                </span>
              </td>
              <td class="v-middle" v-if="actions && actions != 'false'">
                <span v-for="(action, index) in actions" :key="index">
                  <a
                    data-toggle="tooltip"
                    class="btn btn-flat ml-10"
                    :class="'btn-' + action.color"
                    :href="url + '/' + actionUrl(action.path, item.id)"
                    :title="action.label"
                  >
                    <i :class="action.icon"></i> </a
                  >&nbsp;
                </span>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td
                :colspan="titles.length + 2"
                align="left"
                vertical-align="center"
              >
                <button
                  v-if="!notDeletable"
                  type="submit"
                  class="mv-22 btn btn-flat btn-danger"
                  :disabled="btnDelete.disabled"
                >
                  <i :class="btnDelete.icon"></i> {{ btnDelete.text }}
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
.badge-success {
  background-color: #28a745 !important;
}
.badge-danger {
  background-color: #dc3545 !important;
}

.ShowColor {
  border-radius: 10px;
  width: 50%;
  margin: 0 auto;
}

.star {
  color: gold;
}
</style>

<script>
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
    "status_list",
    "selected"
  ],
  data: function () {
    return {
      colorDiv: "",
      deleteItems: [],
      btnDelete: {
        icon: "fa fa-trash",
        text: "Excluir registros selecionados",
        disabled: true,
      },
      options: {},
      selected: ""
    };
  },
  created() {
    if (!this.items.data) {
      this.items.data = this.items;
    }
  },

  mounted() {
    this.options = JSON.parse(this.status_list);
  },
  methods: {
    checkDelete() {
      setTimeout(() => {
        var checkboxes = document.getElementsByClassName("checkbox-delete");
        var checkeds = 0;
        for (var i = 0, n = checkboxes.length; i < n; i++) {
          if (checkboxes[i].checked == true) {
            checkeds++;
          }
        }
        var checkboxOrigin = document.getElementById("checkbox-delete-origin");
        if (checkboxOrigin.checked) {
          this.btnDelete.disabled = false;
        } else {
          this.btnDelete.disabled = checkeds == 0;
        }
      }, 100);
    },
    isMultiBadge(field) {
      if (Array.isArray(field) && field.length > 0) {
        if (field[0].type == "multiBadge") {
          return true;
        }
      }
      return false;
    },
    checkAll: function () {
      var checkboxOrigin = document.getElementById("checkbox-delete-origin");
      var checkboxes = document.getElementsByClassName("checkbox-delete");
      for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = checkboxOrigin.checked;
      }
    },
    confirmDelete: function (e) {
      this.$swal({
        title: "Atenção!",
        text: "Deseja realmente excluir os registros selecionados?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#008d4c",
        cancelButtonColor: "#d4d4d4",
        confirmButtonText: "Sim, deletar!",
        cancelButtonText: "Não!",
      }).then((result) => {
        if (result.value) {
          e.target.submit(), (this.btnDelete.icon = "fa fa-spinner fa-pulse");
          this.btnDelete.text = "Excluindo";
          this.btnDelete.disabled = true;
        }
      });
    },
    actionUrl: (path, id) => {
      return path.replace(/{item}/g, id);
    },
  },
};
</script>