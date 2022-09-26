
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
              <a :href="dashboard">User</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              Create
            </li>
          </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1">Create New User</h4>
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
    <div class="row">
      <div class="col-lg-6">
        <label for="name">Select Employee</label>
        <div class="form-group search-form">
          <select
            class="form-control spinner"
            placeholder="Search name"
            @change="onChange($event)"
            :disabled="this.loading"
            v-model="namedept"
          >
            <option
              v-for="employee in employees"
              v-bind:Key="employee"
              v-bind:value="employee"
            >
              {{ employee }}
            </option>
          </select>
          <button class="btn" type="button" disabled>
            <i
              :class="{
                'pi pi-spin pi-spinner': this.loading,
                'pi pi-user': !this.loading,
              }"
            ></i>
          </button>
        </div>

        <div class="form-group">
          <label for="email"
            >Name<span class="text-danger" aria-required="true">
              *
            </span></label
          >
          <input
            type="text"
            class="form-control"
            id="name"
            name="name"
            v-model="form.name"
            disabled
          />
        </div>
        <div class="form-group">
          <label for="email"
            >Department<span class="text-danger" aria-required="true">
              *
            </span></label
          >
          <input
            type="text"
            class="form-control"
            id="dept"
            name="dept"
            v-model="form.dept"
            disabled
          />
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input
            type="email"
            class="form-control"
            id="email"
            name="email"
            v-model="form.email"
          />
        </div>

        <div class="form-group">
          <label for="username"
            >Username<span class="text-danger" aria-required="true">
              *
            </span></label
          >
          <input
            type="text"
            class="form-control"
            id="username"
            name="username"
            v-model="form.username"
            disabled
          />
        </div>

        <div class="form-group">
          <label for="role"
            >Role<span class="text-danger" aria-required="true">
              *
            </span></label
          >
          <select
            class="custom-select tx-base"
            id="role"
            name="role"
            v-model="form.role_id"
          >
            <option v-for="role in roles" :key="role.id" :value="role.id">
              {{ role.name }}
            </option>
          </select>
        </div>
        <div class="form-group">
          <label>Assigned Module <i class="text-danger">*</i></label>
          <select
          class="custom-select tx-base"
          v-model="form.assigned_module"
          >
            <option value="Department User">Department User</option>
            <option value="Department Officer">Department Officer</option>
            <option value="Receiving">Receiving</option>
            <option value="Assayer">Assayer</option>
            <option value="Tech/Digester">Tech/Digester</option>
            <option value="Analyst">Analyst</option>
            <option value="Officer">Officer</option>
          </select>
        </div>
      </div>

      <div class="col-lg-12 mg-t-30">
        <button
          class="btn btn-primary tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          @click.prevent="save"
        >
          <i data-feather="save" class="mg-r-5"></i> Save
        </button>
        &nbsp;
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
  data() {
    return {
      roles: [],
      employees: [],
      loading: false,
      success: "",
      dashboard: this.$env_Url + "/users/dashboard",
      namedept: "",
      errors_exist: false,
      errors: {},
      form: {
        // role_id: "",
        // name: "",
        // dept: "",
        // username: "",
        // status: false,
      },
    };
  },

  async created() {
    this.loading = true;
    this.fetchEmployees();
  },
  mounted() {
    this.fetchRoles();
  },
  methods: {
    async fetchRoles() {
      const res = await this.getDataFromDB("get", "/roles/getRoles");
      this.roles = res.data;
    },
    async fetchEmployees() {
      const res = await this.callApi("get", "/users/employee-lookup");
      this.employees = res.data;
      this.loading = false;
    },
    onChange(evt) {
      this.employeeArr = evt.target.value.split(":");
      this.form.name = this.employeeArr[0];
      this.form.dept = this.employeeArr[1];
      var fullName = this.employeeArr[0].split(" ");
      fullName = fullName.filter((item) => item !== "");
      this.form.username =
        fullName[0].charAt(0) + fullName[1].charAt(0) + fullName[2];
    },
    async save() {
      const res = await this.submit("post", "/users/store", this.form, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      if (res.status === 200) {
        this.smessage();
        window.location.href = this.$env_Url + "/users/dashboard";
      }  else {
        this.errors_exist = true;
        this.errors = res.data.errors;
      }
    },

    storeValue(statusValue) {
      if (document.getElementById("statusValue").checked) {
        this.form.status = 1;
      } else {
        this.form.status = 0;
      }
    },
  },
};
</script>
