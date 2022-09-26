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
              <li class="breadcrumb-item active" aria-current="page">
                Scheduled Shutdown
              </li>
            </ol>
          </nav>
          <h4 class="mg-b-0 tx-spacing--1">Manage Scheduled Shutdown</h4>
        </div>
      </div>
  
      <ConfirmDialog></ConfirmDialog>
      <div class="row row-sm">
        <!-- Start Pages -->
  
        <div class="col-md-12">
          <Toolbar class="mb-4">
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
              <Button
                label="Reindex"
                icon="pi pi-database"
                class="p-button-info p-button-sm mr-2"
                @click="reindex()"
              />
              <Button
                label="Start"
                icon="pi pi-play"
                class="p-button-success p-button-sm mr-2"
                @click="start()"
              />
              <Button
                label="Stop"
                icon="pi pi-stop-circle"
                class="p-button-danger p-button-sm mr-2"
                @click="stop($event)"
              />
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
                :value="schedules"
                :paginator="true"
                :rows="10"
                stripedRows
                removableSort
                paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :rowsPerPageOptions="[10, 20, 50]"
                responsiveLayout="scroll"
                :loading="loading1"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                v-model:filters="filters"
                filterDisplay="menu"
                :globalFilterFields="[
                  'scheduled_date_time',
                  'reason',
                  'status',
                ]"
              >
                <template #empty> No record found. </template>
                <template #loading> Loading data. Please wait. </template>
  
                <Column field="scheduled_date" header="Scheduled Date" :sortable="true"></Column>
                <Column field="scheduled_time" header="Time" :sortable="false"></Column>
                <Column field="reason" header="Reason" :sortable="false"></Column>
  
                <Column field="status" header="Status" hidden></Column>
  
                <Column
                  :exportable="false"
                  style="min-width: 8rem"
                  header="Actions"
                >
                  <template #body="slotProps">
                    <Button
                      v-bind:title='editMsg'
                      icon="pi pi-pencil"
                      class="p-button-rounded p-button-success mr-2"
                      @click="edit(slotProps)"
                    />
  
                    <Button
                      v-bind:title='deleteMsg'
                      icon="pi pi-trash"
                      class="p-button-rounded p-button-warning"
                      @click="deleteSchedule(slotProps)"
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
  </template>
  <script>
  import { FilterMatchMode, FilterOperator } from "primevue/api";
  export default {
    data() {
      return {
        filters: null,
        loading:true,
        schedules: [],
        form: {
          id: 0,
        },
        editMsg: "Edit Shutdown Schedule",
        deleteMsg: "Delete Shutdown Schedule",
      };
    },
    created() {
      this.initFilters();
      this.fetchRecord();
    },
    methods: {
      initFilters() {
        this.filters = {
          global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        };
      },
      clearFilter() {
        this.filters["global"].value = null;
      },
      addNew() {
        window.location.href = this.$env_Url + "/applications/create";
      },
      async fetchRecord() {
      const res = await this.getDataFromDB("get", "/applications/getScheduledShutdown");
      this.schedules = res.data;
      this.loading = false;
    },
      edit(data) {
        let src = data.data.id,
          alt = data.data.id;
        window.location.href = this.$env_Url + "/applications/edit/" + alt;
      },
  
      deleteSchedule(data) {
        let src = data.data.id,
          alt = data.data.id;
        this.form.id = alt;
        this.$confirm.require({
          message: "Do you want to delete this record?",
          header: "Delete Confirmation",
          icon: "pi pi-info-circle",
          acceptClass: "p-button-danger",
          accept: async () => {
            const res = await this.deleteRecord(
              "post",
              "/applications/delete",
              this.form
            );
            if (res.status === 200) {
              this.rmessage();
              const index = this.schedules.findIndex(
                (shutdownapplication) => shutdownapplication.id === this.form.id
              );
              if (~index) this.schedules.splice(index, 1);
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
  
      exportCSV() {
        this.$refs.dt.exportCSV();
      },
      reindex() {
       
        this.$confirm.require({
          message: "Do you want to initiate re-indexing?",
          header: "Confirmation",
          icon: "pi pi-info-circle",
          acceptClass: "p-button-info",
          accept: async () => {
            const res = await this.submit(
              "post",
              "/applications/reindex",
              this.form
            );
            if (res.status === 200) {
              this.$toast.add({
                severity: "info",
                summary: "Success",
                detail: "Re-indexing Successfully initiated!",
                life: 3000,
            });
            } else {
              this.ermessage();
            }
          },
          reject: () => {
           
          },
        });
      },
      start() {
       
       this.$confirm.require({
         message: "Do you want to start the application?",
         header: "Confirmation",
         icon: "pi pi-info-circle",
         acceptClass: "p-button-info",
         accept: async () => {
           const res = await this.submit(
             "post",
             "/applications/systemUp",
             this.form
           );
           if (res.status === 200) {
             this.$toast.add({
               severity: "info",
               summary: "Success",
               detail: "System Successfully started!",
               life: 3000,
           });
           } else {
             this.ermessage();
           }
         },
         reject: () => {
          
         },
       });
     },
     stop() {
       
       this.$confirm.require({
         message: "Do you want to stop the application?",
         header: "Confirmation",
         icon: "pi pi-info-circle",
         acceptClass: "p-button-info",
         accept: async () => {
           const res = await this.submit(
             "post",
             "/applications/systemDown",
             this.form
           );
           if (res.status === 200) {
             this.$toast.add({
               severity: "info",
               summary: "Success",
               detail: "System Successfully stopped!",
               life: 3000,
           });
           } else {
             this.ermessage();
           }
         },
         reject: () => {
          
         },
       });
     },
    },
  };
  </script>