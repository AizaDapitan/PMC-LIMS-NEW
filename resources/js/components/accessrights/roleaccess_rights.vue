
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
                <a :href="roles">Maintenance</a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Role Access Rights
              </li>
            </ol>
          </nav>
          <h4 class="mg-b-0 tx-spacing--1">List of Roles Access Rights</h4>
        </div>
        <div>
          <div
            class="
              d-lg-flex
              align-items-lg-center
              justify-content-lg-end
              mg-t-25
              mt-lg-0
              mg-b-25
              mb-lg-0
            "
          >
            <label for="role" class="mb-lg-0 mr-lg-2">Role</label>
            <div class="form-group mb-lg-0 mn-wd-250-f">
              <select
                class="custom-select tx-base"
                id="role"
                name="role"
                :disabled="loading"
                v-model="form.role_id"
                @change="getData($event)"
              >
                <option
                  v-for="role in roles"
                  v-bind:key="role.id"
                  :value="role.id"
                >
                  {{ role.description }}
                </option>
              </select>
            </div>
            <button
              type="button"
              class="
                btn btn-primary
                tx-13
                btn-uppercase
                mr-2
                mb-2
                ml-lg-1
                mr-lg-0
                mb-lg-0
              "
              @click.prevent="save"
            >
              <i data-feather="save" class="mg-r-5"></i> Save Changes
            </button>
          </div>
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
        <div class="col-lg-12">
          <div class="table-responsive text-nowrap access-tbl">
            <table class="table table-light dataTable mb-0">
              <thead class="thead-primary">
                <tr>
                  <th class="wd-50p">Module</th>
                  <th>View</th>
                  <th>Create</th>
                  <th>Update</th>
                  <th>Delete</th>
                  <th>Print</th>
                  <th>Upload</th>
                </tr>
              </thead>
              <tbody v-for="module in modules" :key="module.id">
                <tr>
                  <td width="50%">
                    <p
                      class="
                        mg-0
                        pd-t-5 pd-b-5
                        tx-uppercase tx-semibold tx-primary
                      "
                    >
                      {{ module.description }}
                    </p>
                  </td>
  
                  <td>
                    <div class="md-checkbox custom-padding">
                      <input
                        type="checkbox"
                        class="md-check"
                        :id="module.id + '_view'"
                        @click="checkPermission($event)"
                      />
                    </div>
                  </td>
                  <td>
                    <div class="md-checkbox custom-padding">
                      <input
                        type="checkbox"
                        class="md-check"
                        :id="module.id + '_create'"
                        @click="checkPermission($event)"
                      />
                    </div>
                  </td>
                  <td>
                    <div class="md-checkbox custom-padding">
                      <input
                        type="checkbox"
                        class="md-check"
                        :id="module.id + '_edit'"
                        @click="checkPermission($event)"
                      />
                    </div>
                  </td>
                  <td>
                    <div class="md-checkbox custom-padding">
                      <input
                        type="checkbox"
                        class="md-check"
                        :id="module.id + '_delete'"
                        @click="checkPermission($event)"
                      />
                    </div>
                  </td>
                  <td>
                    <div class="md-checkbox custom-padding">
                      <input
                        type="checkbox"
                        class="md-check"
                        :id="module.id + '_print'"
                        @click="checkPermission($event)"
                      />
                    </div>
                  </td>
                  <td>
                    <div class="md-checkbox custom-padding">
                      <input
                        type="checkbox"
                        class="md-check"
                        :id="module.id + '_upload'"
                        @change="checkPermission($event)"
                      />
                    </div>
                  </td>
                </tr>
                <tr v-for="permission in permissions" :key="permission.id">
                  <template v-if="permission.module_type == module.description">
                    <td>
                      {{ permission.description }}
                    </td>
                    <td>
                      <div class="md-checkbox">
                        <input
                          type="checkbox"
                          class="md-check"
                          :id="permission.id + '_' + module.id + '_view'"
                          @change="storeID($event)"
                        />
                      </div>
                    </td>
                    <td>
                      <div class="md-checkbox">
                        <input
                          type="checkbox"
                          class="md-check"
                          :id="permission.id + '_' + module.id + '_create'"
                          @change="storeID($event)"
                        />
                      </div>
                    </td>
                    <td>
                      <div class="md-checkbox">
                        <input
                          type="checkbox"
                          class="md-check"
                          :id="permission.id + '_' + module.id + '_edit'"
                          @change="storeID($event)"
                        />
                      </div>
                    </td>
                    <td>
                      <div class="md-checkbox">
                        <input
                          type="checkbox"
                          class="md-check"
                          :id="permission.id + '_' + module.id + '_delete'"
                          @change="storeID($event)"
                        />
                      </div>
                    </td>
                    <td>
                      <div class="md-checkbox">
                        <input
                          type="checkbox"
                          class="md-check"
                          :id="permission.id + '_' + module.id + '_print'"
                          @change="storeID($event)"
                        />
                      </div>
                    </td>
                    <td>
                      <div class="md-checkbox">
                        <input
                          type="checkbox"
                          class="md-check"
                          :id="permission.id + '_' + module.id + '_upload'"
                          @change="storeID($event)"
                        />
                      </div>
                    </td>
                  </template>
                </tr>
              </tbody>
            </table>
          </div>
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
        modules: [],
        success: "",
        loading: true,
        namedept: "",
        form: {
            rolePermissions: "",
            role_id: "",
        },
        
        errors_exist: false,
        seconds: 0,
        errors: {},
      };
    },
  
    async created() {
      this.fetchUsers();
      this.fetchModules();
      this.fetchPermissions();
    },
    mounted() {
      this.fetchRoles();
    },
    methods: {
      async fetchRoles() {
        const res = await this.getDataFromDB("get", "/roles/getRoles");
        this.roles = res.data;
      },
      async fetchUsers() {
        const res = await this.getDataFromDB("get", "/accessrights/getUsers");
        this.users = res.data;
        this.loading = false;
      },
      async fetchModules() {
        const res = await this.getDataFromDB("get", "/accessrights/getModules");
        this.modules = res.data;
      },
      async fetchPermissions() {
        const res = await this.callApi("get", "/permissions/getPermissions");
        this.permissions = res.data;
      },
      getData(event){
        this.form.role_id = event.target.value
        this.getRolesPermissions(this.form.role_id);
      },
      async getRolesPermissions(roleid) {
        document.querySelectorAll('input[type=checkbox]').forEach(el => el.checked = false);
        let access = new FormData();
        access.append("roleid", roleid);
  
        const res = await this.callApiwParam(
          "post",
          "/accessrights/getRoleAccessRights",
          access
        );
        var chkid = "";
        var oldAction = "";
        var rolePermissionLenght = res.data.length;
        for (var i = 0; i < rolePermissionLenght; i++) {
          chkid = "";
          chkid =
            res.data[i].permission_id +
            "_" +
            res.data[i].module_id +
            "_" +
            res.data[i].action;
          if (chkid != "") {
            document.getElementById(
                res.data[i].module_id +
                "_" +
                res.data[i].action
            ).checked = true;
            document.getElementById(chkid).checked = true;
            if (oldAction != res.data[i].action) {
              this.storeID(
                  res.data[i].module_id +
                  "_" +
                  res.data[i].action
              );
            }
            this.storeID(chkid);
  
            oldAction = res.data[i].action;
          }
        }
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
        this.errors_exist = false;
        const res = await this.submit("post", "/accessrights/storeRole", this.form, {
          headers: {
            "Content-Type":
              "multipart/form-data; charset=utf-8; boundary=" +
              Math.random().toString().substr(2),
          },
        });
        if (res.status === 200) {
          this.smessage();
        } else {
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
      checkPermission(event) {
        var checkboxes = document.getElementsByTagName("input");
        var id = event.target.id;
        const cb = document.getElementById(id);
        for (var i = 0; i < checkboxes.length; i++) {
          if (checkboxes[i].type == "checkbox") {
            if (checkboxes[i].id.includes(id)) {
              document.getElementById(checkboxes[i].id).checked = cb.checked;
              this.storeID(checkboxes[i].id);
            }
          }
        }
      },   
      
      storeID(data) {
        var id;
        if (typeof data == "object") {
          id = data.target.id;
        } else {
          id = data;
        }
  
        var user_permissions = this.form.rolePermissions;
        if (user_permissions == null) {
          user_permissions = "";
        }
        if (document.getElementById(id).checked) {
          if (user_permissions != "") {
            user_permissions = user_permissions + "," + id;
          } else {
            user_permissions = id;
          }
        } else {
          var _count = (id.match(/_/g) || []).length;
          if (_count == 1) {
            if (user_permissions.includes("," + id)) {
              user_permissions = user_permissions.replace("," + id, "");
            } else if (user_permissions.includes(id + ",")) {
              user_permissions = user_permissions.replace(id + ",", "");
            } else {
              user_permissions = user_permissions.replace(id, "");
            }
          } else if (_count == 2) {
            
            var re = new RegExp(id, "g");
            var count = (user_permissions.match(re) || []).length;
            for (var i = 1; i <= count; i++) {
              if (user_permissions.includes("," + id)) {
                user_permissions = user_permissions.replace("," + id, "");
              } else if (user_permissions.includes(id + ",")) {
                user_permissions = user_permissions.replace(id + ",", "");
              } else {
                user_permissions = user_permissions.replace(id, "");
              }
            }
          }
        }
        this.form.rolePermissions = user_permissions;
      },    
    },
  };
  </script>
    