
<template>
  <div class="container-fluid pd-x-0">
    <div
      class="
        d-sm-flex
        align-items-center
        justify-content-between
        mg-b-20 mg-lg-b-25 mg-xl-b-30
      "
    >
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-style1 mg-b-5">
            <li class="breadcrumb-item" aria-current="page">
              <a :href="dashboard">LIMS</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
              <a :href="dashboard">Permissions</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              Edit Permission
            </li>
          </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1">Edit Permission</h4>
      </div>
    </div>
    <div v-if="errors_exist" class="alert alert-danger" role="alert">
      Whoops! Something didn't work.
      <ul>
        <div v-for="error in errors" :key="error.id">
          <li>{{ error[0] }}</li>
        </div>
      </ul>
    </div>
    <div class="row row-sm">
      <div class="col-lg-6">
        <input type="hidden" v-model="form.id" />

        <div class="form-group">
          <label class="d-block">Status <i class="text-danger">*</i></label>
          <input type="checkbox" id="isActive" :checked="form.checked == 1" />
          <span>&nbsp;</span>
          <label for="statusValue">
            <span>Active</span>
          </label>
        </div>

        <div class="form-group">
          <label class="d-block">Module <i class="text-danger">*</i></label>

          <select
            class="custom-select"
            v-model="selectedValue"
            @change="getData($event)"
          >
            <option selected>Select Module</option>
            <option
              v-for="module in modules"
              :key="module.description"
              :value="module.description"
            >
              {{ module.description }}
            </option>
          </select>
        </div>

        <div class="form-group">
          <label class="d-block"
            >Permission Description <i class="text-danger">*</i></label
          >
          <input
            type="text"
            class="form-control"
            v-model="form.description"
            placeholder="Permission Description"
          />
        </div>
      </div>

      <div class="col-lg-12 mg-t-30">
        <button
        class="btn btn-primary tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          @click.prevent="updatePermission"
        >
          <i data-feather="save" class="mg-r-5"></i> Save
        </button>
        <a
          :href="dashboard"
          class="btn btn-white tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          ><i data-feather="arrow-left" class="mg-r-5"></i> Back to Dashboard</a
        >
      </div>
    </div>
    <div class="cms-footer mg-t-50">
      <hr />
      <p class="tx-gray-500 tx-10">
        Admin Portal v1.0 • Developed by WebFocus Solutions, Inc. 2022
      </p>
    </div>
  </div>
  <toast
    :breakpoints="{ '920px': { width: '100%', right: '0', left: '0' } }"
  ></toast>
</template>

<script  type="application/javascript">
export default {
  props: ["permission"],
  data() {
    return {
      errors_exist: false,
      errors: {},
      dashboard: this.$env_Url + "/permissions/dashboard",
      selectedValue: "",
      modules: [],

      form: {
        id: this.permission.id,
        module_type: this.permission.module_type,
        description: this.permission.description,
        active: true,
        checked: this.permission.active,
      },
    };
  },

  mounted() {
    this.fetchModules();
    this.selectedValue = this.permission.module_type;
  },

  methods: {
    getData(event) {
      console.log(event.target.value);
      this.form.module_type = event.target.value;
    },

    async updatePermission() {
      this.form.active = document.getElementById("isActive").checked;
      // console.log(this.form.active);
      const res = await this.submit("post", "/permissions/update", this.form, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      if (res.status === 200) {
        this.updmessage();
        window.location.href = this.$env_Url + "/permissions/dashboard";
      } else {
        this.errors_exist = true;
        this.errors = res.data.errors;
      }
    },

    async fetchModules() {
      const res = await this.callApi("get", "/permissions/modulesList");
      this.modules = res.data;
    },
  },
};
</script>
