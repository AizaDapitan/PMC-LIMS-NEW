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
            <li class="breadcrumb-item" aria-current="page">
              <a :href="dashboard">LIMS</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
              <a :href="dashboard">Analyst</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              Add/Update
            </li>
          </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1">Add/Update - Analyst</h4>
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
      <div class="col-lg-4">
        <div class="border p-2 p-lg-3 rounded mb-3">
          <div class="row row-sm">
            <div class="col-lg-6">
              <div class="form-group">
                <label for="date-shift-assayed">Date Shift and Assayed</label>
                <input
                  type="text"
                  class="form-control"
                  id="date-shift-assayed"
                  name="date-shift-assayed"
                  pattern="\d{2}\/\d{2}\/\d{4}"
                  placeholder="mm/dd/yyyy"
                  v-model="form.dateshift"
                  disabled="true"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-shift-assayed">Time</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-shift-assayed"
                  name="time-shift-assayed"
                  v-model="form.timeshift"
                  disabled="true"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="border p-2 p-lg-3 rounded mb-3">
          <div class="row row-sm">
            <div class="col-lg-12">
              <div class="form-group">
                <label for="fusion-furnace-no">Fusion Furnace No.</label>
                <input
                  type="text"
                  class="form-control"
                  id="fusion-furnace-no"
                  name="fusion-furnace-no"
                  placeholder="#1"
                  v-model="form.fusionfurno"
                  disabled="true"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-from1">Time From</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-from1"
                  name="time-from1"
                  v-model="form.fusiontimefrom"
                  disabled="true"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-to1">To</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-to1"
                  name="time-to1"
                  v-model="form.fusiontimeto"
                  disabled="true"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="border p-2 p-lg-3 rounded mb-3">
          <div class="row row-sm">
            <div class="col-lg-12">
              <div class="form-group">
                <label for="cupellation-furnace-no"
                  >cupellation Furnace No.</label
                >
                <input
                  type="text"
                  class="form-control"
                  id="cupellation-furnace-no"
                  name="cupellation-furnace-no"
                  placeholder="#1"
                  v-model="form.cupellationfurno"
                  disabled="true"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-from2">Time From</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-from2"
                  name="time-from2"
                  v-model="form.cupellationtimefrom"
                  disabled="true"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-to2">To</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-to2"
                  name="time-to2"
                  v-model="form.cupellationtimeto"
                  disabled="true"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="row row-sm">
          <div class="col-lg-6">
            <div class="form-group">
              <label for="lab-batch">Lab Batch</label>
              <input
                type="text"
                class="form-control"
                id="lab-batch"
                name="lab-batch"
                placeholder="MM-00381"
                v-model="form.labbatch"
                disabled="true"
              />
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <label for="type">Type</label>
              <input
                type="text"
                class="form-control"
                id="transType"
                name="transType"
                v-model="form.transType"
                disabled="true"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="form-group">
          <label for="temperature">Temperature </label>
          <input
            type="text"
            class="form-control"
            id="temperature"
            name="temperature"
            placeholder="Display/Pyrometer"
            v-model="form.temperature"
            disabled="true"
          />
        </div>

        <div class="form-group">
          <label for="fusion">Fusion</label>
          <input
            type="text"
            class="form-control"
            id="fusion"
            name="fusion"
            placeholder="1050/1098"
            v-model="form.fusion"
            disabled="true"
          />
        </div>

        <div class="form-group">
          <label for="cupellation">Cupellation</label>
          <input
            type="text"
            class="form-control"
            id="cupellation"
            name="cupellation"
            placeholder="950/993"
            v-model="form.cupellation"
            disabled="true"
          />
        </div>

        <div class="form-group">
          <label for="mold-used">Mold Used</label>
          <input
            type="text"
            class="form-control"
            id="mold-used"
            name="mold-used"
            placeholder="M1M5"
            v-model="form.moldused"
            disabled="true"
          />
        </div>

        <div class="form-group">
          <label for="fire-assayer">Fire Assayer</label>
          <select
            class="custom-select tx-base"
            id="fire-assayer"
            name="fire-assayer"
            v-model="form.fireassayer"
            disabled="true"
          >
          <option v-for="fireassayer in fireassayers" :key="fireassayer.id" :value="fireassayer.name">{{fireassayer.name}}</option>
          </select>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="border p-2 p-lg-3 rounded mb-3">
          <div class="row row-sm">
            <div class="col-lg-6">
              <div class="form-group">
                <label for="date-shift-weighed1">Date and Shift Weighed</label>
                <input
                  type="text"
                  class="form-control"
                  id="date-shift-weighed1"
                  name="date-shift-weighed1"
                  pattern="\d{2}\/\d{2}\/\d{4}"
                  placeholder="mm/dd/yyyy"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="date-shift-weighed2">&nbsp;</label>
                <input
                  type="text"
                  class="form-control"
                  id="date-shift-weighed2"
                  name="date-shift-weighed2"
                  placeholder="S2"
                  v-model="this.form.shiftweighed"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="microbalance-no-check-weights"
            >Microbalance No. and Check Weights</label
          >
          <input
            type="text"
            class="form-control"
            id="microbalance-no-check-weights"
            name="microbalance-no-check-weights"
            placeholder="#1 - 0.00009, 5000, 9111"
            v-model="this.form.micnocheckweights"
          />
        </div>

        <div class="form-group">
          <label for="measured-by">Measured By</label>
          <select
            class="custom-select tx-base"
            id="measured-by"
            name="measured-by"
            v-model="this.form.measuredby"
          >
            <option
              v-for="analyst in analysts"
              :key="analyst.id"
              :value="analyst.name"
            >
              {{ analyst.name }}
            </option>
          </select>
        </div>

        <div class="form-group">
          <label for="analyzed-by">Analyzed By</label>
          <select
            class="custom-select tx-base"
            id="analyzed-by"
            name="analyzed-by"
            v-model="this.form.analyzedby"
          >
            <option
              v-for="analyst in analysts"
              :key="analyst.id"
              :value="analyst.name"
            >
              {{ analyst.name }}
            </option>
          </select>
        </div>
      </div>
      <div class="col-lg-12">
        <hr class="mg-t-10 mg-b-30" />
      </div>
    </div>

    <div class="row row-xs">
      <div class="col-lg-12">
        <button
          @click="downloadCSV"
          class="btn btn-success tx-13 btn-uppercase  mg-b-25"
          :disabled="this.disableAdd"
        >
          <i data-feather="download" class="mg-r-5"></i> Download Item Template
        </button>
      </div>
      <div class="col-lg-12">
        <div class="table-responsive-lg">
          <DataTable
            ref="dt"
            :value="items"
            :rowReorder="true"
            :paginator="true"
            :rows="10"
            stripedRows
            paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :rowsPerPageOptions="[10, 20, 50]"
            responsiveLayout="scroll"
            :loading="loading"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
            v-model:filters="filters"
            filterDisplay="menu"
            rowIndexVar
            editMode="row"
            dataKey="id"
            v-model:editingRows="editingRows"
            @row-edit-save="onRowEditSave"
            @rowReorder="onRowReorder"
          >
            <template #empty> No record found. </template>
            <template #loading> Loading data. Please wait. </template>
            <Column rowReorder headerStyle="width: 3rem" :reorderableColumn="false" />
            <Column header="Sample No.">
              <template #body="slotProps">
                <span :style="{ color: slotProps.data.reaasyedby != null ? 'red' : 'inherit' }">
                  {{ slotProps.index + 1 }}
                </span>
              </template>
            </Column>
            <Column field="id" hidden="true"></Column>
            <Column field="sampleno" header="Sample Code" :sortable="true">
              <template #body="slotProps">
                <span :style="{ color: slotProps.data.reaasyedby != null ? 'red' : 'inherit' }">
                  {{ slotProps.data.sampleno + (slotProps.data.reaasyedby != null ? ' (reassay)' : '') }}
                </span>
              </template>
            </Column>
            <Column field="source" header="Source" :sortable="true" style="min-width: 8rem">
              <template #body="slotProps">
                <span :style="{ color: slotProps.data.reaasyedby != null ? 'red' : 'inherit' }">
                  {{ slotProps.data.source }}
                </span>
              </template>
            </Column>
            <Column field="samplewtgrams" header="Sample Wt.(Grams)" :sortable="true">
              <template #body="slotProps">
                {{ slotProps.data.samplewtgrams ? parseInt(slotProps.data.samplewtgrams) : '' }}
              </template>
            </Column>
            <Column field="crusibleused" header="Crusible Used" :sortable="true"></Column>
            <Column field="transmittalno" header="Transmittal No." :sortable="true">
              <template #body="slotProps">
                <span :style="{ color: slotProps.data.reaasyedby != null ? 'red' : 'inherit' }">
                  {{ slotProps.data.transmittalno }}
                </span>
              </template>
            </Column>
            <Column field="fluxg" header="Flux (Grams)" :sortable="true">
              <template #body="slotProps">
                {{ slotProps.data.fluxg ? parseInt(slotProps.data.fluxg) : '' }}
              </template>
            </Column>
            <Column field="flourg" header="Flour (Grams)" :sortable="true">
              <template #body="slotProps">
                {{ slotProps.data.flourg ? parseInt(slotProps.data.flourg) : '' }}
              </template>
            </Column>
            <Column field="niterg" header="Niter (Grams)" :sortable="true">
              <template #body="slotProps">
                {{ slotProps.data.niterg ? parseInt(slotProps.data.niterg) : '' }}
              </template>
            </Column>
            <Column field="leadg" header="Lead (Grams)" :sortable="true">
              <template #body="slotProps">
                {{ slotProps.data.leadg ? parseInt(slotProps.data.leadg) : '' }}
              </template>
            </Column>
            <Column field="silicang" header="Silican (Grams)" :sortable="true">
              <template #body="slotProps">
                {{ slotProps.data.silicang ? parseInt(slotProps.data.silicang) : '' }}
              </template>
            </Column>
            <Column field="auprillmg" header="Au, Prill(Mg)" :sortable="true" :hidden="['Carbon', 'Solutions'].includes(form.transType)">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="augradegpt" header="Au, Grade(Gpt)" :sortable="true" :hidden="['Carbon', 'Solutions'].includes(form.transType)">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="assreadingppm" header="ASS Reading, ppm" :sortable="true" :hidden="['Bulk', 'Cut', 'Carbon', 'Solids', 'Solutions'].includes(form.transType)">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="agdoremg" header="Ag, Dore(Mg)" :sortable="true" :hidden="['Bulk', 'Cut', 'Rock', 'Mine Drill', 'Solutions'].includes(form.transType)">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="initialaggpt" header="Initial Ag (Gpt)" :sortable="true" :hidden="['Bulk', 'Cut', 'Rock', 'Mine Drill', 'Solids', 'Solutions'].includes(form.transType)">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="crusibleclearance" header="Crusible Clearance" :sortable="true" :hidden="['Rock', 'Mine Drill', 'Solutions'].includes(form.transType)">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="text"
                  min="0"
                  autofocus
                />
              </template></Column>
            <Column field="inquartmg" header="For Inquart (Mg)" :sortable="true" :hidden="['Bulk', 'Cut', 'Rock', 'Mine Drill','Carbon','Solutions'].includes(form.transType)">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="methodremarks" header="Remarks" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="text"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="trans_type" hidden="true"></Column>
            <Column field="reassayed" hidden="true"></Column>
            <Column
              :rowEditor="true"
              style="width: 7%; min-width: 8rem"
              bodyStyle="text-align:right"
            >
            </Column>
            <Column
              :exportable="false"
              style="min-width: 8rem"
              header="Actions"
            >
              <template #body="slotProps">
                <!--<Button
                  v-bind:title="editMsg"
                  icon="pi pi-pencil"
                  class="p-button-rounded p-button-success mr-2"
                  @click="editItem(slotProps)"
                  :id="'btn' + slotProps.data.id"
                  name="btnedit"
                />-->
                <Button
                  v-bind:title="reassayMsg"
                  icon="pi pi-refresh"
                  class="p-button-rounded p-button-danger mr-2"
                  @click="reassay(slotProps)"
                  :id="'btn' + slotProps.data.id"
                  name="btnedit"
                  :disabled="(slotProps.data.reassayed == 1) ? true : false"
                />
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </div>
    <!-- row -->
    <hr class="mg-t-30 mg-b-30" />
    <div class="row">
      <div class="col-lg-12">
        <div class="d-lg-flex justify-content-lg-end">
          <div
            class="
              form-group
              d-flex
              flex-column flex-lg-row
              align-items-lg-center
            "
          >
            <input type="hidden" v-model="form.itemFile" />
            <label for="customFile" class="mg-r-10">Attached CSV</label>
            <div class="custom-file mb-0 mb-lg-2">
              <input
                type="file"
                class="custom-file-input"
                id="customFile"
                ref="file"
                name="attached-csv"
                v-on:change="onFileChange"
                :disabled="disableAdd"
                accept=".csv"
              />
              <label
                class="custom-file-label"
                for="customFile"
                data-button-label="Browse"
                >{{ fileLabel }}</label
              >
            </div>
            <button
              type="submit"
              class="
                btn btn-primary
                tx-13
                btn-uppercase
                mr-2
                mb-2
                ml-lg-1
                mr-lg-0
              "
              :disabled="disableAdd"
              @click.prevent="uploadItem"
            >
              <i data-feather="upload" class="mg-r-5"></i> Upload
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="row flex-column-reverse flex-lg-row">
      <div class="col-lg-6">
        <a :href="dashboard" class="btn btn-white tx-13 btn-uppercase"
          ><i data-feather="arrow-left" class="mg-r-5"></i> Back to Dashboard</a
        >
      </div>
      <div class="col-lg-6 d-flex justify-content-start justify-content-lg-end">
        <button
          type="submit"
          class="btn btn-primary tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          @click.prevent="saveWorksheet"
        >
          <i data-feather="check-circle" class="mg-r-5"></i> Save
        </button>
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
  <DynamicDialog />
  <ConfirmDialog></ConfirmDialog>
