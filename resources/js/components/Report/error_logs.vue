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
              <li class="breadcrumb-item active" aria-current="page">
                LIMS
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Application Error Monitoring
              </li>
            </ol>
          </nav>
          <h4 class="mg-b-0 tx-spacing--1">Error Logs</h4>
        </div>
      </div>
  
      <div class="row row-sm">
        <!-- Start Pages -->
  
        <div class="col-md-12">
          <Toolbar class="mb-4">
            <template #start>
              <div class="search-form mg-r-10" style="width: 200px">
                <Calendar
                  id="icon"
                  v-model="form.dateFrom"
                  :showIcon="true"
                  v-tooltip="'Date From'"
                  pattern="dd/MM/yyyy"
                  autocomplete="off"
                />
              </div>
              <div class="search-form mg-r-10" style="width: 200px">
                <Calendar
                  id="icon2"
                  v-model="form.dateTo"
                  :showIcon="true"
                  v-tooltip="'Date To'"
                />
              </div>
  
              <Button
                icon="pi pi-search"
                class="p-button-success p-button-sm mr-2"
                v-tooltip="'Search'"
                @click="fetchLogs()"
              />
             
            </template>
            <template #end>
              <Button
                label="Export"
                icon="pi pi-download"
                class="p-button-help p-button-sm mr-2"
                @click="exportCSV($event)"
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
                :value="logs"
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
                  'message',
                  'level',
                  'levelname',
                  'datetime',
                ]"
              >
                <template #empty> No record found. </template>
                <template #loading> Loading data. Please wait. </template>
                <Column
                  field="id"
                  header="ID"
                  :sortable="true"
                ></Column>
                <Column
                  field="message"
                  header="Message"
                  :sortable="true"
                ></Column>
                <Column field="level" header="Level" :sortable="true"></Column>
                <Column field="level_name" header="Level Name" :sortable="true"></Column>
                <Column
                  field="datetime"
                  header="Date"
                  :sortable="true"
                ></Column>
                
              </DataTable>
            </div>
          </div>
        </div>
        <!-- End Pages -->
      </div>
    </div>
  
    <toast
      :breakpoints="{ '920px': { width: '100%', right: '0', left: '0' } }"
    ></toast>
  </template>
      <script>
  import { FilterMatchMode, FilterOperator } from "primevue/api";
  export default {
    props: ["roles"],
    data() {
      return {
        dashboard: this.$env_Url + "/dashboard",
        filters: null,
        editMsg: "Edit Role",
        form: {
          dateFrom: new Date(),
          dateTo: new Date(),
        },
        logs: [],
      };
    },
    created() {
      this.fetchLogs();
      this.initFilters();
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
  
      exportCSV() {
        this.$refs.dt.exportCSV();
      },
      async fetchLogs() {
        this.form.dateFrom = this.convert(this.form.dateFrom);
        this.form.dateTo = this.convert(this.form.dateTo);
        const res = await this.callApiwParam(
          "post",
          "/reports/getErrorLogs",
          this.form
        );
        this.logs = res.data;
        this.loading = false;
      },
      convert(string) {
        return new Date(string)
          .toLocaleString("en-us", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
          })
          .replace(/(\d+)\/(\d+)\/(\d+)/, "$3-$1-$2");
      },
    },
  };
  </script>