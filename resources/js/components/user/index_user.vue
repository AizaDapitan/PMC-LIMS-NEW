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
          <ol class="breadcrumb breadcrumb-style1 mg-b-10">
            <li class="breadcrumb-item" aria-current="page">LIMS</li>
            <li class="breadcrumb-item" aria-current="page">
              User Maintenance
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              Dashboard
            </li>
          </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1">List of Users</h4>
      </div>
    </div>

    <div class="row row-sm">
      <!-- Start Pages -->

      <div class="col-md-12">
        <Toolbar class="mb-4 table-light">
          <template #start>
            <Button
              label="Export"
              icon="pi pi-upload"
              class="p-button-help p-button-sm mr-2"
              @click="exportCSV($event)"
            />
            <Button
              label="New"
              icon="pi pi-plus"
              class="p-button-success p-button-sm mr-2"
              @click="addNew()"
            />
          </template>
          <template #end>
            <div class="search-form mg-r-10">
              <input
                name="search"
                type="search"
                id="search"
                class="form-control"
                placeholder="Search"
                v-model="filters['global'].value"
              />
              <button class="btn filter" id="btnSearch">
                <i data-feather="search"></i>
              </button>
            </div>
          </template>
        </Toolbar>
        <div class="table-list mg-b-10">
          <div class="table-responsive-lg">
            <DataTable
              ref="dt"
              :value="users"
              :paginator="true"
              :rows="10"
              stripedRows
              removableSort
              paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
              :rowsPerPageOptions="[10, 20, 50]"
              responsiveLayout="scroll"
              :loading="loading"
              currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
              v-model:filters="filters"
              filterDisplay="menu"
              :globalFilterFields="[
                'username',
                'password',
                'name',
                'dept',
                'active',
                'role_id',
                'role',
                'email',
              ]"
            >
              <template #empty> No record found. </template>
              <template #loading> Loading data. Please wait. </template>
              <Column field="id" hidden="true"></Column>
              <Column
                field="name"
                header="Name"
                :sortable="true"
                style="min-width: 13rem"
              ></Column>
              <Column
                field="username"
                header="UserName"
                :sortable="true"
                style="min-width: 9rem"
              >
              </Column>
              <Column
                field="dept"
                header="Department"
                :sortable="true"
                style="min-width: 13rem"
              ></Column>
              <Column
                field="role"
                header="Role"
                :sortable="true"
                style="min-width: 10rem"
              ></Column>
              <Column field="email" header="Email" :sortable="true"></Column>
              <Column field="status" header="Status" :sortable="true">
                <template #body="slotProps">
                  <span
                    v-if="slotProps.data.status == 'Active'"
                    class="badge badge-success tx-uppercase"
                    >Active</span
                  >
                  <span v-else class="badge badge-danger tx-uppercase"
                    >Inactive</span
                  >
                </template>
              </Column>
              <Column
                :exportable="false"
                style="min-width: 9rem"
                header="Actions"
              >
                <template #body="slotProps">
                  <Button
                    v-bind:title="editMsg"
                    icon="pi pi-pencil"
                    class="p-button-rounded p-button-success mr-2"
                    @click="editUser(slotProps)"
                  />
                  <Button
                    v-bind:title="activeMsg"
                    icon="pi pi-user-plus"
                    class="p-button-rounded p-button-warning"
                    @click="activeUser(slotProps)"
                    :disabled="slotProps.data.isActive == 1"

                  />
                  <span> &nbsp;</span>
                  <Button
                    v-bind:title="inactiveMsg"
                    icon="pi pi-user-minus"
                    class="p-button-rounded p-button-warning"
                    @click="deactivateUser(slotProps)"
                    :disabled="slotProps.data.isActive == 0"
                  />
                </template>
              </Column>
            </DataTable>
          </div>
        </div>
      </div>
      <!-- End Pages -->
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
  <ConfirmDialog></ConfirmDialog>
</template>
  <script>
import { FilterMatchMode, FilterOperator } from "primevue/api";
export default {
  data() {
    return {
      users: [],
      filters: null,
      editMsg: "Edit User",
      deleteMsg: "Delete User",
      transIds: null,
      isDisabled: false,
      loading: true,
      form: {
        id: 0,
      },
    };
  },
  created() {
    this.fetchRecord();
    this.initFilters();
  },
  methods: {
    async fetchRecord() {
      const res = await this.getDataFromDB("get", "/users/getAllUsers");
      this.users = res.data;
      this.loading = false;
    },
    initFilters() {
      this.filters = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      };
    },
    clearFilter() {
      this.filters["global"].value = null;
    },

    viewWorksheet(data) {
      let src = data.data.id,
        alt = data.data.id;
      window.location.href = this.$env_Url + "/analyst/view/" + alt;
    },
    exportCSV() {
      this.$refs.dt.exportCSV();
    },
    editUser(data) {
      let src = data.data.id,
        alt = data.data.id;
      window.location.href = this.$env_Url + "/users/edit/" + alt;
    },
    addNew() {
      window.location.href = this.$env_Url + "/users/create";
    },
    activeUser(data) {
      let src = data.data.id,
        alt = data.data.id;
      this.form.id = alt;
      this.$confirm.require({
        message: "Do you want to activate this user?",
        header: "Activate Confirmation",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        accept: async () => {
          const res = await this.deleteRecord(
            "post",
            "/users/activate",
            this.form
          );
          console.log(res.status);
          if (res.status === 200) {
            this.amessage();
            this.fetchRecord();
          } else {
            this.ermessage();
          }
        },
        reject: () => {
          this.$toast.add({
            severity: "error",
            summary: "Cancelled",
            detail: "You have cancelled the process",
            life: 3000,
          });
        },
      });
    },

    deactivateUser(data) {
      let src = data.data.id,
        alt = data.data.id;
      this.form.id = alt;

      this.$confirm.require({
        message: "Do you want to deactivate this user?",
        header: "Deactivate Confirmation",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        accept: async () => {
          const res = await this.deleteRecord(
            "post",
            "/users/deactivate",
            this.form
          );
          if (res.status === 200) {
            this.imessage();
            this.fetchRecord();
          } else {
            this.ermessage();
          }
        },
        reject: () => {
          this.$toast.add({
            severity: "error",
            summary: "Cancelled",
            detail: "You have cancelled the process",
            life: 3000,
          });
        },
      });
    },
  },
};
</script>