</template>
<script>
import item from "../../components/item/item";
import { h } from "vue";
import Button from "primevue/button";
export default {
  props: ["worksheet"],
  data() {
    return {
      dashboard: this.$env_Url + "/analyst/dashboard",
      loading: true,
      fileLabel: "Choose File",
      editingRows: [],
      items: [],
      errors_exist: false,
      errors: {},
      disableAdd: false,
      isApproved: this.worksheet.isApproved,
      editMsg: "Update Sample",
      reassayMsg: "Reassay",
      fireassayers : [],
      analysts: [],
      form: {
        id: this.worksheet.id,
        labbatch: this.worksheet.labbatch,
        dateshift: this.worksheet.dateshift,
        timeshift: this.worksheet.timeshift,
        fusionfurno: this.worksheet.fusionfurno,
        fusiontimefrom: this.worksheet.fusiontimefrom,
        fusiontimeto: this.worksheet.fusiontimeto,
        fusion: this.worksheet.fusion,
        cupellationfurno: this.worksheet.cupellationfurno,
        cupellationtimefrom: this.worksheet.cupellationtimefrom,
        cupellationtimeto: this.worksheet.cupellationtimeto,
        cupellation: this.worksheet.cupellation,
        temperature: this.worksheet.temperature,
        moldused: this.worksheet.moldused,
        fireassayer: this.worksheet.fireassayer,
        ids: this.transids,
        transType: this.worksheet.transType,
        dateweighed: "",
        shiftweighed: this.worksheet.shiftweighed,
        micnocheckweights: this.worksheet.micnocheckweights,
        measuredby: this.worksheet.measuredby,
        analyzedby: this.worksheet.analyzedby,
        itemFile: null,
        reqfrom: "edit_analyst",
      },
    };
  },
  created() {
    this.fetchFireAssayer();
    this.fetchItems();
    this.fetchAnalyst();
    this.loading = false;
  },
  mounted() {
    this.form.timeshift = this.worksheet.timeshift.replace(":00.0000000", "");
    this.form.fusiontimefrom = this.worksheet.fusiontimefrom.replace(
      ":00.0000000",
      ""
    );
    this.form.fusiontimeto = this.worksheet.fusiontimeto.replace(
      ":00.0000000",
      ""
    );
    this.form.cupellationtimefrom = this.worksheet.cupellationtimefrom.replace(
      ":00.0000000",
      ""
    );
    this.form.cupellationtimeto = this.worksheet.cupellationtimeto.replace(
      ":00.0000000",
      ""
    );

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
    var yyyy = today.getFullYear();
    today = mm + "/" + dd + "/" + yyyy;

    document.getElementById("date-shift-weighed1").value =
      this.worksheet.dateweighed;

    if (this.worksheet.dateweighed == null) {
      document.getElementById("date-shift-weighed1").value = today;
    }
  },
  methods: {
    async fetchItems() {
      const res = await this.callApiwParam(
        "post",
        "/transItem/getWorksheetItems",
        this.form
      );
      this.items = res.data;
    },
    async updateItemOrder(){
      const requestData = {
        items: this.items // Assuming this.items is an array of items
      };
      const res = await this.callApiwParam(
        "post",
        "/assayer/updateItemOrder",
        requestData
      );
    },
    async fetchFireAssayer() {
      const res = await this.getDataFromDB("get", "/fireassayers/getFireAssayerActive");

      this.fireassayers = res.data;
    },
    onFileChange(e) {
      this.form.itemFile = this.$refs.file.files[0];
      this.fileLabel = this.form.itemFile.name;
    },
    async saveWorksheet() {
      this.form.dateweighed = document.getElementById(
        "date-shift-weighed1"
      ).value;
      const res = await this.submit("post", "/analyst/update", this.form, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      if (res.status === 200) {
        window.location.href = this.$env_Url + "/analyst/dashboard";
      } else {
        this.errors_exist = true;
        this.errors = res.data.errors;
      }
    },
    async onRowReorder(event){
      console.log(event);

      const draggedItem = this.items[event.dragIndex];
      this.items.splice(event.dragIndex, 1);
      this.items.splice(event.dropIndex, 0, draggedItem);
      
      this.updateItemOrder();
      this.customMessage("success", "Confirmed!", "Sample reordered successfully.", 3000);

    },
    async onRowEditSave(event) {
      let { newData, index } = event;
      if(newData.reassayed == 1){
        this.customMessage("error", "Failed", "Reassayed sample can't be edited.", 5000);
      }else{
        this.items[index] = newData;

        if(newData.trans_type === 'Bulk' || newData.trans_type === 'Cut'){
          newData.assreadingppm = 0;
          newData.agdoremg = 0;
          newData.initialaggpt = 0;
          newData.inquartmg = 0;
        }else if(newData.trans_type === 'Rock' || newData.trans_type === 'Mine Drill'){
          newData.agdoremg = 0;
          newData.initialaggpt = 0;
          newData.crusibleclearance = 0;
          newData.inquartmg = 0;
        }else if(newData.trans_type === 'Carbon'){
          newData.auprillmg = 0;
          newData.augradegpt = 0;
          newData.assreadingppm = 0;
          newData.inquartmg = 0;
        }else if(newData.trans_type === 'Solids'){
          newData.assreadingppm = 0;
          newData.initialaggpt = 0;
        }else if(newData.trans_type === 'Solutions'){
          newData.auprillmg = 0;
          newData.augradegpt = 0;
          newData.assreadingppm = 0;
          newData.agdoremg = 0;
          newData.initialaggpt = 0;
          newData.crusibleclearance = 0;
          newData.inquartmg = 0;
        }

        let itemForm = {
          id: newData.id,
          auprillmg: newData.auprillmg,
          augradegpt: newData.augradegpt,
          assreadingppm: newData.assreadingppm,
          agdoremg: newData.agdoremg,
          initialaggpt: newData.initialaggpt,
          crusibleclearance: newData.crusibleclearance,
          inquartmg: newData.inquartmg,
          methodremarks: newData.methodremarks,
          isAnalyst: true,
        }
        const res = await this.submit("post", "/transItem/update", itemForm, {
          headers: {
            "Content-Type":
              "multipart/form-data; charset=utf-8; boundary=" +
              Math.random().toString().substr(2),
          },
        });
        if (res.status === 200) {
          this.smessage();
          this.fetchItems();
        } else {
          this.errors_exist = true;
          this.errors = res.data.errors;
          // this.ermessage(res.data.errors);
        }
      }
    },
    downloadCSV() {
      axios.post(this.$env_Url+'/analyst/download-csv', this.form, { responseType: 'blob' })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'Labbatch_'+this.form.labbatch+'_.csv');
          document.body.appendChild(link);
          link.click();
        })
        .catch(error => {
          alert("Error: "+error)
        });
    },
    async uploadItem() {
      //this.fileLabel = this.form.transmittalno + "_" + this.fileLabel;
      let form = new FormData();
      form.append("itemFile", this.form.itemFile);
      _.each(this.form, (value, key) => {
        form.append(key, value);
      });
      const res = await this.submit("post", "/analyst/uploaditems", form, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      if (res.status === 200) {
        this.smessage();
        this.fetchItems();
      } else {
        this.errors_exist = true;
        this.errors = res.data.errors;
        // this.ermessage(res.data.errors);
      }
    },
    editItem(data) {
      this.showDialog(data.data);
    },
    showDialog(data) {
      const dialogRef = this.$dialog.open(item, {
        props: {
          header: "Transmittal item",
          style: {
            width: "50vw",
          },
          breakpoints: {
            "960px": "75vw",
            "640px": "90vw",
          },
          modal: true,
        },
        data: {
          transmittalno: data.transmittalno,
          id: data.id,
          sampleno: data.sampleno,
          samplewtvolume: data.samplewtvolume,
          description: data.description,
          elements: data.elements,
          methodcode: data.methodcode,
          comments: data.comments,
          isDeptUser: false,
          isReceiving: false,
          isAssayer: false,
          isAnalyst: true,
          source: data.source,
          samplewtgrams: data.samplewtgrams,
          fluxg: data.fluxg,
          flourg: data.flourg,
          niterg: data.niterg,
          leadg: data.leadg,
          silicang: data.silicang,
          crusibleused: data.crusibleused,
          labbatch: this.form.labbatch,
          transType: this.form.transType,
          auprillmg: data.auprillmg,
          augradegpt: data.augradegpt,
          assreadingppm: data.assreadingppm,
          agdoremg: data.agdoremg,
          initialaggpt: data.initialaggpt,
          crusibleclearance: data.crusibleclearance,
          inquartmg: data.inquartmg,
          methodremarks: data.methodremarks,
        },
        onClose: (options) => {
          this.fetchItems();
        },
      });
    },
    reassay(data) {
      this.$confirm.require({
        message: "Do you want to reassay sample " + data.data.sampleno + "?",
        header: "Confirmation",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        accept: async () => {
          const res = await this.deleteRecord("post", "/analyst/reassay", {
            id: data.data.id,
          });
          if (res.status === 200) {
            this.$toast.add({
              severity: "warn",
              summary: "Confirmed",
              detail: "Sample Code " + data.data.sampleno + " reassayed!",
              life: 3000,
            });
            this.fetchItems();
          } else {
            this.ermessage();
          }
        },
      });
    },
    async fetchAnalyst() {
      const res = await this.getDataFromDB(
        "get",
        "/qaanalysts/getAnalystActive"
      );

      this.analysts = res.data;
    },
  },
};
</script>

<style scoped>
  .hidden-column {
    display: none;
  }
</style>
