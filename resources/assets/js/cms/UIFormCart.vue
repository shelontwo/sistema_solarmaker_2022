<template>
  <div class="box box-widget">
    <div
      class="h-100 box-header with-border"
      :class="{
        'address_principal': principal == 1
      }"
    >
      <span>
        <h3 class="mt-2 box-title">{{ title }}</h3>
      </span>
      <span v-if="show == 'true'">
        <a href="javascript:window.location.reload();"
          ><button
            type="button"
            class="reload_button btn btn-flat btn-info"
            data-toggle="tooltip"
            title="Atualizar"
          >
            <i class="fa fa-refresh"></i></button
        ></a>
      </span>
    </div>

    <form
      :class="formClass"
      :action="url"
      v-on:submit="btnLoading"
      :method="method == 'GET' ? method : 'POST'"
      enctype="multipart/form-data"
    >
      <input type="hidden" name="_token" :value="token" />
      <input type="hidden" name="_method" :value="method" />
      <div class="box-body">
        <slot></slot>
        <slot name="pagination"></slot>
      </div>
    </form>
  </div>
</template>
<style lang="scss">
.reload_button {
  float: right;
}

.address_principal {
  background-color: rgb(65, 216, 34);
  border-radius: 3px;
}
</style>>
<script>
export default {
  props: ["title", "url", "cancelUrl", "token", "method", "formClass", "show", "principal"],
  data: function () {
    return {
      btnGravar: {
        icon: "fa fa-check",
        text: "Gravar",
        disabled: false,
      },
    };
  },

  methods: {
    btnLoading: function (e) {
      this.btnGravar.icon = "fa fa-spinner fa-pulse";
      this.btnGravar.text = "Gravando";
      this.btnGravar.disabled = true;
    },
  },
};
</script>
